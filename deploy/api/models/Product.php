<?php

// create a product class
class Product {
	public $id;
	public $name;
	public $description;
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