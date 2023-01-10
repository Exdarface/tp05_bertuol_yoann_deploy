<?php

class Client
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $gender;
    public $address;
    public $city;
    public $zip;
    public $country;
    public $login;
    public $password;

    public function getId()
    {
        return $this->id;
    }
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
    public function setLogin($login)
    {
        $this->login = $login;
    }
    public function setGender($gender)
    {
        $this->gender = $gender;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function setAddress($address)
    {
        $this->address = $address;
    }
    public function setCity($city)
    {
        $this->city = $city;
    }
    public function setZip($zip)
    {
        $this->zip = $zip;
    }
    public function setCountry($country)
    {
        $this->country = $country;
    }
}