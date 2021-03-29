<?php

	session_start();
	 
	 if(isset($_POST['email']))
	 {
		//Udana walidacja? Załóżmy że tak!
		$wszystko_ok=true;
		
		//Sprawdzenie poprawności nickname
		$nick = $_POST['nick'];
		
		//Sprawdzenie długości nicka
		if((strlen($nick)<3) || (strlen($nick)>20))
		{
			$wszystko_ok=false;
			$_SESSION['e_nick']='Nick musi posiadać od 3 do 20 znaków!';
			
		}
		//Sprawdzanie czy nick zawiera zanki alfanumeryczne
		if(ctype_alnum($nick)==false)
		{
			$wszystko_ok=false;
			$_SESSION['e_nick']='Nick może składać się tylko z liter i cyfr - bez znaków specialnych';
			 
		}
		//Sprawdzanie poprawonosci adresu e-mail
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($emailB,FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$wszystko_ok=false;
			$_SESSION['e_email']='Nieprawidłowy adres e-mail';
		}
		 //Sprawdz poprawnosc hasla
		 $haslo1 = $_POST['haslo1'];
		 $haslo2 = $_POST['haslo2'];
		 
		 if((strlen($haslo1)<8) || (strlen($haslo1)>20))
		 {
			 $wszystko_ok=false;
			$_SESSION['e_haslo']='Hasło musi posiadać od 8 do 20 znaków!';
		
		 }
		 if($haslo1!=$haslo2)
		 {
			$wszystko_ok=false;
			$_SESSION['e_haslo']='Hasła muszą być identyczne!';
		 
		 }
		 
		 $haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		//Czy zaakceptowano regulamin
		if(!isset($_POST['regulamin']))
		{
			$wszystko_ok=false;
			$_SESSION['e_regulamin']='Musisz zaackeptować regulamin!';
		 
		 }
		 
		 //Bot czy nie bot?
		 $sekret = '6LfsvgwaAAAAAGEWfMLTi1_iVrWllsv_tLF7vRC4';
		 
		 $sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		 
		 $odpowiedz = json_decode($sprawdz);
		 
		 if($odpowiedz->success==false)
		{
			$wszystko_ok=false;
			$_SESSION['e_bot']='Potwierdź że nie jesteś BOTEM!';
		 
		 }
		 
		 require_once "connect.php";
		 
		 mysqli_report(MYSQLI_REPORT_STRICT);
		 
		 try
		 {
			 $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
			 if ($polaczenie->connect_errno!=0)
			{
				 throw new Exception(mysqli_connect_errno());
			}
			else
			{
				 //Czy e-mail juz istnieje?
				 $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
				 
				 if(!$rezultat) throw new Exception($polaczenie->error);
				 
				 $ile_takich_meili = $rezultat->num_rows;
				 if($ile_takich_meili>0)
				 {
					$wszystko_ok=false;
					$_SESSION['e_email']="Istnieje już konto przypisane do adresu e-mail!";
		 
				}
				//Czy nick juz istnieje?
				 $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
				 
				 if(!$rezultat) throw new Exception($polaczenie->error);
				 
				 $ile_takich_nickow = $rezultat->num_rows;
				 if($ile_takich_nickow>0)
				 {
					$wszystko_ok=false;
					$_SESSION['e_nick']="Istnieje już gracz o takim nicku!";
		 
				}
				if($wszystko_ok==true)
				{
					//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
					
					if($polaczenie->query("INSERT INTO uzytkownicy VALUES(NULL,'$nick','$haslo_hash','$email', 100,100,100,14)"))
					{
						$_SESSION['udanarejestracja']=true;
						header('Location: witamy.php');
					}
					else
					{
						throw new Exception($polaczenie->error);
					}
				}
				 $polaczenie->close();
			}
		 }
		 catch(Exception $e)
		 {
			 echo '<span style="color:red">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestracę w innym terminie!</span>';
			// echo '<br/>Informacja Developerska: '.$e;
		 }

	 }

?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>Piraci - Załóż darmowe konto</title>
	 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
	 <style>
		.error
		{
			color:red;
			margin-top: 10px;
			margin-bottom: 10px;
		}
	 </style>
	 
</head>


<body>
   <form method="post">
   Nickname: <br/> <input type="text" name="nick"/><br/>
   
   <?php
   
   if(isset($_SESSION['e_nick']))
   {
	   echo'<div class="error">'.$_SESSION['e_nick'].'</div>';
	   unset($_SESSION['e_nick']);
   }
   ?>
   
   E-mail <br/> <input type="text" name="email"/><br/>
   
   <?php
   
   if(isset($_SESSION['e_email']))
   {
	   echo'<div class="error">'.$_SESSION['e_email'].'</div>';
	   unset($_SESSION['e_email']);
   }
   ?>
   Hasło: <br/> <input type="password" name="haslo1"/><br/>
   <?php
   
   if(isset($_SESSION['e_haslo']))
   {
	   echo'<div class="error">'.$_SESSION['e_haslo'].'</div>';
	   unset($_SESSION['e_haslo']);
   }
   ?>  
   Powtórz hasło: <br/> <input type="password" name="haslo2"/><br/>
   <label>
   <input type="checkbox" name="regulamin"/>Akceptuję regulamin <br/><br/>
    <?php
   
   if(isset($_SESSION['e_regulamin']))
   {
	   echo'<div class="error">'.$_SESSION['e_regulamin'].'</div>';
	   unset($_SESSION['e_regulamin']);
   }
   ?>  
      </label>
    <div class="g-recaptcha" data-sitekey="6LfsvgwaAAAAAJipmNuxo3EV_jI5pUu1TEtgxnys"></div>
	
	    <?php
   
   if(isset($_SESSION['e_bot']))
   {
	   echo'<div class="error">'.$_SESSION['e_bot'].'</div>';
	   unset($_SESSION['e_bot']);
   }
   ?>
   <input type="submit" value="Załóż konto"/><br/>

   
	</form>
</body>
</html>
