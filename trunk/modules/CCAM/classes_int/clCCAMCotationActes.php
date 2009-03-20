<?php
/* Titre  : Classe Cotation des actes CCAM & NGAP
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 11 avril 2005

	Description : Affectation/Désaffectation des actes de la liste restreinte
	par partie anatomique pour le patient en cours
*/

include (MODULE_CCAM."ccam_define.php");

class clCCAMCotationActes{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct($paramCCAM){
global $session;
$this->idEvent=$paramCCAM[idEvent];
//$this->cotationActes();
}

// Gestion de la cotation des actes pour le patient en cours dans la fiche patient
function cotationActes(){
global $session ;
// Appel du template
$mod=new ModeliXe("CCAM_CotationActes.mxt");
$mod->SetModeliXe();

$mod->MxText("titreFormulaire","Cotation des actes");

//Initialisation des valeurs
(!$_POST['idListeSelection1'])?$idListeSelection1="tous":
	$idListeSelection1=$_POST['idListeSelection1'];

//Ajout des actes sélectionnés dans la liste des actes affectés à la liste des actes
//rattachés au patient en cours
if ($_POST['aDroite_x']) $this->infos=$this->addActesPatient();

//Si on a choisi de modifier 
if ($_POST['modifierActe_x']){
	$idActeModif=$_POST['modifierActe'];
	 $this->infos=$this->modifyActesPatient($idActeModif);
}
//Si on a choisi de supprimer 
elseif ($_POST['supprimerActe_x']){
	$idActeSuppr=$_POST['supprimerActe'];
	 $this->infos=$this->delActesPatient($idActeSuppr);
}
//if ($retourInfos[infos]) $this->infos=$retourInfos[infos];

//Récupération des valeurs pour Selection1
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[type]="ANAT";
$tabListeSelection1=$this->tableauValeurs("CCAM_getListeActes",$param,
	"Choisir une famille d'actes");

//Liste des tri
(!$_POST['tri'])?$tri="frequence":$tri=$_POST['tri'];
$tabTri[code]="par code";
$tabTri[libelle]="par libellé";
$tabTri[frequence]="par fréquence d'utilisation décroissante";
$mod->MxSelect("tri","tri",$tri,$tabTri,'','',"onChange=\"reload(this.form)\" size=\"3\""); 
	
//Récupération des actes pour la liste de gauche en ignorant les valeurs de la liste de droite
//en fonction de la famille sélectionnéee dans Selection1
unset($paramRelation);
unset($paramA);
$paramRelation[idDomaine]=CCAM_IDDOMAINE;
$paramA[idDomaine]=CCAM_IDDOMAINE;
if ($idListeSelection1 and $idListeSelection1!="aucun#"){
	$paramA[idListeSelection1]=$idListeSelection1;
	$paramRelation[idEvent]=$this->idEvent;
	if ($idListeSelection1=="tous"){
		$paramA[cw]=" and rel.idActe not like 'NGAP%')
			or (rel.idActe like 'NGAP%' and rel.cotationNGAP <>''";
		
		if ($tri=="code") $paramA[order]="rel.idActe";
		elseif ($tri=="libelle") $paramA[order]="rel.libelleActe";
		elseif ($tri=="frequence") $paramA[order]="rel.frequence desc,rel.idActe";
		
		$requete="CCAM_getActesNonListe";
	}
	else{
		$paramA[idListeSelection1]=$idListeSelection1;
		$paramA[cw]="and (rel.idActe not like 'NGAP%' or 
				rel.idActe like 'NGAP%' and act.cotationNGAP<>'')";
		
		if ($tri=="code") $paramA[order]="act.idActe";
		elseif ($tri=="libelle") $paramA[order]="act.libelleActe,act.idActe";
		elseif ($tri=="frequence") $paramA[order]="act.frequence desc";
		
		$requete="CCAM_getActesAnatomie";
	}
	$tabListeGauche=$this->valeursListeGauche($requete,"CCAM_getActesCotes",
		$paramA,$paramRelation,"Choisir un acte");
}
else	
	$tabListeGauche[0]="Choisir un acte";
	
//Récupération des actes côtés pour le patient en cours
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idEvent]=$this->idEvent;
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesCotes",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
if ($res[INDIC_SVC][2]==0) $mod->MxBloc("actesCotes.action","modify"," ");
else{
	for ($i=0;isset($res[identifiant][$i]);$i++){ 
		$mod->MxText("actesCotes.codeActe",$res[identifiant][$i]);
		$mod->MxText("actesCotes.libActe",$res[libelle][$i]);
		$mod->MxText("actesCotes.action.codeActe",$res[identifiant][$i]);
		$mod->MxText("actesCotes.action.libActe",$res[libelle][$i]);
		$mod->MxBloc("actesCotes","loop");
	}
}

//Gestion du template
$mod->MxText("titreSelection1","Familles d'actes : ");
$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
	$tabListeSelection1,'','',"onChange=reload(this.form) class=\"selectfamille\""); 
	
$mod->MxText("titreDispo","Actes et packs disponibles");
$mod->MxText("titreAffecte","Actes affectés au patient");

//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Decoupage","w")){
	$mod->MxBloc("flDroite","modify"," ");
}

//Ne jamais afficher les boutons suivants
$mod->MxBloc("diagnostics","modify"," ");

// Affichage ou non du champs d'informations.
if ($this->infos) $mod->MxText("informations.infos",$this->infos);
else $mod->MxBloc("informations","modify"," ");

// Affichage ou non du champs d'erreurs.
if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
else $mod->MxBloc("erreurs","modify"," ");

$mod->MxSelect("listeGauche","listeGauche[]",'', $tabListeGauche,'','',
		"size=\"10\" multiple=\"yes\""); 
	
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
	$session->getNavi(1),$session->getNavi(2)));

return $this->af.=$mod->MxWrite("1");     
}

//Ajout des actes sélectionnés dans la liste de gauche à la liste de droite
function addActesPatient(){
global $session ;
if (is_array($_POST['listeGauche'])){
	while (list($key,$val)=each($_POST['listeGauche'])){ 
		if ($val and $val!="aucun#"){
			if (substr($val,0,4)=="PACK"){
				//Si la ligne sélectionnée est un pack, recherche des actes qui le composent
				// pour insertion dans la liste des actes affectés
				unset($paramRq);
				$paramRq[idDomaine]=CCAM_IDDOMAINE;
				$paramRq[idListeSelection1]=$val;
				$paramRq[cw]="and (rel.idActe not like 'NGAP%' or 
					rel.idActe like 'NGAP%' and act.cotationNGAP<>'')";
				$req=new clResultQuery;
				$res=$req->Execute("Fichier","CCAM_getActesPack",$paramRq,"ResultQuery");
				//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
				for ($i=0;isset($res[identifiant][$i]);$i++){ 
					unset($paramRq);
					$paramRq[codeActe]=$res[identifiant][$i];
					$paramRq[idEvent]=$this->idEvent;
					$paramRq[idDomaine]=CCAM_IDDOMAINE;
					$req2=new clResultQuery;
					$res2=$req2->Execute("Fichier","CCAM_get1ActeCote",$paramRq,"ResultQuery");
					//newfct(gen_affiche_tableau,$res2[INDIC_SVC]);
					if ($res2[INDIC_SVC][2]==0) $this->add1Acte($res[identifiant][$i]);
				}
			}
			else $this->add1Acte($val);
		}
	}
	$retourInfos="Les actes sélectionnés ont été affectés au patient en cours";
	return $retourInfos;
}
}

function add1Acte($codeActe){
unset($paramRq);
$paramRq[idActe]=$codeActe;
$paramRq[cw]="and idDomaine=".CCAM_IDDOMAINE;
$req3=new clResultQuery;
$res3=$req3->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res3[INDIC_SVC]);

unset($param);
$param[codeActe]=$codeActe;
$param[idDom]=CCAM_IDDOMAINE;
$param[idEvent]=$this->idEvent;
$param[cotationNGAP]=$res3[cotationNGAP][0];
$majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
$sql=$majrq->addRecord();

//MAJ Fréquence d'utilisation de l'acte
$paramRq[idDomaine]=CCAM_IDDOMAINE;
$req3=new clResultQuery;
$res3=$req3->Execute("Fichier","CCAM_getMaxFreqActe",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res3[INDIC_SVC]);
unset($param);
$param[frequence]=$res3[freq_max][0]+1;
$majrq=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$sql=$majrq->updRecord("idActe='$paramRq[idActe]' AND idDomaine=".CCAM_IDDOMAINE);
}

//Modification de la partie anatomique sélectionnée dans la table ccam_actes_domaine
function modifyAnatomie(){
global $session;
unset($retourInfos);
unset($param);
$param[idActe]=$_POST['nvxCode'];
$param[libelleActe]=$_POST['libActe'];
$requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$requete->updRecord("idActe='$param[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos="La partie anatomique '".$_POST['nvxCode']."' a été modifiée";

return $retourInfos;
}

//Suppression de l'acte sélectionné à la liste des actes affectés au patient en cours
function delActesPatient($idActe){
global $session;
unset($retourInfos);
$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
$sql=$requete->delRecord("codeActe='$idActe' AND idEvent=".$this->idEvent);

//MAJ Fréquence d'utilisation de l'acte
$paramRq[idDomaine]=CCAM_IDDOMAINE;
$paramRq[idActe]=$idActe;
$req3=new clResultQuery;
$res3=$req3->Execute("Fichier","CCAM_getMaxFreqActe",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res3[INDIC_SVC]);
unset($param);
$param[frequence]=$res3[freq_max][0]-1;
$majrq=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$sql=$majrq->updRecord("idActe='$paramRq[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos="L'acte '".$idActe."' n'est plus affecté au patient en cours";

return $retourInfos;
}

//Fabrication d'une liste de valeurs à partir d'une requête
function tableauValeurs($requete,$param="",$lignePresentation=""){
// Récupération de la liste de valeurs
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requete,$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
($requete=="CCAM_getListeActes")?$tab[tous]="Tous les actes de la liste restreinte":
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
		$identifiant=$res[identifiant][$i]."\" title=\"".ucfirst(strtolower($title));
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
($requeteTableA=="CCAM_getActesAnatomie")?
	$paramA[cw].=" and rel.idActe not in ($listeIdRelation)":"";
$paramA[listeIdRelation]=$listeIdRelation;
$tab=$this->tableauValeurs($requeteTableA,$paramA,$lignePresentation);
return $tab;
}

function getIdSuiv($typeCode){
//Retourne la valeur suivante du plus grand code commençant par NGPA... ou Anatomie...
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
function getForm1Anatomie($paramForm){
global $session;
$mod=new ModeliXe("CCAM_Form1Acte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreEnCours",$paramForm[titreEnCours]);

$codeAnatomie=$paramForm[codeAnatomie];
$mod->MxText("titreCodeActe","Code de la partie anatomique : ");
$mod->MxText("codeActe",$codeAnatomie);

$mod->MxText("titreLibActe","Libellé de la partie anatomique : ");
if ($paramForm[action2]!="supprimer")
	$mod->MxFormField("libActe","textarea","libActe",$paramForm[libActe],
		"rows=\"3\" cols=\"50\"	wrap=\"virtual\"");
else{
	$mod->MxText("libVisuActe",$paramForm[libVisuActe]);
	$mod->MxText("confirmSuppr","La suppression de la partie anatomique va également entraîner 
		<br>la suppression de l'association actes/partie anatomique. 
		<br>Confirmez la suppression en cliquant sur 'OK'");
}
//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Listes","w")){
	$mod->MxBloc("validerActe","modify"," ");
	$mod->MxBloc("annulerActe","modify"," ");
}
$mod->MxHidden("hidden2","nvxCode=$codeAnatomie&action2=$paramForm[action2]");
$af=$mod->MxWrite("1");
return $af;
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
