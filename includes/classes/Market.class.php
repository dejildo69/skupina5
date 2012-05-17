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
	
	
	
	public function getCategories()	

	{

		return $this->selectTree('*','item_types', array('Parent_ID' => '0'));

	}

	

	public function getItemsOnSale($type_id)	

	{

		$type_id = substr($type_id,7);

		$all_sub_categories = $this->selectTree('*','item_types', array('ID' => $type_id));

		$entries = $this->select('*', 'items', "`Slot` LIKE '%market%' AND `Type` IN ('".implode("', '",$this->getTreeKeys($all_sub_categories))."')", true);

		$items = array(); 

		foreach($entries as $entry) 

		{ 

			$item = $this->item->getItem($entry['Slot'], $entry['Character_ID']);  

			$item['Owner'] = $this->select('*', 'characters', array('ID' => $entry['Character_ID']));

			$items[] = $item;

		}

		return $items;		

	}

	

	public function getTreeKeys($tree)

	{

		$keys_array = array();

		if(is_array($tree)) 

		{			

			foreach($tree as $branch) 

			{

				array_push($keys_array, $branch['Key']);

				if(is_array($branch['Children'])) 

				{ 

					$keys_array = array_merge($keys_array, $this->getTreeKeys($branch['Children']));

				}

			}

		}

		return $keys_array;

	}
}



?>