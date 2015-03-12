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
class Tx_Patenschaften_Controller_IndexController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Patenschaften_Domain_Repository_BookRepository
	 * @inject
	 */
	protected $bookRepository;

	/**
	 * @param Tx_Patenschaften_Domain_Model_Book $book
	 */
	public function detailAction(Tx_Patenschaften_Domain_Model_Book $book) {
		$singleBook = $this->bookRepository->findByUid($book);
		$this->view->assign('book', $singleBook);
	}

	public function listAction() {
		$books = $this->bookRepository->findNonTakenBooks();
		$this->view->assign('books', $books);
	}

	public function listTakenAction() {

	}

}