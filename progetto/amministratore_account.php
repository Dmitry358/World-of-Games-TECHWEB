<?php

require_once "db.php";
require_once "utils.php";
use DB\DBAccess;

session_set_cookie_params(60*60*24*365*100);//100 anni
session_start();

if(!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
    header("Location: 404.php");
    return;
}

if (!isset($_SESSION["status-message"])) {
    $_SESSION['status-message'] = '';
}

if (!isset($_SESSION["status-message-role"])) {
    $_SESSION['status-message-role'] = '';
}

$paginaHTML = file_get_contents("html/amministratore_account.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();


$user_id = $_SESSION['userId'];

$message = "";
$usernameAlert = "";
$emailAlert = "";
$usernameError = "";
$emailError = "";
$username = "";
$password = "";
$email = "";


	if($connessioneOk){

		if(isset($_POST['submit_aa'])){

			$username = $_POST['username'];
			$password = $_POST['password'];
			$email = 	$_POST['email'];

			$equal = $connessione->isDataEqualAdm($user_id, $username, $password, $email);

			if($equal == null){

				$aux = $connessione->checkInsertData($user_id, $username, $email);

				if($aux[2] == 0){

					$modifyResult = $connessione->modifyAdminData($user_id, $username, $password, $email);

					if($modifyResult){
						$_SESSION["status-message"] = "I nuovi dati sono stati salvati con successo";
                        $_SESSION['status-message-role'] = 'status';
					}
					else{
						$_SESSION["status-message"] = "I nuovi dati non sono stati salvati, riprova";
                        $_SESSION['status-message-role'] = 'alert';
					}
				}

				else{
					if($aux[0] != ""){
						$usernameError = "$aux[0]"; 
						$usernameAlert = "role=\"alert\"";	
					}

					if($aux[1] != ""){
						$emailError = "$aux[1]"; 
						$emailAlert = "role=\"alert\"";
					}
				}				
			} else {
				$message = $equal;
			}
		}

		else{
			$risultatoQuery = $connessione->getAdminsData($user_id);

			$username = $risultatoQuery [0]['username'];
			$password = $risultatoQuery [0]['pw'];						
			$email =    $risultatoQuery [0]['mail'];
		}
	}
	
	else{
		$message = '<p role="alert">I sistemi sono al momento non disponibili, riprova più tardi</p>'; 
	}

	$connessione->closeDBConnection();

	$paginaHTML = str_replace("%header%", setAdminHeaderForAreaPersonale(), $paginaHTML);
	$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);
		
	$paginaHTML = str_replace("<message/>",		  $message, 	  $paginaHTML);
	$paginaHTML = str_replace("usernameAlert",    $usernameAlert, $paginaHTML);
	$paginaHTML = str_replace("emailAlert",       $emailAlert,    $paginaHTML);
	$paginaHTML = str_replace("<usernameError/>", $usernameError, $paginaHTML);
	$paginaHTML = str_replace("<emailError/>",    $emailError,    $paginaHTML);
	$paginaHTML = str_replace("<valueUsername/>", $username,      $paginaHTML);
	$paginaHTML = str_replace("<valuePassword/>", $password,      $paginaHTML);	
	$paginaHTML = str_replace("<valueE-mail/>",   $email,         $paginaHTML);	
	
	echo $paginaHTML;	
?>