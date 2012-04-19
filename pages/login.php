<?php if(!isset($_SESSION['account']['ID'])) { ?>
<form method="POST" action="index.php?login">
	<table>
		<tr><td>Username</td><td><input type="text" name="Username"></td></tr>
		<tr><td>Password</td><td><input type="password" name="Password"></td></tr>
		<tr><td></td><td><input type="submit" value="Login"></td></tr>
	</table>
</form>
<?php } else { 
	echo("Welcome back ".$_SESSION['account']['Nickname']."! ");
	echo('<a href="'.HOME_URL.'index.php?logout">Log out</a>');
} ?>