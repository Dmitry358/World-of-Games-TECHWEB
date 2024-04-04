<?php
//meta description max 150 caratteri


//BREADCRUMB DA IMPLEMENTARE RIGA 228
//header versione print non funziona
//connettere 404.php RIGA 203-205

require_once "db.php";
require_once "utils.php";
use DB\DBAccess;

session_set_cookie_params(60*60*24*365*100);//100 anni
session_start();

if (!isset($_GET["id"])) {
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

$paginaHTML = file_get_contents("html/articolo.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

$confirmComment = array("message"=>"", "confirm" => true);

$title = "Sistemi non disponibili";
$keywords = "";
$description = "";
$breadcrumb = "";
$articolo = "";
$descrizione = "";

$data = ""; 
$comments = ""; 
$commento_ta = "";
$commenti = "";
$isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];

if(!isset($_GET["id"])) {
	header("Location:" . $_SERVER['REQUEST_URI']);
	return;
}

$idArticolo = $_GET["id"];
$location = "index.php";

if (isset($_POST['prev-url'])) {
	$location = $_POST['prev-url'];
} else if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'articolo.php') === false) {
	$location = htmlspecialchars(basename($_SERVER['HTTP_REFERER']));
}

if ($connessioneOk) {
	if (isset($_POST['button_cart'])) {
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
	}

	if(isset($_POST['share_comment'])){
		if(!isset($_SESSION['userId'])) {
			$_SESSION['status-message'] = "Se vuoi scrivere un commento devi prima loggarti";
			$_SESSION['status-message-role'] = "alert";
			$connessione->closeDBConnection();
			header("Location:" . $_SERVER['REQUEST_URI']);
			return;
		}
		$comment = strip_tags($_POST['box_comment']);
		$da_sostituire = ["'", '"'];
        $sostituti = ["\'", '\"'];
        $comment = str_replace($da_sostituire, $sostituti, $comment);
		$commento_ta = $comment;
		$vote = '';
		if(isset($_POST['vote'])){
			$vote = $_POST['vote'];
		}			
		$confirmComment = $connessione->insertComment($_SESSION['userId'], $_GET["id"], $comment, $vote);
	}


		$data = $connessione->getArticolo($idArticolo);
			
		if($data){
			$title= "<title>".$connessione->getTitle($data['nome'])."</title>";
			
			$keywords= '<meta name="keywords" content="'.strip_tags($data['parole_chiave']).'"/>'; 
			
			$description = $connessione->getDescription($data['nome'], $data['prezzo'], $data['pr_scontato'], $data['descrizione']); 

			$articolo = $connessione->getArticoloData($location, $idArticolo, $data['nome'], $data['immagine'], $data['alt_immagine'], $data['prezzo'], $data['pr_scontato'], $isAdmin);			
						
			$descrizione = $connessione->getDescrizione($data['descrizione']); 
			
			$comments = $connessione->getComments($location, $idArticolo, $isAdmin, $confirmComment);
		}else{
			$connessione->closeDBConnection();
			header("Location: 404.php");
			return;
		}
	} else {
		$articolo = "<p role=\"alert\">I sistemi sono al momento non disponibili, riprova più tardi</p>";
	}

	$paginaHTML = str_replace("<title/>",       $title, 	    $paginaHTML);
	$paginaHTML = str_replace("<keywords/>",    $keywords,      $paginaHTML);
	$paginaHTML = str_replace("<description/>", $description,   $paginaHTML);

	if($confirmComment['confirm']){
		$paginaHTML = str_replace("%showCommFunc%", "hideComment()",   $paginaHTML);
	}
	else{
		$paginaHTML = str_replace("%showCommFunc%", "showComment()",   $paginaHTML);
	}
	
	if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
		$numeroDiArticoliInCarrello = getNumeroArticoliCarrelloPerHeader($connessione);
		$paginaHTML = str_replace("%header%", setUserHeaderForArticolo($numeroDiArticoliInCarrello), $paginaHTML);
	} else {
		$paginaHTML = str_replace("%header%", setAdminHeaderForHomeAndElencoArticoli(), $paginaHTML);
	}
	$connessione->closeDBConnection(); 

	$paginaHTML = str_replace("%status-bar%", 	setStatusBar(), $paginaHTML);	

	$breadcrumb .= getArticoloBreadcrumb($location);
	if ($breadcrumb == '') {
	    header("Location: 404.php");
		return;
	}
	
	$paginaHTML = str_replace("<breadcrumb/>",  $breadcrumb,    $paginaHTML);
	$paginaHTML = str_replace("<articolo/>",    $articolo,      $paginaHTML);
	
	$paginaHTML = str_replace("<descrizione/>", $descrizione,   $paginaHTML); 
	
	if ($confirmComment['confirm'] or !isset($confirmComment['confirm'])) {
		$comments = str_replace("%comment%", "", $comments);
	} else{
		$comments = str_replace("%comment%", $commento_ta, $comments);
	}
		 
	$paginaHTML = str_replace("<comments/>", $comments,  $paginaHTML);

	echo $paginaHTML;	
?>