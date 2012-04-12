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
		$(document).ready(function() {

			navigate('login','account_menu');

		});
		
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
		<?php if(isset($_SESSION['account']['ID'])) {?>

		<div class="game_container">
			<div class="game_menu">
				<div class="tab" onclick="navigate('home','game_content')">Home</div>
				<div class="tab" onclick="navigate('tavern','game_content')">Tavern</div>
			</div>
		
		<hr width="100%"/>

			<div id="game_content">

			</div>
		</div>
	</div>
	<?php } ?>
	
</body>