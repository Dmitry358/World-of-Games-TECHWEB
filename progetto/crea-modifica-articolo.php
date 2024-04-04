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

$paginaHTML = file_get_contents("html/crea-modifica-articolo.html");

$connessione = new DBAccess();

$connessioneOk = $connessione->openDBConnection();

if (isset($_POST["id-articolo"])) {
    $id_articolo = $_POST["id-articolo"];
} else {
    $id_articolo = -1;
}

if (isset($_POST["url"])) {
    $pagina_provenienza = $_POST["url"];
} else {
    $pagina_provenienza = "crea-modifica-articolo.php";
}

if (isset($_POST["nuovo-modifica"]) && $_POST["nuovo-modifica"] == "Modifica") {
    $crea_modifica = "Modifica";
    $ins_o_mod = "modifica";
} else {
    $crea_modifica = "Nuovo";
    $ins_o_mod = "inserimento";
}

$disponibilita_sistemi = "";

$nome_articolo = "";
$prezzo_articolo = "";
$prezzo_scontato_articolo = "";
$categoria_articolo = "";
$sottocategoria_articolo = "";
$descrizione_articolo = "";
$parole_chiave_articolo = "";
$alt_immagine_articolo = "";
$immagine_articolo = "";
$visibile_articolo = "";
$ultimi_arrivi_articolo = "";
$in_offerta_articolo = "";

$errore_categoria = "";
$errore_immagine = "";
$errore_nome_articolo = "";
$errore_alt_immagine = "";
$errore_descrizione = "";
$errore_parole_chiave = "";
$errore_prezzo = "";
$errore_scontato = "";


if ($connessioneOk) {
    $disponibilita_sistemi = "";

    if(isset($_POST["button-salva"]) && $_POST["button-salva"] == true){
        $errore = false;

        $nome_articolo = trim($_POST["nome-articolo"]);
        $nome_articolo = strip_tags($nome_articolo);
        if($nome_articolo == ""){
            $errore = true;
            $errore_nome_articolo = '<strong class="errorSuggestion" role="alert">Questo campo non può essere lasciato vuoto</strong>';
        }
        $prezzo_articolo = $_POST["prezzo"];
        $prezzo_articolo = strip_tags($prezzo_articolo);
        if(is_numeric($prezzo_articolo) == false){
            $errore = true;
            $errore_prezzo = '<strong class="errorSuggestion" role="alert">Inserire un numero</strong>';
        }
        $prezzo_scontato_articolo = $_POST["prezzo-scontato"];
        $prezzo_scontato_articolo = strip_tags($prezzo_scontato_articolo);
        if(is_numeric($prezzo_scontato_articolo) == false && $prezzo_scontato_articolo != ""){
            $errore = true;
            $errore_scontato = '<strong class="errorSuggestion" role="alert">Inserire un numero</strong>';
        }

        if($_POST["categoria-articolo"] == 0){
            $errore = true;
            $errore_categoria = '<strong class="errorSuggestion" role="alert">Inserire una categoria</strong>';
        }else{
            $row = $connessione->getSottocategoria(false, $_POST["categoria-articolo"]);
            if($row != null){
                $categoria_articolo = $row["categoria"];
                $sottocategoria_articolo = $row["sottocategoria"];
            }
        }

        $descrizione_articolo = $_POST["descrizione"];
        $descrizione_articolo = strip_tags($descrizione_articolo);
        $descrizione_articolo = preg_replace("/[\r\n]+/", "\n", $descrizione_articolo);
        $descrizione_articolo = rtrim($descrizione_articolo);

        if(trim($descrizione_articolo) == ""){
            $errore = true;
            $errore_descrizione = '<strong class="errorSuggestion" role="alert">Questo campo non può essere lasciato vuoto</strong>';
        }
        $parole_chiave_articolo = $_POST["parole-chiave"];
        $parole_chiave_articolo = strip_tags($parole_chiave_articolo);
        if(trim($parole_chiave_articolo) == ""){
            $errore = true;
            $errore_parole_chiave = '<strong class="errorSuggestion" role="alert">Questo campo non può essere lasciato vuoto</strong>';
        }

        $alt_immagine_articolo = $_POST["alt-immagine"];
        $alt_immagine_articolo = strip_tags($alt_immagine_articolo);
        if(trim($alt_immagine_articolo) == ""){
            $errore = true;
            $errore_alt_immagine = '<strong class="errorSuggestion" role="alert">Questo campo non può essere lasciato vuoto</strong>';
        }

        if($_POST["immagine-articolo"] == ""){
            $errore = true;
            $errore_immagine = '<strong class="errorSuggestion" role="alert">Selezionare un\'immagine</strong>';
        }else{
            $allowed_extensions = array("png", "jpg", "jpeg", "jfif", "PNG");
            $image_extension = explode(".", $_POST["immagine-articolo"]);
            $extension = end($image_extension);
            if(!in_array($extension, $allowed_extensions)){
                $errore = true;
                $errore_immagine = '<strong class="errorSuggestion" role="alert">Selezionare un file di tipo immagine</strong>';
            }else{
                $immagine_articolo = $_POST["immagine-articolo"];
            } 
        }
        
        if(isset($_POST["in-offerta"]) && $_POST["in-offerta"] == true)
            $in_offerta_articolo = 1;
        else
            $in_offerta_articolo = 0;
        if(isset($_POST["visibile"]) && $_POST["visibile"] == true)
            $visibile_articolo = 1;
        else
            $visibile_articolo = 0;
        if(isset($_POST["ultimi-arrivi"]) && $_POST["ultimi-arrivi"] == true)
            $ultimi_arrivi_articolo = 1;
        else
            $ultimi_arrivi_articolo = 0;

        if($errore == false){
            $da_sostituire = ["{", "}", "'", '"'];
            $sostituti = ["<span lang=\"en\">", "</span>", "\'", '\"'];
            $nome_articolo = str_replace($da_sostituire, $sostituti, $nome_articolo);
            $descrizione_articolo = str_replace($da_sostituire, $sostituti, $descrizione_articolo);
            $parole_chiave_articolo = str_replace($da_sostituire, $sostituti, $parole_chiave_articolo);
            $alt_immagine_articolo = str_replace($da_sostituire, $sostituti, $alt_immagine_articolo);

            $id_categoria = $connessione->getIdCategoria($categoria_articolo, $sottocategoria_articolo);

            if(isset($_POST["nuovo-modifica"]) && $_POST["nuovo-modifica"] == "Modifica"){
                if($connessione->getArticoloByIdAndName($_POST["id-articolo"], $nome_articolo) == true){
                    $errore_nome_articolo = '<strong class="errorSuggestion" role="alert">Nome già usato</strong>'; 
                }else{
                    $result = $connessione->updateArticolo($nome_articolo, $id_categoria["id"], $prezzo_articolo, $prezzo_scontato_articolo, $immagine_articolo, $descrizione_articolo, $alt_immagine_articolo, $parole_chiave_articolo, $in_offerta_articolo, $ultimi_arrivi_articolo, $visibile_articolo, $_POST["id-articolo"]);
                    if($result == true){
                        $_SESSION["status-message"] = "Modifica dell'articolo avvenuta correttamente";
                        $_SESSION['status-message-role'] = 'status';
                        $connessione->closeDBConnection();
                        header("location: ".$pagina_provenienza);
                        return;
                    }else{
                        $_SESSION["status-message"] = "Siamo spiacenti, c'è stato un problema con la modifica di questo articolo";
                        $_SESSION['status-message-role'] = 'alert';
                    }
                }
            }else{    
                if($connessione->getArticoloByName($nome_articolo) == true){
                    $errore_nome_articolo = '<strong class="errorSuggestion" role="alert">Nome già usato</strong>'; 
                }else{
                    $result = $connessione->insertArticolo($nome_articolo, $id_categoria["id"], $prezzo_articolo, $prezzo_scontato_articolo, $immagine_articolo, $descrizione_articolo, $alt_immagine_articolo, $parole_chiave_articolo, $in_offerta_articolo, $ultimi_arrivi_articolo, $visibile_articolo);
                    if($result == true){
                        $_SESSION["status-message"] = "Inserimento dell'articolo avvenuta correttamente";
                        $_SESSION['status-message-role'] = 'status';
                        $connessione->closeDBConnection();
                        header("location: ".$pagina_provenienza);
                        return;
                    }else{
                        $_SESSION["status-message"] = "Siamo spiacenti, c'è stato un problema con l'inserimento di questo articolo";
                        $_SESSION['status-message-role'] = 'alert';
                    }
                    
                }
            }
        }

    } else if(isset($_POST["button-modifica"]) && $_POST["button-modifica"] == true){
        $crea_modifica = "Modifica";
        $ins_o_mod = "modifica";
        $id_articolo = $_POST["id-articolo"];
        $row = $connessione->getArticolo($id_articolo);

        $articolo_eliminato = $connessione->esisteArticolo($id_articolo);

        if($articolo_eliminato == false){
            if($row != null){
                $row_categoria = $connessione->getSottocategoriaArticolo($id_articolo);
    
                $nome_articolo = $row["nome"];
                $prezzo_articolo = $row["prezzo"];
                $prezzo_scontato_articolo = $row["pr_scontato"];
                $categoria_articolo = $row_categoria["categoria"];
                $sottocategoria_articolo = $row_categoria["sottocategoria"];
                $descrizione_articolo = $row["descrizione"];
                $parole_chiave_articolo = $row["parole_chiave"];
                $alt_immagine_articolo = $row["alt_immagine"];
                $immagine_articolo = $row["immagine"];
                $visibile_articolo = $row["visibile"];
                $ultimi_arrivi_articolo = $row["ultimi_arrivi"];
                $in_offerta_articolo = $row["offerta"];

                $da_sostituire = ["<span lang=en>", "</span>"];
                $sostituti = ["{", "}"];
                $nome_articolo = str_replace($da_sostituire, $sostituti, $nome_articolo);
                $descrizione_articolo = str_replace($da_sostituire, $sostituti, $descrizione_articolo);
                $parole_chiave_articolo = str_replace($da_sostituire, $sostituti, $parole_chiave_articolo);
                $alt_immagine_articolo = str_replace($da_sostituire, $sostituti, $alt_immagine_articolo);
            }
        }else{
            $_SESSION['status-message'] = "Impossibile modificare, l'articolo selezionato è stato eliminato";
            $_SESSION['status-message-role'] = "alert";
            $connessione->closeDBConnection();
            header("Location: " . $pagina_provenienza);
            return;
        }
        
    } else if(isset($_POST["button-annulla"]) && $_POST["button-annulla"] == true){
        if(isset($_POST["nuovo-modifica"]) && $_POST["nuovo-modifica"] == "Modifica"){
            $connessione->closeDBConnection();
            header("location: ".$pagina_provenienza);
            return;
        }else{
            $connessione->closeDBConnection();
            header("location: crea-modifica-articolo.php");
            return;
        }
    }

    $elementi_categoria = "";
    $elementi_sottocategoria = "";

    $trovato_selected = false;
    $result = $connessione->getSottocategorieResult();
    $connessione->closeDBConnection();
    $da_sostituire = ["{", "}"];
    $sostituti = ["<span lang=en>", "</span>"];
    if($result){
        foreach ($result as $row) {
            $elementi_categoria .= "<option value='".$row["id"]."'";
            if($row["categoria"] == $categoria_articolo && $row["sottocategoria"] == $sottocategoria_articolo){
                $elementi_categoria .= " selected";
                $trovato_selected = true;
            }
            $elementi_categoria .= ">".str_replace($da_sostituire, $sostituti, $row["categoria"])." - ".str_replace($da_sostituire, $sostituti, $row["sottocategoria"])."</option>";
        }
    }
    $elementi_categoria_default = "";
    if($trovato_selected == false){
        $elementi_categoria_default = "selected";
    }

} else {
    $disponibilita_sistemi = "<p role=\"alert\">I sistemi sono al momento non disponibili, riprova più tardi</p>";
}

if (isset($_POST["url"])) {
    $paginaHTML = str_replace("%breadcrumb%", '&gt;&gt; <a href="'.$_POST['url'].'">Elenco articoli</a> ', $paginaHTML);
} else {
    $paginaHTML = str_replace("%breadcrumb%", "", $paginaHTML);
}


$paginaHTML = str_replace("%ins-o-mod%", $ins_o_mod, $paginaHTML);

$paginaHTML = str_replace("%sistemi-disponibili%", $disponibilita_sistemi, $paginaHTML);

$paginaHTML = str_replace("%categorie-articolo%", $elementi_categoria, $paginaHTML);
$paginaHTML = str_replace("%categorie-articolo-default%", $elementi_categoria_default, $paginaHTML);

$paginaHTML = str_replace("%nome-articolo%", $nome_articolo, $paginaHTML);
$paginaHTML = str_replace("%prezzo%", $prezzo_articolo, $paginaHTML);
$paginaHTML = str_replace("%prezzo-scontato%", $prezzo_scontato_articolo, $paginaHTML);
$paginaHTML = str_replace("%descrizione%", $descrizione_articolo, $paginaHTML);
$paginaHTML = str_replace("%parole-chiave%", $parole_chiave_articolo, $paginaHTML);
$paginaHTML = str_replace("%alt-immagine%", $alt_immagine_articolo, $paginaHTML);
$paginaHTML = str_replace("%immagine-articolo%", $immagine_articolo, $paginaHTML);
$paginaHTML = str_replace("%visibile%", ($visibile_articolo?"checked":""), $paginaHTML);
$paginaHTML = str_replace("%ultimi-arrivi%", ($ultimi_arrivi_articolo?"checked":""), $paginaHTML);
$paginaHTML = str_replace("%in-offerta%", ($in_offerta_articolo?"checked":""), $paginaHTML);
    
$paginaHTML = str_replace("%errore-categoria%", $errore_categoria, $paginaHTML);
$paginaHTML = str_replace("%errore-immagine%", $errore_immagine, $paginaHTML);
$paginaHTML = str_replace("%errore-nome-articolo%", $errore_nome_articolo, $paginaHTML);
$paginaHTML = str_replace("%errore-descrizione%", $errore_descrizione, $paginaHTML);
$paginaHTML = str_replace("%errore-parole-chiave%", $errore_parole_chiave, $paginaHTML);
$paginaHTML = str_replace("%errore-alt-immagine%", $errore_alt_immagine, $paginaHTML);
$paginaHTML = str_replace("%errore-prezzo%", $errore_prezzo, $paginaHTML);
$paginaHTML = str_replace("%errore-scontato%", $errore_scontato, $paginaHTML);

$paginaHTML = str_replace("%url%", $pagina_provenienza, $paginaHTML);
$paginaHTML = str_replace("%id%", $id_articolo, $paginaHTML);

$paginaHTML = str_replace("%crea-modifica%", $crea_modifica, $paginaHTML);
$paginaHTML = str_replace("%nuovo-modifica%", $crea_modifica, $paginaHTML);
$paginaHTML = str_replace("%status-bar%", setStatusBar(), $paginaHTML);
$paginaHTML = str_replace("%header%", setAdminHeaderForCreaModificaArticolo(), $paginaHTML);
$paginaHTML = str_replace("%titolo%", setTitolo($crea_modifica), $paginaHTML);

echo $paginaHTML;

?>