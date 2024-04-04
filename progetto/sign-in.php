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

$paginaHTML = file_get_contents("html/sign-in.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

if ($connessioneOk) {
      if (isset($_POST['sign-in']) && $_POST['sign-in'] == true) {
         $username = $_POST['username'];
         $password = $_POST['password'];
         $confPassword = $_POST['confirm-password'];
         $mostraPassword = isset($_POST['mostra-password']);
         $mostraConfPassword = isset($_POST['mostra-confirm-password']);
         $email = $_POST['email'];
         $nome = $_POST['nome'];
         $cognome = $_POST['cognome'];
         $cellulare = $_POST['cellulare'];
         $indirizzo = $_POST['indirizzo'];

         $nome = trim(preg_replace('/\s\s+/',' ', $nome));
         $cognome = trim(preg_replace('/\s\s+/',' ', $cognome));
         $indirizzo = trim(preg_replace('/\s\s+/',' ', $indirizzo));

         $usernameOccupato = $connessione->esisteUsername($username);
         $emailOccupata = $connessione->esisteEmail($email);
         if (($password == $confPassword) && !$usernameOccupato && !$emailOccupata) {
            $signInResult = $connessione->inserisciUtente($nome, $cognome, $email, $password, $username, $indirizzo, $cellulare);
            if ($signInResult) {
               $_SESSION['userId'] = $signInResult;
               $_SESSION['isAdmin'] = 0;
               $_SESSION['status-message'] = "Benvenuto " . $username . "!";
               $_SESSION['status-message-role'] = "status";
               $keysCarrelloSession = array_keys($_SESSION['carrello']);
               $count = count($keysCarrelloSession);
               for ($i = 0; $i < $count; $i++) {
                  $connessione->addToCart($_SESSION['userId'], $keysCarrelloSession[$i], 0, $_SESSION['carrello'][$keysCarrelloSession[$i]]);
               }
               unset($_SESSION['carrello']);
               $connessione->closeDBConnection();
               header("Location: index.php");
               return;
            } else {
               $connessione->closeDBConnection();
               $paginaHTML = str_replace("%message%", '<p id="sistemi-non-disponibili" role="alert">I sistemi sono al momento non disponibili, riprova più tardi.</p>', $paginaHTML);
               $paginaHTML = str_replace('%username-occupato%', '', $paginaHTML);
               $paginaHTML = str_replace('%email-occupata%', '', $paginaHTML);
               $paginaHTML = str_replace('%valoreUsername%', $username, $paginaHTML);
               $paginaHTML = str_replace('%valorePassword%', $password, $paginaHTML);
               $paginaHTML = str_replace('%valoreConfPassword%', $confPassword, $paginaHTML);
               $paginaHTML = str_replace('%valoreEmail%', $email, $paginaHTML);
               $paginaHTML = str_replace('%valoreNome%', $nome, $paginaHTML);
               $paginaHTML = str_replace('%valoreCognome%', $cognome, $paginaHTML);
               $paginaHTML = str_replace('%valoreCellulare%', $cellulare, $paginaHTML);
               $paginaHTML = str_replace('%valoreIndirizzo%', $indirizzo, $paginaHTML);
               $paginaHTML = str_replace('%valoreCheckedShowPsw%', $mostraPassword ? ' checked' : '', $paginaHTML);
               $paginaHTML = str_replace('%valoreCheckedShowConfPsw%', $mostraConfPassword ? ' checked' : '', $paginaHTML);
               $paginaHTML = str_replace('%tipoPswInput%', $mostraPassword ? 'text' : 'password', $paginaHTML);
               $paginaHTML = str_replace('%tipoConfPswInput%', $mostraConfPassword ? 'text' : 'password', $paginaHTML);
            }
         } else {
               $paginaHTML = str_replace("%message%", (($password != $confPassword ? '<p id="credenziali-non-corrette"><strong role="alert">Le password non coincidono</strong></p>' : '')), $paginaHTML);
               $paginaHTML = str_replace('%username-occupato%', ($usernameOccupato ? '<strong id="username-occupato" role="alert">Username già in uso</strong>' : ''), $paginaHTML);
               $paginaHTML = str_replace('%email-occupata%', ($emailOccupata ? '<strong id="email-occupata" role="alert">Email già in uso</strong>' : ''), $paginaHTML);
               $paginaHTML = str_replace('%valoreUsername%', $username, $paginaHTML);
               $paginaHTML = str_replace('%valorePassword%', $password, $paginaHTML);
               $paginaHTML = str_replace('%valoreConfPassword%', $confPassword, $paginaHTML);
               $paginaHTML = str_replace('%valoreEmail%', $email, $paginaHTML);
               $paginaHTML = str_replace('%valoreNome%', $nome, $paginaHTML);
               $paginaHTML = str_replace('%valoreCognome%', $cognome, $paginaHTML);
               $paginaHTML = str_replace('%valoreCellulare%', $cellulare, $paginaHTML);
               $paginaHTML = str_replace('%valoreIndirizzo%', $indirizzo, $paginaHTML);
               $paginaHTML = str_replace('%valoreCheckedShowPsw%', $mostraPassword ? ' checked' : '', $paginaHTML);
               $paginaHTML = str_replace('%valoreCheckedShowConfPsw%', $mostraConfPassword ? ' checked' : '', $paginaHTML);
               $paginaHTML = str_replace('%tipoPswInput%', $mostraPassword ? 'text' : 'password', $paginaHTML);
               $paginaHTML = str_replace('%tipoConfPswInput%', $mostraConfPassword ? 'text' : 'password', $paginaHTML);
         }
      } else {
         if (!isset($_SESSION["userId"])) { //non loggato
            $paginaHTML = str_replace('%message%', '', $paginaHTML);
            $paginaHTML = str_replace('%username-occupato%', '', $paginaHTML);
            $paginaHTML = str_replace('%email-occupata%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreUsername%', '', $paginaHTML);
            $paginaHTML = str_replace('%valorePassword%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreConfPassword%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreEmail%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreNome%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreCognome%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreCellulare%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreIndirizzo%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreCheckedShowPsw%', '', $paginaHTML);
            $paginaHTML = str_replace('%valoreCheckedShowConfPsw%', '', $paginaHTML);
            $paginaHTML = str_replace('%tipoPswInput%', 'password', $paginaHTML);
            $paginaHTML = str_replace('%tipoConfPswInput%', 'password', $paginaHTML);
         } else { //già loggato
            header("Location: 404.php");
            return;
         }
      }
} else {
   $paginaHTML = str_replace("%message%", '<p id="sistemi-non-disponibili" role="alert">I sistemi sono al momento non disponibili, riprova più tardi.</p>', $paginaHTML);
   $paginaHTML = str_replace('%username-occupato%', '', $paginaHTML);
   $paginaHTML = str_replace('%email-occupata%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreUsername%', '', $paginaHTML);
   $paginaHTML = str_replace('%valorePassword%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreConfPassword%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreEmail%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreNome%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreCognome%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreCellulare%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreIndirizzo%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreCheckedShowPsw%', '', $paginaHTML);
   $paginaHTML = str_replace('%valoreCheckedShowConfPsw%', '', $paginaHTML);
   $paginaHTML = str_replace('%tipoPswInput%', 'password', $paginaHTML);
   $paginaHTML = str_replace('%tipoConfPswInput%', 'password', $paginaHTML);
}
echo $paginaHTML;