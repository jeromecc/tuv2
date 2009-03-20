<?php
/* 

  Titre   : Classe clCCAMGestionAnesthesiste
	Auteur  : François Derock <fderock@ch-hyeres.fr>
	Date    : 23 Avril 2008
	
	Gestion de la liste des anesthésistes autorisée à pratiquer une anesthésie

*/

class clCCAMGestionAnesthesiste {

  // Attribut contenant l'affichage
  private $af;
  private $infos;
  private $erreurs;

/*****************************************************************************/
  function __construct ( ) {
/*****************************************************************************/
    
    global $session ;
    
    $this->gestionListes();
  
  }

/*****************************************************************************/
function gestionListes ( ) {
/*****************************************************************************/

  global $session ;

  $action="ccam";
  $nomForm="CCAM_GestionAnesthesiste.mxt";

  // Appel du template
  $mod =  new ModeliXe($nomForm);
  $mod -> SetModeliXe();

  $mod->MxText("titreFormulaire","Gestion de la liste des médecins autorisés à pratiquer une anesthésie");

  if ( $action == "ccam" ) {
    
    //Suppression d'un médecin de la liste des anesthésistes
    if ($_POST['aGauche_x']) { $this->infos=$this->delMedecins(); }
	
    //Ajout des médecins dans la liste des anesthésistes
    elseif ($_POST['aDroite_x']) { $this->infos=$this->addMedecins( );}
	
    //Récupération des valeurs pour Selection1 (correspond liste des specialités)
    (!$_POST['idListeSelection1'])?$idListeSelection1="aucun#":$idListeSelection1=$_POST['idListeSelection1'];
  	unset($param);
    $param[idDomaine]= "1";
    $tabListeSelection1=$this->tableauValeurs("CCAM_getListeSpe",$param,"Choisir une spécialité");
    
    //Récupération des médecins pour la liste de gauche en ignorant les valeurs de la liste de droite
    //en fonction de la famille sélectionnéee dans Selection1
    unset($paramRelation);
    unset($paramA);
    $paramRelation[idDomaine] = CCAM_IDDOMAINE;
    //$paramRelation[base]      = CCAM_BDD;
    if ($idListeSelection1 and $idListeSelection1!="aucun#") {
      $paramA[idListeSelection1] = $idListeSelection1;
      $paramA[base]              = CCAM_BDD;
		  //eko($idListeSelection1);
		  //eko($listeIdRelation);
		  $paramRelation[idListeSelection1] = $idListeSelection1;
		  //eko ($paramRelation);
		  
      // Si specialiste
      //if ( );
      if ( $_POST["idListeSelection1"] == "Urgentiste" )
        $elephant = "CCAM_getMedecinAListeGauche2";
      else if ( $_POST["idListeSelection1"] == "Toutes" )
        $elephant = "CCAM_getMedecinAListeGauche3";
      else
        $elephant = "CCAM_getMedecinAListeGauche";
        
      if ( $_POST["idListeSelection1"] == "Toutes" )
        $tabListeGauche = $this->valeursListeGauche($elephant,"CCAM_getMedecinADomaine2",
			$paramA,$paramRelation,"Choisir un médecin");
      else
        $tabListeGauche = $this->valeursListeGauche($elephant,"CCAM_getMedecinADomaine",
			$paramA,$paramRelation,"Choisir un médecin");
    }
    else
      $tabListeGauche[0] = "Choisir un médecin";
    //Récupération des médecins pour la liste des actes déjà affectés (droite)
    
    unset($param);
    $param[idDomaine]=CCAM_IDDOMAINE;
    $param[idListeSelection1]=$idListeSelection1;
    
    if ( $idListeSelection1 == "Toutes" )
      $requete = "CCAM_getMedecinADomaine2";
    else
      $requete = "CCAM_getMedecinADomaine";
    
    $tabListeDroite=$this->valeursListeDroite($requete,$param,"Choisir un médecin");
    $js="onDblClick=reload(this.form)";
    
    //Gestion du template
    $mod->MxText("titreSelection1","Famille de spécialités : ");
    $mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
		$tabListeSelection1,'','',"onChange=reload(this.form)");
    
    $mod->MxText("titreDispo","Listes des médecins disponibles");
    $mod->MxText("titreAffecte","Listes des médecins autorisés à pratiquer une anesthésie");
    $mod->MxText("commentaireGauche","");
    $mod->MxText("commentaireDroite","");
    
    //Afficher les boutons suivants si droits autorisés
    if (!$session->getDroit("CCAM_Listes","w")) {
      $mod->MxBloc("flDroite","modify"," ");
		  $mod->MxBloc("flGauche","modify"," ");
    }
        
  }
  
  // Affichage ou non du champs d'informations.
  if ($this->infos)
    $mod->MxText("informations.infos",$this->infos);
  else
    $mod->MxBloc("informations","modify"," ");
    
  // Affichage ou non du champs d'erreurs.
  if ($this->erreurs)
    $mod->MxText("erreurs.errs",$this->erreurs);
  else
    $mod->MxBloc("erreurs","modify"," ");
    
  $mod->MxSelect("listeGauche","listeGauche[]",'', $tabListeGauche,'','',
  "size=\"15\" multiple=\"yes\" class=\"selectngap\""); 
  $mod->MxSelect("listeDroite","listeDroite[]",'',$tabListeDroite,'','',
  //"size=\"15\" multiple=\"yes\" class=\"selectngap\" $js");
  "size=\"15\" multiple=\"yes\" class=\"selectngap\" ");
  
  $mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
	$session->getNavi(1)));
  $this->af.=$mod->MxWrite("1");     

}


//Suppression des médecins sélectionnés dans la liste de droite
/*****************************************************************************/
function delMedecins ( ) {
/*****************************************************************************/

  global $session ;
  
  if (is_array($_POST['listeDroite'])) {
    while (list($key,$val)=each($_POST['listeDroite'])){ 
		  if ($val and $val!="aucun#"){
        $requete = new clRequete(CCAM_BDD,"ccam_anesthesie_domaine");
        $requete->delRecord("codeMedecin='$val' AND idDomaine=".CCAM_IDDOMAINE);
      }
  }
	$retourInfos = "Les médecins sélectionnés ont été supprimés de la liste";
	return $retourInfos;
}
}

//Ajout des médecins sélectionnés dans la liste de gauche à la liste de droite
/*****************************************************************************/
function addMedecins ( ) {
/*****************************************************************************/
  
  global $session ;
  
  if (is_array($_POST['listeGauche']))  {
    while ( list($key,$val) = each($_POST['listeGauche']) ) { 
      if ( $val and $val != "aucun#" ) {
        unset($paramRq);
        
        
        // Recherche du nom du medecin
        if ( $_POST['idListeSelection1'] == "Urgentiste" ) {
          $paramRq[code]      = $val;
          $paramRq[idDomaine] = CCAM_IDDOMAINE;
          $req                = new clResultQuery;
          $res                = $req->Execute("Fichier","CCAM_getNomMedURG",$paramRq,"ResultQuery");
          $nom                = $res["nomitem"][0];
          $type               = "Urgentiste";
        }
        else if ( $_POST['idListeSelection1'] == "Toutes" ) {
          $paramRq[code]      = $val;
          $paramRq[idDomaine] = CCAM_IDDOMAINE;
          $req                = new clResultQuery;
          $res                = $req->Execute("Fichier","CCAM_getNomMedURG",$paramRq,"ResultQuery");
          $nom                = $res["nomitem"][0];
          $type               = "Urgentiste";
          
          if ( $res[INDIC_SVC]["2"] == 0 ) {
            $paramRq[code]      = $val;
            $paramRq[idDomaine] = CCAM_IDDOMAINE;
            $req                = new clResultQuery;
            $res                = $req->Execute("Fichier","CCAM_getNomMedSPEC",$paramRq,"ResultQuery");
            $nom                = $res["nomitem"][0];
            $type               = $res["nomliste"][0];
          }
        }
        else {
          $paramRq[code]      = $val;
          $paramRq[idDomaine] = CCAM_IDDOMAINE;
          $req                = new clResultQuery;
          $res                = $req->Execute("Fichier","CCAM_getNomMedSPEC",$paramRq,"ResultQuery");
          $nom                = $res["nomitem"][0];
          $type               = $res["nomliste"][0];
        }
        
        unset($param);
        $param[speMedecin]  = $type;
        $param[codeMedecin] = $val;
        $param[nomMedecin]  = addslashes($nom);
        $param[idDomaine]   = CCAM_IDDOMAINE;
        $majrq              = new clRequete(CCAM_BDD,"ccam_anesthesie_domaine",$param);
        $majrq->addRecord();
      }
    }
    
    $retourInfos = "Les médecins sélectionnés ont été insérés dans la liste des médecins autorisés à pratiquer une anesthésie";
    return $retourInfos;
  }
    
}

//Fabrication d'une liste de valeurs à partir d'une requête
/*****************************************************************************/
function tableauValeurs($requete,$param="",$lignePresentation=""){
/*****************************************************************************/
  
  
  
  $req = new clResultQuery;
  $res = $req->Execute("Fichier",$requete,$param,"ResultQuery");
  //newfct(gen_affiche_tableau,$res[INDIC_SVC]);
  $tab["aucun#"] = $lignePresentation;
  
  //eko ("coucou tableauValeurs");
  // Récupération de la liste de valeurs
  //eko($requete);
  //eko($param);
  //eko($res);
  
  if ( $requete == "CCAM_getListeSpe" ) {
    $tab["Toutes"]      = "Toutes Spécialités";
    $tab["Urgentiste"]  = "Médecin Urgentiste";
  }
  
  for ( $i=0;isset($res[identifiant][$i]);$i++  ) { 
    
    $libelle            = strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
		$identifiant        = $res[identifiant][$i];
		$tab[$identifiant]  = $res[identifiant][$i];
		
    if ( $libelle != "" ) {
			$tab[$identifiant] .= " - ".ucfirst(strtolower($libelle));
		}
		
  }

  return $tab;
  
}

//Fabrication d'une liste de valeurs à partir d'une requête
/*****************************************************************************/
function valeursListeDroite ($requete,$param="",$lignePresentation="") {
/*****************************************************************************/
  
  //eko ("valeursListeDroite");
  // Récupération de la liste de valeurs
  $req           = new clResultQuery;
  
  $res = $req->Execute("Fichier",$requete,$param,"ResultQuery");
  //eko($res);
  //eko($requete);
  $tab["aucun#"] = $lignePresentation;
  
  for ( $i=0;isset($res[identifiant][$i]);$i++ ) { 
    $libelle           = strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
		$identifiant       = $res[identifiant][$i];
    $tab[$identifiant].= $res[identifiant][$i];
    $tab[$identifiant].= " - ".ucfirst(strtolower($libelle));
  }
  
  return $tab;

}

//Fabrication d'une liste de valeurs pour la liste de gauche
//en ignorant les valeurs présentes dans la liste de droite
/*****************************************************************************/
function valeursListeGauche ($requeteTableA,$requeteTableRelation,$paramA="",$paramRelation="",
	$lignePresentation="") {
/*****************************************************************************/
  
  //eko ("valeursListeGauche");
  //Récupération des lignes figurant dans la liste de droite
  $req             = new clResultQuery;
  $res             = $req->Execute("Fichier",$requeteTableRelation,$paramRelation,"ResultQuery");
  //newfct(gen_affiche_tableau,$res[INDIC_SVC]);
  //eko($requeteTableRelation);
  //eko($paramRelation);
  //eko($res);
  $listeIdRelation = "";
  
  //eko($res);
  
  for ( $i=0;isset($res[identifiant][$i]);$i++ ) { 
    $tabRelation[$res[identifiant][$i]] = $res[identifiant][$i];
    $listeIdRelation                   .= "'".$res[identifiant][$i]."',";
  }
  ($listeIdRelation=="")?$listeIdRelation="''":$listeIdRelation=substr($listeIdRelation,0,-1);
  //echo "listeIdRelation:$listeIdRelation<br>";
  
  // Récupération de la liste de valeurs pour la liste de gauche
  $paramA[listeIdRelation] = $listeIdRelation;
  
  $tab = $this->tableauValeurs($requeteTableA,$paramA,$lignePresentation);
  
  return $tab;

}

// Retourne l'affichage de la classe.
/*****************************************************************************/
function getAffichage ( ) {
/*****************************************************************************/
  
  return $this->af ;

}

}
