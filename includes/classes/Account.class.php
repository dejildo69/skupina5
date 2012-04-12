<?php
class Account extends Database {
		public function __construct() 
	{
		session_start();
		if(!parent::$connection) { parent::__construct(); }
	}
	
	public function register($form) 
	{		//TODO: Registracija
	}
	
	public function login($Username, $Password) 
	{
		$account = $this->select('*','accounts',array('Username' => $Username, 'Password' => md5($Password)));
		if($account === false) 
		{ 
			echo("Notify error"); 
		}
		else 
		{
			$_SESSION['account'] = $account; 
			//TODO: Izberi char
		} 
	}
	
	public function logout() 
	{
		unset($_SESSION['account']);
		header('Location: '.HOME_URL);
	}	
}

?>