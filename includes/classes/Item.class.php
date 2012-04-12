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
}

?>