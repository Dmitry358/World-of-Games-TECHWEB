<?php

	/*COMMON*/

	/*HEADER*/

	function getNumeroArticoliCarrelloPerHeader($connessione) {
		$numeroDiArticoliInCarrello = 0;
		if (isset($_SESSION["userId"])) { //user loggato non admin
			if (!$_SESSION["isAdmin"]) {
				$articoliNelCarrello = $connessione->getIdArticoliNelCarrello($_SESSION["userId"]);
				$numeroDiArticoliInCarrello = 0;
				if ($articoliNelCarrello != null) {
					$count = count($articoliNelCarrello);
					$keys = array_keys($articoliNelCarrello);
					for ($i = 0; $i < $count; $i++) {
						$wasDeleted = $connessione->esisteArticolo($keys[$i]);
						if ($wasDeleted == false) {
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
				$wasDeleted = $connessione->esisteArticolo($allKeys[$i]);
				if ($wasDeleted == false) {
					$numeroDiArticoliInCarrello += $_SESSION['carrello'][$allKeys[$i]];
				} else {
					unset($_SESSION['carrello'][$allKeys[$i]]);
					$_SESSION['status-message'] = "Alcuni articoli che erano nel carrello sono stati rimossi perchè non esistono più";
					$_SESSION['status-message-role'] = "alert";
				}
			}
		}
		return $numeroDiArticoliInCarrello;
	}

	function setStatusBar() {
		$statusBar = '';
		if (isset($_SESSION['status-message']) && $_SESSION['status-message'] != '' && isset($_SESSION['status-message-role']) && $_SESSION['status-message-role'] != '') {
			$statusBar = '<div><p id="p-status-bar" class="'.$_SESSION['status-message-role'].'" role="'.$_SESSION['status-message-role'].'">'.$_SESSION['status-message'].'</p></div>';
			$_SESSION['status-message'] = '';
			$_SESSION['status-message-role'] = '';
		}
		return $statusBar;
	}

	function setUserHeaderForHomeAndElencoArticoli($numeroDiArticoliInCarrello) {
		$toReturn = '';
		if (isset($_SESSION["userId"])) { //user loggato
			$toReturn = '<a href="ordini_utente.php" id="login" class="after-login-user">Area personale</a>
			<a id="carrello-off" href="carrello.php">
			<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
			</a>';
		} else { //user non loggato
			$toReturn = '<a href="login.php" id="login" lang="en" class="before-login">Login</a>
			<a id="carrello-off" href="carrello.php">
			<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
			</a>';
		}
		return $toReturn;
	}

	function setUserHeaderForArticolo($numeroDiArticoliInCarrello) {
		$toReturn = '';
		if (isset($_SESSION["userId"])) { //user loggato
			$toReturn = '<a href="ordini_utente.php" id="login" class="after-login-user">Area personale</a>
			<a id="carrello-off" href="carrello.php">
			<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
			</a>';
		} else { //user non loggato
			$toReturn = '<a href="login.php" id="login" lang="en" class="before-login">Login</a>
			<a id="carrello-off" href="carrello.php">
			<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
			</a>';
		}
		return $toReturn;
	}

	function setUserHeaderForCarrello($numeroDiArticoliInCarrello) {
		$toReturn = '';
		if (isset($_SESSION["userId"])) { //user loggato
			$toReturn = '<a href="ordini_utente.php" id="login" class="after-login-user">Area personale</a>
			<p id="carrello">
			<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
			</p>';
		} else { //user non loggato
			$toReturn = '<a href="login.php" id="login" lang="en" class="before-login">Login</a>
			<p id="carrello">
			<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
			</p>';
		}
		return $toReturn;
	}

	function setUserHeaderForAreaPersonale($numeroDiArticoliInCarrello) {
		return '<p id="login" class="after-login-user">Area personale</p>
				<a id="carrello-off" href="carrello.php">
				<span id="cart-title-for-printing">Carrello</span>'.($numeroDiArticoliInCarrello ?? '').'
				</a>';
	}

	function setUserHeaderFor404Page($numeroDiArticoliInCarrello) {
		return setUserHeaderForHomeAndElencoArticoli($numeroDiArticoliInCarrello);
	}

	function setAdminHeaderForHomeAndElencoArticoli() {
		return '<a href="amministratore_account.php" id="login" class="after-login-admin">Area personale</a>
		<a href="crea-modifica-articolo.php" id="insert-new">Nuovo articolo</a>';
	}

	function setAdminHeaderForAreaPersonale() {
		return '<p id="login" class="after-login-admin">Area personale</p>
		<a href="crea-modifica-articolo.php" id="insert-new">Nuovo articolo</a>';
	}

	function setAdminHeaderForCreaModificaArticolo() {
		return '<a href="amministratore_account.php" id="login" class="after-login-admin">Area personale</a>
		<p id="insert-new">Nuovo articolo</p>';
	}

	function setAdminHeaderFor404Page() {
		return setAdminHeaderForHomeAndElencoArticoli();
	}

	/*HEADER*/

	/*COMMON*/


	/*INDEX*/

    function getFilaGiochi($prodotti, &$alreadyInserted) {
    	$da_sostituire = ['{', '}'];
		$sostituti   = ['<span lang="en">', '</span>'];
		$listaProdotti = '';
		$inserted = 0;
		if ($prodotti != null) {
			$numDiProdotti = count($prodotti);
			for ($i = 0; $i < $numDiProdotti && $inserted < 3; $i++) {
				$articolo = $prodotti[$i];
				if(in_array($articolo["id"], $alreadyInserted) == false) {
					$alreadyInserted[] = $articolo["id"];
					$inserted++;
					$listaProdotti .= "<article class='index-box'>
                                <div class='index-slide-img'>
                                    <img src='img/".$articolo["immagine"]."' alt='".$articolo["alt_immagine"]."'>
                                    <div class='index-overlay'>
                                        <h3><a href='articolo.php?id=".$articolo["id"]."' class='index-product-card-link-buy-btn'>Compra</a></h3>
                                    </div>
                                </div>
                                <div class='index-detail-box'>
                                    <div class='index-type'>
                                        <a href='articolo.php?id=".$articolo["id"]."'>".str_replace($da_sostituire, $sostituti, $articolo['nome'])."</a>
                                        <img class=\"index-reviews-stars\" src=\"".getImmagineMediaRecensioni($articolo['media'])."\" alt=\"".getAltImmagineMediaRecensioni($articolo['media'])."\">";
                   	$listaProdotti .= "<p>".str_replace($da_sostituire, $sostituti, $articolo['sottocategoria'])."</p>";
                    $listaProdotti .= "</div>
                                    <div class='index-price-box'>";
                                        if($articolo["pr_scontato"] != null){
                                            $listaProdotti .= "<span class='index-old-price'>€".$articolo["prezzo"]."</span>";
                                            $listaProdotti .= "<a href='articolo.php?id=".$articolo["id"]."' class='index-new-price'>€".$articolo["pr_scontato"]."</a>";
                                        } else {
                                        $listaProdotti .= "<a href='articolo.php?id=".$articolo["id"]."' class='index-product-card-link-price'>€".$articolo["prezzo"]."</a>";
                                    }
                                    $listaProdotti .= "</div></div></article>";
				}
			}
		} else {
			$listaProdotti = '<p>Nessun articolo disponibile.</p>';
		}
		return $listaProdotti;
	}

	/*INDEX*/


	/*ELENCO ARTICOLI*/

	function getSingleVoiceFiltraRecensioni($filtra_recensioni, $num) {
		$valToString = strval($num);
		if ($filtra_recensioni == $valToString.'-e-piu') {
        	return '<li><img class="filter-stars" src="images/'.$valToString.'-stars-medium.png" alt="la media dei voti delle recensioni dei clienti è maggiore o uguale a '.$valToString.' stelle"><span class="e-piu bold"> e più</span></li>';
		} else {
			return '<li><a href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'filtra-recensioni'), 'filtra-recensioni='.$valToString.'-e-piu').'">
                            <img class="filter-stars" src="images/'.$valToString.'-stars-medium.png" alt="la media dei voti delle recensioni dei clienti è maggiore o uguale a '.$valToString.' stelle">
                            <span class="e-piu"> e più</span>
                        </a></li>';
		}
	}

	function setFiltro($filtra_recensioni, $filtra_offerte, $filtra_ultimi_arrivi) {
		$filtro = '<ul id="filter">
            		<li id="li-recensioni-clienti">';
        if ($filtra_recensioni == "1-e-piu" || $filtra_recensioni == "2-e-piu" || $filtra_recensioni == "3-e-piu" || $filtra_recensioni == "4-e-piu") {
        	$filtro .= '&lt;<a href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'filtra-recensioni'), 'filtra-recensioni=false').'" class="cancella-filtro" aria-label="Cancella filtro recensioni">Cancella filtro</a>';
        }
        $filtro .= '<p>Recensioni dei clienti</p>
                	<ul id="filter-recensioni-clienti">';
        $filtro .= getSingleVoiceFiltraRecensioni($filtra_recensioni, 4, $_SERVER['REQUEST_URI']) . getSingleVoiceFiltraRecensioni($filtra_recensioni, 3, $_SERVER['REQUEST_URI']) . getSingleVoiceFiltraRecensioni($filtra_recensioni, 2, $_SERVER['REQUEST_URI']) . getSingleVoiceFiltraRecensioni($filtra_recensioni, 1, $_SERVER['REQUEST_URI']) . '</ul></li><li id="li-offerte">';
        if ($filtra_offerte == "true") {
            $filtro .= '&lt;<a href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'filtra-offerte'), 'filtra-offerte=false').'" class="cancella-filtro" aria-label="Cancella filtro offerte">Cancella filtro</a><div class="filtro-applicato">Mostra le offerte</div></li>';
        } else {
        	$filtro .= '- <a href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'filtra-offerte'), 'filtra-offerte=true').'">Mostra le offerte</a></li>';
        }
        $filtro .= '<li id="li-ultimi-arrivi">';
        if ($filtra_ultimi_arrivi == "true") {
            $filtro .= '&lt;<a href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'filtra-ultimi-arrivi'), 'filtra-ultimi-arrivi=false').'" class="cancella-filtro" aria-label="Cancella filtro ultimi arrivi">Cancella filtro</a><div class="filtro-applicato">Mostra gli ultimi arrivi</div></li>';
        } else {
        	$filtro .= '- <a href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'filtra-ultimi-arrivi'), 'filtra-ultimi-arrivi=true').'">Mostra gli ultimi arrivi</a></li>';
        }
        $filtro .= '</ul>';
        return $filtro;
	}

	function setRisultati($num_di_risultati, $termine_ricerca, $categoria, $sottocategoria) {
		$da_sostituire = ['{', '}'];
		$sostituti   = ['<span lang="en">', '</span>'];
		$toReturn = strval($num_di_risultati);
		if ($num_di_risultati !== 1) {
			$toReturn .= " risultati per ";
		} else {
			$toReturn .= " risultato per ";
		}
		if ($termine_ricerca) {
			$toReturn .=  "'$termine_ricerca'";
		} else if ($categoria && $sottocategoria) {
			$toReturn .= "'$categoria: ".str_replace($da_sostituire, $sostituti, $sottocategoria)."'";
		} else {
			$toReturn .= "'Catalogo'";
		}
		return $toReturn;
	}

	function setOrderBy($ordina_per) {
		$order_by = '<div id="order-by-container">
				        <form id="form-order-by" action="elenco-articoli.php" method="get" aria-label="Ordine di visualizzazione degli articoli" aria-describedby="order-by-description">';
		parse_str($_SERVER['QUERY_STRING'], $parameters);
		unset($parameters["ordina-per"]);
		$allKeys = array_keys($parameters);
		for ($i=0;$i<count($allKeys);$i++) {
			$order_by .= '<input type="hidden" name="'.$allKeys[$i].'" value="'.$parameters[$allKeys[$i]].'"/>';
		}
		$order_by .= '<label for="order-by">Ordina per:</label>
				            <select name="ordina-per" id="order-by" autocomplete="off">';
		if ($ordina_per == "prezzo-decrescente") {
			$order_by .= '<option value="prezzo-decrescente">Prezzo: decrescente</option>
							<option value="prezzo-crescente">Prezzo: crescente</option>
				            <option value="media-recensioni-clienti">Media recensioni clienti</option>';
		} else if ($ordina_per == "media-recensioni-clienti") {
			$order_by .= '<option value="media-recensioni-clienti">Media recensioni clienti</option>
							<option value="prezzo-crescente">Prezzo: crescente</option>
				            <option value="prezzo-decrescente">Prezzo: decrescente</option>';
		} else{
			$order_by .= '<option value="prezzo-crescente">Prezzo: crescente</option>
				            <option value="prezzo-decrescente">Prezzo: decrescente</option>
				            <option value="media-recensioni-clienti">Media recensioni clienti</option>';
		}
		$order_by .= '</select></form></div>';
		return $order_by;
	}

	function setProducts($prodotti, $pagine) {
		$da_sostituire = ['{', '}'];
		$sostituti   = ['<span lang="en">', '</span>'];
		$listaProdotti = '';
		if ($prodotti != null) {
			$numDiProdotti = count($prodotti);
			$listaProdotti = '<section id="product-list"><h1 id="content-heading">Articoli:</h1>';
			for ($i = 0; $i < $numDiProdotti && $i < $pagine*9; $i++) {
				$articolo = $prodotti[$i];
				$listaProdotti .= 
					'<article class="product-card">
						<a href="articolo.php?id='.$articolo['id'].'" class="product-card-link">
							<div class="img-wrapper">
		                      	<img src="img/'.$articolo['immagine'].'" class="product-card-img" alt="'.$articolo['alt_immagine'].'">
		                    </div>
		                    <h2 class="product-card-name">'.str_replace($da_sostituire, $sostituti, $articolo['nome']).'</h2>
		                    <img class="reviews-stars" src="'.getImmagineMediaRecensioni($articolo['media']).'" alt="'.getAltImmagineMediaRecensioni($articolo['media']).'">
		                    <p class="p-number-of-reviews">('.$articolo['count'].')</p>
		                    <div class="div-price-wrapper">';
		        if ($articolo['pr_scontato'] === null) {
					$listaProdotti .= '<p class="product-card-price">'.$articolo['prezzo'].'€</p>';
				} else {
					$listaProdotti .= '<p class="product-card-price discounted">'.$articolo['prezzo'].'€</p>
									   <p class="product-card-price-discounted">'.$articolo['pr_scontato'].'€</p>';
				}
				$listaProdotti .= '</div></a>';
				if ((isset($_SESSION["userId"]) && !$_SESSION["isAdmin"]) || !isset($_SESSION["userId"])) { //user loggato o non loggato
					$listaProdotti .= '<form action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" method="post">
			                       		<input type="hidden" name="id-articolo" value="'.$articolo['id'].'"/>
	                       				<button type="submit" class="button-add-to-cart" name="button-add-to-cart" value="aggiungi">Aggiungi al carrello</button>
					                   </form>';
				} else { //admin
					$listaProdotti .= '<div class="div-admin-buttons">
				                    		<form action="crea-modifica-articolo.php" method="post">
					                            <input type="hidden" name="url" value="'.htmlspecialchars($_SERVER['REQUEST_URI']).'"/>
						                        <input type="hidden" name="id-articolo" value="'.$articolo['id'].'"/>
						                        <button type="submit" class="button-modifica" name="button-modifica" value="modifica">Modifica</button>
		                        			</form>
						                    <form action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" method="post">
						                        <input type="hidden" name="id-articolo" value="'.$articolo['id'].'"/>
						                        <button type="submit" class="button-elimina" name="button-elimina" value="elimina">Elimina</button>
						                    </form>
						                </div>';
				}
				$listaProdotti .= '</article>';
			}
			$listaProdotti .= '</section>';
			if ($numDiProdotti > $pagine*9) {
				$listaProdotti .= '<a id="carica-altri" href="'.pushParamToUrl(stripParamFromUrl($_SERVER['REQUEST_URI'], 'page'), 'page='.strval($pagine+1)).'">Carica altri</a>';
			}
		} else {
			$listaProdotti = '<p id="empty-content">Nessun articolo disponibile.</p>';
		}
		return $listaProdotti;
	}

	function getImmagineMediaRecensioni($media) {
		$img_url = "images/".strval(floor($media))."-stars";
		if ($media - floor($media) != 0) {
			$img_url .= "-half";
		}
		return $img_url . "-medium.png";
	}

	function getAltImmagineMediaRecensioni($media) {
		$img_alt = "la media dei voti delle recensioni dei clienti è ".strval(floor($media))." stelle";
		if ($media - floor($media) != 0) {
			$img_alt .= " e mezzo";
		}
		return $img_alt;
	}

	function stripParamFromUrl($url, $param) {
	    $base_url = strtok($url, '?');
	    $parsed_url = parse_url($url);
	    if (isset($parsed_url['query'])) {
		    $query = $parsed_url['query'];
		    parse_str($query, $parameters);
		    unset($parameters[$param]);
		    $new_query = http_build_query($parameters);
		    return $base_url.'?'.$new_query;
		} else {
			return $url;
		}
	}

	function pushParamToUrl($url, $param) {
	    $base_url = strtok($url, '?');
	    $parsed_url = parse_url($url);
	    if (isset($parsed_url['query'])) {
	    	parse_str($parsed_url['query'], $parameters);
		    return $base_url.'?'.http_build_query($parameters).'&amp;'.$param;
		} else {
			return $base_url.'?'.$param;
		}
	}

    function setMenu($termine_ricerca, $categoria, $sottocategoria, $paginaHTML) {
    	$menuCat = array(
    		'console' => 'tabindex="5" lang="en"',
    		'giochi' => 'tabindex="10"',
    		'controller' => 'lang="en" tabindex="20"',
    		'accessori' => 'tabindex="26"'
    	);
    	$menuSottoCat = array(
    		'console-playstation' => 'lang="en" tabindex="6"',
    		'console-nintendo' => 'tabindex="7"',
    		'console-xbox' => 'tabindex="8" lang="en"',
    		'console-tutti' => 'tabindex="9"',
    		'giochi-sparatutto' => 'lang="en" tabindex="11"',
    		'giochi-avventura' => 'tabindex="12"',
    		'giochi-platform' => 'tabindex="13" lang="en"',
    		'giochi-azione' => 'tabindex="14"',
    		'giochi-sportivi' => 'tabindex="15"',
    		'giochi-corse' => 'tabindex="16"',
    		'giochi-horror' => 'tabindex="17"',
    		'giochi-openworld' => 'tabindex="18" lang="en"',
    		'giochi-tutti' => 'tabindex="19"',
    		'controller-playstation' => 'lang="en" tabindex="21"',
    		'controller-nintendo' => 'tabindex="22"',
    		'controller-xbox' => 'lang="en" tabindex="23"',
    		'controller-pc' => 'tabindex="24"',
    		'controller-tutti' => 'tabindex="25"',
    		'accessori-cuffie' => 'tabindex="27"',
    		'accessori-microfono' => 'lang="en" tabindex="28"',
    		'accessori-tastiera' => 'lang="en" tabindex="29"',
    		'accessori-mouse' => 'lang="en" tabindex="30"',
    		'accessori-tutti' => 'tabindex="31"'
    	);
    	if (!$termine_ricerca && !$categoria && !$sottocategoria) {
    		$paginaHTML = str_replace('<a id="a-vedi-tutti" class="first-level-menu-item" href="elenco-articoli.php" tabindex="4">Catalogo</a>', '<p id="catalogo-selezionato">Catalogo</p>', $paginaHTML);
    	} else if (!$termine_ricerca) {
    		$paginaHTML = str_replace('<li id="li-'.strtolower($categoria).'" class="first-level-menu-item has-submenu" '.$menuCat[strtolower($categoria)].' role="menuitem">', '<li id="li-'.strtolower($categoria).'" class="first-level-menu-item has-submenu selectedMenuItem" '.$menuCat[strtolower($categoria)].' role="menuitem">', $paginaHTML);
    		if ($categoria == "Console" && $sottocategoria == "Tutti") {
    			$paginaHTML = str_replace('<li role="menuitem"><a id="a-console-tutti" href="elenco-articoli.php?categoria=Console&amp;sottocategoria=Tutti" tabindex="9">Tutte le console</a></li>', '<li role="menuitem" class="selectedSubMenuItem" tabindex="9">Tutte le console</li>', $paginaHTML);
    		} else if ($categoria == "Giochi" && $sottocategoria == "Tutti") {
    			$paginaHTML = str_replace('<li role="menuitem"><a id="a-giochi-tutti" href="elenco-articoli.php?categoria=Giochi&amp;sottocategoria=Tutti" tabindex="19">Tutti i giochi</a></li>', '<li role="menuitem" class="selectedSubMenuItem" tabindex="19">Tutti i giochi</li>', $paginaHTML);
    		} else if ($categoria == "Controller" && $sottocategoria == "Tutti") {
    			$paginaHTML = str_replace('<li role="menuitem"><a id="a-controller-tutti" href="elenco-articoli.php?categoria=Controller&amp;sottocategoria=Tutti" tabindex="25">Tutti i controller</a></li>', '<li role="menuitem" class="selectedSubMenuItem" tabindex="25">Tutti i controller</li>', $paginaHTML);
    		} else if ($categoria == "Accessori" && $sottocategoria == "Tutti") {
    			$paginaHTML = str_replace('<li role="menuitem"><a id="a-accessori-tutti" href="elenco-articoli.php?categoria=Accessori&amp;sottocategoria=Tutti" tabindex="31">Tutti gli accessori</a></li>', '<li role="menuitem" class="selectedSubMenuItem" tabindex="31">Tutti gli accessori</li>', $paginaHTML);
    		} else if ($categoria == "Giochi" && $sottocategoria == "Open World") {
    			$paginaHTML = str_replace('<li role="menuitem"><a id="a-giochi-openworld" href="elenco-articoli.php?categoria=Giochi&amp;sottocategoria=Open+World" tabindex="18" lang="en">Open World</a></li>', '<li role="menuitem" class="selectedSubMenuItem" tabindex="18" lang="en">Open World</li>', $paginaHTML);
    		} else {
    			$paginaHTML = str_replace('<li role="menuitem"><a id="a-'.strtolower($categoria).'-'.strtolower($sottocategoria).'" href="elenco-articoli.php?categoria='.$categoria.'&amp;sottocategoria='.$sottocategoria.'" '.$menuSottoCat[strtolower($categoria).'-'.strtolower($sottocategoria)].'>'.$sottocategoria.'</a></li>', '<li role="menuitem" '.$menuSottoCat[strtolower($categoria).'-'.strtolower($sottocategoria)].' class="selectedSubMenuItem">'.$sottocategoria.'</li>', $paginaHTML);
    		}
    	}
    	return $paginaHTML;
    }

    function getEATitle($termine_ricerca, $categoria, $sottocategoria) {
    	if (!$termine_ricerca && !$categoria && !$sottocategoria) {
    		return 'Catalogo';
    	} else if (!$termine_ricerca) {
    		if ($sottocategoria == "Tutti") {
    			return $categoria;
    		}
    		return $sottocategoria.' - '.$categoria;
    	} else {
    		return $termine_ricerca;
    	}
    }

    function getKeywordElencoArticoli($termine_ricerca, $categoria, $sottocategoria) {
    	if ($termine_ricerca) {
    		return ', '.strtolower($termine_ricerca);
    	} else if ($categoria && $sottocategoria) {
    		if ($sottocategoria == "Tutti") {
    			return ', '.$categoria;
    		}
    		return ', '.strtolower($categoria).', '.strtolower($sottocategoria);
    	} else {
    		return ", catalogo completo";
    	}
    }

	/*ELENCO ARTICOLI*/


	/*CARRELLO*/

	function getCarrello($data) {
		$carrello = '';
		$totale = 0;
		$da_sostituire = ['{', '}'];
		$sostituti = ['<span lang="en">', '</span>'];
		if ($data != null) {
			$carrello.='<ul id="carrello_list">';
			foreach ($data as $articolo) {
				$carrello.='<li><h2><a href="articolo.php?id='.$articolo['id'].'">'.str_replace($da_sostituire, $sostituti, $articolo['nome']). '</a></h2>';
				$carrello .= '<div class="img_wrapper_c"><img class = "carrello_article" src = "img/' . $articolo['immagine'] . '"' . ' alt = ""/></div>';
				if($articolo['pr_scontato'] != null){
					$carrello .= '<p class="carrello_price">Prezzo: <span class="carrello_old_price">' . $articolo['prezzo'].' &#8364;</span> <span class="carrello_new_price">'.$articolo['pr_scontato'].' &#8364;</span></p>';
					$totale= $totale + $articolo['pr_scontato']*$articolo['quantita'];
				} else {
					$carrello .= '<p class="carrello_price">Prezzo: ' . $articolo['prezzo'] . ' &#8364;</p>';
					$totale= $totale + $articolo['prezzo']*$articolo['quantita'];
				}
				$carrello.='<form  method="post" id="carrello_form_' . $articolo['id'] . '">';
				$carrello.='<label for="carrello_amount_'.$articolo['id'].'">Seleziona quantit&agrave;:</label>';
				$carrello.='<select id="carrello_amount_'.$articolo['id'].'" class="selectQuantita" name="select_quantita">
	                    <option value="1" %selected1%>1</option>
	                    <option value="2" %selected2%>2</option>
	                    <option value="3" %selected3%>3</option>
	                    <option value="4" %selected4%>4</option>
	                    <option value="5" %selected5%>5</option>
	                    <option value="6" %selected6%>6</option>
	                    <option value="7" %selected7%>7</option>
	                    <option value="8" %selected8%>8</option>
	                    <option value="9" %selected9%>9</option>
	                    </select><input type="hidden" name="select-id" value = "'.$articolo['id'].'">';
			    $carrello .= '</form>';
			    $a = "%selected".$articolo['quantita']."%";
			    $carrello = str_replace($a, "selected", $carrello );
			    for($i = 1; $i < 10; $i++){
			    	$carrello = str_replace("%selected".$i."%", "", $carrello );
			    }
			    $carrello .= '<form  method="post" >';
			    $carrello .= '<input type="hidden" name = "id-bottone" value ='.$articolo['id'].'>';
			    $carrello .= '<button class="carrello_cestino" type="submit" id="carrello_trash_'.$articolo['id']. '" name="button-elimina" value="Rimuovi">Rimuovi</button>' ;
			    $carrello .= '</form>';
			    $carrello .= '</li>';
	    	}
	  		$carrello .= '</ul>';
	    	$carrello .= '<p id="carrello_totale">Totale: '.$totale.' &#8364;</p>';
	    	$carrello .= '<form method="post">';
	    	$carrello .= '<button id="carrello_btn_acquista" type="submit" name="acquista">Acquista</button>';
	    	$carrello .= '</form>';
		} else {
			$carrello .= '<p id="carrello_no_element">Nessun elemento nel carrello</p>';
		}
		return $carrello;
	}

	/*CARRELLO*/


	/*ARTICOLO*/

	function getArticoloBreadcrumb($url) {
    	$pageName = '';
    	$breadcrumb = '<a href="index.php" lang="en">Home</a>';
		if ($url == 'index.php') {
			$pageName = 'Home';
		} else if (strpos($url, 'elenco-articoli.php') !== false) {
			$pageName = 'Elenco articoli';
		} else if ($url == 'carrello.php') {
			$pageName = 'Carrello';
		} else if ($url == 'ordini_utente.php') {
			$pageName = 'Ordini utente';
		}
		if ($pageName != '') {
			if ($pageName == 'Home') {
				return $breadcrumb;
			} else{
				$breadcrumb .= ' &gt;&gt; <a href="'.$url.'">'.$pageName.'</a>';
			}
		} else {
			return '';
		}
		return $breadcrumb;
	}

	/*ARTICOLO*/


	/*CREA-MODIFICA ARTICOLO*/

    function setTitolo($nuovo_modifica){
        if($nuovo_modifica == "Modifica"){
            return "Modifica articolo";
        }
        return "Nuovo articolo";
    }

	/*CREA-MODIFICA ARTICOLO*/
?>