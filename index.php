<?php
require_once('includes/config.php');
require_once('includes/constants.php');
require_once('includes/classes/Database.class.php');

$db = new Database(/*true*/);
$Account = $db->load('Account');

if(isset($_GET['logout'])) { $Account->logout(); }

if(isset($_GET['login'])) { if(isset($_POST['Username']) AND isset($_POST['Password'])) {

	$Account->login($_POST['Username'], $_POST['Password']);

}}

if(isset($_GET['register'])) { if(isset($_POST['Register'])) { $Account->register($_POST['Register']); } }

?>

<head>

	<script type="text/javascript" src="libraries/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript">

		function navigate(location, target)
		{
			$('#'+target).html('<img src="loader-big.gif">');
			$.get("request.php", {page:location}, function(data) { $('#'+target).html(data); });
		}
		
	</script>

	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/game.css" />

</head>

<body>

	<div id="account_menu" class="account_container"><img src="loader-big.gif">
	</div>

	<div class="main_container">	
		<div class="link_home"><a href="<?php echo(HOME_URL); ?>index.php"><?php echo(HOME_NAME);?></a>
		</div>
	</div>

</body>