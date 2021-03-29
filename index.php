<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: gra.php');
		exit();
	}

?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>Piraci - gra przeglądarkowa</title>
	<link rel="stylesheet" href="style.css" />
</head>


<body>
   <div class="title">
	    Największy skarb ukryty jest w miejscu gdzie woda płynie do góry 
   </div>
   <div class="sub-title">Dołącz do gry w największym świecie Piratów. Kliknij  <a href="rejestracja.php"> Załóż konto</a>  i stań się największym piratem 7 oceanów.<br/>
   </div>  
	<div class="form">
		<form action="zaloguj.php" method="post">
			<p>Login:</p> <br/> <input type="text" name="login"/> <br/>
			<p>Hasło:</p> <br/> <input type="password" name="haslo"/> <br/><br/>
			<input type="submit" value="Zaloguj się"/> <br/>
		
<?php
	
	if(isset($_SESSION['blad']))	echo $_SESSION['blad'];
?>
<br/><br/><br/>
</form>
</div>
</body>
</html>
 