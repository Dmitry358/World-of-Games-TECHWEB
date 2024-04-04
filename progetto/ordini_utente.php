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

$paginaHTML = file_get_contents("html/ordini_utente.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

$ordini="";

if ($connessioneOk) {
	$data= $connessione->getOrdiniUtente($_SESSION['userId']);
	$da_sostituire = ['{', '}'];
	$sostituti = ['<span lang="en">', '</span>'];

	if($data) {
		$ordini.='<ol>';

		foreach ($data as $ordine) {
			$totale=0;

			$ordini.='<li>';
			$ordini.='<h2>Ordine del: <time datetime="'.$ordine['data']. '">'  . $ordine['data'] . '</time></h2>';
			
			$id_ordine=$ordine['id'];

			$articoli=$connessione->getArticoliOrdine($id_ordine); 
			
			$ordini.='<ul>';
			foreach ((array)$articoli as $articolo) {
				$ordini.='<li>';
				$ordini.='<h3 class="h3_ou"><a href="articolo.php?id='.$articolo['id'].'">'.str_replace($da_sostituire, $sostituti, $articolo['nome']).'</a></h3>';
				$ordini.='<div class="img_wrapper_ou"><img src="img/' . $articolo['immagine'] . '" alt=""/></div>' ;
				$ordini.='<p class="quantita_ou">Quantit&agrave;:' .$articolo['quantita'] . '</p>';

				if ($articolo['pr_scontato'] != null) {
					$ordini .= '<p>Prezzo: <span class="ou_vecchio_prezzo">' . $articolo['prezzo'].' &#8364;</span> <span class="ou_nuovo_prezzo">'.$articolo['pr_scontato'].' &#8364;</span></p>';

					$totale= $totale + $articolo['pr_scontato']*$articolo['quantita'];
				} else {
					$ordini .= '<p>Prezzo: ' . $articolo['prezzo'] . ' &#8364;</p>';
					$totale= $totale + $articolo['prezzo']*$articolo['quantita'];
				}
				$ordini.='</li>';
			}

			$ordini.='</ul>';
			$ordini.= '<p class="ordini_totale">Totale: '. $totale .'&#8364;</p>';
		}
		$ordini.='</ol>';
	} else {
		$ordini='<p id="ordini_no_order"> Nessun ordine effettuato </p>';
	}
	
	$numeroDiArticoliInCarrello = getNumeroArticoliCarrelloPerHeader($connessione);

	$paginaHTML = str_replace("%header%", setUserHeaderForAreaPersonale($numeroDiArticoliInCarrello), $paginaHTML);

	$connessione->closeDBConnection();
} else {
	$paginaHTML = str_replace("%header%", setUserHeaderForAreaPersonale(null), $paginaHTML);

	$ordini="<p role=\"alert\">I sistemi sono al momento non disponibili, riprova piu tardi</p>";
}

$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);

$paginaHTML= str_replace("%ordini%", $ordini, $paginaHTML);

echo $paginaHTML;
?>