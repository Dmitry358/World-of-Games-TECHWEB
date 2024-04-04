<?php

    require_once "db.php";
    require_once "utils.php";
    use DB\DBAccess;

    session_set_cookie_params(60*60*24*365*100);//100 anni
    session_start();

    if(isset($_POST["button-logout"]) && $_POST["button-logout"] == true){
        unset($_SESSION["userId"]);
        unset($_SESSION["isAdmin"]);
        $_SESSION['carrello'] = array();
        header("Location: index.php");
        return;
    }

    $connessione = new DBAccess();
    $connessioneOK = $connessione->openDBConnection();

    if (!isset($_SESSION["userId"]) && !isset($_SESSION["carrello"])) {
        $_SESSION['carrello'] = array();
    }

    if (!isset($_SESSION["status-message"])) {
        $_SESSION['status-message'] = '';
    }

    if (!isset($_SESSION["status-message-role"])) {
        $_SESSION['status-message-role'] = '';
    }

    $paginaHTML = file_get_contents("html/index.html");

    $connessione = new DBAccess();

    $connessioneOk = $connessione->openDBConnection();

    if ($connessioneOk) {

        $resultUltimiArrivi = $connessione->getUltimiArrivi();

        $resultOfferte = $connessione->getOfferte();


        $alreadyInserted = [];

        $ultimi_arrivi = getFilaGiochi($resultUltimiArrivi, $alreadyInserted);

        $offerte = getFilaGiochi($resultOfferte, $alreadyInserted);


        $paginaHTML = str_replace("%ultimi-arrivi%", $ultimi_arrivi, $paginaHTML);

        $paginaHTML = str_replace("%offerta%", $offerte, $paginaHTML);

        if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
            $numeroDiArticoliInCarrello = getNumeroArticoliCarrelloPerHeader($connessione);
            $paginaHTML = str_replace("%header%", setUserHeaderForHomeAndElencoArticoli($numeroDiArticoliInCarrello), $paginaHTML);
        } else {
            $paginaHTML = str_replace("%header%", setAdminHeaderForHomeAndElencoArticoli(), $paginaHTML);
        }

        $paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);

        $connessione->closeDBConnection();

    } else {

        $paginaHTML = str_replace("%ultimi-arrivi%", "<p role=\"alert\">I sistemi sono al momento non disponibili, riprova più tardi.</p>", $paginaHTML);

        $paginaHTML = str_replace("%offerta%", "<p role=\"alert\">I sistemi sono al momento non disponibili, riprova più tardi.</p>", $paginaHTML);

        if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
            $paginaHTML = str_replace("%header%", setUserHeaderForHomeAndElencoArticoli(null), $paginaHTML);
        } else {
            $paginaHTML = str_replace("%header%", setAdminHeaderForHomeAndElencoArticoli(), $paginaHTML);
        }

        $paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);

    }
    echo $paginaHTML;
?>