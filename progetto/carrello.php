<?php

require_once "db.php";
require_once "utils.php";
use DB\DBAccess;

session_set_cookie_params(60*60*24*365*100);//100 anni
session_start();

if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
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

$paginaHTML = file_get_contents("html/carrello.html");

$connessione = new DBAccess();

$connessioneOK = $connessione->openDBConnection();

if ($connessioneOK) {
	if (isset($_POST["button-elimina"]) && $_POST["button-elimina"] == true) {
		$id_da_eliminare = $_POST["id-bottone"];
	 	if (isset($_SESSION['userId'])){
	 		$result = $connessione->eliminaProdottoCarrello($id_da_eliminare);
			if ($result) {
				$_SESSION['status-message'] = "Articolo rimosso con successo";
				$_SESSION['status-message-role'] = "status";
			} else {
				$_SESSION['status-message'] = "Non è stato possibile rimuovere l'articolo, riprovare più tardi";
				$_SESSION['status-message-role'] = "alert";
			}
	 	} else {
	 		$indice = -1;
	 		foreach ($_SESSION['carrello'] as $id=>$quantita) {
		        if ($id == $id_da_eliminare) {
		         	$indice = $id;
		         	unset($_SESSION["carrello"][$indice]);
			        $_SESSION['status-message'] = "Articolo rimosso con successo";
							$_SESSION['status-message-role'] = "status";
		         	break;
		        }
		    }
		    if ($indice == -1) {
		      	$_SESSION['status-message'] = "Non è stato possibile rimuovere l'articolo, riprova più tardi";
				$_SESSION['status-message-role'] = "alert";
		    }
		}
	 	$connessione->closeDBConnection();
	 	header("Location: carrello.php");
	 	return;
	}

	if (isset($_POST['select_quantita']) && $_POST["select_quantita"] == true) {
		$new_quantita = $_POST['select_quantita'];
		$id_articolo = $_POST['select-id'];
		if (isset($_SESSION['userId'])) {
			$result = $connessione->modificaQuantitaDB($id_articolo, $new_quantita);
			if ($result) {
				$_SESSION['status-message'] = "Quantità modificata con successo";
				$_SESSION['status-message-role'] = "status";
			} else {
				$_SESSION['status-message'] = "Non è stato possibile modificare la quantità, riprova più tardi";
				$_SESSION['status-message-role'] = "alert";
			}
		} else {
			foreach ($_SESSION['carrello'] as $id=>$quantita)  {
				if ($id == $id_articolo) {
					$_SESSION["carrello"][$id] = $new_quantita;
					$_SESSION['status-message'] = "Quantità modificata con successo";
					$_SESSION['status-message-role'] = "status";
					break;
				}
			}
		}
		$connessione->closeDBConnection();
		header("Location: carrello.php");
		return;
	}

	if (isset($_POST["acquista"])) {
		if (isset($_SESSION['userId'])) {
			$carrello = $connessione->getCarrelloDB();
			if (count($carrello) > 0) {
				if ($carrello) {
					$svuotaCarrello = $connessione->svuotaCarrelloUtente();
					if ($carrello) {
						$creaOrdine = $connessione->inserisciOrdineUtente($carrello);
						if ($creaOrdine) {
							$_SESSION['status-message'] = "Acquisto effettuato con successo";
							$_SESSION['status-message-role'] = "status";
							$connessione->closeDBConnection();
							header("Location: carrello.php");
							return;
						}
					}
				}
			} else {
				$_SESSION['status-message'] = "Non è stato possibile effettuare l'acquisto, il carrello è vuoto";
				$_SESSION['status-message-role'] = "alert";
				$connessione->closeDBConnection();
				header("Location: carrello.php");
				return;
			}
			$_SESSION['status-message'] = "Non è stato possibile effettuare l'acquisto, riprova più tardi";
			$_SESSION['status-message-role'] = "alert";
		} else {
			$_SESSION['status-message'] = "Non è stato possibile effettuare l'acquisto, devi prima effettuare il <span lang=\"en\">login</span>";
			$_SESSION['status-message-role'] = "alert";
		}
		$connessione->closeDBConnection();
		header("Location: carrello.php");
		return;
	}

	$numeroDiArticoliInCarrello = getNumeroArticoliCarrelloPerHeader($connessione);

	$data = isset($_SESSION["userId"]) ? $connessione->getCarrelloDB() : $connessione->getCarrelloSession();

	$connessione->closeDBConnection();

	$carrello = getCarrello($data);

	$paginaHTML = str_replace("%header%", setUserHeaderForCarrello($numeroDiArticoliInCarrello), $paginaHTML);
	$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);
	$paginaHTML = str_replace("%carrello%", $carrello, $paginaHTML);
} else {
	$paginaHTML = str_replace("%header%", setUserHeaderForCarrello(null), $paginaHTML);
	$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);
	$paginaHTML = str_replace("%carrello%", '<p id="carrello_no_element" role="alert">I nostri sitemi sono al momento non disponibili, riprova più tardi.</p>', $paginaHTML);
}
echo $paginaHTML;

?>