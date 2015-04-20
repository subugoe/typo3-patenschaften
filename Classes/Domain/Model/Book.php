<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Ingo Pfennigstorf <pfennigstorf@sub-goettingen.de>
 *      Goettingen State Library
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Description
 */
class Tx_Patenschaften_Domain_Model_Book extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $author;

	/**
	 * @var string
	 */
	protected $search;

	/**
	 * @var string
	 */
	protected $caption;

	/**
	 * @var string
	 */
	protected $signature;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $price;

	/**
	 * @var string
	 */
	protected $damage;

	/**
	 * @var string
	 */
	protected $help;

	/**
	 * @var string
	 */
	protected $sponsorship;

	/**
	 * @var Tx_Patenschaften_Domain_Model_Category
	 */
	protected $category;

	/**
	 * @var string
	 */
	protected $images;

	/**
	 * @return string
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @return string
	 */
	public function getImages() {
		return $this->images;
	}

	/**
	 * @param string $bilder
	 */
	public function setImages($bilder) {
		$this->images = $bilder;
	}

	/**
	 * @return string
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * @param string $caption
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
	}

	/**
	 * @return Tx_Patenschaften_Domain_Model_Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param Tx_Patenschaften_Domain_Model_Category $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * @return string
	 */
	public function getDamage() {
		return $this->damage;
	}

	/**
	 * @param string $damage
	 */
	public function setDamage($damage) {
		$this->damage = $damage;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getHelp() {
		return $this->help;
	}

	/**
	 * @param string $help
	 */
	public function setHelp($help) {
		$this->help = $help;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @param string $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

	/**
	 * @return string
	 */
	public function getSearch() {
		return $this->search;
	}

	/**
	 * @param string $search
	 */
	public function setSearch($search) {
		$this->search = $search;
	}

	/**
	 * @return string
	 */
	public function getSignature() {
		return $this->signature;
	}

	/**
	 * @param string $signature
	 */
	public function setSignature($signature) {
		$this->signature = $signature;
	}

	/**
	 * @return string
	 */
	public function getSponsorship() {
		return $this->sponsorship;
	}

	/**
	 * @param string $sponsorship
	 */
	public function setSponsorship($sponsorship) {
		$this->sponsorship = $sponsorship;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $titel
	 */
	public function setTitle($titel) {
		$this->title = $titel;
	}

}