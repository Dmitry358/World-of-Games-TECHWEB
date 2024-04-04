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

$paginaHTML = file_get_contents("html/elenco-articoli.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

if ($connessioneOk) {
	if (isset($_POST['button-add-to-cart']) && $_POST['button-add-to-cart'] == true) {
		$idArticolo = $_POST['id-articolo'];
		$quantita = 0;
		$wasDeleted = $connessione->esisteArticolo($idArticolo);
		if ($wasDeleted == false) {
			if (isset($_SESSION["userId"])) { //user loggato
				$quantita = $connessione->getCartProductQuantity($_SESSION["userId"], $idArticolo);
				if ($quantita <= 8) {
					$result = $connessione->addToCart($_SESSION["userId"], $idArticolo, $quantita, 1);
					if ($result === false) {
						$_SESSION['status-message'] = "È stato impossibile aggiungere l'articolo nel carrello, riprova più tardi";
						$_SESSION['status-message-role'] = "alert";
					} else {
						$_SESSION['status-message'] = "Articolo aggiunto nel carrello";
						$_SESSION['status-message-role'] = "status";
					}
				} else {
					$_SESSION['status-message'] = "Impossibile aggiungere l'articolo nel carrello, quantità massima raggiunta per l'articolo";
					$_SESSION['status-message-role'] = "alert";
				}
			} else {
				if (isset($_SESSION['carrello'][$idArticolo])) {
					$quantita = $_SESSION['carrello'][$idArticolo];
				}
				if ($quantita == 0) {
					$_SESSION['carrello'][$idArticolo] = 1;
					$_SESSION['status-message'] = "Articolo aggiunto nel carrello";
					$_SESSION['status-message-role'] = "status";
				} else {
					if ($quantita <= 8) {
						$_SESSION['carrello'][$idArticolo]++;
						$_SESSION['status-message'] = "Articolo aggiunto nel carrello";
						$_SESSION['status-message-role'] = "status";
					} else {
						$_SESSION['status-message'] = "Impossibile aggiungere l'articolo nel carrello, quantità massima raggiunta per l'articolo";
						$_SESSION['status-message-role'] = "alert";
					}
				}
			}
		} else {
			$_SESSION['status-message'] = "Impossibile aggiungere l'articolo nel carrello, l'articolo selezionato è stato eliminato";
			$_SESSION['status-message-role'] = "alert";
		}
		$connessione->closeDBConnection();
		header("Location:" . $_SERVER['REQUEST_URI']);
		return;
	}

	if (isset($_POST['button-elimina']) && $_POST['button-elimina'] == true) {
		$idArticolo = $_POST['id-articolo'];
		$wasDeleted = $connessione->esisteArticolo($idArticolo);
		if ($wasDeleted == false) {
			$result = $connessione->deleteArticolo($idArticolo);
			if ($result) {
				$_SESSION['status-message'] = "Articolo rimosso con successo";
				$_SESSION['status-message-role'] = "status";
			} else {
				$_SESSION['status-message'] = "Non è stato possibile rimuovere l'articolo, riprova più tardi";
				$_SESSION['status-message-role'] = "alert";
			}
		} else {
			$_SESSION['status-message'] = "L'articolo selezionato è già stato eliminato";
			$_SESSION['status-message-role'] = "alert";
		}
		$connessione->closeDBConnection();
		header("Location: " . $_SERVER['REQUEST_URI']);
		return;
	}

	$termine_ricerca = $_GET['termine-ricerca'] ?? null;
	$categoria = $_GET['categoria'] ?? null;
	$sottocategoria = $_GET['sottocategoria'] ?? null;
	$filtra_recensioni = $_GET['filtra-recensioni'] ?? null;
	$filtra_offerte = $_GET['filtra-offerte'] ?? null;
	$filtra_ultimi_arrivi = $_GET['filtra-ultimi-arrivi'] ?? null;
	$ordina_per = $_GET['ordina-per'] ?? null;
	$pagine = $_GET['page'] ?? null;//numero >= 1 (se < 1 ritorno sempre la prima pagina)
	if (!$pagine) {
		$pagine = 1;
	} else {
		$pagine = intval($pagine);
		if ($pagine < 0) {
			$pagine = 1;
		}
	}

	if (!$categoria || !$sottocategoria) {
		$categoria = null;
		$sottocategoria = null;
	}

	if ($termine_ricerca) {
		$termine_ricerca = trim(preg_replace('/\s\s+/',' ', $termine_ricerca));
		$categoria = null;
		$sottocategoria = null;
	}

	$prodotti = $connessione->getArticoli($termine_ricerca, $categoria, $sottocategoria, $filtra_recensioni, $filtra_offerte, $filtra_ultimi_arrivi, $ordina_per);


	$paginaHTML = str_replace("%listaArticoli%", setProducts($prodotti, $pagine), $paginaHTML);

	if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
		$numeroDiArticoliInCarrello = getNumeroArticoliCarrelloPerHeader($connessione);
		$paginaHTML = str_replace("%header%", setUserHeaderForHomeAndElencoArticoli($numeroDiArticoliInCarrello), $paginaHTML);
	} else {
		$paginaHTML = str_replace("%header%", setAdminHeaderForHomeAndElencoArticoli(), $paginaHTML);
	}

	$connessione->closeDBConnection();

	$paginaHTML = setMenu($termine_ricerca, $categoria, $sottocategoria, $paginaHTML);

	$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);

	$paginaHTML = str_replace("%filtro%", setFiltro($filtra_recensioni, $filtra_offerte, $filtra_ultimi_arrivi), $paginaHTML);

	$paginaHTML = str_replace("%risultati%", setRisultati(isset($prodotti)?count($prodotti):0, $termine_ricerca, $categoria, $sottocategoria), $paginaHTML);

	$paginaHTML = str_replace("%order-by%", setOrderBy($ordina_per), $paginaHTML);

	$paginaHTML = str_replace("%descrizione-ricerca%", $termine_ricerca ?? $sottocategoria ?? "il catalogo completo", $paginaHTML);

	$paginaHTML = str_replace("%title%", getEATitle($termine_ricerca, $categoria, $sottocategoria), $paginaHTML);

	$paginaHTML = str_replace("%keyword%", getKeywordElencoArticoli($termine_ricerca, $categoria, $sottocategoria), $paginaHTML);

} else {

	$paginaHTML = str_replace("%listaArticoli%", '<p id="empty-content" role="alert">I sistemi sono al momento non disponibili, riprova più tardi.</p>', $paginaHTML);
	
	if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
		$paginaHTML = str_replace("%header%", setUserHeaderForHomeAndElencoArticoli(null), $paginaHTML);
	} else {
		$paginaHTML = str_replace("%header%", setAdminHeaderForHomeAndElencoArticoli(), $paginaHTML);
	}

	$paginaHTML = setMenu($termine_ricerca, $categoria, $sottocategoria, $paginaHTML);

	$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);

	$paginaHTML = str_replace("%filtro%", setFiltro("", "", ""), $paginaHTML);

	$paginaHTML = str_replace("%risultati%", '0 risultati', $paginaHTML);

	$paginaHTML = str_replace("%order-by%", setOrderBy("prezzo-crescente"), $paginaHTML);

	$paginaHTML = str_replace("%descrizione-ricerca%", "", $paginaHTML);

	$paginaHTML = str_replace("%title%", "Sistemi non disponibili", $paginaHTML);

	$paginaHTML = str_replace("%keyword%", "", $paginaHTML);

}
echo $paginaHTML;
?>