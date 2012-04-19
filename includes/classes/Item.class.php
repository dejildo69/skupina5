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

public function getRandomSkin($item_key)
{

	if(rand(0,50) > 50) 

	{

		$existant_skins = $this->select('*','skins',array('Item_type' => $item_key));

		if(count($existant_skins) > 10) { return $existant_skins[rand(0,count($existant_skins)-1)]['ID']; }

	}

	

	$skin = $this->load('Skin');

	$item = $this->selectRecurse('*','item_types',array('Key' => $item_key));

	return $skin->getSkin($item);

}

public function getForgeSummary($slot, $character_id = false)
{

	$slot = substr($slot, 0, -6);

	if($character_id == false) { $character_id = $_SESSION['character']['ID']; }

	$smithing_items = $this->select('*','items',"`Character_ID` = '".$character_id."' AND `Slot` LIKE '%blacksmith%'",true);

	$upgrading_slot = $slot;

	$minor_item_slot = '';

	if($slot == 'blacksmith_slot_02') { $minor_item_slot = 'blacksmith_slot_01'; } else { $minor_item_slot = 'blacksmith_slot_02'; }

	

	if(count($smithing_items) == 2)

	{

		$forgeSummary = array();			

		$major = $this->getItem($upgrading_slot, $character_id);

		$minor = $this->getItem($minor_item_slot, $character_id);

		

		if($minor !== false) 

		{

			foreach($minor['Stats'] as $stat)

			{

				if(isset($major['Stats'][$stat['Key']])) 

				{ 

					$forgeSummary[$stat['Key']] = $major['Stats'][$stat['Key']];

					$forgeSummary[$stat['Key']]['Increase'] = $stat['Value'] * 0.15;

					$forgeSummary[$stat['Key']]['AlterationType'] = 'old_stat';

				}

				else 

				{ 

					$forgeSummary[$stat['Key']] = $stat;

					$forgeSummary[$stat['Key']]['Increase'] = $stat['Value'] * 0.15;

					$forgeSummary[$stat['Key']]['AlterationType'] = 'new_stat';

				}

			}

		}

		return $forgeSummary;

	} else { return false; }

}	



public function forge($slot, $character_id = false)
{

	$slot = substr($slot, 0, -6);

	if($character_id == false) { $character_id = $_SESSION['character']['ID']; }

	$smithing_items = $this->select('*','items',"`Character_ID` = '".$character_id."' AND `Slot` LIKE '%blacksmith%'",true);

	$upgrading_slot = $slot;

	$minor_item_slot = '';

	if($slot == 'blacksmith_slot_02') { $minor_item_slot = 'blacksmith_slot_01'; } else { $minor_item_slot = 'blacksmith_slot_02'; }

	

	if(count($smithing_items) == 2)

	{

		$major = $this->getItem($upgrading_slot, $character_id);

		$minor = $this->getItem($minor_item_slot, $character_id);

		$inv_slot_id = $this->getEmptyInventorySlot($character_id);

		

		if($minor !== false) 

		{

			foreach($minor['Stats'] as $stat)

			{

				if(isset($major['Stats'][$stat['Key']])) 

				{ 

					$alteration = $stat['Value'] * 0.15;

					$base = $major['Stats'][$stat['Key']]['Value'];

					$this->update('items_stats',array('Value' => ($alteration + $base)),array('Item_ID' => $major['ID'], 'Property_ID' => $stat['ID']));

				}

				else 

				{ 

					$alteration = $stat['Value'] * 0.15;

					$this->update('items_stats',array('Item_ID' => $major['ID'], 'Value' => $alteration),array('Item_ID' => $minor['ID'], 'Property_ID' => $stat['ID']));

				}

			}

			

			$result = mysql_query("DELETE FROM `aod_items` WHERE `ID` = '".$minor['ID']."'");

			$result = mysql_query("DELETE FROM `aod_items_stats` WHERE `Item_ID` = '".$minor['ID']."'");

			$this->update('items',array('Slot' => $inv_slot_id),array('ID' => $major['ID']));

		}

		

		return array($inv_slot_id);

	}

}


?>