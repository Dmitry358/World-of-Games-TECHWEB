/*COMMON*/

function setUpMenu() {
    document.getElementById('li-console').addEventListener("mouseenter", e => {
        document.getElementById('ul-console').className = 'show-ul-left';
    });

    document.getElementById('li-console').addEventListener('mouseleave', e => {
        document.getElementById('ul-console').className = 'sub-menu';
    });

    document.getElementById('li-giochi').addEventListener("mouseenter", e => {
        document.getElementById('ul-giochi').className = 'show-ul-left';
    });

    document.getElementById('li-giochi').addEventListener('mouseleave', e => {
        document.getElementById('ul-giochi').className = 'sub-menu';
    });

    document.getElementById('li-controller').addEventListener("mouseenter", e => {
        document.getElementById('ul-controller').className = 'show-ul-right';
    });

    document.getElementById('li-controller').addEventListener('mouseleave', e => {
        document.getElementById('ul-controller').className = 'sub-menu';
    });

    document.getElementById('li-accessori').addEventListener("mouseenter", e => {
        document.getElementById('ul-accessori').className = 'show-ul-right';
    });

    document.getElementById('li-accessori').addEventListener('mouseleave', e => {
        document.getElementById('ul-accessori').className = 'sub-menu';
    });

    document.addEventListener("keydown", ({key}) => {
        if (key === "Escape") {
            document.getElementById('ul-console').className = 'sub-menu';
            document.getElementById('ul-giochi').className = 'sub-menu';
            document.getElementById('ul-controller').className = 'sub-menu';
            document.getElementById('ul-accessori').className = 'sub-menu';
        }
    });
}

/*COMMON*/


/*INDEX*/

function onLoadHome() {
    setUpMenu();
}

/*INDEX*/


/*ELENCO ARTICOLI*/

var isPrinting = false;

var wasFilterOpenedBeforePrinting = false;

function onLoadElencoArticoliScript() {
    
    setUpMenu();

    listenForWindowWidthChanges();

    handleButtonFilterOnClick();

    document.getElementById("order-by").onchange = function() {
        document.getElementById("form-order-by").submit();
        document.getElementById("order-by").selectedIndex = 0;
    };

    window.addEventListener("beforeprint", function(event) {
        isPrinting = true;
        var ul_filter = document.getElementById('filter');
        var h3_filter = document.getElementById('h2-filter');
        if (ul_filter.style.display == 'block') {
            wasFilterOpenedBeforePrinting = true;
        } else {
            wasFilterOpenedBeforePrinting = false;
            ul_filter.style.display = 'block';
            h3_filter.style.display = 'inline-block';
            // in questo modo anche nascondendo il filtro, quando stampo lo vedo lo stesso. e meglio cosi o non vedendolo?
        }
    });

    window.addEventListener("afterprint", function(event) {
        isPrinting = false;
        if (!wasFilterOpenedBeforePrinting) {
            document.getElementById('filter').style.display = 'none';
            document.getElementById('h2-filter').style.display = 'none';
        }
    });

    setupOnClickForDeleteButtons();

}

function setupOnClickForDeleteButtons() {
    var bottoniElimina = document.getElementsByClassName("button-elimina");
    for (let i = 0; i < bottoniElimina.length; i++) {
        bottoniElimina[i].onclick = function(){
            if (confirm('Sei sicuro di voler eliminare questo articolo?')) {
                return true;
            } else {
                return false;
            }
        };
    }
}

function listenForWindowWidthChanges() {
    if (matchMedia) {
        const mq = window.matchMedia("(min-width: 741px)");
        mq.addListener(changeFilter);
        changeFilter(mq);
    }
}

function handleButtonFilterOnClick() {
    document.getElementById('button-filter').onclick = function changeContent() {
        var ul_filter = document.getElementById('filter');
        var h3_filter = document.getElementById('h2-filter');
        if (ul_filter.style.display !== 'none') {
            ul_filter.style.display = 'none';
            h3_filter.style.display = 'none';
        } else {
            ul_filter.style.display = 'block';
            h3_filter.style.display = 'inline-block';
        }
    };
}

function changeFilter(mq) {
    var ul_filter = document.getElementById('filter');
    var h3_filter = document.getElementById('h2-filter');
    var results_get_query = document.getElementById('results-get-query');
    if (mq.matches) {
        ul_filter.style.display = 'block';
        h3_filter.style.display = 'inline-block';
    } else {
        if (isPrinting === false) {
            ul_filter.style.display = 'none';
            h3_filter.style.display = 'none';
        }
    }
}

/*ELENCO ARTICOLI*/


/*CARRELLO*/

function onLoadCarrello() {
    setUpMenu();

    document.getElementById("carrello_btn_acquista").onclick = function(){
        if (confirm('Sei sicuro di voler procedere con l\'acquisto degli articoli nel carrello?')) {
            return true;
        } else {
            return false;
        }
    };

    var bottoniElimina = document.getElementsByClassName("carrello_cestino");
    for (let i = 0; i < bottoniElimina.length; i++) {
        bottoniElimina[i].onclick = function(){
            if (confirm('Sei sicuro di voler rimuovere questo articolo dal carrello?')) {
                return true;
            } else {
                return false;
            }
        };
    }

    var selectQuantita = document.getElementsByClassName("selectQuantita");
    for (let i = 0; i < bottoniElimina.length; i++) {
        selectQuantita[i].onchange = function(){
            this.closest('form').submit();
        };
    }
}

/*CARRELLO*/


/*LOGIN & SIGN IN*/

function initLogin() {
  mostraPassword();

  document.getElementById('username-or-email').addEventListener('change', function(event) {
    if (document.getElementById("credenziali-non-corrette")) {
        document.getElementById("credenziali-non-corrette").style.display = "none";
    }
  }, {once: true});

  document.getElementById('password').addEventListener('change', function(event) {
    if (document.getElementById("credenziali-non-corrette")) {
        document.getElementById("credenziali-non-corrette").style.display = "none";
    }
  }, {once: true});

}

function initSignIn() {
  document.getElementById('password').onchange = function(){onChange()};
  document.getElementById('confirm-password').onchange = function(){onChange()};
  mostraPassword();
  mostraConfermaPassword();
  document.getElementById('username').addEventListener('change', function(event) {
    if (document.getElementById("username-occupato")) {
        document.getElementById("username-occupato").style.display = "none";
    }
  }, {once: true});
    document.getElementById('email').addEventListener('change', function(event) {
    if (document.getElementById("email-occupata")) {
      document.getElementById("email-occupata").style.display = "none";
    }
  }, {once: true});
}

function onChange() {
  const password = document.querySelector('input[name=password]');
  const confirm = document.querySelector('input[name=confirm-password]');
  if (confirm.value === password.value) {
    confirm.setCustomValidity('');
  } else {
    confirm.setCustomValidity('Le password non coincidono');
  }
}

function mostraPassword() {
  var checkbox = document.getElementById('mostra-password');
  var label_checkbox = document.getElementById('label-mostra-password');

  checkbox.addEventListener("click", handlePasswordCheckboxEvent, true);
  checkbox.addEventListener("keyup", handlePasswordCheckboxEvent, true);
  label_checkbox.onclick = function(){mostra("password", checkbox);};
}

function mostraConfermaPassword() {
  var checkbox = document.getElementById('mostra-confirm-password');
  var label_checkbox = document.getElementById('label-mostra-confirm-password');

  checkbox.addEventListener("click", handleConfPasswordCheckboxEvent, true);
  checkbox.addEventListener("keyup", handleConfPasswordCheckboxEvent, true);
  label_checkbox.onclick = function(){mostra("confirm-password", checkbox);};
}

function mostra(what, checkbox) {
var x = document.getElementById(what);
    if (x.type === "password") {
      x.type = "text";
      checkbox.checked = true;
    } else {
      x.type = "password";
      checkbox.checked = false;
    }
}

 function handlePasswordCheckboxEvent(e) {
    e.preventDefault();
    if (e.keyCode === 32) {
      this.checked = !this.checked;
      mostra("password", this);
    }
  }

  function handleConfPasswordCheckboxEvent(e) {
    e.preventDefault();
    if (e.keyCode === 32) {
      this.checked = !this.checked;
      mostra("confirm-password", this);
    }
  }

/*LOGIN & SIGN IN*/


/*ARTICOLO*/

function hideComment(){
    setUpMenu();
    var form = document.getElementById("comment_form_as");
    var btnComment = document.getElementById("btnComment_as");
    if (form) {
        form.style.display = "none";
    }
    btnComment.setAttribute("onclick", "showComment()");
    btnComment.innerHTML = "Aggiungi commento";
}

function showComment(){
    setUpMenu();
    var form = document.getElementById("comment_form_as");
    var btnComment = document.getElementById("btnComment_as");
    if (form) {
        form.style.display = "";
    }
    btnComment.setAttribute("onclick", "hideComment()");
    btnComment.innerHTML = "Nascondi aggiungi commento";
}

/*ARTICOLO*/


/*CREA-MODIFICA ARTICOLO*/

function onLoadCreaModificaArticolo() {
    setUpMenu();
}

function mostraIstruzioni() {
    var moreText = document.getElementById("cma-more");
    var btnText = document.getElementById("cma-read-more-btn");
    if (moreText.style.display === "inline") {
      btnText.innerHTML = "Leggi";
      moreText.style.display = "none";
    } else {
      btnText.innerHTML = "Nascondi";
      moreText.style.display = "inline";
    }
}

function confermaLogout()
{
  var agree=confirm("Sei sicuro di voler effettuare il logout?");

  if (agree)
    return true ;
  else
    return false ;
}

function confermaEliminaAccount()
{
  var agree=confirm("Sei sicuro di voler eliminare il tuo account?");

  if (agree)
    return true ;
  else
    return false ;
}

/*CREA-MODIFICA ARTICOLO*/


/*AREA AMMINISTRATORE*/

function onLoadAmministratoreAccount() {
    setUpMenu();
}

/*AREA AMMINISTRATORE*/


/*ORDINI UTENTE*/

function onLoadoOrdiniUtente() {
    setUpMenu();
}

/*ORDINI UTENTE*/


/*DATI PERSONALI*/

function onLoadDatiPersonali() {
    setUpMenu();
}

/*DATI PERSONALI*/