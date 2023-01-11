<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use \Firebase\JWT\JWT as JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/models/Client.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/../bootstrap.php';

const JWT_SECRET = "makey1234567";
// Create Slim AppFactory
$app = AppFactory::create();
// Add Middleware : JSON, Error, Headers
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add(function(Request $request, RequestHandler $handler) {
	$response = $handler->handle($request);
	$response = $response->withAddedHeader('Content-Type', 'application/json');
	$response = $response->withAddedHeader('Access-Control-Allow-Origin', '*');
	$response = $response->withAddedHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
	$response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	return $response;
});
// Set Base Path
$app->setBasePath("/api");

$options = [
    "attribute" => "token",
    "header" => "Authorization",
    "regexp" => "/Bearer\s+(.*)$/i",
    "secure" => false,
    "algorithm" => ["HS256"],
    "secret" => JWT_SECRET,
    "path" => ["/api"],
    "ignore" => ["/api/login", "/api/hello", "/api/signup"],
    "error" => function ($response, $arguments) {
        $data = array('ERREUR' => 'Connexion', 'MESSAGE' => 'non-valid JWT');
        $response = $response->withStatus(401);
		$response->getBody()->write(json_encode($data));
        return $response;
    }
];

function createJwT (Response $response, int $payload) : string {

    $issuedAt = time();
    $expirationTime = $issuedAt +360000; // jwt valid for 100 hours from the issued time
	$payload = array(
		"iat" => $issuedAt,
		"exp" => $expirationTime,
		"data" => $payload
	);
	$token = JWT::encode($payload, JWT_SECRET, "HS256");
	return $token;
}

// Hello world route on '/hello'
$app->get('/hello', function (Request $request, Response $response) {
	global $entityManager;
	$clients = $entityManager->getRepository(Client::class)->findAll();
	$response->getBody()->write(json_encode($clients));
	return $response;
});

#region USER_MIDDLEWARE

$app->post('/login', function (Request $request, Response $response) {
	$login = $request->getParsedBody()['login'] ?? '';
	$password = $request->getParsedBody()['password'] ?? '';

	if (empty($login) || empty($password)|| !preg_match("/^[a-zA-Z0-9]+$/", $login) || !preg_match("/^[a-zA-Z0-9]+$/", $password)) {
		$response = $response->withStatus(401);
		$response->getBody()->write(json_encode(array('ERREUR' => 'Connexion', 'MESSAGE' => 'Incorrect or missing credentials')));
		return $response;
    }

	global $entityManager;
    $user = $entityManager->getRepository(Client::class)->findOneBy(array('login' => $login, 'password' => $password));

	if($user){
		$id = $user->getId();
		$token = createJwT($response, $id);
		$response->getBody()->write(json_encode(array('token' => $token)));
	} else {
		$response = $response->withStatus(401);
		$response->getBody()->write(json_encode(array('ERREUR' => 'Connexion', 'MESSAGE' => 'Unknown User')));
	}
	return $response;
});

#endregion

#region PRODUCT_MIDDLEWARE

$app->get('/product', function (Request $request, Response $response) {
	$data = file_get_contents(__DIR__ . "/mocks/products.json");
	$response->getBody()->write($data);
	return $response;
});

// Get product by id from ./mocks/products.json
$app->get('/product/{id}', function (Request $request, Response $response, $args) {
	$id = $args['id'];
	$data = file_get_contents(__DIR__ . "/mocks/products.json");
	$data = json_decode($data, true);
	foreach ($data as $value) {
		if($value['id'] == $id){
			$product = $value;
		}
	}
	if ($product) {
		$response->getBody()->write(json_encode($product));
	} else {
		$response = $response->withStatus(401);
		$response->getBody()->write("Product not found");
	}
	return $response;
});

// Add product to ./mock/products.json
$app->post('/product', function (Request $request, Response $response) {
	$error=false;
	$body = $request->getParsedBody();
	$id = $body['id'] ?? "";
	$name = $body['name'] ?? "";
	$price = $body['price'] ?? "";
	$description = $body['description'] ?? "";

	// Check format id, name & price
	if (empty($id) || empty($name) || empty($price) || !preg_match("/^[0-9]+$/", $id || !preg_match("/^[a-zA-Z0-9]+$/", $name) || !preg_match("/^[0-9]+$/", $price))) {
		$error=true;
	}

	if($error){
		$response = $response->withStatus(401);
		$response->getBody()->write("Bad request");
		return $response;
	}

	// Add product to ./mock/products.json
	$json = json_decode(file_get_contents(__DIR__ . "/mocks/products.json"), true);
	$json[] = array('id' => $id, 'name' => $name, 'price' => $price, 'description' => $description);
	$json = json_encode($json);
	file_put_contents(__DIR__ . "/mocks/products.json", $json);

	$response->getBody()->write("Product added");
	return $response;
});

// Update product to ./mock/products.json
$app->put('/product', function (Request $request, Response $response) {
	$error=false;
	$body = $request->getParsedBody();
	$id = $body['id'] ?? "";
	$name = $body['name'] ?? "";
	$price = $body['price'] ?? "";
	$description = $body['description'] ?? "";

	// Check format id, name & price
	if (empty($id) || empty($name) || empty($price) || !preg_match("/^[0-9]+$/", $id || !preg_match("/^[a-zA-Z0-9]+$/", $name) || !preg_match("/^[0-9]+$/", $price))) {
		$error=true;
	}

	if($error){
		$response = $response->withStatus(401);
		$response->getBody()->write("Bad request");
		return $response;
	}

	// Update product to ./mock/products.json
	$json = json_decode(file_get_contents(__DIR__ . "/mocks/products.json"), true);
	foreach ($json as $key => $value) {
		if ($value['id'] == $id) {
			$json[$key]['name'] = $name;
			$json[$key]['price'] = $price;
			$json[$key]['description'] = $description;
		}
	}
	$json = json_encode($json);
	file_put_contents(__DIR__ . "/mocks/products.json", $json);

	$response->getBody()->write("Product updated");
	return $response;
});

// Delete product to ./mock/products.json
$app->delete('/product', function (Request $request, Response $response) {
	$error=false;
	$body = $request->getParsedBody();
	$id = $body['id'] ?? "";

	// Check format id
	if (empty($id) || !preg_match("/^[0-9]+$/", $id)) {
		$error=true;
	}

	if($error){
		$response = $response->withStatus(401);
		$response->getBody()->write("Bad request");
		return $response;
	}

	// Delete product to ./mock/products.json
	$json = json_decode(file_get_contents(__DIR__ . "/mocks/products.json"), true);
	foreach ($json as $key => $value) {
		if ($value['id'] == $id) {
			unset($json[$key]);
		}
	}
	$json = json_encode($json);
	file_put_contents(__DIR__ . "/mocks/products.json", $json);

	$response->getBody()->write("Product deleted");
	return $response;
});

#endregion

#region CLIENT_MIDDLEWARE

$app->get('/clients', function (Request $request, Response $response) {
	global $entityManager;
	$clients = $entityManager->getRepository(Client::class)->findAll();
	$response->getBody()->write(json_encode($clients));
	return $response;
});

$app->get('/client', function (Request $request, Response $response) {
	$token_header = $request->getHeader("Authorization")[0];

	if(!preg_match('/Bearer\s(\S+)/', $token_header, $matches)) {
		$response = $response->withStatus(401);
		$response->getBody()->write("Invalid token");
		return $response;
	}
	$token = JWT::decode($matches[1], new Key(JWT_SECRET, "HS256"));
	$now = new DateTimeImmutable();
	if($token->exp < $now->getTimestamp()) {
		$response = $response->withStatus(401);
		$response->getBody()->write("Token expired");
		return $response;
	}
	global $entityManager;
	$client = $entityManager->getRepository(Client::class)->find($token->data);
	if ($client) {
		$response->getBody()->write(json_encode($client->jsonSerialize()));
	} else {
		$response = $response->withStatus(404);
		$response->getBody()->write("Client not found");
	}
	return $response;
});

$app->post('/signup', function (Request $request, Response $response) {
	$body = $request->getParsedBody();
	$firstname = $body['firstname'] ?? "";
	$lastname = $body['lastname'] ?? "";
	$email = $body['email'] ?? "";
	$phone = $body['phone'] ?? "";
	$address = $body['address'] ?? "";
	$city = $body['city'] ?? "";
	$gender = $body['gender'] ?? "";
	$zip = $body['zip'] ?? "";
	$country = $body['country'] ?? "";
	$login = $body['login'] ?? "";
	$password = $body['password'] ?? "";

	if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($gender) || empty($zip) || empty($country) || empty($login) || empty($password) ||!preg_match("/^[a-zA-Z]+$/", $firstname) || !preg_match("/^[a-zA-Z]+$/", $lastname) || !preg_match("/^[a-zA-Z0-9]+$/", $login) || !preg_match("/^[a-zA-Z0-9]+$/", $password) || !preg_match("/^[a-zA-Z0-9\s]+$/", $city) || !preg_match("/^[a-zA-Z0-9\s]+$/", $country) || !preg_match("/^[0-9]+$/", $zip) || !preg_match("/^[0-9]+$/", $phone) || !preg_match("/^[a-zA-Z0-9\s]+$/", $address) || !preg_match("/^[a-zA-Z0-9]+$/", $gender) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$response = $response->withStatus(401);
		$response->getBody()->write("Bad request");
		return $response;
	}

	global $entityManager;
	$client = new Client();
	$client->setFirstname($firstname);
	$client->setLastname($lastname);
	$client->setEmail($email);
	$client->setPhone($phone);
	$client->setGender($gender);
	$client->setAddress($address);
	$client->setCity($city);
	$client->setZip($zip);
	$client->setCountry($country);
	$client->setLogin($login);
	$client->setPassword($password);
	$entityManager->persist($client);
	$entityManager->flush();
	$response->getBody()->write(json_encode($client->jsonSerialize()));
	return $response;
});

$app->put('/client', function (Request $request, Response $response) {
	$body = $request->getParsedBody();
	$id = $body['id'] ?? "";

	if (empty($id) || !preg_match("/^[0-9]+$/", $id)) {
		$response = $response->withStatus(401);
		$response->getBody()->write("Missing id");
		return $response;
	}

	global $entityManager;
	$client = $entityManager->getRepository(Client::class)->find($id);
	$client = $body['firstname'] ? $client->setFirstname($body['firstname']) : $client;
	$client = $body['lastname'] ? $client->setLastname($body['lastname']) : $client;
	$client = $body['email'] ? $client->setEmail($body['email']) : $client;
	$client = $body['phone'] ? $client->setPhone($body['phone']) : $client;
	$client = $body['gender'] ? $client->setGender($body['gender']) : $client;
	$client = $body['address'] ? $client->setAddress($body['address']) : $client;
	$client = $body['city'] ? $client->setCity($body['city']) : $client;
	$client = $body['zip'] ? $client->setZip($body['zip']) : $client;
	$client = $body['country'] ? $client->setCountry($body['country']) : $client;
	$client = $body['login'] ? $client->setLogin($body['login']) : $client;
	$client = $body['password'] ? $client->setPassword($body['password']) : $client;
	$entityManager->persist($client);
	$entityManager->flush();
	$response->getBody()->write(json_encode($client->jsonSerialize()));
	return $response;
});

$app->delete('/client', function (Request $request, Response $response) {
	$body = $request->getParsedBody();
	$id = $body['id'] ?? "";

	// Check format
	if (empty($id) || !preg_match("/^[0-9]+$/", $id)) {
		$response = $response->withStatus(401);
		$response->getBody()->write("Bad request");
		return $response;
	}

	// Delete client to database
	global $entityManager;
	$client = $entityManager->getRepository(Client::class)->find($id);
	$entityManager->remove($client);
	$entityManager->flush();
	$response->getBody()->write("Client deleted");
	return $response;
});

#endregion

$app->add(new Tuupola\Middleware\JwtAuthentication($options));
$app->run();