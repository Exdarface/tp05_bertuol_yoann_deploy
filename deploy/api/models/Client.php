<?php

use Doctrine\ORM\Mapping\Entity as Entity;
use Doctrine\ORM\Mapping\Table as Table;
use Doctrine\ORM\Mapping\Column as Column;
use Doctrine\ORM\Mapping\Id as Id;
use Doctrine\ORM\Mapping\GeneratedValue as GeneratedValue;

/**
 * @Entity @Table(name="clients")
 **/
class Client
{
	/**
	 * @Id @Column(type="integer") @Identity
	 **/
    public $id;
	/**
	 * @Column(type="string")
	 **/
    public $firstname;
	/**
	 * @Column(type="string")
	 **/
    public $lastname;
	/**
	 * @Column(type="string")
	 **/
    public $email;
	/**
	 * @Column(type="string")
	 **/
    public $phone;
	/**
	 * @Column(type="string")
	 **/
    public $gender;
	/**
	 * @Column(type="string")
	 **/
    public $address;
	/**
	 * @Column(type="string")
	 **/
    public $city;
	/**
	 * @Column(type="string")
	 **/
    public $zip;
	/**
	 * @Column(type="string")
	 **/
    public $country;
	/**
	 * @Column(type="string")
	 **/
    public $login;
	/**
	 * @Column(type="string")
	 **/
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