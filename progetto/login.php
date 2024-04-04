<?php

require_once "db.php";
require_once "utils.php";
use DB\DBAccess;

session_set_cookie_params(60*60*24*365*100);//100 anni
session_start();

if (!isset($_SESSION["status-message"])) {
	$_SESSION['status-message'] = '';
}

if (!isset($_SESSION["status-message-role"])) {
	$_SESSION['status-message-role'] = '';
}

if (!isset($_SESSION["userId"]) && !isset($_SESSION["carrello"])) {
	$_SESSION['carrello'] = array();
}

$paginaHTML = file_get_contents("html/login.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

if ($connessioneOk) {
		if (isset($_POST['login']) && $_POST['login'] == true) {
			$usernameOrEmail = $_POST['username-or-email'];
			$password = $_POST['password'];

		    $usernameOrEmail = trim(preg_replace('/\s\s+/',' ', $usernameOrEmail));
		    $password = trim(preg_replace('/\s\s+/',' ', $password));

			$mostraPassword = isset($_POST['mostra-password']);

			$result = $connessione->login($usernameOrEmail, $password);
			if ($result !== null) {
				$_SESSION['userId'] = $result['id'];
				$_SESSION['isAdmin'] = $result['admin'];
				if ($result['admin'] == 0) {
					$_SESSION['status-message'] = 'Ciao '.$result['username'].'. Bentornato!';
					$_SESSION['status-message-role'] = "status";
				}

				$articoliNelCarrelloDB = $connessione->getIdArticoliNelCarrello($_SESSION["userId"]);
				if ($articoliNelCarrelloDB != null) {
					$keysCarrelloSession = array_keys($_SESSION['carrello']);
					$count = count($keysCarrelloSession);
					for ($i = 0; $i < $count; $i++) {
						if (isset($articoliNelCarrelloDB[$keysCarrelloSession[$i]])) {
							if ($_SESSION['carrello'][$keysCarrelloSession[$i]] + $articoliNelCarrelloDB[$keysCarrelloSession[$i]] <= 9) {
								$connessione->setQuantitaArticoloToCart($_SESSION['userId'], $keysCarrelloSession[$i], $_SESSION['carrello'][$keysCarrelloSession[$i]]+$articoliNelCarrelloDB[$keysCarrelloSession[$i]]);
							} else {
								if ($articoliNelCarrelloDB[$keysCarrelloSession[$i]] < 9) {
									$connessione->setQuantitaArticoloToCart($_SESSION['userId'], $keysCarrelloSession[$i], 9);
								}
								$_SESSION['status-message'] = 'Alcuni articoli nel carrello hanno sforato la quantità massima';
								$_SESSION['status-message-role'] = "alert";
							}
						} else {
							$connessione->addToCart($_SESSION['userId'], $keysCarrelloSession[$i], 0, $_SESSION['carrello'][$keysCarrelloSession[$i]]);
						}
					}
				} else {
					$keysCarrelloSession = array_keys($_SESSION['carrello']);
					$count = count($keysCarrelloSession);
					for ($i = 0; $i < $count; $i++) {
						$connessione->addToCart($_SESSION['userId'], $keysCarrelloSession[$i], 0, $_SESSION['carrello'][$keysCarrelloSession[$i]]);
					}
				}
				unset($_SESSION['carrello']);

				$connessione->closeDBConnection();

				header("Location: index.php");
				return;
			} else {
				$paginaHTML = str_replace('%message%', '<p id="credenziali-non-corrette"><strong role="alert">Credenziali errate</strong></p>', $paginaHTML);
				$paginaHTML = str_replace('%valoreUsernameOEmail%', $usernameOrEmail, $paginaHTML);
				$paginaHTML = str_replace('%valorePassword%', $password, $paginaHTML);
				$paginaHTML = str_replace('%tipoPswInput%', $mostraPassword ? 'text' : 'password', $paginaHTML);
				$paginaHTML = str_replace('%valoreCheckedShowPsw%', $mostraPassword ? ' checked' : '', $paginaHTML);
			}
		} else {
			if (!isset($_SESSION["userId"])) { //non loggato
				$paginaHTML = str_replace('%message%', '', $paginaHTML);
				$paginaHTML = str_replace('%valoreUsernameOEmail%', '', $paginaHTML);
				$paginaHTML = str_replace('%valorePassword%', '', $paginaHTML);
				$paginaHTML = str_replace('%valoreCheckedShowPsw%', '', $paginaHTML);
				$paginaHTML = str_replace('%tipoPswInput%', 'password', $paginaHTML);
			} else { //già loggato
				header("Location: 404.php");
				return;
			}
		}
} else {
    $paginaHTML = str_replace("%message%", '<p id="sistemi-non-disponibili" role="alert">I sistemi sono al momento non disponibili, riprova più tardi.</p>', $paginaHTML);
	$paginaHTML = str_replace('%valoreUsernameOEmail%', "", $paginaHTML);
	$paginaHTML = str_replace('%valorePassword%', "", $paginaHTML);
	$paginaHTML = str_replace('%tipoPswInput%', 'password', $paginaHTML);
	$paginaHTML = str_replace('%valoreCheckedShowPsw%', '', $paginaHTML);
}
echo $paginaHTML;
?>