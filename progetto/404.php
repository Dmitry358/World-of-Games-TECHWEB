<?php

require_once "db.php";
require_once "utils.php";
use DB\DBAccess;

session_set_cookie_params(60*60*24*365*100);//100 anni
session_start();

if (!isset($_SESSION["userId"]) && !isset($_SESSION["carrello"])) {
	$_SESSION['carrello'] = array();
}

if (!isset($_SESSION["status-message"])) {
	$_SESSION['status-message'] = '';
}

if (!isset($_SESSION["status-message-role"])) {
	$_SESSION['status-message-role'] = '';
}

$paginaHTML = file_get_contents('html/404.html');

$connessione = new DBAccess();
$connessioneOk = $connessione->openDBConnection();
if ($connessioneOk) {
	if (!isset($_SESSION['userId']) || (isset($_SESSION['userId']) && !$_SESSION['isAdmin'])) {
		$numeroDiArticoliInCarrello = 0;
		if (isset($_SESSION["userId"])) { //user loggato non admin
			if (!$_SESSION["isAdmin"]) {
				$articoliNelCarrello = $connessione->getIdArticoliNelCarrello($_SESSION["userId"]);
				$numeroDiArticoliInCarrello = 0;
				if ($articoliNelCarrello != null) {
					$count = count($articoliNelCarrello);
					$keys = array_keys($articoliNelCarrello);
					for ($i = 0; $i < $count; $i++) {
						$doesItExist = $connessione->esisteArticolo($keys[$i]);
						if ($doesItExist == false) {
							$numeroDiArticoliInCarrello += $articoliNelCarrello[$keys[$i]];
						} else {
							$result = $connessione->removeFromCart($_SESSION["userId"], $keys[$i]);
							if ($result) {
								$_SESSION['status-message'] = "Alcuni articoli che erano nel carrello sono stati rimossi perchè non esistono più";
							} else {
								$_SESSION['status-message'] = "Alcuni articoli che erano nel carrello non esistono più, ma non è stato possibile rimuoverli dal carrello";
							}
							$_SESSION['status-message-role'] = "alert";
						}
					}
				}
			}
		} else { //user non loggato
			$allKeys = array_keys($_SESSION['carrello']);
			for ($i=0;$i<count($allKeys);$i++) {
				$doesItExist = $connessione->esisteArticolo($allKeys[$i]);
				if ($doesItExist == false) {
					$numeroDiArticoliInCarrello += $_SESSION['carrello'][$allKeys[$i]];
				} else {
					unset($_SESSION['carrello'][$allKeys[$i]]);
					$_SESSION['status-message'] = "Alcuni articoli che erano nel carrello sono stati rimossi perchè non esistono più";
					$_SESSION['status-message-role'] = "alert";
				}
			}
		}
		$connessione->closeDBConnection();

		$paginaHTML = str_replace('%header%', setUserHeaderFor404Page($numeroDiArticoliInCarrello), $paginaHTML);
		$paginaHTML = str_replace('%status-bar%', setStatusBar(), $paginaHTML);
	} else {
		$paginaHTML = str_replace('%header%', setAdminHeaderFor404Page(), $paginaHTML);
		$paginaHTML = str_replace('%status-bar%', setStatusBar(), $paginaHTML);
	}
} else {
	$_SESSION['status-message'] = "I sistemi sono al momento non disponibili, riprova più tardi.";
	$_SESSION['status-message-role'] = "alert";
	
	$paginaHTML = str_replace('%header%', setUserHeaderFor404Page(null), $paginaHTML);
	$paginaHTML = str_replace('%status-bar%', setStatusBar(), $paginaHTML);
}

echo $paginaHTML;

?>