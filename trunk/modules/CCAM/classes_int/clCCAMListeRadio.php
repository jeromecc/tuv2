<?php
/* Titre  : Classe ListeRestreinte
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 04 mars 2005

	Description : Affectation/Désaffectation des actes CCAM pour le domaine en cours
	Les actes disponibles sont affichés après avoir sélectionné au préalable une famille
	d'actes issue de la nomenclature CCAM
	La classe permet également de créer des actes non CCAM et de les affecter/désaffecter
	pour le domaine en cours
	Elle gère aussi la correspondance NGAP pour chaque acte de laliste restreinte
*/
class clCCAMlisteRadio{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct(){
global $session ;
$this->gestionListes();
}

// Gestion des listes restreintes
function gestionListes(){
global $session ;
(!$_POST['action'])?$action="ccam":$action=$_POST['action'];
($action=="affectation_ngap")?$nomForm="CCAM_AffectationNGAP.mxt":
	$nomForm="CCAM_ListeRestreinte.mxt";
// Appel du template
$mod=new ModeliXe($nomForm);
$mod->SetModeliXe();

$mod->MxText("titreFormulaire","Gestion de la liste restreinte");

//Liste des actions
$tabAction[ccam]="Actes CCAM";
$tabAction[non_ccam]="Actes non CCAM";
$tabAction[affectation_ngap]="Affectation NGAP";
while (list($key,$val)=each($tabAction)){
	$mod->MxCheckerField("action.action","radio","action",$key,
		(($action==$key)?true:false),"onClick=reload(this.form)");
	$mod->MxText("action.libAction",$val);
	$mod->MxBloc("action","loop");
}

$js="";
if ($action=="ccam"){
	//Suppression des actes sélectionnés
	if ($_POST['aGauche_x']) $this->infos=$this->delActes();
	//Ajout des actes sélectionnés dans la liste resteinte
	elseif ($_POST['aDroite_x']) $this->infos=$this->addActes("CCAM_get1ActeCCAM");
	//Modification de l'acte sélectionné
	elseif ($_POST['OK_x']){
		$retourInfos=$this->modifyActe();
		if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
		elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
	}
	//Si on a choisi de modifier un acte de la liste restreinte
	elseif ($_POST['modifierActe_x']){
		//Récupération des infos pour le dernier acte sélectionné dans la liste
		$paramRq[idActe]=$this->getDerIdSel($_POST['listeDroite']);
		if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
			$paramRq[cw]="and idDomaine=2";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			
			$cotationNGAP=$this->explodeNGAP($res[cotationNGAP][0]);
			
			//Gestion du sous-template
			$paramForm[titreEnCours]="Modification d'un acte";
			$paramForm[codeNGAP]=$paramRq[idActe];
			$paramForm[libActe]=$res[libelle][0];
			
			$paramForm[LC1]=$cotationNGAP[LC1];
			$paramForm[coeff1]=$cotationNGAP[coeff1];
			$paramForm[LC2]=$cotationNGAP[LC2];
			$paramForm[coeff2]=$cotationNGAP[coeff2];
			$paramForm[LC3]=$cotationNGAP[LC3];
			$paramForm[coeff3]=$cotationNGAP[coeff3];
			$paramForm[coeffKARE]=$cotationNGAP[coeffKARE];
			
			$paramForm[action]="ccam";
			$paramForm[action2]="modifier";
			$mod->MxText("form1Acte",$this->getForm1Acte($paramForm));
		}
	}
	else{
		//Récupération des infos pour le dernier acte sélectionné dans la liste
		$paramRq[idActe]=$this->getDerIdSel($_POST['listeDroite']);
		if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
			$paramRq[cw]="and idDomaine=2";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			
			$cotationNGAP=$this->explodeNGAP($res[cotationNGAP][0]);
			
			//Gestion du sous-template
			$paramForm[titreEnCours]="Modification d'un acte";
			$paramForm[codeNGAP]=$paramRq[idActe];
			$paramForm[libActe]=$res[libelle][0];
			
			$paramForm[LC1]=$cotationNGAP[LC1];
			$paramForm[coeff1]=$cotationNGAP[coeff1];
			$paramForm[LC2]=$cotationNGAP[LC2];
			$paramForm[coeff2]=$cotationNGAP[coeff2];
			$paramForm[LC3]=$cotationNGAP[LC3];
			$paramForm[coeff3]=$cotationNGAP[coeff3];
			$paramForm[coeffKARE]=$cotationNGAP[coeffKARE];
			
			$paramForm[action]="ccam";
			$paramForm[action2]="modifier";
			$mod->MxText("form1Acte",$this->getForm1Acte($paramForm));
		}
	}
	
	//Récupération des valeurs pour Selection1
	(!$_POST['idListeSelection1'])?$idListeSelection1="aucun#":
		$idListeSelection1=$_POST['idListeSelection1'];
	unset($param);
	$param[cw]="";
	$tabListeSelection1=$this->tableauValeurs("CCAM_getFamilles",$param,"Choisir une famille");
	
	//Récupération des actes pour la liste de gauche en ignorant les valeurs de la liste de droite
	//en fonction de la famille sélectionnéee dans Selection1
	unset($paramRelation);
	unset($paramA);
	$paramRelation[idDomaine]=2;
	if ($idListeSelection1 and $idListeSelection1!="aucun#"){
		$paramA[idListeSelection1]=$idListeSelection1;
		eko($idListeSelection1);
		eko($listeIdRelation);
		$paramRelation[idListeSelection1]=$idListeSelection1;
		$tabListeGauche=$this->valeursListeGauche("CCAM_getActesListeGauche","CCAM_getActesDomaine",
			$paramA,$paramRelation,"Choisir un acte");
	}
	else $tabListeGauche[0]="Choisir un acte";
	
	//Récupération des actes pour la liste des actes déjà affectés
	unset($param);
	$param[idDomaine]=2;
	$param[idListeSelection1]=$idListeSelection1;
	$tabListeDroite=$this->valeursListeDroite("CCAM_getActesDomaine",$param,"Choisir un acte");
	$js="onDblClick=reload(this.form)";
	
	//Gestion du template
	$mod->MxText("titreSelection1","Famille d'actes : ");
	$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
		$tabListeSelection1,'','',"onChange=reload(this.form)"); 
	
	$mod->MxText("titreDispo","Actes CCAM disponibles");
	$mod->MxText("titreAffecte","Actes CCAM affectés");
	$mod->MxText("commentaireGauche","");
	$mod->MxText("commentaireDroite","<font color=\"red\">Les actes en rouge ne sont plus valides par rapport 
    à la ".CCAM_VERSION."</font>");
	
	//Afficher les boutons suivants si droits autorisés
	if (!$session->getDroit("CCAM_Listes","w")){
		$mod->MxBloc("flDroite","modify"," ");
		$mod->MxBloc("flGauche","modify"," ");
		$mod->MxBloc("btnModifier","modify"," ");
	}
	(count($tabListeDroite)<=1)?$mod->MxBloc("btnModifier","modify"," "):"";
	
	//Ne jamais afficher les boutons suivants
	$mod->MxBloc("btnAjouter","modify"," ");
	$mod->MxBloc("btnSupprimer","modify"," ");
	$mod->MxBloc("btnAjouterSelection1","modify"," ");
	$mod->MxBloc("btnModifierSelection1","modify"," ");
	$mod->MxBloc("btnSupprimerSelection1","modify"," ");
}
elseif ($action=="non_ccam"){
	 //Suppression d'un acte non CCAM de la liste restreinte
	 if ($_POST['supprimerActe_x']) $this->infos=$this->delActes();
	 //Ajout des actes non CCAM provenant d'autres domaines dans la liste restreinte
	 elseif ($_POST['aDroite_x']) $this->infos=$this->addActes("CCAM_get1Acte");
	 //Validation du nouvel acte non CCAM ou modification d'un acte non CCAM
	 elseif ($_POST['OK_x']){
	 	if ($_POST['action2']=="creer") $retourInfos=$this->addNvxActe();
		elseif ($_POST['action2']=="modifier") $retourInfos=$this->modifyActe();
		
		if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
		elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
	}
	//Si on a choisi de créer un nouvel acte non CCAM
	elseif ($_POST['ajouterActe_x']){
		//Gestion du sous-template
		$paramForm[titreEnCours]="Saisie d'un nouvel acte";
		$paramForm[codeNGAP]="sera calculé à l'insertion";
		$paramForm[libActe]="";
		
		$paramForm[LC1]="aucun#";
		$paramForm[coeff1]="";
		$paramForm[LC2]="aucun#";
		$paramForm[coeff2]="";
		$paramForm[LC3]="aucun#";
		$paramForm[coeff3]="";
		$paramForm[coeffKARE]="";
		
		$paramForm[action2]="creer";
		$mod->MxText("form1Acte",$this->getForm1Acte($paramForm));
	}
	//Si on a choisi de modifier un acte non CCAM
	elseif ($_POST['modifierActe_x']){
		//Récupération des infos pour le dernier acte sélectionné dans la liste
		$paramRq[idActe]=$this->getDerIdSel($_POST['listeDroite']);
		if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
			$paramRq[cw]="and idDomaine=2";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			
			$cotationNGAP=$this->explodeNGAP($res[cotationNGAP][0]);
			
			//Gestion du sous-template
			$paramForm[titreEnCours]="Modification d'un acte";
			$paramForm[codeNGAP]=$paramRq[idActe];
			$paramForm[libActe]=$res[libelle][0];
			
			$paramForm[LC1]=$cotationNGAP[LC1];
			$paramForm[coeff1]=$cotationNGAP[coeff1];
			$paramForm[LC2]=$cotationNGAP[LC2];
			$paramForm[coeff2]=$cotationNGAP[coeff2];
			$paramForm[LC3]=$cotationNGAP[LC3];
			$paramForm[coeff3]=$cotationNGAP[coeff3];
			$paramForm[coeffKARE]=$cotationNGAP[coeffKARE];
			
			$paramForm[action2]="modifier";
			$mod->MxText("form1Acte",$this->getForm1Acte($paramForm));
		}
	}
	else{
	//Récupération des infos pour le dernier acte sélectionné dans la liste
		$paramRq[idActe]=$this->getDerIdSel($_POST['listeDroite']);
		if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
			$paramRq[cw]="and idDomaine=2";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			
			$cotationNGAP=$this->explodeNGAP($res[cotationNGAP][0]);
			
			//Gestion du sous-template
			$paramForm[titreEnCours]="Modification d'un acte";
			$paramForm[codeNGAP]=$paramRq[idActe];
			$paramForm[libActe]=$res[libelle][0];
			
			$paramForm[LC1]=$cotationNGAP[LC1];
			$paramForm[coeff1]=$cotationNGAP[coeff1];
			$paramForm[LC2]=$cotationNGAP[LC2];
			$paramForm[coeff2]=$cotationNGAP[coeff2];
			$paramForm[LC3]=$cotationNGAP[LC3];
			$paramForm[coeff3]=$cotationNGAP[coeff3];
			$paramForm[coeffKARE]=$cotationNGAP[coeffKARE];
			
			$paramForm[action2]="modifier";
			$mod->MxText("form1Acte",$this->getForm1Acte($paramForm));
		}
	}
	//Récupération des actes non CCAM des autres domaines non présents dans la liste de droite
	unset($paramRelation);
	unset($paramA);
	$paramRelation[idDomaine]=2;
	$paramRelation[typeCode]="NGAP";
	$paramA[idDomaine]=2;
	$paramA[typeCode]="NGAP";
	$tabListeGauche=$this->valeursListeGauche("CCAM_getActesAutresDomaines","CCAM_getActesNonCCAM",
		$paramA,$paramRelation,"Choisir un acte");
	
	//Récupération des actes non CCAM du domaine
	unset($param);
	$param[idDomaine]=2;
	$param[typeCode]="NGAP";
	$tabListeDroite=$this->valeursListeDroite("CCAM_getActesNonCCAM",$param,"Choisir un acte");
	$js="onDblClick=reload(this.form)";
	 
	//Gestion du template
	$mod->MxText("titreDispo","Actes disponibles (en provenance d'autres domaines)");
	$mod->MxText("titreAffecte","Actes du domaine");
	$mod->MxText("commentaireGauche","");
	$mod->MxText("commentaireDroite","* Actes non côtés NGAP");
	
	//Afficher les boutons suivants si droits autorisés
	if (!$session->getDroit("CCAM_Listes","w")){
		$mod->MxBloc("btnAjouter","modify"," ");
		$mod->MxBloc("btnModifier","modify"," ");
		$mod->MxBloc("btnSupprimer","modify"," ");
		$mod->MxBloc("flDroite","modify"," ");
	}
	//Ne jamais afficher les boutons suivants
	$mod->MxBloc("flGauche","modify"," ");
	$mod->MxBloc("btnAjouterSelection1","modify"," ");
	$mod->MxBloc("btnModifierSelection1","modify"," ");
	$mod->MxBloc("btnSupprimerSelection1","modify"," ");
}
elseif ($action=="affectation_ngap"){
	//RAZ de la cotation NGAP pour les actes sélectionnés
	if ($_POST['aGauche_x']) $this->infos=$this->RazNGAP();
	
	//Cotation NGAP des actes sélectionnés dans la liste resteinte
	if ($_POST['egal_x']){
		$retourInfos=$this->addNGAP();
		if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
		elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
	}
	
	//Récupération des actes non côtés NGAP
	unset($param);
	$param[idDomaine]=2;
	$tabListeGauche=$this->tableauValeurs("CCAM_getNGAPVide",$param,"Choisir un acte");
	
	//Récupération des actes non côtés NGAP
	unset($param);
	$param[idDomaine]=2;
	$tabListeDroite=$this->tableauValeurs("CCAM_getNGAP",$param,"Choisir un acte");
	
	//Gestion du template
	($_POST['LC1'])?$paramForm[LC1]=$_POST['LC1']:$paramForm[LC1]="aucun#";
	($_POST['coeff1'])?$paramForm[coeff1]=$_POST['coeff1']:$paramForm[coeff1]="";
	($_POST['LC2'])?$paramForm[LC2]=$_POST['LC2']:$paramForm[LC2]="aucun#";
	($_POST['coeff2'])?$paramForm[coeff2]=$_POST['coeff2']:$paramForm[coeff2]="";
	($_POST['LC3'])?$paramForm[LC3]=$_POST['LC3']:$paramForm[LC3]="aucun#";
	($_POST['coeff3'])?$paramForm[coeff3]=$_POST['coeff3']:$paramForm[coeff3]="";
	($_POST['coeffKARE'])?$paramForm[coeffKARE]=$_POST['coeffKARE']:$paramForm[coeffKARE]="";
	$mod->MxText("formNGAP",$this->getFormNGAP($paramForm));
	
	$mod->MxText("titreDispo","Actes non côtés NGAP");
	$mod->MxText("titreAffecte","Actes côtés NGAP");
	$mod->MxText("commentaireGauche","");
	$mod->MxText("commentaireDroite","");
	$mod->MxImage("plus",URLIMG."Plus.gif");
	
	//Afficher les boutons suivants
	if (!$session->getDroit("CCAM_Listes","w")){
		$mod->MxBloc("egal","modify"," ");
		$mod->MxBloc("flGauche","modify"," ");
	}
}

// Affichage ou non du champs d'informations.
if ($this->infos) $mod->MxText("informations.infos",$this->infos);
else $mod->MxBloc("informations","modify"," ");

// Affichage ou non du champs d'erreurs.
if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
else $mod->MxBloc("erreurs","modify"," ");

$mod->MxSelect("listeGauche","listeGauche[]",'', $tabListeGauche,'','',
		"size=\"15\" multiple=\"yes\" class=\"selectngap\""); 
$mod->MxSelect("listeDroite","listeDroite[]",'',$tabListeDroite,'','',
	"size=\"15\" multiple=\"yes\" class=\"selectngap\" $js");

$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
	$session->getNavi(1)));

$this->af.=$mod->MxWrite("1");     
}

//Suppression des actes sélectionnés dans la liste de droite=======================================
function delActes(){
global $session ;
if (is_array($_POST['listeDroite'])){
	while (list($key,$val)=each($_POST['listeDroite'])){ 
		if ($val and $val!="aucun#"){
			$requete = new clRequete(CCAM_BDD,"ccam_actes_domaine");
			$requete->delRecord("idActe='$val' AND idDomaine=2");
		}
	}
	$retourInfos="Les actes sélectionnés ont été supprimés de la liste restreinte";
	return $retourInfos;
}
}

//Ajout des actes sélectionnés dans la liste de gauche à la liste de droite
function addActes($requete){
global $session ;
if (is_array($_POST['listeGauche'])){
	while (list($key,$val)=each($_POST['listeGauche'])){ 
		if ($val and $val!="aucun#"){
			unset($paramRq);
			$paramRq[idActe]=$val;
			$paramRq[cw]="";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier",$requete,$paramRq,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			unset($param);
			$param[idActe]=$val;
			$param[idDomaine]=2;
			$param[libelleActe]=addslashes($res[libelle][0]);
			$majrq=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
			$majrq->addRecord();
		}
	}
	$retourInfos="Les actes sélectionnés ont été insérés dans la liste restreinte";
	return $retourInfos;
}
}

//Ajout de l'acte créé manuellement dans la table ccam_actes_domaine
function addNvxActe(){
global $session ;
//Concaténation des correspondances NGAP
$cotationNGAP=$this->getCotationNGAP();
unset($param);
$param[idActe]=$this->getIdSuiv("NGAP");
if ($cotationNGAP){
	$param[libelleActe]=$_POST['libActe'];
	$param[idDomaine]=2;
	($cotationNGAP=="aucun#")?$cotationNGAP="":"";
	$param[cotationNGAP]=$cotationNGAP;
	$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
	$requete->addRecord();
	
	$retourInfos[infos]="L'acte '".$param[idActe]."' a été inséré dans la liste restreinte";
}
else $retourInfos[erreur]="Insertion de l'acte '".$param[idActe]."' : 
	Les coefficients saisis ne sont pas numériques ; l'insertion a été annulée";
return $retourInfos;
}

//Modification de l'acte sélectionné dans la table ccam_actes_domaine
function modifyActe(){
global $session;
unset($retourInfos);
$cotationNGAP=$this->getCotationNGAP();
if ($cotationNGAP){
	unset($param);
	$param[idActe]=$_POST['nvxCode'];
	$param[libelleActe]=$_POST['libActe'];
	($cotationNGAP=="aucun#")?$cotationNGAP="":"";
	$param[cotationNGAP]=$cotationNGAP;
	$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
	$requete->updRecord("idActe='$param[idActe]' AND idDomaine=2");
	
	$retourInfos[infos]="L'acte '".$_POST['nvxCode']."' a été modifié";
}
else $retourInfos[erreur]="Modification de l'acte '".$_POST['nvxCode']."' : 
	Les coefficients saisis ne sont pas numériques ; la modification a été annulée";

return $retourInfos;
}

//Fabrication d'une liste de valeurs à partir d'une requête
function tableauValeurs($requete,$param="",$lignePresentation=""){
// Récupération de la liste de valeurs
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requete,$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
$tab["aucun#"]=$lignePresentation;
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$libelle=strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
	if ($res[title][$i]==""){
		$tab[$res[identifiant][$i]]=$res[identifiant][$i];
		if ($res[cotationNGAP][$i]!=""){
			$tab[$res[identifiant][$i]].=" - ".$res[cotationNGAP][$i];
		}
		if ($libelle!=""){
			$tab[$res[identifiant][$i]].=" - ".ucfirst(strtolower($libelle));
		}
	}
	else{
		$title=strtr($res[title][$i],"ÉÈÊÀ","éèêà");
		$identifiant=$res[identifiant][$i]."\" onmouseover=\"window.status='Voir cet événement'; 
			show(event, 'id".$res[identifiant][$i]."'); return true;\" 
			onmouseout=\"hide('id".$res[identifiant][$i]."'); return true;\"";
		$tab[$identifiant]=$res[identifiant][$i];
		if ($res[cotationNGAP][$i]!=""){
			$tab[$identifiant].=" - ".$res[cotationNGAP][$i];
		}
		if ($libelle!=""){
			$tab[$identifiant].=" - ".ucfirst(strtolower($libelle));
		}
		$mod = new ModeliXe ( "CCAM_InfoBulleActes.mxt" ) ;
		$mod -> SetModeliXe ( ) ;
		$mod -> MxText ( "iddiv", "id".$res[identifiant][$i] ) ;
		$mod -> MxText ( "libelleActe", $libelle ) ;
		// Récupération du code HTML généré.
		$this->af .= $mod -> MxWrite ( "1" ) ;
	}
}
return $tab;
}

//Fabrication d'une liste de valeurs à partir d'une requête
function valeursListeDroite($requete,$param="",$lignePresentation=""){
// Récupération de la liste de valeurs
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requete,$param,"ResultQuery");
//eko($res["INDIC_SVC"]);
$tab["aucun#"]=$lignePresentation;
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$libelle=strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
	if ($res[title][$i]==""){
		if ($res[cotationNGAPvide][$i]=="" and substr($res[identifiant][$i],0,4)=="NGAP"){
			$tab[$res[identifiant][$i]].="*";
		}
		$tab[$res[identifiant][$i]].=$res[identifiant][$i];
		if ($res[cotationNGAP][$i]!=""){
			$tab[$res[identifiant][$i]].=" - ".$res[cotationNGAP][$i];
		}
		if ($libelle!=""){
			$tab[$res[identifiant][$i]].=" - ".ucfirst(strtolower($libelle));
		}
	}
	else{
		$title=strtr($res[title][$i],"ÉÈÊÀ","éèêà");
		if ($res["date_fin"][$i]!="0000-00-00" and substr($res["identifiant"][$i],0,4)!="NGAP")
      $style="style=\"color:red;\"";
    else $style="";
		$identifiant=$res[identifiant][$i]."\"$style onmouseover=\"window.status='Voir cet événement'; 
			show(event, 'id".$res[identifiant][$i]."'); return true;\" 
			onmouseout=\"hide('id".$res[identifiant][$i]."'); return true;\"";
		if ($res[cotationNGAPvide][$i]=="" and substr($res[identifiant][$i],0,4)=="NGAP"){
			$tab[$identifiant].="*";
		}
		$tab[$identifiant].=$res[identifiant][$i];
		if ($res[cotationNGAP][$i]!=""){
			$tab[$identifiant].=" - ".$res[cotationNGAP][$i];
		}
		if ($libelle!=""){
			$tab[$identifiant].=" - ".ucfirst(strtolower($libelle));
		}
		$mod = new ModeliXe ( "CCAM_InfoBulleActes.mxt" ) ;
		$mod -> SetModeliXe ( ) ;
		$mod -> MxText ( "iddiv", "id".$res[identifiant][$i] ) ;
		$mod -> MxText ( "libelleActe", $libelle ) ;
		// Récupération du code HTML généré.
		$this->af .= $mod -> MxWrite ( "1" ) ;
	}
}
return $tab;
}

//Fabrication d'une liste de valeurs pour la liste de gauche
//en ignorant les valeurs présentes dans la liste de droite
function valeursListeGauche($requeteTableA,$requeteTableRelation,$paramA="",$paramRelation="",
	$lignePresentation=""){
//Récupération des lignes figurant dans la liste de droite
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requeteTableRelation,$paramRelation,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
$listeIdRelation="";
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$tabRelation[$res[identifiant][$i]]=$res[identifiant][$i];
	$listeIdRelation.="'".$res[identifiant][$i]."',";
}
($listeIdRelation=="")?$listeIdRelation="''":$listeIdRelation=substr($listeIdRelation,0,-1);
//echo "listeIdRelation:$listeIdRelation<br>";

// Récupération de la liste de valeurs pour la liste de gauche
$paramA[listeIdRelation]=$listeIdRelation;
$tab=$this->tableauValeurs($requeteTableA,$paramA,$lignePresentation);
return $tab;
}

function getIdSuiv($typeCode){
//Retourne la valeur suivante du plus grand code commençant par NGPA... ou PACK...
$req=new clResultQuery;
$param[typeCode]=$typeCode;
$res=$req->Execute("Fichier","CCAM_getIdSuiv",$param,"ResultQuery");
$maxCodeRq=$res[maxCode][0];
if ($maxCodeRq!=""){
	$derniers=substr($maxCodeRq,4,3);
	if (substr($derniers,0,2)=="00") $maxCode=substr($derniers,2,1)+1;
	elseif (substr($derniers,0,1)=="0") $maxCode=substr($derniers,1,2)+1;
	else $maxCode=substr($derniers,0,3)+1;
	
	if ($maxCode<10) $maxCode="00".$maxCode;
	elseif ($maxCode<100) $maxCode="0".$maxCode;
	$maxCode=$typeCode.$maxCode;
}
else $maxCode=$typeCode."001";
return $maxCode;
}

//Gère le template relatif à la saisie ou la modif d'un acte de la liste restreinte
function getForm1Acte($paramForm){
global $session;
$mod=new ModeliXe("CCAM_Form1Acte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreEnCours",$paramForm[titreEnCours]);

$codeNGAP=$paramForm[codeNGAP];
$mod->MxText("titreCodeActe","Code de l'acte : ");
$mod->MxText("codeActe",$codeNGAP);

$mod->MxText("titreLibActe","Libellé de l'acte : ");
$mod->MxFormField("libActe","textarea","libActe",$paramForm[libActe],
	"rows=\"3\" cols=\"50\"	wrap=\"virtual\"");

//Inclure le formulaire NGAP
if ($paramForm[action]=="ccam")
	$mod->MxBloc("NGAP","modify"," ");
else
	$mod->MxText("NGAP.formNGAP",$this->getFormNGAP($paramForm));

//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Listes","w")){
	$mod->MxBloc("validerActe","modify"," ");
	$mod->MxBloc("annulerActe","modify"," ");
}
$mod->MxHidden("hidden2","nvxCode=$codeNGAP&action2=$paramForm[action2]");
$af=$mod->MxWrite("1");
return $af;
}

//Gère le template relatif à la nomenclature NGAP
function getFormNGAP($paramForm){
global $session;
$mod=new ModeliXe("CCAM_FormNGAP.mxt");
$mod->SetModeliXe();

unset($param);
$param[nomListe]="Lettres-clé NGAP";
$param[idDomaine]=2;
$tabLC=$this->tableauValeurs("CCAM_getListeLC",$param,"");

$mod->MxSelect("LC1","LC1",$paramForm[LC1],$tabLC,'','',"class=\"select2\""); 
$mod->MxFormField("coeff1","text","coeff1",$paramForm[coeff1],"size=\"3\"");

$mod->MxSelect("LC2","LC2",$paramForm[LC2],$tabLC,'','',"class=\"select2\""); 
$mod->MxFormField("coeff2","text","coeff2",$paramForm[coeff2],"size=\"3\"");

$mod->MxSelect("LC3","LC3",$paramForm[LC3],$tabLC,'','',"class=\"select2\""); 
$mod->MxFormField("coeff3","text","coeff3",$paramForm[coeff3],"size=\"3\"");

$mod->MxFormField("coeffKARE","text","coeffKARE",$paramForm[coeffKARE],"size=\"3\"");

$af=$mod->MxWrite("1");
return $af;
}

function getDerIdSel($listeItem){
//Renvoit que le dernier item sélectionné dans une liste
global $session ;
if (is_array($listeItem)){
	while (list($key,$val)=each($listeItem)){ 
		$idModify=$val;
	}
}
return $idModify;
}

function getCotationNGAP(){
//Renvoit une chaine représentant la concaténation des variables de saisie NGAP
$cotationNGAP="";
$err=0;
if ($_POST['LC1']!="" and $_POST['LC1']!="aucun#" and $_POST['coeff1']!=""){
	$coeff1=strtr($_POST['coeff1'],",",".");
	if (is_numeric($coeff1)) $cotationNGAP.=$_POST['LC1']." ".$coeff1;
	else $err=1;
}
if ($_POST['LC2']!="" and $_POST['LC2']!="aucun#" and $_POST['coeff2']!=""){
	$coeff2=strtr($_POST['coeff2'],",",".");
	if (is_numeric($coeff2)) $cotationNGAP.="+".$_POST['LC2']." ".$coeff2;
	else $err=1;
}
if ($_POST['LC3']!="" and $_POST['LC3']!="aucun#" and $_POST['coeff3']!=""){
	$coeff3=strtr($_POST['coeff3'],",",".");
	if (is_numeric($coeff3)) $cotationNGAP.="+".$_POST['LC3']." ".$coeff3;
	else $err=1;
}
if ($_POST['coeffKARE']!=""){
	$coeffKARE=strtr($_POST['coeffKARE'],",",".");
	if (is_numeric($coeffKARE)) $cotationNGAP.="+KARE ".$coeffKARE;
	else $err=1;
}

if ($err==0){
	(substr($cotationNGAP,0,1)=="+")?$cotationNGAP=substr($cotationNGAP,1):"";
	($cotationNGAP=="")?$cotationNGAP="aucun#":"";
	return $cotationNGAP;
}
}

function explodeNGAP($chaineNGAP){
//Renvoit un tableau contenant la chaîne NGAP explodée
unset($cotationNGAP);
$cotation=explode("+",$chaineNGAP);
$i=1;
//Récupération des valeurs de la chaîne
while (list($key,$val)=each($cotation)){
	$item=explode(" ",$val);
	$LC="LC".$i;
	$coeff="coeff".$i;
	if ($item[0]=="KARE"){
		$cotationNGAP[coeffKARE]=$item[1];
		$i--;
	}
	else{
		$cotationNGAP[$LC]=$item[0];
		$cotationNGAP[$coeff]=$item[1];
	}
	
	$i++;
}
//Initialisation pour les valeurs restantes
for ($j=$i;$j<4;$j++){
	$LC="LC".$j;
	$coeff="coeff".$j;
	$cotationNGAP[$LC]="aucun#";
	$cotationNGAP[$coeff]="";
}
return $cotationNGAP;
}

function RazNGAP(){
//Remet à zéro (à blanc) la cotation NGAP des actes sélectionnés dans la liste restreinte
if (is_array($_POST['listeDroite'])){
	while (list($key,$val)=each($_POST['listeDroite'])){ 
		if ($val and $val!="aucun#"){
			unset($param);
			$param[cotationNGAP]="";
			$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
			$requete->updRecord("idActe='$val' AND idDomaine=2");
		}
	}
	$retourInfos="La cotation NGAP des actes sélectionnés a été remise à zéro";
	return $retourInfos;
}
}

function AddNGAP(){
//Affectation de la cotation NGAP aux actes sélectionnés dans la liste de gauche
if (is_array($_POST['listeGauche'])){
	unset($retourInfos);
	$cotationNGAP=$this->getCotationNGAP();
	if ($cotationNGAP){
		while (list($key,$val)=each($_POST['listeGauche'])){ 
			if ($val and $val!="aucun#"){
				if ($cotationNGAP!="aucun#"){
					unset($param);
					$param[cotationNGAP]=$cotationNGAP;
					$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
					$requete->updRecord("idActe='$val' AND idDomaine=2");
					$retourInfos[infos]="Les actes sélectionnés ont désormais une cotation NGAP égale à $cotationNGAP";
				}
				else $retourInfos[erreur]="Cotation des actes sélectionnés : La 1ère lettre et le 1er coefficient doivent être 
					renseignés ; la modification a été annulée";
			}
		}
		
	}
	else $retourInfos[erreur]="Cotation des actes sélectionnés : 
		Les coefficients saisis ne sont pas numériques ; la modification a été annulée";
	return $retourInfos;
}
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
