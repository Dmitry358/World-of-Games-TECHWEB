drop table if exists commento;
drop table if exists ordine;
drop table if exists carrello;
drop table if exists articolo;
drop table if exists ordine_utente;
drop table if exists utente;
drop table if exists sottocategoria;

CREATE TABLE IF NOT EXISTS utente (
  id        int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome      varchar(40),
  cognome   varchar(40),
  mail      varchar(50) UNIQUE  NOT NULL,
  username  varchar(20) UNIQUE  NOT NULL,
  pw        varchar(30) NOT NULL,
  indirizzo varchar(60),
  cellulare varchar(10),
  admin     boolean NOT NULL
);

CREATE TABLE IF NOT EXISTS sottocategoria(
  id              int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria       varchar(100) NOT NULL,
  sottocategoria  varchar(100) NOT NULL,
  UNIQUE (categoria, sottocategoria)
);

CREATE TABLE IF NOT EXISTS articolo (
  id             int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome           varchar(100) NOT NULL,
  sottocategoria int(11) UNSIGNED NOT NULL,
  prezzo         float(16,2) NOT NULL,
  pr_scontato    float(16,2),
  immagine       varchar(100)  NOT NULL,
  descrizione    varchar(1000) NOT NULL,
  alt_immagine   varchar(100) NOT NULL,
  parole_chiave  varchar(200) NOT NULL,
  offerta        boolean NOT NULL,
  ultimi_arrivi  boolean NOT NULL,
  visibile       boolean NOT NULL,
  eliminato      boolean NOT NULL,
  FOREIGN KEY(sottocategoria) REFERENCES sottocategoria(id)
);

CREATE TABLE IF NOT EXISTS commento (
  id       int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  utente   int(11) UNSIGNED NOT NULL,
  articolo int(11) UNSIGNED NOT NULL,    
  commento varchar(500)  NOT NULL,
  voto     tinyint unsigned NOT NULL,
  data     date NOT NULL,
  FOREIGN KEY (utente) REFERENCES utente(id),
  FOREIGN KEY (articolo) REFERENCES articolo(id)
);

CREATE TABLE IF NOT EXISTS ordine_utente (
  id      int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  data    date NOT NULL,
  utente  int(11) UNSIGNED NOT NULL,
  FOREIGN KEY (utente) REFERENCES utente(id)
);

CREATE TABLE IF NOT EXISTS ordine (
  ordine    int(11) UNSIGNED NOT NULL,
  articolo  int(11) UNSIGNED NOT NULL,
  quantita  tinyint unsigned NOT NULL,
  PRIMARY KEY (ordine, articolo),
  FOREIGN KEY(ordine) REFERENCES ordine_utente(id),
  FOREIGN KEY(articolo) REFERENCES articolo(id)
);

CREATE TABLE IF NOT EXISTS carrello(
  utente      int(11) UNSIGNED NOT NULL,
  articolo    int(11) UNSIGNED NOT NULL,
  quantita    tinyint unsigned NOT NULL,
  PRIMARY KEY (utente, articolo),
  FOREIGN KEY(articolo) REFERENCES articolo(id),
  FOREIGN KEY(utente) REFERENCES utente(id)
);


/*----------------------------------------------------------------------------------------------------------------------------------*/


INSERT INTO sottocategoria(categoria, sottocategoria)
VALUES
 /*1*/('Giochi',     'Sparatutto'),
 /*2*/('Giochi',     'Avventura'),
 /*3*/('Giochi',     '{Platform}'),
 /*4*/('Giochi',     'Sportivi'),
 /*5*/('Giochi',     'Azione'),
 /*6*/('Giochi',     'Corse'),
 /*7*/('Giochi',     'Horror'),
 /*8*/('Giochi',     '{Open World}'),
 /*9*/('Console',    '{Playstation}'),
/*10*/('Console',    'Nintendo'),
/*11*/('Console',    'Xbox'),
/*12*/('Controller', '{Playstation}'),
/*13*/('Controller', 'Nintendo'),
/*14*/('Controller', 'Xbox'),
/*15*/('Controller', 'Pc'),
/*16*/('Accessori',  'Cuffie'),
/*17*/('Accessori',  'Microfono'),
/*18*/('Accessori',  'Tastiera'),
/*19*/('Accessori',  '{Mouse}');

  
INSERT INTO articolo(nome, sottocategoria, prezzo, pr_scontato, immagine, descrizione, alt_immagine, parole_chiave, offerta, ultimi_arrivi, visibile, eliminato)
VALUES 

/*1*/('{PlayStation} 5 {Standard edition}', 9, 499.00, null, 'ps5_standard_edition.jpg', 'La console {PlayStation} 5 {Standard edition} rende possibili forme di gioco che non avresti mai immaginato. Sperimenta tempi di caricamento rapidi grazie a un {Solid State Drive} ad alta velocità, un''immersione più profonda con {feedback} aptico, {trigger} adattivi e audio 3D, oltre a una nuova generazione di giochi {PlayStation}. Scopri un''esperienza di gioco più intensa attraverso la tecnologia {haptic}, i {trigger} adattivi e l''audio 3D. Lasciati sorprendere dalla grafica straordinaria e sperimenta le nuove funzionalità di {PlayStation} 5.', 'Versione della console del {day one}, scocca bianca, è presente il lettore dischi', 'PlayStation 5 Standard edition, console, Sony, World of games', false, true, true, false), 

/*2*/('{PlayStation} 5 {Digital edition}', 9, 399.00, null, 'ps5_digital_edition.jpg', 'La console {PlayStation} 5 {Digital edition} rende possibili forme di gioco che non avresti mai immaginato. Sperimenta tempi di caricamento rapidi grazie a un {Solid State Drive} ad alta velocità, un''immersione più profonda con {feedback} aptico, {trigger} adattivi e audio 3D, oltre a una nuova generazione di giochi {PlayStation}. Scopri un''esperienza di gioco più intensa attraverso la tecnologia {haptic}, i {trigger} adattivi e l''audio 3D. Lasciati sorprendere dalla grafica straordinaria e sperimenta le nuove funzionalità di PS5.', 'Versione della console del {day one}, scocca bianca, non è presente il lettore dischi', 'PlayStation 5 Digital edition, console, Sony, digital, World of games', false, true, true, false), 

/*3*/('Xbox {series} X', 11, 499.00, 349.00, 'xbox_serie_x.jpg', 'La nuova Xbox {Series} X, la console Xbox più veloce e potente di sempre. Gioca a migliaia di titoli di quattro generazioni di console: tutti i giochi hanno un aspetto e una riproduzione ottimali. Sperimenta velocità e prestazioni di nuova generazione grazie a Xbox {Velocity Architecture}, con memoria personalizzata e {software} integrato. Gioca a migliaia di giochi di quattro generazioni di Xbox con retrocompatibilità, inclusi titoli ottimizzati al momento del lancio', 'Scocca nera, è presente il lettore dischi', 'Xbox series X, console, Microsoft, World of games', true, false, true, false),

/*4*/('Xbox {series} S', 11, 299.00, null, 'xbox_serie_s.jpg', 'Arriva la nuova Xbox {Series} S, la console Xbox più piccola ed elegante di sempre. Prova la velocità e le prestazioni di ultima generazione di una console {all-digital} ad un prezzo straordinario. Inizia con una libreria istantanea di più di 100 giochi di alta qualità, inclusi tutti i nuovi titoli Xbox {Game Studios} nel giorno del lancio, con Xbox {Game Pass Ultimate} (abbonamento venduto separatamente). Con {Quick Resume} passi senza problemi e in un lampo da un gioco all’altro. Il cuore di {Series} X è Xbox {Velocity Architecture}, che abbina una memoria personalizzata con {software} integrato per un {gameplay} più veloce e semplificato, con tempi di caricamento notevolmente ridotti.', 'Scocca bianca, non è presente il lettore dischi', 'Xbox series S, console, Microsoft, digital, World of games', false, false, true, false),

/*5*/('{Nintendo Switch}', 10, 299.00, null, 'nintendo_switch.jpg', 'Nintendo {Switch} è una console casalinga rivoluzionaria che non solo si connette al televisore di casa, ma si trasforma anche in un sistema da gioco portatile grazie al suo schermo da 6 pollici con {touch screen multi-touch} capacitivo. Fino a otto console possono essere collegate tramite connessione locale, per giocare in {multiplayer} dove, quando e con chi si vuole. La console permette inoltre di giocare in {multiplayer online} e anche in modalità da tavolo.', 'Versione della console del 2019, versione con i joy-con rossi e blu', 'Nintendo Switch, console, multiplayer, World of games', false, false, true, false),

/*6*/('{Nintendo Switch OLED}', 10, 349.00, null, 'nintendo-switch-oled.jpg', 'Divertiti con i tuoi giochi preferiti dove e quando vuoi con Nintendo {Switch OLED}, dotato di uno schermo da 7 pollici con colori intensi e un contrasto elevato. Nintendo {Switch OLED} dispone di uno schermo da 7 pollici con una cornice più sottile. I colori intensi e l''elevato contrasto dello schermo garantiscono un''esperienza di gioco appagante in modalità portatile e da tavolo. Osserva i tuoi giochi prendere vita, che tu stia sfrecciando a tutta velocità su un circuito o affrontando i nemici in furiosi combattimenti.', 'Versione con i joy-con bianchi', 'Nintendo Switch OLED, console, World of games', false, true, true, false),

/*7*/('{Nintendo Switch Lite}', 10, 219.00, 179.00, 'nintendo_switch_lite.jpg', 'Vivi le tue avventure con una nuova colorazione di Nintendo {Switch Lite}! Un Blu che farà la gioia dei fan di lunga data e dei nuovi giocatori Nintendo, che espande ulteriormente la tavolozza di colori del modello di Nintendo {Switch} concepito per gli amanti del gioco in mobilità. Nintendo {Switch Lite} è l’opzione ideale per chi ha tante occasioni per giocare fuori casa, e anche per chi desidera divertirsi in {multiplayer online} e locale con amici e parenti che possiedono già una console Nintendo {Switch top} di gamma.', 'Colore turchese, non collegabile alla tv', 'Nintendo Switch Lite, console, World of games', true, false, true, false),

/*8*/('Controller {PlayStation} 5', 12, 75.00, 59.00, 'controller_playstation5.jpg', 'Il controller {wireless DualSense} per {PlayStation} 5 offre un coinvolgente {feedback} aptico, grilletti adattivi dinamici e microfono integrato, perfettamente integrati in un {design} iconico.', 'Controller ps5 bianco', 'Playstation 5 controller, Sony, World of games', true, false, true, false),

/*9*/('{Joy}-con Nintendo {Switch}', 13, 59.00, null, 'joy_nintendo_switch.jpg', 'Un controller {wireless} simile a un controller tradizionale, comodo e ideale per lunghe sessioni di gioco in modalità televisore e da tavolo.', 'Joy-con destro rosso e sinistro blu', 'Joy-con Nintendo Switch, controller, World of games', false, false, true, false),

/*10*/('Controller Xbox', 14, 59.00, 49.00, 'controller_xbox.jpg', 'Rendi la tua esperienza di gioco più coinvolgente con gli accessori e i controller Xbox per console Xbox {Series} X e Xbox {One}. Impugnature antiscivolo su grilletti e pulsanti dorsali, croce direzionale ibrida, mappatura dei pulsanti e Tecnologia {Bluetooth}', 'Controller bianco con serigrafie e logo a tema', 'XBoX controller, Microsoft, World of games', false, false, true, false),

/*11*/('{Trust} Controller Pc', 15, 32.00, null, 'thrust_controller_pc.jpg', 'Compatibile con {Windows} 7, 8, 10 con {Plug & Play} - Doppio motore per vibrazione {motion}. Design ergonomico con impugnatura anti-scivolo per una sensazione confortevole durante il gioco. Tasti morbidi e piacevoli al tatto, riducono la sensazione di attrito legato alla pressione.', 'Controller modulare composto da pezzi intercambiabili. Controller nero', 'Trust Controller Pc, World of games', false, false, true, false),

/*12*/('{Call of Duty Black} 3', 1, 59.00, null, 'call_of_duty_black_3.jpg', '{Call of Duty} è una serie di videogiochi sparatutto in prima persona disponibile su PC, {smartphone, PlayStation} e Xbox (in precedenza anche sulle console Nintendo), i cui videogiochi della saga vengono pubblicati annualmente da {Activision} e sviluppati da diverse {software house}, tra cui {Infinity Ward, Treyarch, Sledgehammer Games, Raven Software} e {Tencent}. In ogni capitolo della serie sono comprese tre modalità diverse, fra cui il giocatore singolo, il multigiocatore e la modalità cooperativa.', '', 'Call of Duty Black 3, Playstation, Xbox, Pc, World of games', false, true, true, false),

/*13*/('{Uncharted} 4: la fine di un ladro', 2, 59.00, 49.00, 'uncharted_4.jpg', '{Uncharted} 4: Fine di un ladro ({Uncharted} 4: {Thief''s End}) è un videogioco {action-adventure} del 2016, sviluppato da {Naughty Dog}. Include un numero di collezionabili senza precedenti, per un totale di oltre 190 tesori, documenti del diario e conversazioni facoltative. Esplora ogni anfratto degli scenari e non lasciarti sfuggire gli appigli nascosti, i percorsi di scalata sulle pareti e le piattaforme meno visibili.', '', 'Uncharted 4, Sony, Playstation, World of games', true, false, true, false),

/*14*/('FIFA 21', 4,  59.00, null, 'fifa21.jpg', 'FIFA 21 è un videogioco di calcio sviluppato da {EA Sports} e pubblicato per {PlayStation} 4, Xbox {One, Microsoft Windows} e Nintendo {Switch} il 9 ottobre 2020, successivamente per {PlayStation} 5 e Xbox {Series} X,S il 4 dicembre dello stesso anno ed infine per {Google Stadia} il 17 marzo 2021.', '', 'Fifa 21, playstaton, Xbox, pc, switch, World of games', false, true, true, false),

/*15*/('{New} Super Mario {Bros U Deluxe}' , 3, 59.00, null, 'mario_bros_deluxe.jpg', 'Super Mario {Bros} è la riedizione del classico {platform} per Nintendo {Switch}, che vede protagonista Mario, il personaggio più iconico di tutti. Goditi 164 livelli insieme a un massimo di tre amici quando e dove vuoi in Super Mario {Bros}.', '', 'New Super Mario Bros U Deluxe, Switch, Nintendo, esclusiva, World of games', false, true, true, false),

/*16*/('{Returnal}', 5, 69.00, 50.00, 'returnal.jpg', '{Returnal} è un videogioco di genere sparatutto in terza persona horror psicologico con elementi {roguelike} sviluppato da {Housemarque} e pubblicato da {Sony Interactive Entertainment}, rilasciato in esclusiva per {PlayStation} 5 il 30 aprile 2021.', '', 'Returnal, Playstation, Sony, World of games', false, false, true, false),

/*17*/('{Gran Turismo}', 6, 69.00, null, 'gran_turismo.jpg', '{Gran Turismo} è una serie di videogiochi di simulazione di guida sviluppati da {Polyphony Digital} e pubblicati da {Sony Computer Entertainment} per le console {PlayStation}. Dall''uscita del primo {Gran Turismo} nel 1997, la serie ha venduto circa 76 milioni di copie in tutto il mondo.', '', 'Gran Turismo, Playstation, Sony, esclusiva, World of games', false, false, true, false),

/*18*/('{Resident Evil Village}', 7, 59.00, 49.00, 'resident_evil_village.jpg', '{Resident Evil Village}, conosciuto in Giappone con il titolo di {Biohazard Village}, è un videogioco {survival horror} sviluppato e pubblicato dalla {software house} giapponese {Capcom} per {PlayStation} 5, {PlayStation} 4, Xbox {Series} X,S, Xbox {One, Microsoft Windows} e {Google} Stadia il 7 maggio 2021.', '', 'Resident Evil Village, PlayStation, Xbox, pc, World of games', true, false, true, false),

/*19*/('{Red Dead Redemption} 2', 8, 79.00, null, 'red_dead_redemption_2.jpg', '{Red Dead Redemption} 2 è un gioco d''avventura e azione in prima e terza persona ambientato in un {open world} a tema western. Il giocatore impersona {Arthur Morgan}, un fuorilegge appartenente alla banda {Van der Linde}. Il videogioco dispone di un comparto giocatore singolo e multigiocatore.', '', 'Red Dead Redemption II, PlayStation, Xbox, pc, World of games', false, true, true, false),

/*20*/('{Corsair} Virtuoso {Wireless}', 16, 279.00, null, 'corsair_virtuoso_wireless.jpg', 'Sono perfette per giocare con dei padiglioni comodi ed un microfono rimovibile di alto livello. La loro connettività universale permette il collegamento a tutte le macchine da gioco. Non manca nemmeno il modulo {Bluetooth} per la connessione simultanea ad altri dispositivi. Il design moderno ed elegante permette di utilizzarle sia a casa che all''aperto, disponendo anche di un''ottima autonomia d''utilizzo.', 'Cuffie wireless nere con rivestimento esterno in metalli e microfono pieghevole', 'Corsair Virtuoso Wireless, cuffie, World of games', false, false, true, false),

/*21*/('{Trust Gaming} 838', 18, 35.00, null, 'trust_gaming_838.jpg', 'La tastiera dal {layout} integrale e completo e i 12 tasti multimediali ti permettono di tenere tutto sotto controllo, durante le sessioni di gioco o altre attività. L''anti-{ghosting} a 8 tasti e la possibilità di disabilitare il tasto {Windows} ti permettono di giocare senza interruzione, impedendoti di tornare per sbaglio al {desktop}', 'Tastiera con led multicolor', 'Trust Gaming  838, tastiera, pc, World of games', false, false, true, false),

/*22*/('{Trust} 232 {Mantis}', 17, 29.00, null, 'trust_232_mantis.jpg','Il {Mantis} è dotato di adattatore che consente un audio digitale di alta qualità, privo di distorsioni. Oltre alle conversazioni con i compagni di squadra, il microfono è realizzato per un uso perfetto con {Twitch, YouTube} o la piattaforma preferita: l’ascolto giungerà nitido a tutti. Il {Mantis} è dotato di un treppiede e consente di regolare l’angolazione del microfono. In pratica, è possibile utilizzare con facilità il microfono nel modo più comodo possibile. Lo {shock-mount} universale e il filtro pop garantiscono una configurazione perfetta per la nitidezza della riproduzione audio.', 'Microfono professionale con treppiede', 'Trust GXT 232 Mantis, microfono, World of games', false, false, true, false),

/*23*/('{Logitech} 203 {Gaming}', 19, 19.00, null, 'logitech_203_gaming.jpg', '{Design} classico testato dai giocatori: gioca in tutto comfort e con controllo totale; l''intuitivo {layout} a 6 pulsanti e la classica forma da {gaming} definiscono un {design} confortevole, collaudato nel tempo e amato dai giocatori, ciascun pulsante può essere personalizzato con il {software logitech} per semplificare le operazioni.', 'Mouse nero di forma classica, non wireless', 'Logitech 203 Gaming, mouse, pc, World of games', false, false, true, false),

/*24*/('{Battlefield} 2042 {Ultimate Edition}', 1, 55.00, null, 'battlefield_2042_ue.jpg', 'Un mondo trasformato dal caos – nel 2042, eventi climatici estremi e conflitti per il controllo delle risorse hanno alterato gli equilibri mondiali. Ambienti vasti e dinamici: combatti in una spettacolare guerra su vasta scala su sette enormi mappe, realizzate in base a una nuova filosofia di progettazione. Sfrutta al meglio la tua creatività in combattimento grazie a un ampio assortimento di armi, veicoli, {jet}, elicotteri ed equipaggiamento all''avanguardia, ispirati al futuro prossimo del 2042. Per la prima volta nella storia di {Battlefield}, 128 giocatori possono partecipare contemporaneamente a uno scontro senza compromessi, Xbox {One} e {PlayStation} 4 supportano 64 giocatori.', '', 'Battlefield 2042 Ultimate Edition, World of games', false, true, true, false),

/*25*/('{Battlefield} 2042 {Gold Edition}', 1, 42.00, 39.00, 'battlefield_2042_ge.jpg', 'Un mondo trasformato dal caos – nel 2042, eventi climatici estremi e conflitti per il controllo delle risorse hanno alterato gli equilibri mondiali. Ambienti vasti e dinamici: combatti in una spettacolare guerra su vasta scala su sette enormi mappe, realizzate in base a una nuova filosofia di progettazione. Sfrutta al meglio la tua creatività in combattimento grazie a un ampio assortimento di armi, veicoli, {jet}, elicotteri ed equipaggiamento all''avanguardia, ispirati al futuro prossimo del 2042. Per la prima volta nella storia di {Battlefield}, 128 giocatori possono partecipare contemporaneamente a uno scontro senza compromessi, Xbox {One} e {PlayStation} 4 supportano 64 giocatori.', '', 'Battlefield 2042 Gold Edition, World of games', false, false, true, false),

/*26*/('{Battlefield} 2042 {Standard Edition}', 1, 39.00, null, 'battlefield_2042_se.jpg', 'Un mondo trasformato dal caos – nel 2042, eventi climatici estremi e conflitti per il controllo delle risorse hanno alterato gli equilibri mondiali. Ambienti vasti e dinamici: combatti in una spettacolare guerra su vasta scala su sette enormi mappe, realizzate in base a una nuova filosofia di progettazione. Sfrutta al meglio la tua creatività in combattimento grazie a un ampio assortimento di armi, veicoli, {jet}, elicotteri ed equipaggiamento all''avanguardia, ispirati al futuro prossimo del 2042. Per la prima volta nella storia di {Battlefield}, 128 giocatori possono partecipare contemporaneamente a uno scontro senza compromessi, Xbox {One} e {PlayStation} 4 supportano 64 giocatori.', '', 'Battlefield 2042 Standard Edition, World of games', false, false, true, false), 

/*27*/('{Destiny} 2 I Rinnegati', 1, 25.00, null, 'destiny_2_rinnegati.jpg', 'È la tua prima esperienza nell''universo di {Destiny} 2 o sei uno tra i milioni di giocatori che vogliono vendicare {Cayde}-6? In entrambi i casi, {Destiny} 2 I Rinnegati - Collezione leggendaria offre l''esperienza completa di {Destiny} 2 per giocatori nuovi e veterani. Nei Rinnegati, la giustizia è nelle tue mani e dovrai vendicare la dipartita del tuo amico {Cayde}-6. Esplora nuove zone, risveglia nuovi poteri, ottieni nuove armi e scopri i misteri perduti degli insonni. La caccia ha inizio. I Rinnegati introduce una nuova storia, un nuovo archetipo di arma (arco leggendario), nove super abilità da scoprire, nuove attività e missioni, nuovi luoghi e assalti, un''incursione e un''infinità di equipaggiamento e armi. Inoltre, {Destiny} 2 I Rinnegati - Collezione leggendaria include un bene di consumo che ti permetterà di affrontare i nuovi contenuti fin da subito con un personaggio appena creato o esistente.', '', 'Destiny 2 I Rinnegati, World of games', false, false, true, false),

/*28*/('{The Legend of Zelda Breath of The Wild}', 2, 70.00, 50.00, 'the_legend_of_zelda.jpg', 'Dimentica tutto quello che sai sui giochi di {The Legend of Zelda} e immergiti in un mondo di scoperte, esplorazione e avventura in {The Legend of Zelda Breath of the Wild}, il nuovo capitolo di questa amatissima serie. Attraversa campi, foreste e montagne mentre cerchi di capire cosa è successo al regno di {Hyrule} in questa enorme, straordinaria avventura. Scala torri e montagne in cerca di nuove destinazioni, poi scegli un percorso per raggiungerle e parti all''avventura. Lungo la strada affronterai nemici giganteschi, caccerai animali selvatici e raccoglierai il cibo e le pozioni che ti servono per sopravvivere.', '', 'The Legend of Zelda Breath of The Wild, Hyrule, World of games', true, false, true, false),

/*29*/('{The Witcher} 3', 2, 59.00, 29.00, 'the-witcher-3.jpg', 'Vincitore di oltre 250 premi come gioco dell''anno, {The Witcher 3} è un''avventura {open world} ambientata in un universo {dark fantasy}, in cui impersoni il cacciatore di mostri mercenario Geralt di Rivia. La {Complete Edition} include tutti i contenuti scaricabili pubblicati, incluse due ricche espansioni per la storia: {Hearts of Stone} e {Blood and Wine}. È l''occasione perfetta per scoprire questo mondo per la prima volta o per rivivere l''avventura, anche fuori casa!', '', 'The Witcher 3, World of games', true, false, true, false),

/*30*/('Leggende Pokemon {Arceus}', 2, 60.00, null, 'leggende_pokemon_arceus.jpg', 'Un''avventura Pokemon completamente nuova, con meccaniche da gioco di ruolo e d''azione. Esplora le vaste aree aperte della regione da ogni prospettiva grazie ai Pokemon cavalcabili e completa il primo Pokedex della regione. Scopri un sistema di combattimento rinnovato, con tecniche speciali e incontri dinamici e senza sosta. Crea gli strumenti per la tua avventura sfruttando le risorse e affronta la furia dei Pokemon Regali.', '', 'Leggende Pokemon Arceus, World of games', false, false, true, false),

/*31*/('Rugby 22', 4, 50.00, null, 'rugby_22.jpg', 'Assumi il controllo dei più grandi club e delle migliori nazioni di rugby, e partecipa alle competizioni più prestigiose nelle modalità {single-player} e {multiplayer}, locale e {online}. Rugby 22 offre un rugby totale, combinando tattiche e intensità attraverso un {gameplay} fedele allo sport. Guida i tuoi giocatori alla vittoria in tornei, campionati ufficiali e in una modalità Carriera in cui potrai creare e gestire la tua squadra. Gioca con una vasta gamma di club ufficiali e squadre nazionali comprendenti i giocatori e i kit di questa stagione. L''elenco e i dettagli completi saranno rivelati molto presto! Rugby 22 offre una vera esperienza del rugby dalla grande profondità tattica e con un''autenticità capace di ricreare l''intensità delle grandi partite di questo sport.', '', 'Rugby 22, World of games', false, false, true, false),

/*32*/('{Instant Sports Paradise}', 4, 35.00, null, 'instant_sports_paradise.jpg', '{nstant Sports Paradise} fa splendere il sole sui tuoi allenamenti in famiglia! Viaggia dall''altra parte del pianeta e goditi un''esperienza fino a 4 giocatori locali, tutti sul tuo comodo divano. {Instant Sports Paradise} si svolge in un''oasi piena di storia e misteri. Una laguna blu, palme e sabbia calda... Ci sono molti segreti da scoprire tra un allenamento e l''altro. Gioca con o contro i tuoi amici. Sfida i tuoi punteggi e batti i tuoi record. 9 Sport estivi: divertiti e mettiti alla prova con sport esilaranti come idrovolante, bowling, calcio, ping-pong e molto altro. Goditi le vacanze e sii un vero turista, goditi le attività {chill-out} al mare. Sblocca tutti i tipi di elementi di personalizzazione del personaggio, lascia il tuo hotel con i tuoi vestiti preferiti. Sorprendi gli isolani e i tuoi amici con abiti divertenti e sorprendenti! Hai mai visto Babbo Natale in spiaggia?', '', 'Instant Sports Paradise, World of games', false, false, true, false),  

/*33*/('{Sackboy A Big Adventure}', 3, 65.00, null, 'sackboy_a_big_adventure.jpg', 'Affronta sfide imprevedibili, esplora magici mondi artigianali ed esprimi il tuo talento con un sistema di controllo straordinariamente immersivo. Lanciati in un''epica corsa contro il tempo e sfuggi ai pericoli in solitaria, o coinvolgi fino a tre avventurieri per dare vita a esilaranti avventure di squadra e sconfiggere le forze del male. Riuscirai a salvare il Mondo del fai da te dalle mire del sordido {Vex}, a distruggere il suo Sconquassatore... e a diventare il leggendario Cavaliere Sferruzzato? Esplora l''esplorabile e sfrutta le abilità di {Sackboy} per portare a casa la stoffa di fronte a sfide mozzafiato, feroci nemici e incredibili sorprese. Unisciti ad altri giocatori e dai inizio alla festa con divertentissime sfide in locale e {online}.', '', 'Sackboy A Big Adventure, World of games', false, false, true, false), 

/*34*/('{Crash Bandicoot} 4 - {It''s About Time}', 3, 40.00, null, 'crash_bandicoot_4-its_about_time.jpg', 'Il nuovissimo gioco di {Crash Bandicoot}! Parti per un''avventura oltre i confini del tempo in compagnia dei tuoi marsupiali preferiti. I personaggi sono tornati e questa volta non hanno preso di mira solo questo universo, ma l''intero Multiverso! Include aggiornamento per {PlayStation} 5 senza costi aggiuntivi. Per aggiornare le copie su disco per {PlayStation} 4 idonee, i giocatori devono disporre di una console {PlayStation} 5 con unità disco.', '', 'Crash Bandicoot 4 - It''s About Time, World of games', false, false, true, false),

/*35*/('{Devil May Cry} 5 - {Special Edition}', 5, 35.00, null, 'devil_may_cry_ 5-special_edition.jpg', 'Grafica mozzafiato e azione al cardiopalma. {Devil May Cry} 5 è tornato, rinnovato per le console di nuova generazione! Sfrutta la potenza delle nuove console e regola le impostazioni per personalizzare l''esperienza di gioco a seconda delle tue preferenze. Scegli tra gli splendidi effetti di luce fotorealistici del {ray tracing}, una risoluzione incredibilmente alta, o un {frame rate} elevato che ti permetterà di goderti il gioco con una fluidità mai vista prima. Se questo non basta, sarà inoltre possibile godere di un realistico audio, e di tempi di caricamento così rapidi che la tensione e il ritmo di gioco non verranno mai spezzati. Potrai inoltre affinare al massimo il tuo stile di combattimento grazie alle nuove modalità, più intense che mai. E la ciliegina su questa torta infernale? Vergil si unisce al gruppo come personaggio giocabile!', '', 'Devil May Cry 5 - Special Edition, World of games', false, false, true, false), 

/*36*/('{God of War}', 5, 22.00, null, 'god_of_war.jpg', 'Lasciatosi alle spalle la sua sete di vendetta verso gli dèi dell''Olimpo, il protagonista ora vive nella terra delle divinità e dei mostri norreni. È in questo mondo ostile e spietato che dovrà combattere per la sopravvivenza e insegnare a suo figlio non solo a fare lo stesso, ma anche a evitare di ripetere gli stessi errori fatali del Fantasma di Sparta... Questa sorprendente rielaborazione di {God of War} contiene tutti gli elementi caratteristici dell''iconica serie (combattimenti brutali, scontri epici con i boss e dimensioni mozzafiato) uniti a una narrazione intensa e commovente.', '', 'God of War, World of games', false, false, true, false), 

/*37*/('Mario Kart 8 {Deluxe}', 6, 61.00, null, 'mario_kart_8_deluxe.jpg', 'Gioca a Mario Kart dovunque ti trovi! Vuoi sfidare la tua famiglia sullo schermo di casa, giocare all''aria aperta al parco o andare a casa di un amico? Nessun problema! Con Nintendo {Switch} puoi giocare a Mario Kart dove, quando e con chi vuoi! In modalità portatile, fino a otto giocatori possono partecipare a partite {multiplayer} grazie alla comunicazione locale. In modalità schermo grande o in modalità da tavolo, fino a quattro giocatori possono sfidarsi sullo stesso schermo, diviso in parti uguali! Collegati a Internet e affronta giocatori di tutto il mondo. A ogni corsa {online} sono ammessi fino a 12 giocatori. Inoltre, due giocatori possono correre {online} insieme utilizzando lo stesso Nintendo {Switch}! È il Mario Kart migliore di sempre!', '', 'Mario Kart 8 Deluxe, World of games', false, false, true, false), 

/*38*/('Forza {Horizon} 5', 6, 55.00, 50.00, 'forza_horizon_5.jpg', 'Un’avventura incredibile ti aspetta in Forza {Horizon} 5 per Xbox {One} (compatibile con Xbox {Series} X). Alla guida delle auto da corsa più belle del pianeta, esplorerai i vivaci paesaggi open world del Messico, tra condizioni climatiche proibitive e sfide che ti coinvolgeranno in prima persona. Tenere il volante ben saldo tra le mani non è un gioco da ragazzi in {Forza Horizon} 5 disponibile per Xbox {One}. Le tue ruote viaggeranno tra paesaggi di grande bellezza e contrasti suggestivi. Scoprirai deserti brulicanti di vita, giungle lussureggianti, città storiche, rovine nascoste, spiagge incontaminate, vasti {canyon} e un imponente vulcano ricoperto di neve. Per guidare con stile, potresti aver bisogno del controller {wireless} per Xbox - Forza {Horizon} 5 in edizione limitata: il design è ispirato proprio alle corse!', '', 'Forza Horizon 5, World of games', false, false, true, false),

/*39*/('{Little Nightmares} 2', 7, 49.00, null, 'little_nightmares_2.jpg', 'Torna in un mondo di {Little Nightmares} 2, un gioco d''avventura e di {suspence} con protagonista Mono, un ragazzo intrappolato in un mondo distorto dal brusio della trasmissione malvagia di un ripetitore lontano. Con {Six}, la ragazza con l''impermeabile gialla, a fargli da guida, Mono parte alla scoperta degli oscuri segreti del Ripetitore. Il loro viaggio non sarà facile: Mono e {Six} affronteranno nuove minacce da parte dei terrificanti abitanti di questo mondo. Avrai il coraggio di affrontare questa nuova serie di piccoli incubi?', '', 'Little Nightmares 2, World of games', false, false, true, false),

/*40*/('{Assassin''s Creed Valhalla}', 8, 71.00, 40.00, 'assassins_creed_valhalla.jpg', 'Nell''espansione più ambiziosa nella storia della serie, {Eivor} affronterà il proprio destino nei panni di Odino, il dio norreno della guerra e della saggezza. Scatena i tuoi nuovi poteri divini in una disperata missione che ti porterà ad attraversare un universo mozzafiato. Vivi una leggendaria saga vichinga e salva tuo figlio mentre un fato oscuro minaccia gli dèi. Usa poteri ultraterreni. Assorbi nuove abilità dai nemici sconfitti e usale per sconfiggere chi ti sbarrerà la strada. Assumi la forma di un corvo per compiere assassinii spettacolari oppure rianima i nemici sconfitti per farne tuoi alleati. Trasforma la tua pelle in magma per proteggerti, sferra attacchi devastanti infondendo il ghiaccio nella tua arma e molto altro ancora.', '', 'Assassin''s Creed Valhalla, World of games', true, false, true, false),

/*41*/('{Cyberpunk} 2077', 8, 45.00, 40.00, 'cyberpunk_2077.jpg', '{Cyberpunk} 2077 è un''avventura a mondo aperto ambientata a {Night City}, una megalopoli ossessionata dal potere, dalla moda e dalle modifiche cibernetiche. Vestirai i panni di V, un mercenario fuorilegge alla ricerca di un impianto unico in grado di conferire l''immortalità. Potrai personalizzare il {cyberware}, le abilità e lo stile di gioco del tuo personaggio ed esplorare un''immensa città dove ogni scelta che farai plasmerà la storia e il mondo intorno a te.', '', 'Cyberpunk 2077, World of games', false, false, true, false),

/*42*/('Cuffie {Bluetooth Trust} - Primo {Touch Black}', 16, 16.00, null, 'cuffie_bluetooth_trust.jpg', 'Le cuffie {wireless Trust} Primo {Touch} offrono libertà di movimento con l''ampia portata del segnale {wireless} e la possibilità di ricarica anche in giro. Grazie al loro design, ti permettono di muoverti non solo in piena libertà ma anche con stile. Con 10 metri di portata del segnale {wireless}, le cuffie sono utili anche quando sei lontano dal dispositivo. Puoi girare liberamente per la stanza mentre il tuo telefono è in carica senza dover interrompere la musica.', 'Auricolari neri bluetooth', 'Cuffie Bluetooth Trust Primo Touch Black, World of games', false, false, true, false), 

/*43*/('Cuffie {PlayStation} 4', 16, 160.00, null, 'cuffie_playstation_4.jpg', 'Le cuffie portano l''audio di gioco in una dimensione completamente nuova, offrendo uno spettacolare suono surround virtuale d''alta qualità nei giochi {PlayStation} 4. Ottieni un vantaggio competitivo, individuando ogni mossa che fa il tuo nemico e immergiti in spettacolari suoni {multi-layer}, sia mentre combatti in partite multigiocatore o ti impegni in una avventura per giocatore singolo.', 'cuffie wireless con bordo morbido', 'Cuffie PlayStation 4, World of games', false, false, true, false), 

/*44*/('Microfono {Wave} 3', 17, 140.00, null, 'microfono_wave_3.jpg', 'I contenuti di qualità richiedono un audio eccellente. Ti serve un microfono di tipo broadcast da integrare direttamente nella tua postazione e un {mixer} per miscelare le fonti audio con facilità. Scopri {Wave} 3, è la soluzione completa. Architettura elegante. Audio impeccabile. Controllo tattile robusto. {Wave} 3 fa la sua parte senza intromettersi.', 'microfono nero da tavolo con filo ', 'Microfono Wave 3, World of games', false, false, true, false), 

/*45*/('Microfono {Trust} 242', 17, 48.00, null, 'microfono_trust_242.jpg', 'Microfono {streaming} con treppiede e {pattern} di registrazione a cardioide ad alta precisione; per streaming, podcasting, voce fuoricampo e musica acustica', 'microfono grigio da tavolo con filo', 'Microfono Trust 242, World of games', false, false, true, false), 

/*46*/('Tastiera meccanica {Gaming} 750', 18, 56.00, 30.00, 'tastiera_gaming_750.jpg', 'Tastiera ottica meccanica ad alte prestazioni, compatta senza tastierino numerico. {Cover} superiore in metallo. Retroilluminazione colorata. Configurabile separatamente per ciascun tasto.', 'tastiera con cavo', 'Tastiera meccanica Gaming 750, World of games', true, false, true, false),

/*47*/('Tastiera meccanica Trust 7000 M', 18, 48.00, null, 'tastiera_trust_ 7000_m.jpg', 'La tastiera ottico-meccanica è in grado di sostenere fino a 50 milioni di pressioni, è la dotazione giusta per giocare come un professionista. La 7000 M integra la tecnologia anti-{ghosting}, che assicura la possibilità di giocare in modo rapido e preciso. La completa retroilluminazione con ben 16 milioni di colori selezionabili e impostabili per ogni tasto garantisce massima controllabilità anche durante le sessioni gaming notturne.', '', 'Tastiera meccanica Trust 7000 M, World of games', false, false, true, false),  

/*48*/('{Mouse Trust Deathadder} V2 ', 19, 64.00, null, 'trust_deathadder_v2.jpg', 'Con più di 10 milioni di {Trust DeathAdder} venduti, il {mouse} più celebrato e premiato del mondo si è guadagnato la sua popolarità grazie al suo eccezionale design ergonomico. Perfettamente adatto ad un''impugnatura, funziona bene anche con gli stili {claw} e {fingertip}. Il {Trust DeathAdder} V2 porta avanti questa tradizione, mantenendo la sua forma distintiva ma alleggerendosi, per una manovrabilità più rapida che ti permette di migliorare la tua esperienza di gioco. Oltre all''ergonomia convenzionale da ufficio, il design ottimizzato offre un maggior comfort per giocare.', 'Mouse nero con logo della casa produttrice Razer. Non wireless', 'Mouse Trust Deathadder V2, World of games', false, false, true, false);


INSERT INTO utente(nome, cognome, mail, username, pw, indirizzo, cellulare, admin) VALUES
 /*1*/('',         '',        'admin@admin.it',         'admin',         'admin', '',                             '',          true),
 /*2*/('user',     'user',    'user@user.it',           'user',          'user',  'piazza delle Erbe 3 Padova',   '511111111', false),
 /*3*/('Mario',    'Rossi',   'mario_rossi@user.it',    'marioRossi',    'user',  'piazza della Frutta 1 Padova', '111111111', false),
 /*4*/('Elia',     'Verdi',   'elia_verdi@user.it',     'eliaVerdi',     'user',  'piazza della Frutta 7 Padova', '211111111', false),
 /*5*/('Aldo',     'Moro',    'aldo_moro@user.it',      'aldoMoro',      'user',  'via Pogibonsi 4 Siena',        '311111111', false),
 /*6*/('Ciro',     'Negri',   'ciro_negri@user.it',     'ciroNegri',     'user',  'via Siena 2 Montepulciano',    '411111111', false),
 /*7*/('Sofia',    'Bianchi', 'sofia_bianchi@user.it',  'sofiaBianchi',  'user',  'prato Valle 4 Padova',         '611111111', false),
 /*8*/('Giovanni', 'Russo',   'giovanni_russo@user.it', 'giovanniRusso', 'user',  'via Montepulciano 4 Pisa',     '811111111', false),
 /*9*/('Andrea',   'Moro',    'andrea_moro@user.it',    'andMoro',       'user',  'via Pisa 8 Montalcino',        '911111111', false),
/*10*/('Carla',    'Vespa',   'carla_vespa@user.it',    'carla12',       'user',  'via Montalcino 2 Arezzo',      '022222222', false),
/*11*/('Mirko',    'Rech',    'mirko_rech@user.it',     'Mirko32',       'user',  'via Arezzo 5 Firenze',         '122222222', false),
/*12*/('Carlo',    'Rossi',   'carlo_rossi@user.it',    'carros1',       'user',  'via Firenze 3 Pogibonsi',      '222222222', false),
/*13*/('Giulio',   'Fabri',   'giulio_fabri@user.it',   'Giulio34',      'user',  'via Pogibonsi 4 Cortona',      '322222222', false);


INSERT INTO commento (utente, articolo, commento, voto, data) VALUES
('2',  '1', 'Bellissima console!', '5', '2017-10-5'),
('4',  '1', 'Soluzione migliore per passare il lockdown con gli amici!', '5', '2018-09-6'),
('6',  '1', 'Un po'' costosetta, per il resto ok!', '4', '2019-08-7'),
('8',  '1', 'La spedizione è veloce, entro i termini e l''oggetto corrisponde alla descrizione sul sito', '4', '2020-05-8'),
('10', '1', 'L''oggetto è arrivato danneggiato...', '1', '2021-02-19'),
('12', '1', 'Finalmente ho scoperto questa console, l''ho aspettata da così tanto!', '5', '2022-01-10'),

('7',  '2', 'Questa versione senza lettore costa meno, secondo me conviene', '4', '2019-08-7'),
('5',  '2', 'Finalmente la ps5!!!', '5', '2018-09-6'),
('9',  '2', 'La console funziona bene, niente di cui lamentarmi :)', '4', '2020-05-8'),
('3',  '2', 'Il design non mi convince, ma i giochi girano bene', '3', '2017-10-5'),
('11', '2', 'La console si è bloccata al primo avvio, ho chiesto subito un reso', '1', '2021-02-19'),
('13', '2', 'Console difficile da trovare in questo momento, averla a casa è una gioia!', '5', '2022-01-10'),

('3',  '3', 'Ottima console!', '4', '2017-10-5'),
('4',  '3', 'La console più potente al momento. Soldi ben spesi!', '5', '2018-09-6'),
('6',  '3', 'L''ultima console di Microsoft. L''ho trovata molto deludente, mi aspettavo molte più funzioni', '1', '2019-08-7'),
('8',  '3', 'Ho anche la ps5, ma l''Xbox è meglio', '5', '2020-05-8'),
('10', '3', 'Me l''hanno consigliata i miei amici', '4', '2021-02-19'),
('12', '3', '500 euro sono tanti, ma almeno posso giocare comodo sul divano', '3', '2022-01-10'),

('7',  '4', 'Console perfetta per usufruire del gamepass', '5', '2019-08-7'),
('5',  '4', 'Non riesce a raggiungere la risoluzione 4k, ma va bene lo stesso', '3', '2018-09-6'),
('9',  '4', 'Deluso. Non sapevo che questa versione non avesse il lettore per i dischi. Ma io ho già comprato un gioco!', '1', '2020-05-8'),
('3',  '4', 'Costa meno rispetto alla versione X, versione adatta se si vuole risparmiare', '4', '2017-10-5'),
('11', '4', 'Consegna imbarazzante. La scatola era danneggiata, ma almeno la console funziona', '2', '2021-02-19'),

('6',  '5', 'Una console davvero strana, portatile e fissa allo stesso tempo!', '5', '2017-10-5'),
('13', '5', 'La risuzione da portatile è troppo bassa... Solo 720px', '2', '2018-09-6'),
('5',  '5', 'Mi piace il design, sembra moderno ed elegante', '4', '2019-08-7'),
('7',  '5', 'L''ho comprata solo per giocare a Zalda. Merita', '5', '2020-05-8'),
('8',  '5', 'Una buona console da usare quando ci sono ospiti', '3', '2021-02-19'),
('10', '5', 'La versione 2019 ha la batteria migliorata, ve la consiglio', '5', '2022-01-10'),

('6',  '6', 'Finalmente lo schermo OLED, i colori sono molto più vivi', '5', '2017-10-5'),
('9',  '6', 'Avevo la verisone vecchia, ho comprato anche questa ma l''unica differenza è uno schermo leggermente migliore. Non compratela', '1', '2018-09-6'),
('11', '6', 'Nintendo è sempre una garanzia!', '5', '2019-08-7'),
('12', '6', 'Bella console, ma ho saputo che i controller si rompono facilmente, speriamo di no!', '3', '2020-05-8'),
('4',  '6', 'Il nuovo dock bianco è meraviglioso!', '5', '2021-02-19'),
('13', '6', 'Non ho saputo resistere all''acquisto!', '5', '2022-01-10'),

('3',  '7', 'Console nintendo switch nella sua versione solo portatile, ma almeno costa molto meno', '3', '2017-10-5'),
('11', '7', 'La console sembra più resistente rispetto alla versione ibrida', '4', '2018-09-6'),
('8',  '7', 'L''ho comprata a mio foglio, è molto contento!', '5', '2019-08-7'),
('9',  '7', 'Colori bruttissimi. Come posso farmi vedere in giro con questa cosa??', '1', '2020-05-8'),
('5',  '7', 'Non mi è piaciuta per niente. Schermo troppo piccolo! è per bambini', '1', '2021-02-19'),
('7',  '7', 'Ora posso giocare con i pokemon!!!', '5', '2022-01-10'),

('2',  '8', 'Il controller è molto comodo e sensibile, ma un po'' pesante', '4', '2017-10-5'),
('4',  '8', 'Molto contento di aver acquistato questo gadeget', '5', '2018-09-6'),
('6',  '8', 'Consegna in ritardo di 3 giorni', '3', '2019-08-7'),
('8',  '8', 'Il controller è in linea con console come qualita, comodità e purtroppo anche il prezzo', '4', '2020-05-8'),
('10', '8', 'Molto contento di averlo acquistato, consegna entro i termini, corrisponde alla descrizione sul sito', '5', '2021-02-19'),
('12', '8', 'Consigliato a tutti gli amanti di videogiochi!', '5', '2022-01-10'),

('11', '9', 'Controller un po'' piccoli', '4', '2021-02-19'),
('5',  '9', 'Il prezzo è a dir poco ESAGERATO', '2', '2021-09-6'),
('7',  '9', 'Gli stick non funzionano bene, è colpa del famoso joy-con drift??', '1', '2020-08-7'),
('3',  '9', 'Ne ho presi un altro paio, così possiamo giocare in 4!', '5', '2017-10-5'),
('9',  '9', 'Acquisto obbligatorio per trarre il massimo dalla console', '5', '2021-05-8'),

('9',  '10', 'Il controller più comodo presente nel mercato', '5', '2017-10-5'),
('3',  '10', 'Che affare, posso usarlo anche su pc!', '5', '2018-09-6'),
('4',  '10', 'Non male, ma va ancora a batterie??', '3', '2019-08-7'),
('5',  '10', 'Mi piace la forma, il peso va bene ma sembra manchi qualcosa', '4', '2020-05-8'),
('6',  '10', 'Microsoft, mi hai veramente deluso questa volta. è durato 3 giorni, poi non ha più funzionato', '1', '2021-02-19'),
('7',  '10', 'Che bel controller!', '4', '2022-01-10'),

('11',  '11', 'In realtà non mi sembra tutto questo granchè, soldi buttati', '2', '2017-10-5'),
('3',  '11', 'ma che mi compro a fare un controller per pc?? meglio il muose e la tastiera', '1', '2018-09-6'),
('6',  '11', 'PC MASTER RACE!!', '5', '2019-08-7'),
('10',  '11', 'Con tutti i soldi che risparmio giocando su pc, posso permettermi tanti di questi controller', '5', '2020-05-8'),
('13',  '11', 'Non male, ma la vibrazione non è fatta benissimo', '3', '2021-02-19'),
('8',  '11', 'Fastastico! Niente da riposrtare', '5', '2022-01-10'),

('6',  '12', 'è sempre il solito cod. dopo un po'' annoia', '2', '2017-10-5'),
('8',  '12', 'Online ben fatto, mi sono diverito molto!', '5', '2018-09-6'),

('5',  '13', 'Che forte Nathan Drake! Le sue avventure sono bellissime!', '5', '2017-10-5'),
('9',  '13', 'Gioco incredibilmente curato. Wow', '5', '2018-09-6'),
('3',  '13', 'Si ma oltre ai bei paesaggi cos''ha questo gioco? Mi spettavo qualcosa di più', '3', '2017-10-5'),
('13',  '13', 'Che bella la parte in macchina, molto emozionante!', '5', '2018-09-6'),

('6',  '14', 'è il classico fifa, è sempre divertente', '4', '2017-10-5'),

('3',  '15', 'Super mario è sempre una garanzia di divertimento!', '5', '2017-10-5'),
('7',  '15', 'Non mi sono divertito, è solo un gioco per bambini', '1', '2018-09-6'),
('10',  '15', 'Davvero bello e super colorato', '5', '2017-10-5'),
('13',  '15', 'L''avevo già comprato su wii u, ma avevo voglia di rigiocarlo!', '5', '2018-09-6'),

('4',  '16', 'Gioco molto impegnativo', '4', '2018-09-6'),
('8',  '16', 'La prima vera esclusiva ps5. E non è male!', '4', '2017-10-5'),
('12',  '16', 'Non si può salvare quando inizi una run. Non ci siamo', '2', '2018-09-6'),

('12',  '17', 'Il gioco è molto interessante e coinvolgente, consigliato a tutti!', '5', '2017-10-5'),
('4',  '17', 'Non è migliore nel genere, ma tutto sommato soddisfacente ed è un buon passatempo', '4', '2018-09-6'),
('6',  '17', 'Mi aspettavo molto di più...', '2', '2019-08-7'),
('8',  '17', 'Molto contento dell''acquisto, prenderà una bella posizione nella mia collezione!', '5', '2020-05-8'),
('10', '17', 'Anche questa volta la softwear house non mi ha deluso, bravi!', '5', '2021-02-19'),

('12', '18', 'Fa davvero troppa paura, specialmente se giocato al buio', '3', '2017-10-5'),
('11', '18', 'Mi piace, il migliore della serie!', '5', '2018-09-6'),
('9',  '18', 'Non so più cosa aspettarmi dopo questo capitolo!', '4', '2019-08-7'),

('7',  '19', 'Gioco ENORME! Tantissime missioni', '5', '2017-10-5'),
('4',  '19', 'Bello, ma il finale non mi è piaciuto', '3', '2018-09-6'),
('6',  '19', 'Finalmente il 2! Saranno passati almeno 10 anni', '4', '2019-08-7'),
('8',  '19', 'Rockstar ci sa fare! Un applauso agli sviluppatori', '5', '2020-05-8'),
('10', '19', 'Noiosissimo, non mi sono divertito per niente', '1', '2021-02-19'),

('5',  '20', 'Si sente molto bene, le uso anche quando vado in studio', '5', '2020-05-8'),
('13',  '20', 'Meh... non le migliori', '2', '2017-10-5'),

('9',  '21', 'Non male, belle anche le decorazioni', '4', '2020-05-8'),
('12',  '21', 'Finalmente una buonatastiera meccanica!', '5', '2017-10-5'),
('5',  '21', 'Non riesco a capire che cosa ci trovi la gente in questa tastiera. Quelle da 5 euro funzionano meglio...', '1', '2020-05-8'),
('13',  '21', 'Ma non costa un po'' troppo per quello che offre?', '3', '2017-10-5'),

('6',  '23', 'Classico mouse, non mi fa nè caldo nè freddo', '3', '2020-05-8'),
('13',  '23', 'Funziona bene, sopra alla media direi', '4', '2017-10-5'),
('11',  '23', 'La rotella è davvero imprecisa, la rende inutilizzabile', '2', '2020-05-8'),

('5',  '24', 'Sono appassionato di questo tipo di giochi e questo non è male', '4', '2020-05-8'),
('9',  '24', 'Meglio di cod, di sicuro', '5', '2017-10-5'),
('3',  '24', 'Ma quanti bug ci sono?? Non riesco a divertirmi così', '1', '2020-05-8'),

('7',  '25', 'Forse sarebbe stato meglio spendere un po'' di più e prendere la ultimate edition', '3', '2020-05-8'),
('10',  '25', 'Bel gioco, peccato per l''intelligenza artificiale dei nemici che è ridicola', '4', '2017-10-5'),

('6',  '26', 'Contenuti troppo ridotti per il prezzo del prodotto', '3', '2020-05-8'),

('7',  '27', 'Finalmente il 2!!!', '5', '2017-10-5'),
('6',  '27', 'Gli sviluppatori hanno detto che supporteranno il gioco per 14 anni. Vi pare possibile?', '3', '2018-09-6'),
('10',  '27', 'Ora posso giocare con i miei amici!', '5', '2019-08-7'),
('11',  '27', 'Non so come la gente riesca a divertirsi con questo gioco. è più divertente vedere l''erba crescere.', '1', '2020-05-8'),
('13', '27', 'Se potessi darei 0 stelle!!', '1', '2021-02-19'),

('3',  '28', 'Avevo tanto hyper per questo gioco, finalmente è qui!', '5', '2017-10-5'),
('4',  '28', 'Bellissimo gioco, ma storia banale', '4', '2018-09-6'),
('5',  '28', 'Perchè la protagonista zelda è un uomo?', '3', '2019-08-7'),
('6',  '28', 'Davvero un capolavoro', '5', '2020-05-8'),
('7', '28', 'Mi piace girare per il mondo di hyrule, avrei voluto più tipi di nemici', '4', '2021-02-19'),
('8',  '28', 'Non vedo l''ora del 2!', '5', '2017-10-5'),
('9',  '28', 'Magari tutti i giochi fossero come questo', '5', '2018-09-6'),
('10',  '28', 'Secondo me è un gioco discreto, sopravvalutato', '3', '2019-08-7'),
('11',  '28', 'La mia popolazione preferita sono i rito!', '5', '2020-05-8'),
('13', '28', 'Ho raggiunto le 200 ore di gioco e ho ancora voglia di giocare!', '5', '2021-02-19'),

('7', '29', 'Bel sistema di combattimento!', '4', '2021-02-19'),
('8',  '29', 'Questo gioco è troppo lungo, alla lunga stanca', '2', '2017-10-5'),
('12',  '29', 'è davvero un capolavoro, fidatevi!', '5', '2018-09-6'),

('5',  '30', 'A me è piaciuto molto. Catture i pokemon così è molto divertente', '5', '2017-10-5'),
('3',  '30', 'Grafica orribile. Sembra un gioco ps2', '1', '2018-09-6'),
('10',  '30', 'Sono stanco di chi dice che è un gioco brutto. è evidente che non capisce niente di videogiochi', '5', '2019-08-7'),
('11',  '30', 'Finalmente un gioco di pokemon diverso dal solito!', '5', '2020-05-8'),
('9', '30', 'Il gioco tecnicamente è indietro di 15 anni, non va bene,', '1', '2021-02-19'),

('7',  '31', 'Un bel gioco di rugby, il mio sport preferito', '5', '2017-10-5'),
('12',  '31', 'Non male, ma mi aspettavo più modalità', '3', '2018-09-6'),

('6',  '32', 'Una brutta copia di wii sport', '2', '2017-10-5'),
('9',  '32', 'mediocre. Non vale i soldi che costa', '2', '2018-09-6'),
('10',  '32', 'Niente di originale, ma almeno è giocabile', '3', '2017-10-5'),
('4',  '32', 'Pessimo, non è per niente divertente', '1', '2018-09-6'),

('3',  '33', 'Un bel gioco per bambini!', '5', '2017-10-5'),
('8',  '33', 'Dura poco, ma è super divertente', '4', '2018-09-6'),
('11',  '33', 'una esclusiva ps5, ma non tra le migliori', '3', '2017-10-5'),

('8',  '34', 'Finalmente è usciro il quarto capitolo, ormai non ci speravo più', '4', '2017-10-5'),
('3',  '34', 'Mi ricorda il gioco che giocavo da piccolo su ps1', '5', '2018-09-6'),
('4',  '34', 'Ormai è troppo tardi, crash non è più divertente', '2', '2017-10-5'),
('12',  '34', 'Buon sequel, ma dopo tutto questo tempo mi aspettavo di più', '3', '2018-09-6'),

('6',  '35', 'Uno dei più bei giochi action!', '5', '2017-10-5'),
('5',  '35', 'Il protagonista è un personaggio bellissimo', '4', '2018-09-6'),
('9',  '35', 'Un gioco pieno di violenza gratuita. Non capisco come possa essere in commercio', '1', '2017-10-5'),
('10',  '35', 'La sotria è un po'' così così, ma il resto è da 10 e lode', '4', '2018-09-6'),

('7',  '36', 'Wow, che grafica incredibile!', '5', '2017-10-5'),
('12',  '36', 'Kratos non è nato per fare il padre, non è crdibile in quel personaggio', '3', '2018-09-6'),
('8',  '36', 'Una delle migliori esclusive playstation', '5', '2017-10-5'),

('3',  '37', 'è mario kart, è sempre divertente', '4', '2017-10-5'),
('4',  '37', 'Mi sono piaciute le nuove piste, soprattutto quella del campo da scii', '5', '2018-09-6'),
('11',  '37', 'L''ideale è giocarci con gli amici!', '5', '2017-10-5'),
('12',  '37', 'è sempre il solito mario kart che c''era su wii u, pochissime novità', '2', '2017-10-5'),

('6',  '38', 'un buon simulatore di corse, ve lo dico io che sono un appassionato', '4', '2017-10-5'),
('8',  '38', 'Divertente ma non troppo', '3', '2018-09-6'),
('5',  '38', 'Una delle sclusive storiche di Sony e anche oggi è godibilissima', '5', '2017-10-5'),
('13',  '38', 'Non mi sono divertito per niente, nei percorsi non ci sono neanche gli oggetti da prendere per i bonus', '1', '2017-10-5'),

('7',  '39', 'è un gioco ok, non fa tanta paura', '3', '2017-10-5'),
('13',  '39', 'è troppo simile all''uno. Sembra che sia stato riciclato troppo dal primo gioco.', '2', '2018-09-6'),
('4',  '39', 'Carino, ma lo raccomando solo agli appassionati.', '3', '2017-10-5'),

('6',  '40', 'Il gioco dura davvero troppo, almeno 70 ore. Mi sono scocciato prima', '2', '2017-10-5'),
('10',  '40', 'L''ambientazione è molto bella! la sotria abbastanza', '4', '2018-09-6'),
('4',  '40', 'Io adoro i vichinghi, il gioco l''ho trovato molto bello', '5', '2017-10-5'),
('3',  '40', 'Ubisoft non ha più idee', '1', '2017-10-5'),

('7',  '41', 'Se non avesse avuto così tanti bug, srebbe stato divertente giocarlo', '2', '2017-10-5'),
('13',  '41', 'Come si può rilasciare un gioco palesemente incompleto??', '1', '2018-09-6'),
('3',  '41', 'Io l'' trovato divertente, nessun problema rilevato', '4', '2017-10-5'),

('10',  '42', 'Delle buone cuffie bluetooth. Posso stare lontano dalla console quanto voglio', '4', '2017-10-5'),

('4',  '44', 'Qualità non eccelsa, ma fa il suo lavoro', '3', '2017-10-5'),
('11',  '44', 'L''o regalato ad un amico e lui ha apprezzato!', '4', '2018-09-6'),
('12',  '44', 'Perfetto, anche la spedizione è andata a buon fine', '5', '2017-10-5'),

('5',  '45', 'Si è rotto dopo un giorno, non è possibile', '1', '2017-10-5'),
('6',  '45', 'Non il migliore, ma fa quel che deve fare', '3', '2018-09-6'),
('9',  '45', 'Vale ogni centesimo che costa', '5', '2017-10-5'),
('10',  '45', 'Si vede che sono andati un po'' al risparmio nella produzione, ma va bene lo stesso', '3', '2017-10-5'),

('7',  '46', 'Fa davvero troppo rumore', '2', '2017-10-5'),
('8',  '46', 'La tastiera è molto reattiva, consigliata al 100%', '5', '2018-09-6'),
('9',  '46', 'Per il gaming è proprio quel che ci vuole!!', '5', '2017-10-5'),

('3',  '47', 'Pessima. I tasti si staccano e la sto usando da un solo mese', '1', '2017-10-5'),

('4',  '48', 'Incredibile, finalmente un mouse ottimo!', '5', '2018-09-6'),
('5',  '48', 'Qualità sufficiente, va abbastanza bene per giocare', '3', '2017-10-5');


INSERT INTO ordine_utente (data, utente) VALUES 
/*1*/('2018-02-5',  '2'),
/*2*/('2018-03-6',  '3'),
/*3*/('2018-04-7',  '2'),
/*4*/('2018-05-8',  '4'),
/*5*/('2018-06-9',  '3'),
/*6*/('2019-07-10',  '5'),
/*7*/('2019-08-11',  '3'),
/*8*/('2019-09-12',  '7'),
/*9*/('2019-09-13',  '11'),
/*10*/('2019-11-14',  '12'),

/*11*/('2020-02-1',  '8'),
/*12*/('2020-03-2',  '2'),
/*13*/('2020-04-3',  '4'),
/*14*/('2020-05-4',  '5'),
/*15*/('2020-06-5',  '3'),
/*16*/('2020-07-6',  '8'),
/*17*/('2020-08-7',  '6'),
/*18*/('2020-09-8',  '9'),
/*19*/('2020-10-9',  '3'),
/*20*/('2020-11-10',  '2'),

/*21*/('2021-02-1',  '11'),
/*22*/('2021-03-2',  '12'),
/*23*/('2021-04-3',  '10'),
/*24*/('2021-05-4',  '9'),
/*25*/('2021-06-5',  '8'),
/*26*/('2021-07-6',  '4'),
/*27*/('2021-08-7',  '5'),
/*28*/('2021-09-8',  '6'),
/*29*/('2021-10-9',  '7');

INSERT INTO ordine(ordine, articolo, quantita) VALUES 
('1', '2',  '2'),
('1', '4',  '1'),
('1', '7',  '1'),
('2', '17', '1'),
('2', '18', '1'),
('3', '22', '3'),
('4', '23', '2'),
('5', '24', '1'),
('5', '25', '4'),
('6', '2',  '2'),
('6', '4',  '1'),
('7', '7',  '1'),
('8', '17', '1'),
('8', '18', '1'),
('9', '17', '1'),
('9', '18', '1'),

('10', '2',  '2'),
('11', '2',  '2'),
('11', '4',  '1'),
('11', '7',  '1'),
('12', '17', '1'),
('12', '18', '1'),
('13', '22', '3'),
('14', '23', '2'),
('15', '24', '1'),
('15', '25', '4'),
('16', '2',  '2'),
('16', '4',  '1'),
('17', '7',  '1'),
('18', '17', '1'),
('18', '18', '1'),
('19', '17', '1'),
('19', '18', '1'),

('20', '3',  '2'),
('21', '13',  '2'),
('21', '15',  '1'),
('21', '2',  '1'),
('22', '23', '1'),
('22', '28', '1'),
('23', '22', '3'),
('24', '23', '2'),
('25', '24', '1'),
('25', '25', '4'),
('26', '22',  '2'),
('26', '24',  '1'),
('27', '27',  '1'),
('28', '27', '1'),
('28', '28', '1'),
('29', '27', '1'),
('29', '28', '1');
