<?php
use Doctrine\ORM\Mapping\Entity as Entity;
use Doctrine\ORM\Mapping\Table as Table;
use Doctrine\ORM\Mapping\Column as Column;
use Doctrine\ORM\Mapping\Id as Id;
use Doctrine\ORM\Mapping\GeneratedValue as GeneratedValue;

/**
 * @Entity @Table(name="products")
 **/
class Product {
	/**
	 * @Id @Column(type="integer") @GeneratedValue
	 **/
	public $id;
	/**
	 * @Column(type="string")
	 **/
	public $name;
	/**
	 * @Column(type="string")
	 **/
	public $description;
	/**
	 * @Column(type="integer")
	 **/
	public $price;

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getPrice() {
		return $this->price;
	}
	public function setPrice($price) {
		$this->price = $price;
	}
}