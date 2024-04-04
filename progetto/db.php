<?php

namespace DB;

class DBAccess {
	private const HOST_DB = "127.0.0.1";
	private const DATABASE_NAME = "gromano";
	private const USERNAME = "gromano";
	private const PASSWORD = "Iwaelai6onah8Lai";

	private $connection;

	public function openDBConnection() {
		$this->connection = mysqli_connect(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);
		if(mysqli_errno($this->connection)) {
			return false;
		} else {
			return true;
		}
	}

	public function closeDBConnection() {
		mysqli_close($this->connection);
	}


	/*COMMON*/

	function removeFromCart ($idUtente, $idArticolo) {
		$query = "DELETE FROM carrello WHERE utente = $idUtente AND articolo = $idArticolo";
		mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getIdArticoliNelCarrello($idUtente) {
		$query = "SELECT articolo, quantita FROM carrello WHERE utente = $idUtente";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_num_rows($queryResult) > 0) {
			$result = array();
			while ($row = mysqli_fetch_assoc($queryResult)) {
				$result[$row['articolo']] = $row['quantita'];
			}
			$queryResult->free();
			return $result;
		} else {
			return null;
		}
	}

	function esisteArticolo ($idArticolo) {
		$query = "SELECT eliminato, visibile FROM articolo WHERE id = $idArticolo";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		$result = $queryResult->fetch_array();
		$queryResult->free();
		if (!isset($_SESSION['userId']) || (isset($_SESSION['userId']) && !$_SESSION['isAdmin'])) {
			return $result['eliminato'] or !$result['visibile'];
		}
		return $result['eliminato'];
	}

	public function addToCart ($idUtente, $idArticolo, $quantitaCorrente, $howMany) {
		if ($quantitaCorrente == 0) {
			$queryResult = mysqli_query($this->connection, "INSERT INTO carrello(utente, articolo, quantita) VALUES ($idUtente, $idArticolo, $howMany)") or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		} else {
			$queryResult = mysqli_query($this->connection, "UPDATE carrello SET quantita = ($quantitaCorrente+$howMany) WHERE carrello.utente = $idUtente AND carrello.articolo = $idArticolo;") or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		}
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/*COMMON*/


	/*INDEX*/

	public function getArticoliHome ($query) {
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_num_rows($queryResult) > 0) {
			$result = array();
			while ($row = mysqli_fetch_assoc($queryResult)) {
				array_push($result, $row);
			}
			$queryResult->free();
			return $result;
		} else {
			return null; //null=0=false=""
		}
	}

	public function getUltimiArrivi() {
		$query = "SELECT articolo.id, articolo.nome, articolo.prezzo, articolo.pr_scontato, articolo.immagine, articolo.alt_immagine, AVG(commento.voto) AS media, count(commento.id) as count, sottocategoria.sottocategoria FROM articolo INNER JOIN sottocategoria ON articolo.sottocategoria = sottocategoria.id LEFT JOIN commento ON articolo.id = commento.articolo WHERE articolo.eliminato = 0 AND articolo.ultimi_arrivi = 1 AND sottocategoria.categoria = 'Giochi'";
		if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
			$query .= " AND articolo.visibile = 1";
		}
		$query .= " GROUP BY articolo.id";
		return $this->getArticoliHome($query);
	}

	public function getOfferte(){
		$query = "SELECT articolo.id, articolo.nome, articolo.prezzo, articolo.pr_scontato, articolo.immagine, articolo.alt_immagine, AVG(commento.voto) AS media, count(commento.id) as count, sottocategoria.sottocategoria FROM articolo INNER JOIN sottocategoria ON articolo.sottocategoria = sottocategoria.id LEFT JOIN commento ON articolo.id = commento.articolo WHERE articolo.eliminato = 0 AND articolo.offerta = 1 AND sottocategoria.categoria = 'Giochi'";
		if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
			$query .= " AND articolo.visibile = 1";
		}
		$query .= " GROUP BY articolo.id";
		return $this->getArticoliHome($query);
	}

	/*INDEX*/


	/*ELENCO ARTICOLI*/

	function getArticoli ($termine_ricerca, &$categoria, &$sottocategoria, $filtra_recensioni, $filtra_offerte, $filtra_ultimi_arrivi, $ordina_per) {
		$query = "SELECT articolo.id, articolo.nome, articolo.prezzo, articolo.pr_scontato, articolo.immagine, articolo.alt_immagine, AVG(commento.voto) AS media, count(commento.id) as count, sottocategoria.sottocategoria FROM articolo INNER JOIN sottocategoria ON articolo.sottocategoria = sottocategoria.id LEFT JOIN commento ON articolo.id = commento.articolo";
		$categorie = array(
    		'Console',
    		'Giochi',
    		'Controller',
    		'Accessori'
    	);
    	$sottocategorie = array(
    		'Playstation',
    		'Nintendo',
    		'Xbox',
    		'Sparatutto',
    		'Avventura',
    		'Platform',
    		'Azione',
    		'Sportivi',
    		'Corse',
    		'Horror',
    		'Open World',
    		'Playstation',
    		'Nintendo',
    		'Xbox',
    		'Pc',
    		'Cuffie',
    		'Microfono',
    		'Tastiera',
    		'Mouse', 
    		'Tutti'
    	);

    	if ($categoria && $sottocategoria) {
    		if (!in_array("$categoria", $categorie) || !in_array("$sottocategoria", $sottocategorie)) {
    			$categoria = null;
    			$sottocategoria = null;
    		}
		}

		if ($termine_ricerca) {
			if ($termine_ricerca == 'Catalogo' || $termine_ricerca == 'catalogo') {
				$query .= " WHERE 1";
			} else {
				$termini = explode(" ", $termine_ricerca);
				$query .= " WHERE (nome LIKE '%$termini[0]%' OR sottocategoria.categoria LIKE '%$termini[0]%' OR sottocategoria.sottocategoria LIKE '%$termini[0]%'";
				for ($i = 1; $i < count($termini); $i++) {
					$query .= " OR nome LIKE '%$termini[$i]%' OR sottocategoria.categoria LIKE '%$termini[$i]%' OR sottocategoria.sottocategoria LIKE '%$termini[$i]'";
				}
				$query .= ")";
			}
		} else if ($categoria && $sottocategoria) {
			$query .= " WHERE sottocategoria.categoria LIKE '%$categoria%'";
			if ($sottocategoria != "Tutti") {
				$query .= " AND sottocategoria.sottocategoria LIKE '%$sottocategoria%'";
			}
		} else {
			$query .= " WHERE 1";
		}
		$query .= " AND articolo.eliminato = 0";
		if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
			$query .= " AND articolo.visibile = 1";
		}
		if ($filtra_offerte == "true") {
			$query .= " AND articolo.offerta = 1";
		}
		if ($filtra_ultimi_arrivi == "true") {
			$query .= " AND articolo.ultimi_arrivi = 1";
		}
		$query .= " GROUP BY articolo.id";
		if ($filtra_recensioni) { //se non null e non false
			$filtra_voti = intval($filtra_recensioni[0]);
			if ($filtra_voti >= 1 && $filtra_voti <= 4) {
				$query .= " HAVING media >= $filtra_voti";
			}
		}
		if ($ordina_per == "prezzo-decrescente") {
			$query .= " ORDER BY COALESCE(pr_scontato, prezzo) DESC";
		} else if ($ordina_per == "media-recensioni-clienti") {
			$query .= " ORDER BY media DESC";
		} else {
			$query .= " ORDER BY COALESCE(pr_scontato, prezzo) ASC";
		}
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_num_rows($queryResult) > 0) {
			$result = array();
			while ($row = mysqli_fetch_assoc($queryResult)) {
				array_push($result, $row);
			}
			$queryResult->free();
			return $result;
		} else {
			return null; //null=0=false=""
		}
	}

	function getCartProductQuantity ($idUtente, $idArticolo) {
		$query = "SELECT COALESCE((SELECT quantita FROM carrello WHERE utente=$idUtente AND articolo=$idArticolo), 0) AS quantita";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		$result = $queryResult->fetch_array();
		$queryResult->free();
		return $result['quantita'];
	}

	function deleteArticolo ($idArticolo) {
		mysqli_query($this->connection, "UPDATE articolo SET eliminato = 1 WHERE articolo.id = $idArticolo") or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/*ELENCO ARTICOLI*/


	/*CARRELLO*/

	public function eliminaProdottoCarrello($id_da_eliminare){
		mysqli_query($this->connection, "DELETE FROM carrello WHERE articolo = $id_da_eliminare AND utente = ".$_SESSION['userId']) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}


	public function modificaQuantitaDB($id_articolo, $quantita){
		mysqli_query($this->connection, "UPDATE carrello SET quantita=$quantita WHERE articolo=$id_articolo AND utente = ".$_SESSION['userId']) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function inserisciOrdineUtente($carrello){
		date_default_timezone_set("Europe/Rome");
		$data = date("Y-m-d");
		$userId = $_SESSION['userId'];
		mysqli_query($this->connection, "INSERT INTO ordine_utente(data, utente) VALUES ('$data', $userId)") or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			$idOrdine = mysqli_insert_id($this->connection);
			foreach ($carrello as $articolo) {
				mysqli_query($this->connection, "INSERT INTO ordine VALUES ($idOrdine, $articolo[articolo], $articolo[quantita])") or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection));
			}
			return true;
		} else {
			return false;
		}
	}

	public function svuotaCarrelloUtente() {
		mysqli_query($this->connection, "DELETE FROM carrello WHERE utente = ".$_SESSION['userId']) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getCarrelloSession() {
		$result = array();
		foreach ($_SESSION['carrello'] as $id=>$quantita) {
     		$query = "SELECT *  FROM articolo WHERE id = $id";
     		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection) );
     		if (mysqli_num_rows($queryResult) > 0) {
     			$row = $queryResult->fetch_assoc();
     			$row['quantita'] = $quantita;
     			array_push($result, $row);
     		}
    	}
    	return count($result) > 0 ? $result : null;
    }

	public function getCarrelloDB() {
		$userId = $_SESSION['userId'];
		$query = "SELECT * FROM carrello INNER JOIN (articolo) ON (articolo.id = carrello.articolo)  WHERE utente = $userId AND articolo.eliminato = false";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection) );
		if (mysqli_num_rows($queryResult) > 0) {
			$result = array();
			while ($row = mysqli_fetch_assoc($queryResult)) {
				array_push($result, $row);
			}
			$queryResult->free();
			return $result;
		}
		return null;
	}

	/*CARRELLO*/


	/*LOGIN & SIGN IN*/

	function login ($usernameOrEmail, $password) {
		$usernameOrEmail = str_replace('"', '\"', $usernameOrEmail);
		$query = "SELECT id, username, admin FROM utente WHERE (mail = \"$usernameOrEmail\" OR username = \"$usernameOrEmail\") AND pw = \"$password\"";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_num_rows($queryResult) > 0) {
			$result = $queryResult->fetch_array();
			$queryResult->free();
			return $result;
		} else {
			return null;
		}
	}

	function esisteUsername($username) {
		$query = "SELECT COUNT(*) as esiste FROM utente WHERE username = \"$username\"";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		$result = $queryResult->fetch_array();
		$queryResult->free();
		return $result['esiste'];
	}

	function esisteEmail($email) {
		$query = "SELECT COUNT(*) as esiste FROM utente WHERE mail = \"$email\"";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		$result = $queryResult->fetch_array();
		$queryResult->free();
		return $result['esiste'];
	}

	function inserisciUtente($nome, $cognome, $email, $password, $username, $indirizzo, $cellulare) {
		$query = "INSERT INTO utente(nome, cognome, mail, pw, username, indirizzo, cellulare, admin) VALUES(\"$nome\", \"$cognome\", \"$email\", \"$password\", \"$username\", \"$indirizzo\", \"$cellulare\", 0);";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return mysqli_insert_id($this->connection);
		} else {
			return false;
		}
	}

	public function setQuantitaArticoloToCart ($idUtente, $idArticolo, $quantita) {
		$queryResult = mysqli_query($this->connection, "UPDATE carrello SET quantita = $quantita WHERE carrello.utente = $idUtente AND carrello.articolo = $idArticolo;") or die("Errore nell'esecuzione della query: " . mysqli_error($this->connection));
		if (mysqli_affected_rows($this->connection) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/*LOGIN & SIGN IN*/


	/*ARTICOLO*/

	public function getArticolo($idArticolo){
		$query = "SELECT * FROM articolo WHERE id = $idArticolo AND eliminato = 0";
		if (!isset($_SESSION["userId"]) || (isset($_SESSION["userId"]) && !$_SESSION["isAdmin"])) {
			$query .= " AND visibile = 1";
		}
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection));
		$result = $queryResult->fetch_array();
		$queryResult->free();
		return $result;
	}	

	private function isVerified($idArticolo, $idUser){
		$query = "SELECT ordine_utente.utente, ordine.articolo FROM (ordine_utente JOIN ordine ON ordine_utente.id = ordine.ordine) 
				  WHERE ordine.articolo = '$idArticolo' AND ordine_utente.utente = '$idUser'"; 
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection));
		if (mysqli_num_rows($queryResult) > 0) {
			$queryResult->free();
			return true;
		} else {
			return false;
		}
	}

	public function getComments($location, $idArticolo, $isAdmin, $confirmComment){ 
		$query = "SELECT utente.id, utente.username, commento.commento, commento.voto, commento.data FROM (commento JOIN utente ON commento.utente = utente.id) WHERE commento.articolo = '$idArticolo' ORDER BY commento.data DESC";
		$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection) );
		
		$commentForm = "";

		if(!$isAdmin){
			$commentForm = $confirmComment['message']."<button id=\"btnComment_as\" onclick=\"showComment()\">Nascondi commenti</button>
							<div id=\"post_as\">
								<form id=\"comment_form_as\" action=\"".htmlspecialchars($_SERVER['REQUEST_URI'])."\" method=\"post\">
									<input type=\"hidden\" name=\"prev-url\" value=\"".$location."\"/>

	                    			<label id=\"label_comm_as\" for=\"input_comment_as\">Scrivi commento:</label> 
	                    			<textarea id=\"input_comment_as\" name=\"box_comment\" autocomplete=\"off\" aria-required=\"true\">%comment%</textarea>                
					                
					                <label id=\"vote_form_as\">Metti voto:</label>	
					                <label for=\"vote0_as\" id=\"l-vote0\">Voto zero</label>
					                <input type=\"radio\" id=\"vote0_as\" name=\"vote\" value=\"0\"/>
					                <img id=\"i-vote0_as\" src=\"images/0-stars-medium.png\" alt=\"voto zero\">
					                    
					                <label for=\"vote1_as\" id=\"l-vote1\">Voto uno</label>
					                <input type=\"radio\" id=\"vote1_as\" name=\"vote\" value=\"1\"/>
					                <img id=\"i-vote1_as\" src=\"images/1-stars-medium.png\" alt=\"voto uno\">
					                    
					                <label for=\"vote2_as\" id=\"l-vote2\">Voto due</label>
					                <input type=\"radio\" id=\"vote2_as\" name=\"vote\" value=\"2\"/>
					                <img id=\"i-vote2_as\" src=\"images/2-stars-medium.png\" alt=\"voto due\">
					                    
					                <label for=\"vote3_as\" id=\"l-vote3\">Voto tre</label>
					                <input type=\"radio\" id=\"vote3_as\" name=\"vote\" value=\"3\"/>
					                <img id=\"i-vote3_as\" src=\"images/3-stars-medium.png\" alt=\"voto tre\">
					                    
					                <label for=\"vote4_as\" id=\"l-vote4\">Voto quattro</label>
					                <input type=\"radio\" id=\"vote4_as\" name=\"vote\" value=\"4\"/>
					                <img id=\"i-vote4_as\" src=\"images/4-stars-medium.png\" alt=\"voto quattro\">
					                    
					                <label for=\"vote5_as\" id=\"l-vote5\">Voto cinque</label>
					                <input type=\"radio\" id=\"vote5_as\" name=\"vote\" value=\"5\"/>
					                <img id=\"i-vote5_as\" src=\"images/5-stars-medium.png\" alt=\"voto cinque\">
	                    			
	                    			<input type=\"submit\" id=\"share_comment_as\" name=\"share_comment\" value=\"Pubblica\">
	                    		</form>
	                    	</div>";
		}		
             
        if(mysqli_num_rows($queryResult) > 0){        
			$result = array();
			while($row = mysqli_fetch_assoc($queryResult)){
				array_push($result, $row);
			}
			$queryResult->free();
			$comments = "<div id =\"comment_section_as\">".$commentForm."
						 	<h2>Commenti</h2>
						 	<div id=\"allComments_as\">";				
			
			$count = count($result);
			$hideComments = false;
			if(isset($_POST['load'])){
				$prox = $count - $_POST['load'];
	            if($prox > 4){
	            	$aux = 5 + $_POST['load'];
	            	$prox = $count - $aux;
	            }
	            else{
	            	$aux = $prox + $_POST['load'];
	            	$prox = 0;
	            }
			}
			else{
				$aux = 3;
				$prox = $count - $aux;
	        }

			if($aux >= $count){
				$aux = $count;
				$hideComments = true;
			}

			for($i=0; $i < $aux; $i++){
				$voto = $this->translateVote($result[$i]['voto']);

				$verified = $this->isVerified($idArticolo, $result[$i]['id']);
				
				$comments .= "<article><h3 class=\"comm_name_as\">".$result[$i]['username']."</h3>";

				if($verified){
					$comments .= "<span class=\"userVerified_as\">(verificato)</span>";
				}

				$comments .="<time class=\"comm_date_as\"  datetime=\"".$result[$i]['data']."\">".$result[$i]['data']."</time><p 
				class=\"comm_vote_as\">Voto: <img src=\"images/".$result[$i]['voto']."-stars-medium.png\""." alt=\"voto ".$voto
				."\"></p><p class=\"comm_txt_as\">".$result[$i]['commento']."</p></article>";	 
			}
			
			if($aux > 3 or $prox > 0){ 
				if(!$hideComments){					
					if($prox > 4){ $num = 5; }
					else{ $num = $prox; }
	            	$comments .="<form action=\"".htmlspecialchars($_SERVER['REQUEST_URI'])."\" method=\"post\">
									<input type=\"hidden\" name=\"prev-url\" value=\"".$location."\"/>
	             			     	<input type=\"hidden\" id=\"load\" name=\"load\" value = \"$aux\">
	             			     	<input type=\"submit\" name=\"load_more\" id=\"load_more_as\" value=\"Carica ";
	             	if($num > 1){ $comments .= "altri $num";}
	             	else{         $comments .= "ultimo";}
	             	$comments .="\"></form>";
	            }
	            else{
	                $comments .="<form action=\"".htmlspecialchars($_SERVER['REQUEST_URI'])."\" method=\"post\">
									<input type=\"hidden\" name=\"prev-url\" value=\"".$location."\"/>

	             			     	<input type=\"hidden\" id=\"load\" name=\"load\" value=\"-2\">
	             			     	<input type=\"submit\" name=\"load_more\" id=\"load_more_as\" value=\"Nascondi commenti\"> 
	             			     </form>";
	            }
	        }

            $comments .= "</div></div>";
			return $comments;
		}
		else{
			return "<div id =\"comment_section_as\">".$commentForm."
						<h2>Commenti</h2>
						<div id=\"allComments_as\">
							<p id=\"no_comments_as\">Non ci sono i commenti per questo articolo</p>
						</div>
					</div>";
   		}			
	}

	private function haveCommented($idArticolo, $idUser){ 
		$stringaQuery = "SELECT id FROM commento WHERE articolo = '$idArticolo' AND utente = '$idUser'";
		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));
		if(mysqli_num_rows($queryResult) > 0){
			$queryResult->free();
			return true;
		} else{
			return false;
		}		
	}
	
	public function insertComment($idUser, $idArticolo, $comment, $vote){
		$date = date("Y-m-d");
		$confirmComment = array("message" =>"", "confirm" => false);

		$comment = trim($comment); 
		
		if($comment == ''){
			$confirmComment['message'] = "<p id=\"commentStatus\" class=\"comm_not_confirmed_as\" role=\"alert\">Commento non può essere vuoto</p>";
			return $confirmComment;
		}

		if(strlen($comment) > 500){
			$confirmComment['message'] =  "<p id=\"commentStatus\" class=\"comm_not_confirmed_as\" role=\"alert\">Commento non può contenere più di 500 caratteri</p>";
			return $confirmComment;
		}

		if($vote == ''){
			$confirmComment['message'] = "<p id=\"commentStatus\" class=\"comm_not_confirmed_as\" role=\"alert\">Voto non può essere non selezionato</p>";
			return $confirmComment;
		}
		
		if(!$this->haveCommented($idArticolo, $idUser)){		
			$stringaQuery = "INSERT INTO commento(utente, articolo, commento, voto, data) VALUES ('$idUser', '$idArticolo', '$comment', '$vote', '$date')";	

			$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));
			
			if($queryResult){
				$confirmComment['message'] = "<p id=\"commentStatus\" class=\"comm_confirmed_as\" role=\"alert\">Commento aggiunto con successo</p>";
				$confirmComment['confirm'] = true;
				return $confirmComment;
			}
			else{
				$confirmComment['message'] = "<p id=\"commentStatus\" class=\"comm_not_confirmed_as\" role=\"alert\">Commento non è stato aggiunto</p>";
				return $confirmComment;
			}
		}
		else{
			$confirmComment['message'] = "<p id=\"commentStatus\" class=\"comm_not_confirmed_as\" role=\"alert\">Non puoi aggiungere più di un commento</p>";
			return $confirmComment;
		}
	}

	public function getDescrizione($descrizione){
 		$descrizione = $this->convertData($descrizione);
		return "<div id=\"div_description_as\">
					<h2>Descrizione</h2>
	 				    <p>".$descrizione."</p>
				</div>";
	}

	private function translateVote($vote){
		if($vote == 0){return "zero";}	
		else if($vote == 1){return "uno";}	
		else if($vote == 2){return "due";}	
		else if($vote == 3){return "tre";}
		else if($vote == 4){return "quattro";}	
		else {return "cinque";}	
	}

	public function getArticoloData($location, $id, $nome, $immagine, $alt_immagine, $prezzo, $pr_scontato, $isAdmin){
		$da_sostituire = ['{', '}'];
		$sostituti   = ['<span lang="en">', '</span>'];

		$articolo = "";
			
		$articolo .= '<h1 id="titolo_articolo_as">'.str_replace($da_sostituire, $sostituti, $nome).'</h1><div id="img_wrapper_as"><img id="prod_img_as" src="img/'.$immagine .'"'.' alt= "'.$alt_immagine.'"/></div>';

		if($pr_scontato != null){
			if ($isAdmin) {
				$articolo .= '<p id="price_as" class="price_as_margin_top">Prezzo: <span class="old_price_as">'.$prezzo.'&#8364;</span> <span class="discount_as">'.$pr_scontato.' &#8364;</span></p>';
			} else {
				$articolo .= '<p id="price_as">Prezzo: <span class="old_price_as">'.$prezzo.'&#8364;</span> <span class="discount_as">'.$pr_scontato.' &#8364;</span></p>';
			}
		} else {
			if ($isAdmin) {
				$articolo .= '<p id="price_as" class="price_as_margin_top"> Prezzo: '.$prezzo.' &#8364;</p>';
			} else {
				$articolo .= '<p id="price_as"> Prezzo: '.$prezzo.' &#8364;</p>';
			}
		}
				
		if(!$isAdmin){
			$articolo .= "<form id=\"add_cart_as\" action=\"".htmlspecialchars($_SERVER['REQUEST_URI'])."\" method=\"post\">
									<input type=\"hidden\" name=\"prev-url\" value=\"".$location."\"/>
				  					<input type=\"hidden\" name=\"id-articolo\" value=\"".$id."\"/>
						      		<button type=\"submit\" id=\"button_cart_as\" name=\"button_cart\">
						          	<img id=\"imgchart_as\" src=\"images/addTo_cart.png\" alt=\"Aggiungi al carrello\">
						      		</button>
						  			</form>";
		}

		return $articolo;
	}

	public function convertData($data){
		$data = strip_tags($data);
		$data = str_replace('\n', '</p><p>', $data);
		$data = str_replace('{', '<span lang="en">', $data);
		$data = str_replace('}', '</span>', $data);
		return $data;
	}

	public function getTitle($data){
		$data = strip_tags($data);
		$data = str_replace('\n', '', $data);
		$data = str_replace('{', '', $data);
		$data = str_replace('}', '', $data);
		return $data;
	}	
	
	public function getDescription($nome, $prezzo, $pr_scontato, $descrizione){
		$nome = $this->getTitle($nome);
		$descrizione = $this->getTitle($descrizione);
		$aux = "<meta name=\"description\" content=\"Acquista $nome a prezzo ";
		if($pr_scontato == null){
			$aux .= $prezzo;
		}
		else{
			$aux .= $pr_scontato;
		}
		$aux .= " dal sito World of games. ".substr($descrizione, 0, 30)."\"/>";
		
		return $aux;
	}

	/*ARTICOLO*/


	/*CREA-MODIFICA ARTICOLO*/

	public function getSottocategoria($tutte, $id_categoria){
		$query = "SELECT * FROM sottocategoria ";
		if($tutte == false){
			$query .= "WHERE id = ".$id_categoria;
		}
		$result = mysqli_query($this->connection, $query);
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$result->free();
			return $row;
		}
		else{
			return null;
		}
	}

	public function getIdCategoria($categoria, $sottocategoria){
		$query = "SELECT id FROM sottocategoria WHERE categoria = '".$categoria."' AND sottocategoria = '".$sottocategoria."'";
		$result = mysqli_query($this->connection, $query);
		if($result->num_rows == 1){
			$row = $result->fetch_assoc();
			$result->free();
			return $row;
		}
		else{
			return null;
		}
	}

	public function updateArticolo($nome_articolo, $id_categoria, $prezzo_articolo, $prezzo_scontato_articolo, $immagine_articolo, $descrizione_articolo, $alt_immagine_articolo, $parole_chiave_articolo, $in_offerta_articolo, $ultimi_arrivi_articolo, $visibile_articolo, $id_articolo){
		$query = "UPDATE articolo SET nome = '".$nome_articolo."', sottocategoria = ".$id_categoria.", prezzo = ".$prezzo_articolo;
                if($prezzo_scontato_articolo != ""){
                    $query .= ", pr_scontato = ".$prezzo_scontato_articolo;
                }
                $query .= ", immagine = '".$immagine_articolo."', descrizione = '".$descrizione_articolo."', alt_immagine = '".$alt_immagine_articolo."', parole_chiave = '".$parole_chiave_articolo."', offerta = ".$in_offerta_articolo.", ultimi_arrivi = ".$ultimi_arrivi_articolo.", visibile = ".$visibile_articolo." WHERE id = ".$id_articolo;
		$result = mysqli_query($this->connection, $query);

		if(mysqli_affected_rows($this->connection) > 0){ return true; }
		else {
		echo $query; return false; }	
	}

	public function insertArticolo($nome_articolo, $id_categoria, $prezzo_articolo, $prezzo_scontato_articolo, $immagine_articolo, $descrizione_articolo, $alt_immagine_articolo, $parole_chiave_articolo, $in_offerta_articolo, $ultimi_arrivi_articolo, $visibile_articolo){
		$query = "INSERT INTO articolo (nome, sottocategoria, prezzo,"; 
                if($prezzo_scontato_articolo != "")
                    $query .= " pr_scontato, ";
                $query .= "immagine, descrizione, alt_immagine, parole_chiave, offerta, ultimi_arrivi, visibile, eliminato) VALUES ('".$nome_articolo."', ".$id_categoria.", ".$prezzo_articolo;
                if($prezzo_scontato_articolo != ""){
                    $query .= ", ".$prezzo_scontato_articolo;
                }
                $query .= ", '".$immagine_articolo."', '".$descrizione_articolo."', '".$alt_immagine_articolo."', '".$parole_chiave_articolo."', ".$in_offerta_articolo.", ".$ultimi_arrivi_articolo.", ".$visibile_articolo.", 0)";
                
		$result = mysqli_query($this->connection, $query);

		if(mysqli_affected_rows($this->connection) > 0){ return true; }
		else { return false; }	
	}

	public function getArticoloByName($nome_articolo){
		$query = "SELECT * FROM articolo WHERE nome = '".$nome_articolo."' AND eliminato = 0";
		$result = mysqli_query($this->connection, $query);
		if($result->num_rows >= 1){
			$result->free();
			return true;
		}
		else{
			return false;
		}
	}

	public function getArticoloByIdAndName($id_articolo, $nome_articolo){
		$query = "SELECT * FROM articolo WHERE nome = '".$nome_articolo."' AND id <> ".$id_articolo."AND eliminato = 0";
		$result = mysqli_query($this->connection, $query);
		if($result->num_rows >= 1){
			$result->free();
			return true;
		}
		else{
			return false;
		}
	}

	public function getSottocategoriaArticolo($id_articolo){
		$query = "SELECT s.categoria, s.sottocategoria FROM sottocategoria s JOIN articolo a ON(a.sottocategoria = s.id) WHERE a.id = ".$id_articolo;
		$result = mysqli_query($this->connection, $query);
		if($result->num_rows == 1){
			$row = $result->fetch_assoc();
			$result->free();
			return $row;
		}
		else{
			return null;
		}
	}

	public function getSottocategorieResult(){
		$query = "SELECT * FROM sottocategoria";
		$queryResult = mysqli_query($this->connection, $query);
		if (mysqli_num_rows($queryResult) > 0) {
			$result = array();
			while ($row = mysqli_fetch_assoc($queryResult)) {
				array_push($result, $row);
			}
			$queryResult->free();
			return $result;
		} else {
			return null; //null=0=false=""
		}
	}

	/*CREA-MODIFICA ARTICOLO*/


	/*AMMINISTRATORE ACCOUNT*/

	public function	isDataEqualAdm($user_id, $username, $password, $email){ 
		$stringaQuery = "SELECT * FROM utente WHERE id = '$user_id'";

		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection)); 		
		$row = mysqli_fetch_assoc($queryResult);
		$queryResult->free();

		if($row['username'] != $username or $row['pw'] != $password or $row['mail'] != $email){
			return null;
		}else{
			return "<p class=\"errori-dati-uguali\" role=\"alert\">I dati inseriti corrispondono ai dati attuali</p>";
		}
	}

	public function checkInsertData($user_id, $username, $email){

		$stringaQuery = "SELECT username, mail FROM utente WHERE id <> '$user_id' AND (username = '$username' OR mail = '$email')";
		
		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));
		
		$errori = array("", "", "0");

		if (mysqli_num_rows($queryResult) == 0){
			return $errori;	
		}
		
		else{
						
			while($row = mysqli_fetch_assoc($queryResult) ){ 
				$aux1 = mb_strtolower($row['username']);
				$aux2 = mb_strtolower($username);
				if($aux1 == $aux2){						
					$errori[0] = "<span class=\"error_aa\" role=\"alert\">\"$username\" è già occupato</span>";
				}
				
				$aux1 = mb_strtolower($row['mail']);
				$aux2 = mb_strtolower($email);
				if($aux1 == $aux2){
					$errori[1] = "<span class=\"error_aa\" role=\"alert\">\"$email\" è già occupato</span>";						
				}						
			}

			if($errori[0] != ""){
				$errori[2] = $errori[2] + 1;
			}
			if($errori[1] != ""){
				$errori[2] = $errori[2] + 1;
			}
						
			$queryResult->free();
			return $errori;			
		}
	}

	public function modifyAdminData($user_id, $username, $password, $email){

		$stringaQuery = "UPDATE utente SET mail = \"$email\", username=\"$username\", pw =\"$password\" WHERE id = $user_id";

		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));
		
		if($queryResult){
			return true;	
		}
		else{
			return false;
		}
	}

	public function getAdminsData($user_id){
		$stringaQuery = "SELECT mail, username, pw FROM utente WHERE id = $user_id";
				
		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));

		if (mysqli_num_rows($queryResult) > 0){
			$result= array();
			
			$row = mysqli_fetch_assoc($queryResult);			
			array_push($result, $row);
			
			$queryResult->free();
			return $result;			
		}
		
		else{
			return null;	
		}		
	}

	/*AMMINISTRATORE ACCOUNT*/


	/*ORDINI UTENTE*/

	public function getOrdiniUtente($id_utente){
			$query="SELECT id, data FROM ordine_utente WHERE utente=$id_utente ORDER By data DESC";
			$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection) );
			if(mysqli_num_rows($queryResult) > 0)
			{
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult))
				{
					array_push($result, $row);
				}
				$queryResult->free();
				return $result;
			}
			else
			{
				return null;
			}
		}

		public function getArticoliOrdine($id_ordine){
			$query="SELECT o.quantita, a.id, a.nome, a.immagine, a.prezzo, a.pr_scontato FROM (ordine as o) INNER JOIN (articolo as a) ON (a.id=o.articolo) WHERE o.ordine=$id_ordine";
			$queryResult = mysqli_query($this->connection, $query) or die("Errore nell'esecuzione della query: ". mysqli_error($this->connection) );
			if(mysqli_num_rows($queryResult) > 0)
			{
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult))
				{
					array_push($result, $row);
				}
				$queryResult->free();
				return $result;
			}
			else
			{
				return null;
			}
		}

	/*ORDINI UTENTE*/


	/*DATI PERSONALI*/

	public function	isDataEqual($user_id, $username, $password, $name, $lastname, $email, $phone, $address){ 
		$stringaQuery = "SELECT * FROM utente WHERE id = '$user_id'";

		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection)); 		
		$row = mysqli_fetch_assoc($queryResult);
		$queryResult->free();

		if($row['username'] != $username or $row['pw'] != $password or $row['nome'] != $name or $row['cognome'] != $lastname or $row['mail'] != $email
			or $row['cellulare'] != $phone or $row['indirizzo'] != $address){
			return null;
		}
		else{
			return "<p class=\"error_aa\" role=\"alert\">I dati inseriti corrispondono ai dati attuali</p>";
		}
	}

	public function modifyUsersData($user_id, $username, $password, $name, $lastname, $email, $phone, $address){

		$stringaQuery = "UPDATE utente SET nome=\"$name\", cognome=\"$lastname\", mail = \"$email\", username=\"$username\", pw =\"$password\", cellulare =\"$phone\", 
		indirizzo =\"$address\" WHERE id = $user_id";

		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));
		
		if($queryResult){
			return true;	
		}
		else{
			return false;
		}
	}

	public function getUsersData($user_id){
		$stringaQuery = "SELECT nome, cognome, mail, username, pw, indirizzo, cellulare FROM utente WHERE id = $user_id";
				
		$queryResult = mysqli_query($this->connection, $stringaQuery) or die("Errore nell'esecuzione della query: " .mysqli_error($this->connection));

		if (mysqli_num_rows($queryResult) > 0){
			$result= array();
			
			$row = mysqli_fetch_assoc($queryResult);			
			array_push($result, $row);
			
			$queryResult->free();
			return $result;			
		}
		
		else{
			return null;	
		}		
	}
}

/*DATI PERSONALI*/
?>