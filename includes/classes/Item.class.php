<?php



class Item extends Database {

	

	public function __construct() 

	{

		if(!parent::$connection) { parent::__construct(); }

	}



	

	public function getRandomName($length = false)

	{

		$consonants = array('q','w','r','t','z','p','s','d','f','g','h','j','k','l','y','x','c','v','b','n','m'); //0-20

		$syllables = array('a','e','i','o','u'); //0-4

		$resulingName = "";

		if($length == false) { $length = rand(3, 10); }

		for($i = 0; $i < $length; $i++)

		{

			if($i%2 == 0) { $resulingName .= $consonants[rand(0,20)]; } else { $resulingName .= $syllables[rand(0,4)]; }

		}

		return ucfirst($resulingName);

	}
	
	public function giveRandomItem($item_key, $character_id = false)

	{

		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }

		list($skin_id, $image) = $this->getRandomSkin($item_key);

		$inv_slot = $this->getEmptyInventorySlot($character_id);

		$name = $this->getRandomName();

		

		$quality = '';

		$definite_properties = 0;

		if(rand(0,100) > 50) 

		{

			$quality = 'Polished';

			$definite_properties = 1;

			if(rand(0,100) > 60)

			{

				$quality = 'Refurbished';

				$definite_properties = 2;

				if(rand(0,100) > 70)

				{

					$quality = 'Unique';

					$definite_properties = 3;

					if(rand(0,100) > 80)

					{

						$quality = 'Supreme';

						$definite_properties = 4;

						if(rand(0,100) > 90)

						{

							$quality = 'Legendary';

							$definite_properties = 5;

						}

					}

				}

			}

		}

		

		$gen_item_id = $this->insert('items',array('Character_ID' => $character_id, 'Skin_ID' => $skin_id, 'Type' => $item_key, 'Quality' => $quality, 'Name' => $name, 'Slot' => $inv_slot));

		if($quality != '') { $skin = $this->load('Skin'); $skin->applyEffect($image, strtolower($quality), 'under_image'); }		

		

		$item_type = $this->selectRecurse('*','item_types',array('Key' => $item_key));

		$item_base_type = "'".$item_type['Key']."'"; while(isset($item_type['Parent'])) { $item_type = $item_type['Parent']; $item_base_type .= ', '."'".$item_type['Key']."'"; }

		$available_properties = $this->select('*', 'item_properties', '`Item_type` IN ('.$item_base_type.')', true);

		

		if((rand(0,100) > 30 OR $definite_properties > 0) AND count($available_properties) > 0) 

		{

			$select_property = rand(0,count($available_properties)-1); $definite_properties--;

			$this->insert('items_stats',array('Item_ID' => $gen_item_id, 'Property_ID' => $available_properties[$select_property]['ID'], 'Value' => rand(1,50)));

			unset($available_properties[$select_property]); $available_properties = array_values($available_properties);

			

			if((rand(0,100) > 60 OR $definite_properties > 0) AND count($available_properties) > 0) 

			{

				$select_property = rand(0,count($available_properties)-1); $definite_properties--;

				$this->insert('items_stats',array('Item_ID' => $gen_item_id, 'Property_ID' => $available_properties[$select_property]['ID'], 'Value' => rand(1,50)));

				unset($available_properties[$select_property]); $available_properties = array_values($available_properties);

				

				if((rand(0,100) > 70 OR $definite_properties > 0) AND count($available_properties) > 0) 

				{

					$select_property = rand(0,count($available_properties)-1); $definite_properties--;

					$this->insert('items_stats',array('Item_ID' => $gen_item_id, 'Property_ID' => $available_properties[$select_property]['ID'], 'Value' => rand(1,50)));

					unset($available_properties[$select_property]); $available_properties = array_values($available_properties);

					

					if((rand(0,100) > 80 OR $definite_properties > 0) AND count($available_properties) > 0) 

					{

						$select_property = rand(0,count($available_properties)-1); $definite_properties--;

						$this->insert('items_stats',array('Item_ID' => $gen_item_id, 'Property_ID' => $available_properties[$select_property]['ID'], 'Value' => rand(1,50)));

						unset($available_properties[$select_property]); $available_properties = array_values($available_properties);

						if((rand(0,100) > 90 OR $definite_properties > 0) AND count($available_properties) > 0) 

						{

							$select_property = rand(0,count($available_properties)-1); $definite_properties--;

							$this->insert('items_stats',array('Item_ID' => $gen_item_id, 'Property_ID' => $available_properties[$select_property]['ID'], 'Value' => rand(1,50)));

							unset($available_properties[$select_property]); $available_properties = array_values($available_properties);

						}

					}

				}

			}

		}

	

		$this->pre($this->getItem($inv_slot,$character_id));

	}

}



?>