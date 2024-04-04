<?php

require_once "db.php";
require_once "utils.php";
use DB\DBAccess;

session_set_cookie_params(60*60*24*365*100);//100 anni
session_start();

if(!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && $_SESSION["isAdmin"])) {
    header("Location: 404.php");
    return;
}

if (!isset($_SESSION["userId"]) && !isset($_SESSION["carrello"])) {
	$_SESSION['carrello'] = array();
}

if (!isset($_SESSION["status-message"])) {
	$_SESSION['status-message'] = '';
}

if (!isset($_SESSION["status-message-role"])) {
	$_SESSION['status-message-role'] = '';
}

$paginaHTML = file_get_contents("html/dati-personali.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

$message = "";
$usernameAlert = "";
$emailAlert = "";
$usernameError = "";
$emailError = "";
$username = "";
$password = "";
$name = "";
$lastname = "";
$email = "";
$phone = "";	
$address = "";
$user_id = $_SESSION['userId'];

if ($connessioneOk) {
	if(isset($_POST['submit'])){

			$username = $_POST['username'];
			$password = $_POST['password'];
			$name = 	$_POST['name'];
			$lastname = $_POST['lastname'];
			$email = 	$_POST['email'];
			$phone = 	$_POST['phone'];
			$address = 	$_POST['address'];
			
			$name = trim(preg_replace('/\s\s+/',' ', $name));
			$lastname = trim(preg_replace('/\s\s+/',' ', $lastname));
			$address = trim(preg_replace('/\s\s+/',' ', $address));

			$equal = $connessione->isDataEqual($user_id, $username, $password, $name, $lastname, $email, $phone, $address);

			if($equal == null){

				$aux = $connessione->checkInsertData($user_id, $username, $email);

				if($aux[2] == 0){

					$modifyResult = $connessione->modifyUsersData($user_id, $username, $password, $name, $lastname, $email, $phone, $address);

					if($modifyResult){
						$_SESSION["status-message"] = "I nuovi dati sono stati salvati con successo";
                        $_SESSION['status-message-role'] = 'status';
					}
					else{
						$_SESSION["status-message"] = "I nuovi dati non sono stati salvati, riprova";
                        $_SESSION['status-message-role'] = 'alert';
                    }
				} else{
					if($aux[0] != ""){
						$usernameError = "$aux[0]";
						$usernameAlert = "role=\"alert\"";
					}

					if($aux[1] != ""){
						$emailError = "$aux[1]";
						$emailAlert = "role=\"alert\"";
					}
				}				
			} else{
				$message = $equal;
			}			
		}

		else{
			$risultatoQuery = $connessione->getUsersData($user_id);

			$username = $risultatoQuery [0]['username'];
			$password = $risultatoQuery [0]['pw'];
			$name =     $risultatoQuery [0]['nome'];
			$lastname = $risultatoQuery [0]['cognome'];
			$email =    $risultatoQuery [0]['mail'];
			$phone =    $risultatoQuery [0]['cellulare'];
			$address =  $risultatoQuery [0]['indirizzo'];
		}
	$numeroDiArticoliInCarrello = getNumeroArticoliCarrelloPerHeader($connessione);

	$paginaHTML = str_replace("%header%", setUserHeaderForAreaPersonale($numeroDiArticoliInCarrello), $paginaHTML);

	$connessione->closeDBConnection();
} else {
	$paginaHTML = str_replace("%header%", setUserHeaderForAreaPersonale(null), $paginaHTML);

	$message = '<p role="alert">I sistemi sono al momento non disponibili, riprova più tardi</p>'; 
}

$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);
		
$paginaHTML = str_replace("<message/>",       $message,       $paginaHTML);
$paginaHTML = str_replace("usernameAlert",    $usernameAlert, $paginaHTML);
$paginaHTML = str_replace("emailAlert",       $emailAlert,    $paginaHTML);
$paginaHTML = str_replace("<usernameError/>", $usernameError, $paginaHTML);
$paginaHTML = str_replace("<emailError/>",    $emailError,    $paginaHTML);
$paginaHTML = str_replace("<valueUsername/>", $username,      $paginaHTML);
$paginaHTML = str_replace("<valuePassword/>", $password,      $paginaHTML);
$paginaHTML = str_replace("<valueName/>",     $name,          $paginaHTML);
$paginaHTML = str_replace("<valueLastname/>", $lastname,      $paginaHTML);
$paginaHTML = str_replace("<valueE-mail/>",   $email,         $paginaHTML);
$paginaHTML = str_replace("<valuePhone/>",    $phone,         $paginaHTML);
$paginaHTML = str_replace("<valueAddress/>",  $address,       $paginaHTML);
	
echo $paginaHTML;	
?>