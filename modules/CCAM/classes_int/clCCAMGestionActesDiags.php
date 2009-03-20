<?php
/* Titre  : Classe ListeRestreinte
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 04 mars 2005

	Description : Création, modification, suppression de parties anatomiques
	Affectation/Désaffectation des actes de la liste restreinte pour la partie anatomique en cours
*/
class clCCAMGestionActesDiags{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct(){
global $session ;
$this->gestionActesDiags();
}

// Gestion des parties anatomiques
function gestionActesDiags(){
global $session ;
/*(!$_POST['action'])?$action="ccam":$action=$_POST['action'];
($action=="affectation_ngap")?$nomForm="CCAM_AffectationNGAP.mxt":
	$nomForm="CCAM_ListeRestreinte.mxt";*/
// Appel du template
$mod=new ModeliXe("CCAM_ListeRestreinte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreFormulaire","Association des actes aux diagnostics");

$mod->MxBloc("action","delete");

//Initialisation des valeurs
(!$_POST['idListeSelection0'])?$idListeSelection0="aucun#":
	$idListeSelection0=$_POST['idListeSelection0'];
(!$_POST['idListeSelection1'])?$idListeSelection1="aucun#":
	$idListeSelection1=$_POST['idListeSelection1'];
(!$_POST['idListeSelection2'])?$idListeSelection2="tous":
	$idListeSelection2=$_POST['idListeSelection2'];

//Suppression des actes sélectionnés dans la liste des actes affectés à la partie anatomique en cours
if ($_POST['aGauche_x']) $this->infos=$this->delActesDiags();

//Ajout des actes sélectionnés dans la liste des actes affectés à la partie anatomique sélectionné
if ($_POST['aDroite_x']) $this->infos=$this->addActesDiags();

/*//Validation d'une nouvelle partie anatomique ou modification, suppression d'une partie anatomique
if ($_POST['OK_x']){
 	if ($_POST['action2']=="creer") $retourInfos=$this->addNvxDiags();
	elseif ($_POST['action2']=="modifier") $retourInfos=$this->modifyDiags();
	elseif ($_POST['action2']=="supprimer") $retourInfos=$this->delDiags();
	
	if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
	elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
}

//Si on a choisi de créer une nouvelle partie anatomique
if ($_POST['ajouterSelection1_x']){
	//Gestion du sous-template
	unset($paramForm);
	$paramForm[titreEnCours]="Saisie d'une nouvelle famille d'actes";
	$paramForm[codeDiags]="sera calculé à l'insertion";
	$paramForm[libActe]="";
	$paramForm[action2]="creer";
	$mod->MxText("form1Acte",$this->getForm1Diags($paramForm));
}
//Si on a choisi de modifier une partie anatomique
elseif ($_POST['modifierSelection1_x']){
	//Récupération des infos pour le dernier acte sélectionné dans la liste
	$paramRq[idActe]=$idListeSelection1;
	if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
		$paramRq[cw]="and idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		
		//Gestion du sous-template
		unset($paramForm);
		$paramForm[titreEnCours]="Modification d'une famille d'actes";
		$paramForm[codeDiags]=$paramRq[idActe];
		$paramForm[libActe]=$res[libelle][0];
		$paramForm[action2]="modifier";
		$mod->MxText("form1Acte",$this->getForm1Diags($paramForm));
	}
}
//Si on a choisi de supprimer une partie anatomique
elseif ($_POST['supprimerSelection1_x']){
	//Récupération des infos pour le dernier acte sélectionné dans la liste
	$paramRq[idActe]=$idListeSelection1;
	if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
		$paramRq[cw]="and idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		
		//$cotationNGAP=$this->explodeNGAP($res[cotationNGAP][0]);
		
		//Gestion du sous-template
		unset($paramForm);
		$paramForm[titreEnCours]="Suppression d'une famille d'actes";
		$paramForm[codeDiags]=$paramRq[idActe];
		$paramForm[libVisuActe]=$res[libelle][0];
		$paramForm[action2]="supprimer";
		$mod->MxText("form1Acte",$this->getForm1Diags($paramForm));
	}
}*/

//Récupération des valeurs pour Selection0
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$tabListeSelection0=$this->tableauValeurs("CCAM_getListeCatDiag",$param,
	"Choisir une catégorie de diagnostics");

//Récupération des valeurs pour Selection1
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idListeSelection0]=addslashes(stripslashes($idListeSelection0));
$param[order]="libelle";
$tabListeSelection1=$this->tableauValeurs("CCAM_getListeDiags",$param,"Choisir un diagnostic");

//Récupération des valeurs pour Selection2
unset($param);
$param[cw]="";
$tabListeSelection2=$this->tableauValeurs("CCAM_getFamilles",$param,
	"Choisir une famille d'actes");
$tabListeSelection2[NGAP]="Actes NGAP";
$tabListeSelection2[PACK]="Packs d'actes";
	
//Récupération des actes pour la liste de gauche en ignorant les valeurs de la liste de droite
//en fonction de la famille sélectionnéee dans Selection1
unset($paramRelation);
unset($paramA);
$paramRelation[idDomaine]=CCAM_IDDOMAINE;
$paramRelation[cw]="";
$paramA[idDomaine]=CCAM_IDDOMAINE;
if ($idListeSelection1 and $idListeSelection1!="aucun#"){
	$paramA[idListeSelection1]=$idListeSelection1;
	$paramA[cw]="";
	$paramRelation[idListeSelection1]=$idListeSelection1;
	$paramA[order]="rel.idActe";
	$paramRelation[order]="rel.idActe";
	if ($idListeSelection2=="tous"){
		$requete="CCAM_getActesNonListe";
	}
	elseif ($idListeSelection2=="NGAP"){
		$paramA[type]="NGAP";
		$requete="CCAM_getAutresActesNonListe";
	}
	elseif ($idListeSelection2=="PACK"){
		$paramA[type]="PACK";
		$requete="CCAM_getAutresActesNonListe";
	}
	else{
		$paramA[idListeSelection2]=$idListeSelection2;
		$requete="CCAM_getActesCCAMAnatomie";
	}
	$tabListeGauche=$this->valeursListeGauche($requete,"CCAM_getActesDiags",
		$paramA,$paramRelation,"Choisir un acte");
}
else	
	$tabListeGauche[0]="Choisir un acte";

//Récupération des actes pour la liste des actes déjà affectés
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idListeSelection1]=$idListeSelection1;
$param[cw]="";
$param[order]="rel.idActe";
$tabListeDroite=$this->tableauValeurs("CCAM_getActesDiags",$param,"Choisir un acte");

//Gestion du template
$mod->MxText("titreSelection0","Catégories de diagnostics :");
$mod->MxSelect("idListeSelection0","idListeSelection0",stripslashes($idListeSelection0),
	$tabListeSelection0,'','',"onChange=reload(this.form)"); 

$mod->MxText("titreSelection1","Diagnostics :");
$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
	$tabListeSelection1,'','',"onChange=reload(this.form)"); 

$mod->MxText("titreSelection2","Familles CCAM : ");
$mod->MxSelect("idListeSelection2","idListeSelection2",$idListeSelection2,
	$tabListeSelection2,'','',"onChange=reload(this.form) class=\"selectfamille\""); 
	
$mod->MxText("titreDispo","Actes et packs disponibles");
$mod->MxText("titreAffecte","Actes et packs affectés au diagnostic sélectionné");
$mod->MxText("commentaireGauche","* Actes non côtés NGAP");
$mod->MxText("commentaireDroite","* Actes non côtés NGAP<br><font color=\"red\">Les actes en rouge ne sont plus 
  valides par rapport à la ".CCAM_VERSION."</font>");

//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Decoupage","w")){
	$mod->MxBloc("flDroite","delete");
	$mod->MxBloc("flGauche","delete");
}

//Ne jamais afficher les boutons suivants
$mod->MxBloc("btnSupprimer","delete");
$mod->MxBloc("btnAjouter","delete");
$mod->MxBloc("btnModifier","delete");
$mod->MxBloc("btnAjouterSelection1","delete");
$mod->MxBloc("btnModifierSelection1","delete");
$mod->MxBloc("btnSupprimerSelection1","delete");

// Affichage ou non du champs d'informations.
if ($this->infos) $mod->MxText("informations.infos",$this->infos);
else $mod->MxBloc("informations","delete");

// Affichage ou non du champs d'erreurs.
if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
else $mod->MxBloc("erreurs","delete");

$mod->MxSelect("listeGauche","listeGauche[]",'', $tabListeGauche,'','',
		"size=\"15\" multiple=\"yes\" class=\"selectngap\""); 
$mod->MxSelect("listeDroite","listeDroite[]",'',$tabListeDroite,'','',
	"size=\"15\" multiple=\"yes\" class=\"selectngap\"");
	
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
	$session->getNavi(1)));

$this->af.=$mod->MxWrite("1");     
}

//Suppression des actes sélectionnés dans la liste de droite
function delActesDiags(){
global $session ;
if (is_array($_POST['listeDroite'])){
	while (list($key,$val)=each($_POST['listeDroite'])){ 
		if ($val and $val!="aucun#"){
			$idListeSelection1=$_POST['idListeSelection1'];
			$requete = new clRequete(CCAM_BDD,"ccam_actes_diagnostic");
			$requete->delRecord("idActe='$val' and idDiag='$idListeSelection1' 
				and idDomaine=".CCAM_IDDOMAINE);
		}
	}
	$retourInfos="Les actes sélectionnés ont été supprimés de la liste des actes 
		rattachés au diagnostic '".$_POST['idListeSelection1']."'";
	return $retourInfos;
}
}

//Ajout des actes sélectionnés dans la liste de gauche à la liste de droite
function addActesDiags(){
global $session ;
if (is_array($_POST['listeGauche'])){
	while (list($key,$val)=each($_POST['listeGauche'])){ 
		if ($val and $val!="aucun#"){
			unset($param);
			$param[idActe]=$val;
			$param[idDomaine]=CCAM_IDDOMAINE;
			$param[idDiag]=$_POST['idListeSelection1'];
			$majrq=new clRequete(CCAM_BDD,"ccam_actes_diagnostic",$param);
			$majrq->addRecord();
		}
	}
	$retourInfos="Les actes sélectionnés ont été insérés dans la liste des actes 
		rattachés au diagnostic '".$_POST['idListeSelection1']."'";
	return $retourInfos;
}
}

//Ajout de la partie anatomique dans la table ccam_actes_domaine
function addNvxDiags(){
global $session ;
unset($param);
$param[idActe]=$this->getIdSuiv("ANAT");
$param[libelleActe]=$_POST['libActe'];
$param[idDomaine]=CCAM_IDDOMAINE;
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->addRecord();

$retourInfos[infos]="La famille d'actes '".$param[idActe]."' a été insérée dans la liste";
return $retourInfos;
}

//Modification de la partie anatomique sélectionnée dans la table ccam_actes_domaine
function modifyDiags(){
global $session;
unset($retourInfos);
unset($param);
$param[idActe]=$_POST['nvxCode'];
$param[libelleActe]=$_POST['libActe'];
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->updRecord("idActe='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos[infos]="La famille d'actes '".$_POST['nvxCode']."' a été modifiée";

return $retourInfos;
}

//Suppression de la partie anatomique sélectionnée dans la table ccam_actes_domaine
//et des actes associés dans ccam_actes_Diags
function delDiags(){
global $session;
unset($retourInfos);
unset($param);
$param[idActe]=$_POST['nvxCode'];
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->delRecord("idActe='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);
$requete=new clRequete(CCAM_BDD,"ccam_actes_Diags",$param);
$requete->delRecord("idDiags='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos[infos]="La famille d'actes '".$_POST['nvxCode'].
	"' et les actes associés ont été supprimés";

return $retourInfos;
}

//Fabrication d'une liste de valeurs à partir d'une requête
function tableauValeurs($requete,$param="",$lignePresentation=""){
// Récupération de la liste de valeurs
if ( ! isset ( $param['order'] ) ) $param['order'] = 'nomliste' ;
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requete,$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
($requete=="CCAM_getFamilles")?$tab[tous]="Tous les actes de la liste restreinte":
	$tab["aucun#"]=$lignePresentation;
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$libelle=strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
	if ($res[title][$i]=="" ){
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
		if ($res["date_fin"][$i] and $res["date_fin"][$i]!="0000-00-00" and substr($res["identifiant"][$i],0,4)!="NGAP")
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
//Retourne la valeur suivante du plus grand code commençant par NGPA... ou Diags...
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
function getForm1Diags($paramForm){
global $session;
$mod=new ModeliXe("CCAM_Form1Acte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreEnCours",$paramForm[titreEnCours]);

$codeDiags=$paramForm[codeDiags];
$mod->MxText("titreCodeActe","Code de la famille d'actes : ");
$mod->MxText("codeActe",$codeDiags);

$mod->MxText("titreLibActe","Libellé de la famille d'actes : ");
if ($paramForm[action2]!="supprimer")
	$mod->MxFormField("libActe","textarea","libActe",$paramForm[libActe],
		"rows=\"3\" cols=\"50\"	wrap=\"virtual\"");
else{
	$mod->MxText("libVisuActe",$paramForm[libVisuActe]);
	$mod->MxText("confirmSuppr","La suppression de la famille d'actes va également entraîner 
		<br>la suppression de l'association actes/famille d'actes. 
		<br>Confirmez la suppression en cliquant sur 'OK'");
}
//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Listes","w")){
	$mod->MxBloc("validerActe","delete");
	$mod->MxBloc("annulerActe","delete");
}
$mod->MxHidden("hidden2","nvxCode=$codeDiags&action2=$paramForm[action2]");
$af=$mod->MxWrite("1");
return $af;
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
