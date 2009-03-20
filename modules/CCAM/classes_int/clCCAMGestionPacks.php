<?php
/* Titre  : Classe ListeRestreinte
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 04 mars 2005

	Description : Création, modification, suppression de packs d'actes
	Affectation/Désaffectation des actes de la liste restreinte pour le pack en cours
*/
class clCCAMGestionPacks{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct(){
global $session ;
$this->gestionPacks();
}

// Gestion des packs d'actes
function gestionPacks(){
global $session ;
/*(!$_POST['action'])?$action="ccam":$action=$_POST['action'];
($action=="affectation_ngap")?$nomForm="CCAM_AffectationNGAP.mxt":
	$nomForm="CCAM_ListeRestreinte.mxt";*/
// Appel du template
$mod=new ModeliXe("CCAM_ListeRestreinte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreFormulaire","Gestion des packs d'actes");

$tabPeriodicite[aucune]="Sans objet";
$tabPeriodicite[quart]="1 quart d'heure";
$tabPeriodicite[demi]="1 demi-heure";
$tabPeriodicite[1]="1 heure";
$tabPeriodicite[2]="2 heures";
$tabPeriodicite[3]="3 heures";
$tabPeriodicite[4]="4 heures";
$tabPeriodicite[6]="6 heures";
$tabPeriodicite[8]="8 heures";
$tabPeriodicite[12]="12 heures";
$tabPeriodicite[24]="24 heures";
$tabPeriodicite[48]="48 heures";

$mod->MxBloc("action","delete");

//Initialisation des valeurs pour Selection1
(!$_POST['idListeSelection1'])?$idListeSelection1="aucun#":
	$idListeSelection1=$_POST['idListeSelection1'];
(!$_POST['idListeSelection2'])?$idListeSelection2="tous":
	$idListeSelection2=$_POST['idListeSelection2'];

//Suppression des actes sélectionnés dans la liste des actes affectés au pack en cours
if ($_POST['aGauche_x']) $this->infos=$this->delActesPack();
//Ajout des actes sélectionnés dans la liste des actes affectés au pack sélectionné
elseif ($_POST['aDroite_x']) $this->infos=$this->addActesPack();
//Validation d'un nouveau pack ou modification, suppression d'un pack
elseif ($_POST['OK_x']){
 	if ($_POST['action2']=="creer") $retourInfos=$this->addNvxPack();
	elseif ($_POST['action2']=="modifier") $retourInfos=$this->modifyPack();
	elseif ($_POST['action2']=="supprimer") $retourInfos=$this->delPack();
	elseif ($_POST['action2']=="modifierActe") $retourInfos=$this->modifyActe();
	
	if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
	elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
}
//Si on a choisi de créer un nouveau pack
elseif ($_POST['ajouterSelection1_x']){
	//Gestion du sous-template
	unset($paramForm);
	$paramForm[titreEnCours]="Saisie d'un nouveau pack";
	$paramForm[codePack]="sera calculé à l'insertion";
	$paramForm[titreCodePack]="Code du pack :";
	$paramForm[titreLibPack]="Libellé du pack :";
	$paramForm[libActe]="";
	$paramForm[action2]="creer";
	$mod->MxText("form1Acte",$this->getForm1Pack($paramForm));
}
//Si on a choisi de modifier un pack
elseif ($_POST['modifierSelection1_x']){
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
		$paramForm[titreEnCours]="Modification d'un pack";
		$paramForm[codePack]=$paramRq[idActe];
		$paramForm[titreCodePack]="Code du pack :";
		$paramForm[titreLibPack]="Libellé du pack :";
		$paramForm[libActe]=$res[libelle][0];
		$paramForm[action2]="modifier";
		$mod->MxText("form1Acte",$this->getForm1Pack($paramForm));
	}
}
//Si on a choisi de supprimer un pack
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
		$paramForm[titreEnCours]="Suppression d'un pack";
		$paramForm[codePack]=$paramRq[idActe];
		$paramForm[titreCodePack]="Code du pack :";
		$paramForm[titreLibPack]="Libellé du pack :";
		$paramForm[libVisuActe]=$res[libelle][0];
		$paramForm[action2]="supprimer";
		$mod->MxText("form1Acte",$this->getForm1Pack($paramForm));
	}
}
//Si on a choisi de modifier un acte (gestion quantité)
elseif ($_POST['modifierActe_x']){
	//Récupération des infos pour le dernier acte sélectionné dans la liste
	$paramRq[idActe]=$this->getDerIdSel($_POST['listeDroite']);
	if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
		$paramRq[cw]="and idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		
		//Gestion du sous-template
		$paramForm[titreEnCours]="Modification d'un acte";
		$paramForm[codePack]=$paramRq[idActe];
		$paramForm[titreCodePack]="Code de l'acte :";
		$paramForm[titreLibPack]="Libellé de l'acte :";
		$paramForm[libVisuActe]=$res[libelle][0];
		$paramForm[titreQte]="Quantité :";
		$paramForm[titrePeriodicite]="Latence entrer les actes :";
		
		$paramRq[cw]="and rel.idPack='$idListeSelection1' and rel.idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1ActePack",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		$paramForm[qte]=$res[quantite][0];
		$paramForm[periodicite]=$res[periodicite][0];
		$paramForm[tabPeriodicite]=$tabPeriodicite;
		
		$paramForm[action2]="modifierActe";
		$mod->MxText("form1Acte",$this->getForm1Pack($paramForm));
	}
}
else{
	//Récupération des infos pour le dernier acte sélectionné dans la liste
	$paramRq[idActe]=$this->getDerIdSel($_POST['listeDroite']);
	if ($paramRq[idActe]!="" and $paramRq[idActe]!="aucun#"){
		$paramRq[cw]="and idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		
		//Gestion du sous-template
		$paramForm[titreEnCours]="Modification d'un acte";
		$paramForm[codePack]=$paramRq[idActe];
		$paramForm[titreCodePack]="Code de l'acte :";
		$paramForm[titreLibPack]="Libellé de l'acte :";
		$paramForm[libVisuActe]=$res[libelle][0];
		$paramForm[titreQte]="Quantité :";
		$paramForm[titrePeriodicite]="Latence entrer les actes :";
		
		$paramRq[cw]="and rel.idPack='$idListeSelection1' and rel.idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1ActePack",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		$paramForm[qte]=$res[quantite][0];
		$paramForm[periodicite]=$res[periodicite][0];
		$paramForm[tabPeriodicite]=$tabPeriodicite;
		
		$paramForm[action2]="modifierActe";
		$mod->MxText("form1Acte",$this->getForm1Pack($paramForm));
	}
}

//Récupération des valeurs pour Selection1
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[type]="PACK";
$tabListeSelection1=$this->tableauValeurs("CCAM_getListeActes",$param,"Choisir un pack");

//Récupération des valeurs pour Selection2
unset($param);
$param[cw]="";
$tabListeSelection2=$this->tableauValeurs("CCAM_getFamilles",$param,
	"Choisir une famille d'actes");
$tabListeSelection2[NGAP]="Actes NGAP";
//$tabListeSelection2[PACK]="Packs d'actes";

//Récupération des actes pour la liste de gauche en ignorant les valeurs de la liste de droite
//en fonction de la famille sélectionnéee dans Selection1
unset($paramRelation);
unset($paramA);
$paramRelation[idDomaine]=CCAM_IDDOMAINE;
$paramRelation[cw]="";
$paramA[idDomaine]=CCAM_IDDOMAINE;
if ($idListeSelection1 and $idListeSelection1!="aucun#"){
	$paramA[idListeSelection1]=$idListeSelection1;
	$paramA[cw]="and rel.idActe not like 'PACK%'";
	$paramRelation[idListeSelection1]=$idListeSelection1;
	$paramA[order]="rel.idActe";
	if ($idListeSelection2=="tous"){
		$requete="CCAM_getActesNonListe";
	}
	elseif ($idListeSelection2=="NGAP"){
		$paramA[type]="NGAP";
		$paramA[cw]="";
		$requete="CCAM_getAutresActesNonListe";
	}
	elseif ($idListeSelection2=="PACK"){
		$paramA[type]="PACK";
		$paramA[cw]="";
		$requete="CCAM_getAutresActesNonListe";
	}
	else{
		$paramA[idListeSelection2]=$idListeSelection2;
		$requete="CCAM_getActesCCAMAnatomie";
	}
	$tabListeGauche=$this->valeursListeGauche($requete,"CCAM_getActesPack",
		$paramA,$paramRelation,"Choisir un acte");
}
else
	$tabListeGauche[0]="Choisir un acte";

//Récupération des actes pour la liste des actes déjà affectés
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idListeSelection1]=$idListeSelection1;
$param[cw]="";
$tabListeDroite=$this->tableauValeurs("CCAM_getActesPack",$param,"Choisir un acte");

//Gestion du template
$mod->MxText("titreSelection1","Packs d'actes : ");
$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
	$tabListeSelection1,'','',"onChange=reload(this.form)"); 

$mod->MxText("titreSelection2","Familles CCAM : ");
$mod->MxSelect("idListeSelection2","idListeSelection2",$idListeSelection2,
	$tabListeSelection2,'','',"onChange=reload(this.form) class=\"selectfamille\""); 
	
$mod->MxText("titreDispo","Actes disponibles");
$mod->MxText("titreAffecte","Actes affectés au pack sélectionné");
$mod->MxText("commentaireGauche","* Actes non côtés NGAP");
$mod->MxText("commentaireDroite","* Actes non côtés NGAP<br><font color=\"red\">Les actes en rouge ne sont plus 
  valides par rapport à la ".CCAM_VERSION."</font>");

//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Packs","w")){
	$mod->MxBloc("flDroite","delete");
	$mod->MxBloc("flGauche","delete");
	$mod->MxBloc("btnAjouterSelection1","delete");
}
if (count($tabListeSelection1)<=1){
	$mod->MxBloc("btnModifierSelection1","delete");
	$mod->MxBloc("btnSupprimerSelection1","delete");
}
if (count($tabListeDroite)<=1){
	$mod->MxBloc("btnModifier","delete");
}

//Ne jamais afficher les boutons suivants
$mod->MxBloc("btnSupprimer","delete");
$mod->MxBloc("btnAjouter","delete");

// Affichage ou non du champs d'informations.
if ($this->infos) $mod->MxText("informations.infos",$this->infos);
else $mod->MxBloc("informations","delete");

// Affichage ou non du champs d'erreurs.
if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
else $mod->MxBloc("erreurs","delete");

$mod->MxSelect("listeGauche","listeGauche[]",'', $tabListeGauche,'','',
		"size=\"15\" multiple=\"yes\" class=\"selectngap\""); 
$mod->MxSelect("listeDroite","listeDroite[]",'',$tabListeDroite,'','',
	"size=\"15\" multiple=\"yes\" class=\"selectngap\" onDblClick=\"reload(this.form)\"");
	
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
	$session->getNavi(1)));

$this->af.=$mod->MxWrite("1");     
}

//Suppression des actes sélectionnés dans la liste de droite
function delActesPack(){
global $session ;
if (is_array($_POST['listeDroite'])){
	while (list($key,$val)=each($_POST['listeDroite'])){ 
		if ($val and $val!="aucun#"){
			$idListeSelection1=$_POST['idListeSelection1'];
			$requete = new clRequete(CCAM_BDD,"ccam_actes_pack");
			$requete->delRecord("idActe='$val' and idPack='$idListeSelection1' 
				and idDomaine=".CCAM_IDDOMAINE);
		}
	}
	$retourInfos="Les actes sélectionnés ont été supprimés de la liste des actes 
		rattachés au pack '".$_POST['idListeSelection1']."'";
	return $retourInfos;
}
}

//Ajout des actes sélectionnés dans la liste de gauche à la liste de droite
function addActesPack(){
global $session ;
if (is_array($_POST['listeGauche'])){
	while (list($key,$val)=each($_POST['listeGauche'])){ 
		if ($val and $val!="aucun#"){
			unset($param);
			$param[idActe]=$val;
			$param[idDomaine]=CCAM_IDDOMAINE;
			$param[idPack]=$_POST['idListeSelection1'];
			$param[quantite]=1;
			$param[periodicite]="aucune";
			$majrq=new clRequete(CCAM_BDD,"ccam_actes_pack",$param);
			$majrq->addRecord();
		}
	}
	$retourInfos="Les actes sélectionnés ont été insérés dans la liste des packs";
	return $retourInfos;
}
}

//Modification de l'acte sélectionné dans la table ccam_actes_pack
function modifyActe(){
global $session;
unset($retourInfos);
$qte=$_POST['qte'];
($qte=="")?$qte=1:"";
($qte<=1)?$periodicite="aucune":$periodicite=$_POST['periodicite'];
unset($param);
$idPack=$_POST['idListeSelection1'];
$idActe=$_POST['nvxCode'];
$param[quantite]=$qte;
$param[periodicite]=$periodicite;
$requete=new clRequete(CCAM_BDD,"ccam_actes_pack",$param);
$requete->updRecord("idActe='$idActe' and idPack='$idPack' 
	and idDomaine=".CCAM_IDDOMAINE);
$retourInfos[infos]="L'acte '".$_POST['nvxCode']."' a été modifié";

return $retourInfos;
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

//Ajout du pack créé manuellement dans la table ccam_actes_domaine
function addNvxPack(){
global $session ;
unset($param);
$param[idActe]=$this->getIdSuiv("PACK");
$param[libelleActe]=$_POST['libActe'];
$param[idDomaine]=CCAM_IDDOMAINE;
//echo "<br>$param[idActe]";
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->addRecord();

$retourInfos[infos]="Le pack '".$param[idActe]."' a été inséré dans la liste restreinte";
return $retourInfos;
}

//Modification du pack sélectionné dans la table ccam_actes_domaine
function modifyPack(){
global $session;
unset($retourInfos);
unset($param);
$param[idActe]=$_POST['nvxCode'];
$param[libelleActe]=$_POST['libActe'];
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->updRecord("idActe='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos[infos]="Le pack '".$_POST['nvxCode']."' a été modifié";

return $retourInfos;
}

//Suppression du pack sélectionné dans la table ccam_actes_domaine 
//et des actes associés dans ccam_actes_pack
function delPack(){
global $session;
unset($retourInfos);
unset($param);
$param[idActe]=$_POST['nvxCode'];
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->delRecord("idActe='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);
$requete=new clRequete(CCAM_BDD,"ccam_actes_pack",$param);
$requete->delRecord("idPack='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos[infos]="Le pack '".$_POST['nvxCode']."' et les actes associés ont été supprimés";

return $retourInfos;
}

//Fabrication d'une liste de valeurs à partir d'une requête
function tableauValeurs($requete,$param="",$lignePresentation=""){
// Récupération de la liste de valeurs
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
function getForm1Pack($paramForm){
global $session;
$mod=new ModeliXe("CCAM_Form1Acte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreEnCours",$paramForm[titreEnCours]);

$codePack=$paramForm[codePack];
$mod->MxText("titreCodeActe",$paramForm[titreCodePack]);
$mod->MxText("codeActe",$codePack);

$mod->MxText("titreLibActe",$paramForm[titreLibPack]);
if ($paramForm[action2]!="supprimer" and $paramForm[action2]!="modifierActe")
	$mod->MxFormField("libActe","textarea","libActe",$paramForm[libActe],
		"rows=\"3\" cols=\"50\"	wrap=\"virtual\"");
else{
	$mod->MxText("libVisuActe",$paramForm[libVisuActe]);
	if ($paramForm[action2]=="supprimer")
		$mod->MxText("confirmSuppr","La suppression du pack va également entraîner
			<br>la suppression de l'association actes/pack.
			<br>Confirmez la suppression en cliquant sur 'OK'");
	else{
		$mod->MxText("titreQte",$paramForm[titreQte]);
		$mod->MxFormField("qte","text","qte",$paramForm[qte],"size=\"3\"");
		$mod->MxText("titrePeriodicite",$paramForm[titrePeriodicite]);
		
		
		$mod->MxSelect("periodicite","periodicite",$paramForm[periodicite],
			$paramForm[tabPeriodicite],'',''); 
	}
}
//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Listes","w")){
	$mod->MxBloc("validerActe","delete");
	$mod->MxBloc("annulerActe","delete");
}
$mod->MxHidden("hidden2","nvxCode=$codePack&action2=$paramForm[action2]");
$af=$mod->MxWrite("1");
return $af;
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
