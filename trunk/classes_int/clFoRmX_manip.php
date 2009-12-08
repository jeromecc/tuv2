<?php

/*
Titre  : Classe TG_FoRmX
Emmanuel Cervetti
Date   : Aout 2005

Description : 
Cette classe genere une instance de "Formulaire Etendu" à partir d'un fichier XML bien formé ( voir inclusion_guidage.xml pour exemple ultra commenté).
Cela permet de generer, charger, stocker, modifier des formulaires "actifs"

la variable statique FORMX_LOCATION doit être definie
*/	



class  clFoRmX_manip extends clFoRmX {

    function __construct ($ids='_pipo_',$options='NO_POST_THREAT') {

	if ( is_object($ids)) {
	    $this->subject = $ids  ;// ($ids est un patient )
	    $this->ids = $this->subject->getIDU()  ;
	} else {
	    $this->subject= new clPatient($ids);
	    $this->ids = $ids ;
	}
	//$this->accessor = new clUser();
	$this->accessor = null ;
	$this->construire($this->ids,$options);
    //eko ( $_REQUEST ) ;

    }

    //genere la case d'infos par IDS mais en lectrue seule
    function genCaseRO($bubulle) {
	global $tool ;
	global $session;

	// Chargement du template ModeliXe.
	$mod = new ModeliXe ( "FX_blocActions.mxt" ) ;
	$mod -> SetModeliXe (  ) ;
	$padaction = "1";
	//la en fait on ne genere pas un lien, qui serait la solution la plus simple, mais l'enjeu est
	//de tout gerer (Post, variables de la classe...) lors de la création de la classe. On ne touche
	//pas aux variables de navigation. Donc on va créer un champ qui - pour l'utilisateur - ressemble
	//comme deux gouttes d'eau à un lien mais qui en fait est un appel javascript remplissant un champ
	//caché par une variable donnée.
	$data = $this->ListFromIds('F');
	for ( $i = 0 ; isset ( $data[id_instance][$i] ) ; $i++ ) {
	    $padaction = '';
	    $libelle = $data[libelle][$i] ;
	    if(! $libelle) $libelle = $data[idformx][$i] ;
	    $mod -> MxText ( "actions.statut","<span  style='color:#00DD00'>Fini</span>") ;
	    $mod -> Mxattribut("actions.code","document.FoRmXcase.FormX_ext_goto_.value = '".$data[id_instance][$i]."';document.FoRmXcase.submit();");
	    $mod -> MxText ( "actions.libelle", $libelle  ) ;

	    $mod -> Mxattribut("actions.codemouseover","montre_resume_formulaire(event,'resu_".$data[id_instance][$i]."')");
	    $mod ->Mxattribut("actions.codemouseout","hide('resu_".$data[id_instance][$i]	."')");
	    $newInstance = new clFoRmX_manip($this->ids,'NO_POST_THREAT');
	    $newInstance->loadInstance($data[id_instance][$i]);
	    if($bubulle)
		$bubulle->addBulle("resu_".$data[id_instance][$i],$newInstance->gen_resume());
	    unset($newInstance);

	    //onmouseover="show(event,'nv_action')" onmouseout="hide('nv_action')"


	    $mod -> MxBloc ( "actions.frem", "delete" ) ;
	    $mod -> MxBloc ( "actions", "loop" ) ;
	}
	$data = $this->ListFromIds('E');
	for ( $i = 0 ; isset ( $data[id_instance][$i] ) ; $i++ ) {

	    $padaction = '';
	    $libelle = $data[libelle][$i] ;
	    if(! $libelle) $libelle = $data[idformx][$i] ;
	    $mod -> MxText ( "actions.statut","<span  style='color:#AA8822'>En cours</span>") ;
	    $mod -> MxText ( "actions.libelle", $libelle ) ;
	    $mod -> Mxattribut("actions.codemouseover","montre_resume_formulaire(event,'resu_".$data[id_instance][$i]."')");
	    $mod ->Mxattribut("actions.codemouseout","hide('resu_".$data[id_instance][$i]	."')");
	    $newInstance = new clFoRmX_manip($this->ids,'NO_POST_THREAT');
	    $newInstance->loadInstance($data[id_instance][$i]);
	    if($bubulle)
		$bubulle->addBulle("resu_".$data[id_instance][$i],$newInstance->gen_resume());
	    unset($newInstance);
		/*$mod -> Mxattribut("actions.code","document.FoRmXcase.FormX_ext_goto_.value = '".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		
		if ( $session->getDroit ( "guidage_administrer" ,'r') ) {
		$mod->Mxattribut("actions.frem.code","document.FoRmXcase.FormX_ext_goto_.value = 'RM".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		} else { $mod -> MxBloc ( "actions.frem", "delete" ) ; }*/
	    $mod -> MxBloc ( "actions.frem", "delete" ) ;
	    $mod -> MxBloc ( "actions", "loop" ) ;
	}
	$data = $this->ListFromIds('I');
	for ( $i = 0 ; isset ( $data[id_instance][$i] ) ; $i++ ) {
	    $padaction = '';
	    $libelle = $data[libelle][$i] ;
	    if(! $libelle) $libelle = $data[idformx][$i] ;
	    $mod -> MxText ( "actions.statut","<span  style='color:#FF0000'>Initialisée</span>") ;
	    $mod -> MxText ( "actions.libelle", $libelle ) ;
		/*$mod -> Mxattribut("actions.code","document.FoRmXcase.FormX_ext_goto_.value = '".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		
		/*if ( $session->getDroit ( "guidage_administrer",'r' ) ) {
		$mod->Mxattribut("actions.frem.code","document.FoRmXcase.FormX_ext_goto_.value = 'RM".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		} else { $mod -> MxBloc ( "actions.frem", "delete" ) ; }*/
	    $mod -> MxBloc ( "actions.frem", "delete" ) ;
	    $mod -> MxBloc ( "actions", "loop" ) ;
	}


	if($padaction) {
	    $mod -> MxBloc ( "actions.frem", "delete" ) ;
	    $mod -> MxBloc ( "titre", "delete" ) ;
	} else {
	    $mod -> MxBloc ( "padetitre", "delete" ) ;
	}
	//le bouton de creation de nouvelle instance d'action
	//$mod -> Mxattribut("newact_code","document.FoRmXcase.FormX_ext_goto_.value = 'new';document.FoRmXcase.submit();");
	if($bubulle)
	    $bubulle->addBulle("nv_action","<b style=\"color:red;\">Vous ne pouvez pas créer de nouvelle action sur ce patient</b>");
	// Variable de navigation.
	$mod -> MxHidden ( "hidden", "navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2) )) ;
	return $mod -> MxWrite ( "1" ) ;
    }

    //liste les actions definies










    function list_actions() {
	$dos=opendir(FORMX_LOCATION);
	$liste = array(''=>'');
	while ($fich = readdir($dos)) {
	    if (ereg("^.*\.xml$",$fich)) {
	    //on ouvre le fichier pour en trouver les caracteristiques principales
		$xml =  simplexml_load_file(FORMX_LOCATION.$fich);
		if (! $xml) {
		//eko("pb chargement de l'instance");
		} else {
		    if($xml['hide']=='y') continue;
		    $liste[(string) $xml[id]] = utf8_decode((string) $xml->Libelle) ;
		}
	    }
	}
	return $liste;
    }


    function gen_resume() {

    //pour commodité d'ecriture
	global $session;
	$xml = & $this->XMLCore ;

	if(! is_object($xml))
	    return ;
	if (  $xml['access'] &&  ! $session->getDroit(utf8_decode( (string) $xml['access']),'r' ) ) return '';
	foreach ($xml->xpath('//ITEM') as $item) {
	    if((string) $item->Val) {
		if ( ! (string) $item['access'] ) {
		    $af .= '<B>'.utf8_decode((string) $item->Libelle.': </B>') ;
		    $af .= utf8_decode((string) $item->Val);
		    $af .= '-';
		}
	    }
	}
	return $af;
    }

    function testAccesRestreint() {
	$xml = $this->XMLCore ;
	if (  $xml['access'] ) return 'o';
	if (  $xml['noHopiHist'] ) return 'o';

	foreach ($xml->xpath('//ITEM') as $item) {
	    if ( (string) $item['access'] ) {

		return 'o';
	    }
	}
	return '';

    }


 /*Genere la case d'infos*/
    function genCase($bubulle, $droit='form_non_spe') {
	global $tool ;
	global $session;
	global $options;
	if($this->droit) $droit =$this->droit;
	// Chargement du template ModeliXe.
	$mod = new ModeliXe ( "FX_blocActions.mxt" ) ;
	$mod -> SetModeliXe (  ) ;
	$padaction = true;
	//la en fait on ne genere pas un lien, qui serait la solution la plus simple, mais l'enjeu est de tout gerer (Post, variables de la classe...) lors de la création de la classe. On ne touche pas aux variables de navigation. Donc on va créer un champ qui - pour l'utilisateur - ressemble comme deux gouttes d'eau à un lien mais qui en fait est un appel javascript remplissant un champ caché par une variable donnée.

	$data = $this->ListFromIds(array('I','E','F'));
	if ( ! isset ( $data[id_instance][0] ) ) $mod -> MxBloc ( 'actions.frep', 'delete' ) ;
	if ( $session->getDroit ( $droit,'r' ) )
	    for ( $i = 0 ; isset ( $data[id_instance][$i] ) ; $i++ ) {
	    //chargement
		$newInstance = new clFoRmX_manip($this->ids,'NO_POST_THREAT');
		$newInstance->loadInstance($data[id_instance][$i]);
		$xml =  $newInstance->XMLCore ;
		//test sur les droits
		if (  $xml['access'] &&  ! $session->getDroit(utf8_decode( (string) $xml['access']),'r' ) )
		    continue ;
		elseif ( ! $xml['access'] && ! $session->getDroit($this->droit,'r'))
		    continue ;
		$padaction = false;
		$libelle = $data[libelle][$i] ;
		$id = $data['idformx'][$i] ;

		//$creation = $tool->date_simple($data['dt_creation'][$i]) ;
		//$modif = $tool->date_simple($data['dt_modif'][$i]) ;
		$dateCr = new clDate ( $data['dt_creation'][$i] ) ;
		$mod -> MxText ( "actions.dateForm", $dateCr -> getDate ( "d/m/y H:i") ) ;
		if(! $libelle) $libelle = $data[idformx][$i] ;
		switch($data[status][$i]) {
		    case 'I':
			$mod -> MxText ( "actions.statut","<span  style='color:red'>Initialisée</span>") ;
			break;
		    case 'E':
			$mod -> MxText ( "actions.statut","<span  style='color:orange'>En cours</span>") ;
			break;
		    case 'F':
			$mod -> MxText ( "actions.statut","<span  style='color:green'>Fini</span>") ;
			break;
		    default:
			$mod -> MxText ( "actions.statut","<span  style='color:blue'>Inconnu</span>") ;
			break;
		}
		//$mod -> MxText ( "actions.creation", $creation ) ;
		//$mod -> MxText ( "actions.modif", $modif ) ;
		$mod -> MxText ( "actions.libelle", $libelle ) ;

		//eko($id);

		if ( (   ! eregi ( 'Radio', $libelle ) AND ! eregi ( 'Bio', $libelle ) AND ! eregi ( 'Spécialisée', $libelle ) ) ) $mod -> MxBloc ( 'actions.frep', 'delete' ) ;
		else {
		    if ( eregi ( 'Radio', $libelle ) ) $libelleC = 'radio' ;
		    elseif ( eregi ( 'Bio', $libelle ) && eregi ( '2009', $libelle )) $libelleC = 'bio2009';
		    elseif ( eregi ( 'Bio', $libelle ) ) $libelleC = 'labo' ;
		    else $libelleC = 'spe' ;
		    //$mod->Mxattribut("actions.frep.code","document.FoRmXcase.Formulaire2print.value = '".$libelleC."';document.FoRmXcase.submit();");
		    if ((!($options->getOption("imprRadioRadio")) && ereg ( 'Radio', $libelle ) && ereg ( 'radio', $libelle ))
//			$mod -> MxBloc ( "actions.frep", "delete" );
			|| (!($options->getOption("imprRadioScanner")) && ereg ( 'Radio', $libelle ) && ereg ( 'scanner', $libelle ))
//			$mod -> MxBloc ( "actions.frep", "delete" );
			|| (!($options->getOption("imprRadioEcho")) && ereg ( 'Radio', $libelle ) && ereg ( 'échographie', $libelle )))
			$mod -> MxBloc ( "actions.frep", "delete" );
		    else
			$mod -> MxText ( "actions.frep.lienPrint", "<a href=\"".URLNAVI.$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2))."&Formulaire2print=$libelleC&FormX_ext_goto_=".$data[id_instance][$i]."&act_print=1\" target=\"_blank\">" ) ;
		}

		$resume = addslashes(str_replace('"',"'",$newInstance->gen_resume()));

		$mod -> Mxattribut("actions.codemouseover","return overlib('".$resume."', CAPTION, '".addslashes($data[libelle][$i])."');");
		unset($newInstance);
		$mod ->Mxattribut("actions.codemouseout","return nd()");

		$mod -> Mxattribut("actions.code","document.FoRmXcase.FormX_ext_goto_.value = '".$data[id_instance][$i]."';document.FoRmXcase.submit();");

		if ( $session->getDroit ( $droit,'d' ) ) {
		    $mod->Mxattribut("actions.frem.code","document.FoRmXcase.FormX_ext_goto_.value = 'RM".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		} else { $mod -> MxBloc ( "actions.frem", "delete" ) ; }

		if ( $session->getDroit ( $droit,'a' ) && $data[status][$i] == 'F') {
		    $mod->Mxattribut("actions.fed.code","document.FoRmXcase.FormX_ext_goto_.value = 'ED".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		} else { $mod -> MxBloc ( "actions.fed", "delete" ) ; }

		$mod -> MxBloc ( "actions", "loop" ) ;
	    }

	if($padaction) {
	    $mod -> MxBloc ( "actions.frem", "delete" ) ;
	    $mod -> MxBloc ( "actions.fed", "delete" ) ;
	    $mod -> MxBloc ( "titre", "delete" ) ;
	} else {
	    $mod -> MxBloc ( "padetitre", "delete" ) ;
	}
	//le bouton de creation de nouvelle instance d'action
	if ( $session->getDroit ( $droit,'w' ) ) {
	    $mod -> Mxattribut("nouveauF.newact_code","document.FoRmXcase.FormX_ext_goto_.value = 'new';document.FoRmXcase.submit();");
	    global $options ;
	    if ( $options -> getOption ( "racBonRadio" ) )
		$mod -> Mxattribut("racbonradio.newRadio","document.FoRmXcase.FormX_to_open_.value = 'Formulaire_Radio';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    else $mod -> MxBloc ( "racbonradio", "delete" ) ;
	    if ( $options -> getOption ( "racBonLabo" ) ) {
		$options->getOption("bio2009") ? $f = "Formulaire_Bio2009" : $f = "Formulaire_Bio";
		$mod -> Mxattribut("racbonlabo.newLabo","document.FoRmXcase.FormX_to_open_.value = '" . $f . "';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    }
	    else $mod -> MxBloc ( "racbonlabo", "delete" ) ;
	    if ( $options -> getOption ( "racBonConSpe" ) )
		$mod -> Mxattribut("racboncs.newCS","document.FoRmXcase.FormX_to_open_.value = 'Formulaire_Consultation_Specialisee';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    else $mod -> MxBloc ( "racboncs", "delete" ) ;
	    if ( $options -> getOption ( "racBonRadioRadio" ) )
		$mod -> Mxattribut("racbonradioradio.newRadioRadio","document.FoRmXcase.FormX_to_open_.value = 'Formulaire_Radio_Partie_Radio';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    else $mod -> MxBloc ( "racbonradioradio", "delete" ) ;
	    if ( $options -> getOption ( "racBonRadioScanner" ) )
		$mod -> Mxattribut("racbonradioscanner.newRadioScanner","document.FoRmXcase.FormX_to_open_.value = 'Formulaire_Radio_Partie_Scanner';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    else $mod -> MxBloc ( "racbonradioscanner", "delete" ) ;
	    if ( $options -> getOption ( "racBonRadioEcho" ) )
		$mod -> Mxattribut("racbonradioecho.newRadioEcho","document.FoRmXcase.FormX_to_open_.value = 'Formulaire_Radio_Partie_Echographies';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    else $mod -> MxBloc ( "racbonradioecho", "delete" ) ;

	    if ( $options -> getOption ( "DoubleEtablissement" ) )
		$mod -> Mxattribut("transfert.newT","document.FoRmXcase.FormX_to_open_.value = 'Formulaire_Transfert';document.FoRmXcase.FoRmX_selValid.value = 'on';document.FoRmXcase.FoRmX_selValid_x.value = 'on';document.FoRmXcase.ids.value = '".$this->ids."';document.FoRmXcase.submit();");
	    else $mod -> MxBloc ( "transfert", "delete" ) ;
	} else $mod -> MxBloc ( "nouveauF", "delete" ) ;
	if($bubulle)
	    $bubulle->addBulle("nv_action","Créez une nouvelle action sur ce patient");
	// Variable de navigation.
	$mod -> MxHidden ( "hidden", "navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2) )) ;

	return $mod -> MxWrite ( "1" ) ;
    }


    function traiterFini() {
	return 'F';
    }




    static function rangerDossMedChrono($patientArg) {
	global $patient;
	$patient = $patientArg ;
	$res =clFoRmXtOoLs::ListFromIds($patient->getIDU(),array('I','E'));
	$formAnt = "" ;
	if ( is_array ($res) ) {
	    for ($i=0;$i<$res['INDIC_SVC'][2];$i++) {
		if ( $res['idformx'][$i] == 'Dossier_Medical_Chronologique' ) {
		    $formAnt = $res['id_instance'][$i] ;
		    break ;
		}
	    }
	}
	if (! $formAnt) return false ;
	$form = new clFoRmX_manip($patient->getIDU()) ;
	$form->loadInstance($formAnt);
	$form->close('H') ;
	$form->passDocopi();
    }

    static function rangerDossMedAEV($patientArg) {
	global $patient;
	$patient = $patientArg ;
	$res =clFoRmXtOoLs::ListFromIds($patient->getIDU(),array('F','E','I'));
	//eko($res);
	$formAnt = "" ;
	if ( is_array ($res) ) {
	    for ($i=0;$i<$res['INDIC_SVC'][2];$i++) {
		if ( $res['idformx'][$i] == 'Dossier_AEV_Synthese' ) {
		    $formAnt = $res['id_instance'][$i] ;
		    break ;
		}
	    }
	}
	if (! $formAnt) return false ;
	$form = new clFoRmX_manip($patient->getIDU()) ;
	$form->loadInstance($formAnt);
	$form->close('H') ;
	$form->passDocopi();
    }



    function passDocopi() {
	global $session;
	global $options;
	global $patient;
	$param = array();
	$param["idu"] = $patient->getIDU() ;
	if (  ! $options->getOption('ModuleFormx2doc') ) {
	    return 'F';
	}

	//si il y a un acces excplicite particulier sur le formulaire, on ne le met pas dans hopi non plus
	$xml = $this->XMLCore ;
	//eko($xml["ok4docshopi"]);
	if(! $xml["ok4docshopi"] ) return 'F' ;

	$param['ilp'] = $patient->getILP();

	//construction du fichier html

	$a = $this->affFoRmX();
	$b = $this->convMP($a);
	$htmlform=utf8_decode($b);
	$filename="css/FoRmX_mail.css";
	$handle=fopen($filename,'r');
	$style=fread($handle,filesize($filename));
	fclose($handle);
	$filename="css/FoRmX.css";
	$handle=fopen($filename,'r');
	$style.=fread($handle,filesize($filename));
	fclose($handle);
	$buffer="<html><body><style>$style</style><br/>$message<br/><br/>$htmlform</body></html>";

	//on vire les images d'impression
	$conv = array(URLIMGEDI => URLIMGRIEN, URLIMGCLO => URLIMGRIEN , URLIMGVAL => URLIMGRIEN , URLIMGANNMINI => URLIMGRIEN );
	$buffer2 = strtr(& $buffer ,& $conv) ;

	$fic_html = 'FoRmX-'.date('y-m-j-h-i-s-').rand(1,1000);
	$fic_pdf = $fic_html . '.pdf';
	$fic_html.='.html';
	if($fp = fopen(URLACCESLOCALDOCFORMS.$fic_html,"a" ) ) {
	    fputs($fp,$buffer2);
	    fclose($fp);
	}


	$param['titre'] = (string) strtr (utf8_decode($this->XMLCore->Libelle),"'"," ") ;
	$param['url'] =  URLACCESEXTDOCFORMS.$fic_html;
	$param['idsej'] = $patient->getNSej();
	$param['auteur'] = "Automate Sortie Urgences";
	//enregistrement du doc dans la BDD BASEDOC
	$req = new clResultQuery();

	if ( $param['titre'] == "Dossier AEV Synthèse" ) {
	    $param['titre'] = "Dossier AEV Synth&egrave;se";
	    $res = $req -> Execute ( "Fichier", "delHopiDoc", $param, "ResultQuery" ) ;
	    $res = $req -> Execute ( "Fichier", "setHopiDoc", $param, "ResultQuery" ) ;

	    // envoie de mail à la sortie du patient du dossier AEV.
	    $headers      = "MIME-Version: 1.0\n";
	    $headers     .= "Content-type: text/html; charset=iso-8859-1\n";
	    //$headers   .= "Content-type:text/css\n";
	    // Quelques types d’entêtes : errors, From cc's, bcc's, etc
	    $headers     .= "From: Terminal des urgences CH-HYERES"."\n";
	    if ($options->getOption ('ActiverEnvoiMailFormulaireAEV')) {
		$param['cw'] = "WHERE nomliste='".addslashes("Mails Alertes AES")."'";
		$req = new clResultQuery ;
		$res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
		eko($res);
		for ( $i=0 ; $i < $res["INDIC_SVC"][2] ; $i++ ) {
		    mail($res["nomitem"][$i], "AEV Terminal des urgences || Patient: ".$patient->getNom()." ".$patient->getPrenom()."|| Sej: ".$patient->getNSej(),$buffer2, $headers);
		}
	    }
	}
	else
	    $res = $req -> Execute ( "Fichier", "setHopiDoc", $param, "ResultQuery" ) ;

	//eko($res);
	//print affTab($res);
	unset($buffer,$buffer2);
	return 'H';
    }







    function getAffichage($mode='') {
	global $pi;
	if(is_object($pi)) {
	    $pi->addMove('formX','formx_titre');
	    $pi->addMove('formx_infobulle');
	}
	return parent::getAffichage();
    }

}

?>
