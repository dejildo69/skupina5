<?php

class Character extends Database {
	
	private $item;
	private $experience;
	
	public function __construct() 
	{
		if(!parent::$connection) { parent::__construct(); }
		$this->item = $this->load('Item');
		$this->experience = $this->load('Experience');
	}
	
	public function answer()
	{
		if(isset($_GET['slot'])) { $item = $this->item->getItem($_GET['slot']); require_once('pages/blocks/item.php'); }
		if(isset($_GET['upgrade_item'])) { $item = $this->item->getItem($_GET['upgrade_item']); require_once('pages/blocks/upgrade_items.php'); }
		if(isset($_GET['sell_item'])) { $item = $this->item->getItem($_GET['sell_item']); require_once('pages/blocks/sell_items.php'); }
		if(isset($_GET['buy_item'])) { $item = $this->item->getItem($_GET['buy_item']); require_once('pages/blocks/buy_items.php'); }
		
		
		if(isset($_GET['total'])) { $stats = $this->getFullStats($_SESSION['character']['ID']); require_once('pages/blocks/stats.php'); }
		if(isset($_GET['inventory'])) { require_once('pages/blocks/inventory.php'); }
		
		if(isset($_GET['equip'])) { $slots = $this->item->equip($_GET['equip']); if($slots === false) { return "error"; } else { echo($slots[0].'|'.$slots[1]); }  }
		if(isset($_GET['smith'])) { $slots = $this->item->smith($_GET['smith']); if($slots === false) { return "error"; } else { echo($slots[0].'|'.$slots[1]); }  }
		if(isset($_GET['forge'])) { $slots = $this->item->forge($_GET['forge']); if($slots === false) { return "error"; } else { echo($slots[0]); }  }
		if(isset($_GET['sell']) AND isset($_GET['price'])) { $slots = $this->sell($_GET['sell'], $_GET['price']); if($slots === false) { return "error"; } else { echo($slots); }  }
		if(isset($_GET['buy'])) { $slots = $this->buy($_GET['buy']); if($slots === false) { return "error"; } else { echo($slots); }  }
	
		/* Temp trials */
		if(isset($_GET['killing'])) { if($this->giveExperience($_GET['damage']*4)) { echo("You just gained a level"); } }
		if(isset($_GET['skilling'])) { if($this->giveSkillExperience($_GET['effort']*4, $_GET['skill_id'],$_SESSION['character']['ID'])) { echo("You just gained a level"); } }
	}	
	
	public function buy($item_id, $character_id = false)
	{
		$item_id = substr($item_id, 0, -4);
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		
		$purchused_item = $this->select('*', 'items', array('ID' => $item_id));
		$buying_character = $this->select('*', 'characters', array('ID' => $character_id));
		if($buying_character['Gold'] >= $purchused_item['Price'])
		{
			$selling_character = $this->select('*', 'characters', array('ID' => $purchused_item['Character_ID']));
			$inventory_slot = $this->item->getEmptyInventorySlot($character_id);
			$this->update('characters',array('Gold' => $buying_character['Gold']-$purchused_item['Price']),array('ID' => $buying_character['ID']));
			$this->update('characters',array('Gold' => $selling_character['Gold']+$purchused_item['Price']),array('ID' => $selling_character['ID']));			
			$this->update('items',array('Slot' => $inventory_slot, 'Character_ID' => $character_id),array('ID' => $item_id));
			return $inventory_slot;
		}
		else
		{
			return false;
		}
	}
	public function sell($slot, $price, $character_id = false)
	{
		//$slot = substr($slot, 0, -5);
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$merchant_slot = $this->item->getEmptyMerchantSlot($character_id);
		$this->update('items',array('Slot' => $merchant_slot, 'Price' => $price),array('Character_ID' => $character_id, 'Slot' => $slot));
		return $slot;
	}
	
	public function levelUp($character) 
	{
		return $this->update('characters',array('Level' => $character['Level']+1),array('ID' => $character['ID']));
	}
	
	public function skillUp($character, $skill) 
	{
		return $this->update('characters_skills',array('Level' => $skill['Level']+1),array('Character_ID' => $character['ID'], 'Skill_ID' => $skill['Skill_ID']));
	}
	
	public function giveExperience($experience, $character_id = false)
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$character = $this->getCharacter($character_id);
		if($this->update('characters',array('Experience' => $character['Experience']+$experience),array('ID' => $character_id)))
		{
			if($character['Experience'] + $experience >= $character['Next']) 
			{
				return $this->levelUp($character);
			}
			else
			{
				return false;
			}
		} 
		else { return false; }
	}
	
	public function giveSkillExperience($experience, $skill_id, $character_id = false)
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$character = $this->getCharacter($character_id);
		$skill = $this->getSkill($skill_id);
		echo("<pre>Char"); print_r($character); echo("</pre>");
		echo("<pre>Skill"); print_r($skill); echo("</pre>");
		if($this->update('characters_skills',array('Experience' => $skill['Experience']+$experience),array('Character_ID' => $character_id, 'Skill_ID' => $skill_id)))
		{
			if($skill['Experience'] + $experience >= $skill['Next']) 
			{
				return $this->skillUp($character, $skill);
			}
			else
			{
				return false;
			}
		} 
		else { return false; }
	}
	
	public function getCharacter($character_id = false)
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$character = $this->select('*','characters',array('ID' => $character_id));
		foreach($this->experience->getProgress($character['Experience']) as $key => $value) { $character[$key] = $value; }	
		return $character;
	}
	
	public function getSkill($skill_id, $character_id = false)
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$skill = $this->select('*','characters_skills',array('Character_ID' => $character_id, 'Skill_ID' => $skill_id));
		foreach($this->experience->getProgress($skill['Experience']) as $key => $value) { $skill[$key] = $value; }	
		return $skill;
	}
	
	public function getSkills($character_id = false)
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$skills = array();
		$skills = $this->select('*','character_skills','1');
		if(is_array($skills)) 
		{  
			foreach($skills as $key => $skill) 
			{ 
				$skills[$skill['Key']] = $skill; unset($skills[$key]);
				$skills[$skill['Key']]['Data'] = $this->select('*','characters_skills',array('Character_ID' => $character_id, 'Skill_ID' => $skill['ID']));
				if($skills[$skill['Key']]['Data'] == false) { $skills[$skill['Key']]['Data'] = $this->experience->getProgress(0); }
				else { $skills[$skill['Key']]['Data'] = $this->experience->getProgress($skills[$skill['Key']]['Data']['Experience']); }	
			}
		}
		return $skills;
	}
	
	public function getGears($character_id = false)
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$gears = array(); 
		$items = $this->select('*','items',"`Slot` NOT LIKE '%inv%' AND `Slot` NOT LIKE '%market%' AND `Character_ID` = '".$character_id."'",true);
		if(is_array($items)) 
		{
			foreach($items as $key => $item) 
			{
				$item_properties = $this->select('*','items_stats',array('Item_ID' => $item['ID']),true);
				foreach($item_properties as $key => $stat) { 
					$property = $this->select('*','item_properties',array('ID' => $stat['Property_ID']));
					if(!isset($gears[$property['Type']][$property['Key']])) { $gears[$property['Type']][$property['Key']]['Value'] = 0; $gears[$property['Type']][$property['Key']]['Type'] = $property['Value_type']; $gears[$property['Type']][$property['Key']]['Name'] = $property['Name']; }
					$gears[$property['Type']][$property['Key']]['Value'] += $stat['Value'];
				} 
			}
		}
		
		return $gears;
	}
	
	public function getFullStats($character_id = false) 
	{
		if($character_id == false) { $character_id = $_SESSION['character']['ID']; }
		$stats = array();
		$stats = $this->getGears($character_id);
		$stats['skills'] = $this->getSkills($character_id);
		$stats['character'] = $this->getCharacter($character_id);
		
		ksort($stats);
		return $stats;
	}
				
}

?>