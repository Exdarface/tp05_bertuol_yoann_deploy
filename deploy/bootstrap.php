<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

date_default_timezone_set('America/Lima');


$isDevMode = true;
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);
$conn = array(
	'host' => 'dpg-ceurj2g2i3mq2ss975d0-a.frankfurt-postgres.render.com',
	'driver' => 'pdo_pgsql',
	'user' => 'yb_db_user',
	'password' => 'A7CJZuDE72Q9of3lovUK2xEB90ySx0Zq',
	'dbname' => 'yb_db',
	'port' => '5432'
);

$entityManager = EntityManager::create($conn, $config);

