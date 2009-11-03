<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class clTbExport {

    private $af ="";
    // constructeur de la classe
    public function __construct() {
	if ((TBKEY == "") || (TBURL == "") || (TBIDSITE == "")) return;
	$xml = $this->getXML();
	$a = $xml->saveXML($xml->documentElement);
	$rep = XhamUpdater::sendPostData(TBURL, array('xml' => $a));
	$this->traitementReponse($rep);
    }

    // inutile pour l'envoi auto
    public function getAffichage() {
	return $this->af;
    }

    public function traitementReponse($chaineXML) {
	if (empty($chaineXML)) return;
	$xml = new DOMDocument();
	$xml->loadXML($chaineXML);
	if (!$root = $xml->documentElement) return;

	if ($v = $root->getElementsByTagName("versionDemandee")) {
	    $versionDemandee= $v->item(0)->nodeValue;
	    if (($versionDemandee != "") && (version_compare($versionDemandee, $this->getVersion(), ">"))) {
		if (file_exists(URLLOCAL . "temp/maj.txt")) {
		    $var = substr(file_get_contents(URLLOCAL . "temp/maj.txt"), 0, -2);
		    if (mktime(date('c')) - mktime($var) > 15*60) unlink(URLLOCAL . "temp/maj.txt");
		}
		if (! file_exists(URLLOCAL . "temp/maj.txt")) {

		    $file = fopen(URLLOCAL . "temp/maj.txt" , "w+");
		    fwrite($file, "a");
		    fclose($file);
		    try{
		    $v = XhamUpdater::updateTU();
		    XhamUpdater::decompact($v);
		    XhamUpdater::applyPatchs(TBIDSITE);
		    }catch(Exception $e){}
		    unlink(URLLOCAL . "temp/maj.txt");
		}
	    }

	}
	if ($tab_enq = $root->getElementsByTagName("active_enquete")) {
	    foreach ($tab_enq as $enquete) {
		$idEnq = $enquete->nodeValue;
		$enq = new clTuFormxTrigger($idEnq);
		$enq->start();
	    }

	}

	if ($tab_opt_change = $root->getElementsByTagName("delete_option")) {
	    foreach ($tab_opt_change as $opt) {
		$idOpt = $opt->getAttribute("id");
		$valeur = $opt->nodeValue;
		$obRequete = new clRequete(BASEXHAM, 'options', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
		$requete = "DELETE FROM options WHERE idoption=".$idOpt;
		$obRequete->exec_requete($requete);
	    }
	}
	if ($tab_opt_change = $root->getElementsByTagName("change_option")) {
	    foreach ($tab_opt_change as $opt) {
		$idOpt = $opt->getAttribute("id");
		$valeur = $opt->nodeValue;
		$obRequete = new clRequete(BASEXHAM, 'options', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
		$requete = "UPDATE options SET valeur='".$valeur."' WHERE idoption=".$idOpt;
		$obRequete->exec_requete($requete);
	    }
	}

    }

    // fonction permettant d'encoder en utf8 si ?a ne l'est pas d?j?
    public function encode($s) {
	if (utf8_encode(utf8_decode($s)) == $s) return $s;
	else return utf8_encode($s);
    }

    // fonction cr??ant le fichier xml ? envoyer
    public function getXML() {
	$xml = new DomDocument();
	$xml->encoding = "UTF-8";
	// cr?ation de la racine avec id du tu
	$root = $this->createNoeud($xml, $xml, "tu");
	$root->setAttribute("id",TBIDSITE);

	$root_key = $this->createNoeud($xml, $root, "key");
	$root_key->appendChild($xml->createTextNode(TBKEYSITE));

	// noeud id serveur veille
	$root_veille = $this->createNoeud($xml, $root, "idVeille");
	$root_veille->appendChild($xml->createTextNode($this->encode($a)));


	$root_date = $this->createNoeud($xml, $root, "date");
	$root_date->appendChild($xml->createTextNode(date("c")));

	// noeud version
	$root_version = $this->createNoeud($xml, $root, "version");
	$root_version->appendChild($xml->createTextNode($this->encode($this->getVersion())));

	// noeud nombre de patients
	$root_nb_patients = $this->createNoeud($xml, $root, "nbPatients");
	$root_nb_patients->appendChild($xml->createTextNode($this->encode($this->getNbPatients())));

	// noeud nombre de m?decins pr?sents
	$root_nb_medecins = $this->createNoeud($xml, $root, "nbMedecins");
	$root_nb_medecins->appendChild($xml->createTextNode($this->encode($this->getNbMedecins())));

	// ENQUETES
	$root_enq = $this->createNoeud($xml, $root, "enquetes");
	$tab_enq = $this->getEnquetes();
	foreach ($tab_enq as $enq) {
	    $node_enq = $this->createNoeud($xml, $root_enq, "enquete");
	    $node_enq->setAttribute("id", $this->encode($enq['id']));

	    $enq_name = $this->createNoeud($xml, $node_enq, "name");
	    $enq_name->appendChild($xml->createTextNode($this->encode($enq['nom'])));

	    $enq_valeur = $this->createNoeud($xml, $node_enq, "value");
	    $enq_valeur->appendChild($xml->createTextNode($this->encode($enq['is_active'])));
	}

	// OPTIONS
	$root_option = $this->createNoeud($xml, $root, "options");
	$opt_categs = $this->getOptCateg();
	foreach($opt_categs as $categ) {
	    $node_categ = $this->createNoeud($xml, $root_option, "categ");
	    $node_categ->setAttribute("nom", $this->encode($categ['categorie']));

	    $opts = $this->getTabOptions($categ['categorie']);
	    // noeuds options
	    foreach ($opts as $opt) {
		$node_opt = $this->createNoeud($xml, $node_categ, "option");
		$node_opt->setAttribute("id", $this->encode($opt['idoption']));

		$opt_name = $this->createNoeud($xml, $node_opt, "name");
		$opt_name->appendChild($xml->createTextNode($this->encode($opt['libelle'])));

		$opt_valeur = $this->createNoeud($xml, $node_opt, "value");
		$opt_valeur->appendChild($xml->createTextNode($this->encode($opt['valeur'])));
	    }
	}

	// ajout des tests du index.test.php
	$root_tests = $xml->createElement("tests"); $root->appendChild($root_tests);

	$tests_categ1 = $this->createCategTest($xml, $root_tests, "Configuration php basique");
	$this->createNoeudTest($xml, $tests_categ1, "Test version de PHP > 5.1.0", clUpdater::checkPHPVersion('5.1.0'));
	$this->createNoeudTest($xml, $tests_categ1, "Safe mode non activ?", clUpdater::testSafeMode());
	$this->createNoeudTest(
	    $xml, $tests_categ1,
	    "Test de la desactivation de la limite temporelle d'execution du script",
	    clUpdater::testLimiteTempo()
	);
	$this->createNoeudTest(
	    $xml, $tests_categ1,
	    "Test de l'augmentation de la memoire allouee a 512M",
	    clUpdater::testNoNoNoNoNoNoThereIsNoLimit("512M")
	);


	$tests_categ2 = $this->createCategTest($xml, $root_tests, "Modules php necessaires");
	$modules = array("soap", "xsl", "xml", "ftp", "mysql", "calendar", "gd", "zlib", "mbstring", "sockets");
	foreach ($modules as $module) {
	    $this->createNoeudTest(
		$xml, $tests_categ2,
		"Test de la presence du module PHP " . $module,
		clUpdater::testModule($module)
	    );
	}

	$tests_categ3 = $this->createCategTest($xml, $root_tests, "Modules php pour fonctionalites etendues");
	$modules = array("curl", "openssl");
	foreach ($modules as $module) {
	    $this->createNoeudTest(
		$xml, $tests_categ3,
		"Test de la presence du module PHP " . $module,
		clUpdater::testModule($module)
	    );
	}

	$tests_categ4 = $this->createCategTest($xml, $root_tests, "Verification des repertoires");

	$dirs = array(
	    URLCACHE, URLDOCS, URLLOCAL.'hprim/', URLLOCAL.'hprim/ok/', URLLOCAL.'hprim/xml/',
	    URLLOCAL.'rpu/', URLLOCAL.'rpu/ok/', URLLOCAL.'rpu/logs/', URLLOCAL.'var/',
	    URLLOCAL.'var/maj/', URLLOCAL.'temp/', URLLOCAL.'var/dist/'
	);
	foreach ($dirs as $dir) {
	    $this->createNoeudTest(
		$xml, $tests_categ4,
		"Test du droit d'ecriture sur le dossier " . $dir,
		clUpdater::testEcritureDossier($dir),
		true
	    );
	}

	$tests_categ5 = $this->createCategTest($xml, $root_tests, "Connexions aux bases");

	$this->createNoeudTest(
	    $xml, $tests_categ5,
	    "Connexion au serveur MySQL '".MYSQL_USER."@".MYSQL_HOST." (using password: ".(MYSQL_PASS?'YES':'NO').")'",
	    mysql_pconnect ( MYSQL_HOST, MYSQL_USER, MYSQL_PASS )
	);

	$bases = array(BASEXHAM, BDD, CCAM_BDD);
	$bbases = array("xham", "tuv2", "ccam");
	foreach ($bases as $i => $base) {
	    $this->createNoeudTest(
		$xml, $tests_categ5,
		"Connexion ? la base " . $bbases[$i] . " '" . $base . "'",
		mysql_select_db ( $base )
	    );

	    $this->createNoeudTest(
		$xml, $tests_categ5,
		"Test des privileges " . $bbases[$i] . " : CREATE ALTER DROP base '" . $base . "'",
		clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,$base)
	    );
	}

	$tests_categ6 = $this->createCategTest($xml, $root_tests, "Communication");
	ob_flush();flush();

	$ftp_server = 'www.veille-arh-paca.com' ;
	$ftp_user_name = 'importsrv' ;
	$ftp_user_pass = '4dS#3!b';
	$this->createNoeudTest(
	    $xml, $tests_categ6,
	    "Test de connexion FTP vers serveur de veille  (ftp://www.veille-arh-paca.com)",
	    clUpdater::testDepotFTP($ftp_server, $ftp_user_name, $ftp_user_pass),
	    true
	);

	$this->createNoeudTest(
	    $xml, $tests_categ6,
	    "Test de cryptage avec la clef publique ARH",
	    clUpdater::clefARH(), true
	);
	return $xml;
    }


    // fonction ajoutant un noeud ? un autre noeud
    public function createNoeud($xml, $root, $s) {
	$noeud = $xml->createElement($s);
	$root->appendChild($noeud);
	return $noeud;
    }

    // fonction cr??ant une cat?gorie de tests
    public function createCategTest($xml, $root, $s) {
	$noeud = $this->createNoeud($xml, $root, "categ");
	$noeud->setAttribute("name", $this->encode($s));
	return $noeud;
    }


    // fonction cr??ant un test dans une cat?gorie
    public function createNoeudTest($xml, $root, $s, $var, $msg=false) {
	$noeud = $this->createNoeud($xml, $root, "test");
	$name = $this->createNoeud($xml, $noeud, "name");

	$name->appendChild($xml->createTextNode($this->encode($s)));

	$valeur = $msg ? $var[0] : $var;

	$value = $this->createNoeud($xml, $noeud, "value");
	$value->appendChild($xml->createTextNode($valeur ? 1 : 0));

	if (($msg) && (!$valeur)){
	    $error = $this->createNoeud($xml, $noeud, "error");
	    $error->appendChild($xml->createTextNode($this->encode($var[1])));
	}
	return $noeud;
    }


    // fonction retournant la version du TU
    public function getVersion() {
	$v = "";
	if (file_exists(URLLOCAL.'version.txt')) {
	    $v .=  str_replace("\n",'', file_get_contents(URLLOCAL.'version.txt'));
	}
	return $v;
    }

    // fonction retournant le nombre de patients pr?sents
    public function getNbPatients() {
	$obRequete = new clRequete(BDD, 'patients_presents', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
	$requete = "SELECT count(*) AS c FROM `patients_presents` ";
	$tabResult = $obRequete->exec_requete($requete, 'tab');
	return $tabResult[0]['c'];
    }

    // fonction retournant le nombre de patients pr?sents
    public function getNbMedecins() {
	$obRequete = new clRequete(BDD, 'patients_presents', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
	$requete = "SELECT distinct count(medecin_urgences) AS c FROM `patients_presents` WHERE medecin_urgences IS NOT NULL AND medecin_urgences NOT LIKE '' AND medecin_urgences NOT LIKE '0' ";
	$tabResult = $obRequete->exec_requete($requete, 'tab');
	return $tabResult[0]['c'];
    }

    // fonction retournant l'id du TU pour le serveur de veille
    public function getIdVeille() {
	$obRequete = new clRequete(BASEXHAM, 'options', array(), MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
	$requete = "SELECT valeur FROM options  WHERE  libelle='RPU_IdActeur'";
	$tabResult = $obRequete->exec_requete($requete, 'tab');
	return $tabResult[0]['valeur'];
    }

    // retourne un tableau de toutes les actions d'une cat?gorie avec leur id, leur libell? et leur valeur
    function getTabOptions($categ) {
	$obRequete = new clRequete(BASEXHAM, 'options', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
	$requete = "SELECT o.idoption, o.libelle, o.valeur FROM options o WHERE o.categorie='" . $categ . "'and o.idapplication=".IDAPPLICATION;

	$tabResult = $obRequete->exec_requete($requete, 'tab');
	return $tabResult;
    }


    // retourne un tableau des cat?gories d'options
    function getOptCateg() {
	$obRequete = new clRequete(BASEXHAM, 'options', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
	$requete = "SELECT DISTINCT o.categorie FROM options o WHERE idapplication=" . IDAPPLICATION ;
	$tabResult = $obRequete->exec_requete($requete, 'tab');
	return $tabResult;
    }


    // retourne un tableau  de toutes les enqu?tes disponibles
    function getEnquetes() {
	$tabEnquetes = clTuFormxTrigger::getTriggers();
	$tab = array();
	foreach ($tabEnquetes as $enquete) {
	    $tab[] = array(
		"id" => $enquete->getIdTrigger(),
		"nom" => $enquete->getNomEnquete(),
		"is_active" => ($enquete->isActive() ? 1 : 0)
	    );
	}
	return $tab;
    }

}
?>