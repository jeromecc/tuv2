<?php

// Titre  : Classe ExportActes.
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 29 Mai 2006

// Description : 
// Cette classe gère un export graphique des actes pour un jour donné.


class clCCAMExportActes {
	
	private $af ;
	
	function __construct ( ) {
		$this->initVars ( ) ;
		$this->genFiltres ( ) ;
	}
	
	// Génère l'affichage des filtres.
	function genFiltres ( ) {
		global $session ;
		$this->getListeDates ( ) ;
		$this->getListeActions ( ) ;
		$this->getListeTypes ( ) ;
		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "CCAM_ExportActes.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	// Affichage des filtres.
    	$mod -> MxSelect ( 'listeDates', 'filtreDate', $this->filtreDate, $this->getListeDates(), '', '', "onChange=\"reload(this.form)\" style=\"width: 200px;\"" ) ;
    	$mod -> MxSelect ( 'listeTypes', 'type', $this->type, $this->getListeTypes(), '', '', "onChange=\"reload(this.form)\" style=\"width: 200px;\"" ) ;
    	$mod -> MxSelect ( 'listeActions', 'action', $this->action, $this->getListeActions(), '', '', "onChange=\"reload(this.form)\" style=\"width: 200px;\"" ) ;
    	// Calcul de la variable de navigation.
    	$mod -> MxHidden ( "hidden", "navi=".$session->genNaviFull ( ) ) ;
    	$this->genFiltre ( ) ;
		$this->genAffichage ( $mod ) ;
		// Récupération du code HTML généré par le template.
    	$this->af .= $mod -> MxWrite ( "1" ) ;
	}

	// Affichage des actes trouvés.
	function genAffichage ( $mod ) {
		global $pi ;
		$req = new clResultQuery ( ) ;
		$param['cw'] = $this->filtre ;
     	$res = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
		eko ( $res['INDIC_SVC'] ) ;
		for ( $i = 0 ; isset ( $res['ID'][$i] ) ; $i++ ) {
			$tabActe = array ( ) ;
			$tabActe = explode ( '|', $res['CONTENU'][$i] ) ;
			$lib = $this->getActeLibelle ( $tabActe[11], $res['TYPE'][$i] ) ;
			$libSansHTML = $this->getActeLibelle ( $tabActe[11], $res['TYPE'][$i], "1" ) ;
			if ( $lib )	$java = $pi -> genInfoBulle ( $lib ) ; else $java = '' ;
			if ( $i % 2 ) $mod -> MxText ( "listeActes.tr", "<tr class='paire' $java >" ) ;	
			else $mod -> MxText ( "listeActes.tr", "<tr class='impaire' $java >" ) ;	
			$mod -> MxText ( "listeActes.type", $res['TYPE'][$i] ) ;
			$mod -> MxText ( "listeActes.ipp", $tabActe[1] ) ;	
			$mod -> MxText ( "listeActes.dossier", $tabActe[0] ) ;	
			$mod -> MxText ( "listeActes.patient", $tabActe[2].' '.$tabActe[3].' ('.$tabActe[4].')' ) ;	
			if ( $tabActe[9] == 'suppression' ) 
				$mod -> MxText ( "listeActes.action", "<td style='background-color: red;'>".$tabActe[9]."</td>" ) ;
			else $mod -> MxText ( "listeActes.action", "<td>".$tabActe[9]."</td>" ) ;
			$mod -> MxText ( "listeActes.date", $tabActe[6].' '.$tabActe[7] ) ;	
			$mod -> MxText ( "listeActes.acte", $tabActe[11] ) ;
			$mod -> MxText ( "listeActes.lib", $lib ) ;	
			$mod -> MxText ( "listeActes.medecin", $tabActe[17] ) ;
			$mod -> MxText ( "listeActes.ADELI", $tabActe[19] ) ;
			$mod -> MxBloc ( "listeActes", "loop" ) ;
			
			$type    = $res['TYPE'][$i] ;
			$idpass  = $tabActe[0] ;
			$idu     = $tabActe[1] ;
			$nomu    = $tabActe[2] ;
			$pren    = $tabActe[3] ;
			$sexe    = $tabActe[4] ;
			$dtnai   = $tabActe[5] ;
			$dtdem   = $tabActe[6] ;
			$hhdem   = $tabActe[7] ;
			$ufd     = $tabActe[8] ;
			$action  = $tabActe[9] ;
			$idact   = $tabActe[10] ;
			$cdccam  = $tabActe[11] ;
			$cddiags = $tabActe[12] ;
			//if ( $cddiags and $cddiags!=1 and $type = 'DIAG' ) {
				$cdacti  = $tabActe[13] ;
				$cdphase = $tabActe[14] ;
				$dtr     = $tabActe[15] ;
				$hhr     = $tabActe[16] ;
				$nomumed = $tabActe[17] ;
				$prenmed = $tabActe[18] ;
				$adeli   = $tabActe[19] ;
				$ufr     = $tabActe[20] ;
				$modif   = $tabActe[21] ;
				$ngapl   = $tabActe[22] ;
				$ngapc   = $tabActe[23] ;
				$factu   = $tabActe[24] ;
				$cdasso  = $tabActe[25] ;
				$nuitjf  = $tabActe[26] ;
			/*} else {
				$cdacti  = $tabActe[13] ;
				//$cdphase = $tabActe[14] ;
				$dtr     = $tabActe[14] ;
				$hhr     = $tabActe[15] ;
				$nomumed = $tabActe[16] ;
				$prenmed = $tabActe[17] ;
				$adeli   = $tabActe[18] ;
				$ufr     = $tabActe[19] ;
				$modif   = $tabActe[20] ;
				$ngapl   = $tabActe[21] ;
				$ngapc   = $tabActe[22] ;
				$factu   = $tabActe[23] ;
				$cdasso  = $tabActe[24] ;
				$nuitjf  = $tabActe[25] ;
			}*/
			if ( $res['TYPE'][$i] == "NGAP" ) $codeActe = $ngapl." ".str_replace('.',',',$ngapc) ;
			else $codeActe = $cdccam ;

			//if ( $cddiags ) eko ( $tabActe ) ;

			$fic .= "$action\t$type\t$idu\t$idpass\t$nomu\t$pren\t$sexe\t$dtnai\t$dtdem\t$hhr\t$ufd\t" ;
			$fic .= "$codeActe\t$cddiags\t$cdasso\t$factu\t$nuitjf\t$modif\t$cdacti\t\t$nomumed\t$adeli\t$libSansHTML\n" ;
		}
		
		$mod -> MxText ( "fichierTableur", $this->creerFichier ( $tit.$fic, $this->filtreDate ) ) ;
	}
	
	// Récupération du libellé de l'acte.
	function getActeLibelle ( $code, $type, $sansHTML='' ) {
		switch ( $type ) {
			case 'CCAM':
				$req = new clResultQuery ( ) ;
				$param['table'] = "ccam_acte" ;
				$param['cw'] = "WHERE CODE='$code'" ;
     			$res = $req -> Execute ( "Fichier", "CCAM_getActeLibelle", $param, "ResultQuery" ) ;
     			if ( $sansHTML ) return "($code) ".$res['LIBELLE_COURT'][0]." : ".$res['LIBELLE_LONG'][0] ;
     			return "<b><u>($code) ".$res['LIBELLE_COURT'][0]."</u></b> :<br />".$res['LIBELLE_LONG'][0] ;
     			break ;
     		case 'NGAP':
     			$req = new clResultQuery ( ) ;
     			$param['table'] = "ccam_actes_domaine" ;
				$param['cw'] = "WHERE idActe='$code'" ;
     			$res = $req -> Execute ( "Fichier", "CCAM_getActeLibelle", $param, "ResultQuery" ) ;
     			if ( $sansHTML ) return "($code) ".$res['cotationNGAP'][0]." : ".$res['libelleActe'][0] ;
     			return "<b><u>($code) ".$res['cotationNGAP'][0]."</u></b> :<br />".$res['libelleActe'][0] ;
     			break ;
     		default:
     			break ;
		}
	}

	// Calcule le filtre MySQL.
	function genFiltre ( ) {
		$r  = " WHERE" ;
		$r .= " DTINS BETWEEN '".$this->filtreDate." 00:00:00' AND '".$this->filtreDate." 23:59:59'" ;
		$r .= " AND TYPE LIKE '".$this->type."'" ;
		$r .= " AND CONTENU LIKE '%".$this->action."%'" ;
		$this->filtre = $r ;
	}

	// Initialisation des variables.
	function initVars ( ) {
		$date = new clDate ( ) ;	
		$this->initVar ( 'filtreDate', $date->getDate ( 'Y-m-d' ) ) ;
		$this->initVar ( 'type', '%' ) ;
		$this->initVar ( 'action', '%' ) ;
	}

	// Initialise une variable.
	function initVar ( $nom, $valeur='' ) {
		if ( isset ( $_POST[$nom] ) ) $val = $_POST[$nom] ;
		elseif ( isset ( $_SESSION[$nom] ) ) $val = $_SESSION[$nom] ;
		else $val = $valeur ;
		$_SESSION[$nom] = $val ;
		eval ( '$this->'.$nom.'="'.$val.'";' ) ;
	}

	// Retourne la liste des dates.
	function getListeDates ( ) {
		$dateDeb = new clDate ( DATELANCEMENT ) ;
		$dateFin = new clDate ( ) ;
		$tDeb = $dateDeb -> getTimestamp ( ) ;
		$tab = array ( ) ;
		for ( ; $dateFin -> getTimestamp ( ) >= $tDeb ; $dateFin -> addDays ( -1 ) )
			$tab[$dateFin->getDate ( "Y-m-d")] = $dateFin -> getDate ( "d/m/Y" ) ;
		return $tab ;
	}
	
	// Retourne la liste des actions.
	function getListeActions ( ) {
		return array ( '%'=>'--', 'creation'=>'Création', 'suppression'=>'Suppression' ) ;
	}
	
	// Retourne la liste des types.
	function getListeTypes ( ) {
		return array ( '%'=>'--', 'CCAM'=>'CCAM', 'NGAP'=>'NGAP', 'DIAG'=>'DIAG' ) ;
	}
	
	function creerFichier ( $contenu='', $date='' ) {
		// eko ( $contenu ) ;
		// Calcul du nom du fichier temporaire.
      	//$nomfic = "exportActes-".$date.".xls" ;
      	$nomfic = "exportActes.xls" ;
     	// Création, écriture et fermeture du fichier.
      	$FIC = fopen ( URLCACHE.$nomfic, "w" ) ;
      	fwrite ( $FIC, $contenu ) ;
      	fclose ( $FIC ) ;
      	// Calcul du lien vers ce fichier.
      	//$mod -> MxUrl  ( "donnees.lienExport", URLCACHEWEB.$nomfic ) ;    
      	// On purge le répertoire temporaire de tous les fichiers qui ont plus de deux heures.
      	$poub = new clPoubelle ( URLCACHE ) ;
      	$poub -> purgerRepertoire ( "2" ) ;
      	return '<a href="'.URLCACHEWEB.$nomfic.'" name="Export Tableur"><img src="'.URLIMG.'tableur.jpg" alt="Icone Tableur" /></a>' ;
	}
	
	// Retourne l'affichage généré par la classe.
	function getAffichage ( ) {
		return $this->af ;
	}
}

?>
