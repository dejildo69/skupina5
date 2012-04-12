<?php

class Market extends Database {
	
	private $item;
	
	public function __construct() 
	{
		if(!parent::$connection) { parent::__construct(); }
		$this->item = $this->load('Item');
	}
	
	public function answer()
	{
		if(isset($_GET['categories'])) { $categories = $this->getCategories(); require_once('pages/blocks/categories.php'); }
		if(isset($_GET['browse'])) 
		{ 
			$items = $this->getItemsOnSale($_GET['browse']); 
			$category = $this->select('*','item_types',array('ID' => substr($_GET['browse'],7))); 
			require_once('pages/blocks/shopping_list.php'); 
		}
	}
}

?>