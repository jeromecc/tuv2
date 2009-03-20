<?php
/* Titre  : Classe Contrôle de la validité des atces selon la version CCAM en cours
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : août 2006

	Description : Contrôle de la validité des acts selon la version CCAM en cours
	Remplacement des actes invalides par un code acte valide dans la liste restreinte, les correspondances
	diagnostics et packs
*/
class clCCAMCtrlActesVersion{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct(){
global $session ;
$this->ctrlActes();
}

function ctrlActes(){
global $session ;
$req=new clResultQuery;
$dateFin=date("Y-m-d");

// Appel du template
$mod=new ModeliXe("CCAM_CtrlActesVersion.mxt");
$mod->SetModeliXe();
$mod->MxText("versionCCAM",CCAM_VERSION);

//Liste des actions
if (!$_POST['action']) $action="invalide"; else $action=$_POST['action'];
$tabAction["invalide"]="Actes invalides";
$tabAction["liste_restreinte"]="Actes de la liste restreinte";
$tabAction["tarif_nul"]="Actes dont le tarif est nul";
while (list($key,$val)=each($tabAction)){
	$mod->MxCheckerField("action.action","radio","action",$key,(($action==$key)?true:false),"onClick=reload(this.form)");
	$mod->MxText("action.libAction",$val);
	$mod->MxBloc("action","loop");
}

//On a validé la suppression ou le remplacement
$validerRemplacer=0;
if ($_POST['imgValiderRemplacer'] or $_POST['imgValiderRemplacer_x']){
  $idActe=$_POST['listeActesInvalides'];
  $idNvxActe=$_POST['listeARemplacer'];
  if ($idNvxActe=="suppr"){
    $requete=new clRequete(CCAM_BDD,"ccam_actes_domaine");
    $requete->delRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
    $requete=new clRequete(CCAM_BDD,"ccam_actes_pack");
    $requete->delRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
    $requete=new clRequete(CCAM_BDD,"ccam_actes_diagnostic");
    $requete->delRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
  }
  else{
    if (ereg($idNvxActe,$_POST['dejaDansLR'])){
      $requete=new clRequete(CCAM_BDD,"ccam_actes_domaine");
      $requete->delRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
    }
    else{
      unset($param);
      $param["idActe"]=$idNvxActe;
      $param["date_fin"]="0000-00-00";
      $requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
      $requete->updRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
    }
    unset($param);
    $param["idActe"]=$idNvxActe;
    $requete=new clRequete(CCAM_BDD,"ccam_actes_pack",$param);
    $requete->updRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
    $requete=new clRequete(CCAM_BDD,"ccam_actes_diagnostic",$param);
    $requete->updRecord("idActe='$idActe' and idDomaine=".CCAM_IDDOMAINE);
  }
  $validerRemplacer=1;
}
else $mod->MxBloc("informations","delete");

//Récupération des actes invalides
if ($action=="invalide"){
  $res=$req->Execute("Fichier","CCAM_getActesInvalides",array(),"ResultQuery");
  $mod->MxText("existeActes.libelleTypeActes","Actes invalides");
}
elseif ($action=="liste_restreinte"){
  unset($paramRq);
  $paramRq["cw"]="date_fin='0000-00-00'";
  $res=$req->Execute("Fichier","CCAM_getActesDomaine2",$paramRq,"ResultQuery");
  $mod->MxText("existeActes.libelleTypeActes","Actes de la liste restreinte");
}
elseif ($action=="tarif_nul"){
  $res=$req->Execute("Fichier","CCAM_getTarifCCAMNul",array(),"ResultQuery");
  $mod->MxText("existeActes.libelleTypeActes","Actes dont le tarif est nul");
}
//eko($res["INDIC_SVC"]);
if ($res["INDIC_SVC"][2]){
  $mod->MxBloc("nonExisteActes","delete");
  $listeActes="";
  for ($i=0;isset($res["idActe"][$i]);$i++){
    $idActe=$res["idActe"][$i];
    if ($i==0) $idActe0=$idActe;
    $tabActesInvalides[$idActe]="$idActe - ".$res["libelleActe"][$i];
    $listeActes.="'$idActe',";
  }
  if (!isset($_POST['listeActesInvalides']) or 
      (isset($_POST['actionPrec']) and $action!=$_POST['actionPrec']) 
      or $validerRemplacer==1) $listeActesInvalides=$idActe0;
  else $listeActesInvalides=$_POST['listeActesInvalides'];
  $mod->MxSelect("existeActes.listeActesInvalides","listeActesInvalides",$listeActesInvalides,$tabActesInvalides,
    '','',"size=\"15\" class=\"selectngap\" onChange=\"reload(this.form)\"");
  
  //Récupération du libellé de l'acte sélectionné (liste gauche)
  unset($paramRq);
  $paramRq["idActe"]=$listeActesInvalides;
  $res=$req->Execute("Fichier","CCAM_get1ActeCCAM",$paramRq,"ResultQuery");
  //eko($res["INDIC_SVC"]);
  $mod->MxText("existeActes.libelleActe","$listeActesInvalides - ".$res["libelle"][0]);
  
  //Récupération des packs utilisant l'acte sélectionné
  unset($paramRq);
  $paramRq["cw"]="and rel.idActe='$listeActesInvalides' and rel.idDomaine=".CCAM_IDDOMAINE;
  $res=$req->Execute("Fichier","CCAM_getPackActes",$paramRq,"ResultQuery");
  //eko($res["INDIC_SVC"]);
  $listePacks="<p><u>Liste des packs associés :</u> ";
  if ($res["INDIC_SVC"][2]){
    for ($i=0;isset($res["idPack"][$i]);$i++){
      $listePacks.=$res["idPack"][$i]."-".$res["libelleActe"][$i].", ";
    }
    $listePacks=substr($listePacks,0,-2);
  }
  else $listePacks.="Aucun pack n'a été associé";
  $mod->MxText("existeActes.listePacks",$listePacks);
  
  //Récupération des diagnostics associés à l'acte sélectionné
  unset($paramRq);
  $paramRq["cw"]="and rel.idActe='$listeActesInvalides' and rel.idDomaine=".CCAM_IDDOMAINE;
  $res=$req->Execute("Fichier","CCAM_getDiagsActe",$paramRq,"ResultQuery");
  //eko($res["INDIC_SVC"]);
  $listeDiags="<p><u>Liste des diagnostics associés :</u> ";
  if ($res["INDIC_SVC"][2]){
    for ($i=0;isset($res["idDiag"][$i]);$i++){
      $listeDiags.=$res["idDiag"][$i]."-".$res["nomItem"][$i].", ";
    }
    $listeDiags=substr($listeDiags,0,-2);
  }
  else $listeDiags.="Aucun diagnostic n'a été associé";
  $mod->MxText("existeActes.listeDiags",$listeDiags);
  
  //Récupération du tarif de l'acte sélectionné
  unset($paramRq);
  $paramRq["cw"]="and a.code='$listeActesInvalides' order by t.aadt_modif desc";
  $res=$req->Execute("Fichier","CCAM_get1TarifCCAM",$paramRq,"ResultQuery");
  eko($res);
  $mod->MxText("existeActes.tarif","<br><u>Tarif en vigueur :</u> ".
    number_format($res["pu_base"][0],2,',','.')." euros");
  
  //Mise à jour de la date de fin de validité
  if ($action=="invalide"){
    $listeActes=substr($listeActes,0,-1);
    unset($param);
    $param["date_fin"]=date("Y-m-d");
    $requete=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
    $requete->updRecord("idActe in ($listeActes) and date_fin='0000-00-00' and idDomaine=".CCAM_IDDOMAINE);
  }
  
  //Affichage de la zone début de code
  $mod->MxText("existeActes.codeSelectionne",$listeActesInvalides);
  if (!isset($_POST['debCodeActe']) or 
      (isset($_POST['listeActesPrec']) and $listeActesInvalides!=$_POST['listeActesPrec'])) 
    $debCodeActe=substr($listeActesInvalides,0,4); 
  else $debCodeActe=strtoupper($_POST['debCodeActe']);
  if (!$debCodeActe or $debCodeActe=="%") $debCodeActe="A";
  $mod->MxFormField("existeActes.debCodeActe","text","debCodeActe",$debCodeActe,"size=\"7\" 
    onChange=\"reload(this.form)\"");
  
  //Récupération des actes correspondant au début du code déjà présents dans la liste restreinte
  unset($paramRq);
  $paramRq["cw"]="idActe like '$debCodeActe%'";
  $res=$req->Execute("Fichier","CCAM_getActesDomaine2",$paramRq,"ResultQuery");
  //eko($res["INDIC_SVC"]);
  $listeActes="";
  for ($i=0;isset($res["idActe"][$i]);$i++) $listeActes.=$res["idActe"][$i].",";
  /*if ($listeActes){
    $listeActes=substr($listeActes,0,-1);
    $cwListeActes="and code not in ($listeActes)";
  }*/
  
  //Récupération des actes correspondant au début du code
  unset($paramRq);
  $paramRq["cw"]="dt_fin is null and code like '$debCodeActe%' and code!='$listeActesInvalides'";
  $res=$req->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
  //eko($res["INDIC_SVC"]);
  $tabARemplacer=array();
  $dejaDansLR="";
  for ($i=0;isset($res["CODE"][$i]);$i++){
    $idActe=$res["CODE"][$i];
    $idActeAff=$idActe;
    if (ereg($idActe,$listeActes)){
      if ($idActe==$_POST["listeARemplacer"]) $idActe.="\"selected=\"selected\" style=\"color:green;\"";
      else $idActe.="\"style=\"color:green;\"";
    }
    if ($i==0) $idActe0=$idActeAff;
    $tabARemplacer[$idActe]="$idActeAff - ".$res["LIBELLE_COURT"][$i];
  }
  $tabARemplacer["suppr"]="Supprimer l'acte '$listeActesInvalides' des différentes listes";
  if (!isset($_POST['listeARemplacer']) or 
      (isset($_POST['listeActesPrec']) and $listeActesInvalides!=$_POST['listeActesPrec']) or 
      (isset($_POST['debCodeActePrec']) and $debCodeActe!=$_POST['debCodeActePrec']))
    $listeARemplacer=$idActe0;
  else $listeARemplacer=$_POST['listeARemplacer'];
  $mod->MxSelect("existeActes.listeARemplacer","listeARemplacer",$listeARemplacer,$tabARemplacer,'','',"size=\"15\" 
    onChange=\"reload(this.form)\"");
  if ($res["INDIC_SVC"][2]){
    if ($listeARemplacer!="suppr"){
      //Récupération du libellé de l'acte sélectionné (liste droite)
      unset($paramRq);
      $paramRq["idActe"]=$listeARemplacer;
      $res=$req->Execute("Fichier","CCAM_get1ActeCCAM",$paramRq,"ResultQuery");
      //eko($res["INDIC_SVC"]);
      $mod->MxText("existeActes.libelleARemplacer","$listeARemplacer - ".$res["libelle"][0]);
      
      //Récupération du tarif de l'acte sélectionné
      unset($paramRq);
      $paramRq["cw"]="and a.code='$listeARemplacer' order by t.aadt_modif desc";
      $res=$req->Execute("Fichier","CCAM_get1TarifCCAM",$paramRq,"ResultQuery");
      //eko ($res);
      //eko($res["INDIC_SVC"]);
      $mod->MxText("existeActes.tarifNvx","<br><u>Tarif en vigueur :</u> ".
        number_format($res["pu_base"][0],2,',','.')." euros");
    }
  }
  $mod->MxFormField("existeActes.imgValiderRemplacer","image","imgValiderRemplacer","",
  			"src=\"".URLIMG."Ok.gif\" align=\"abscenter\" 
        title=\"Mettre à jour l'acte invalide dans les différentes listes\"");
}
else{
  $mod->MxBloc("existeActes","delete");
  $mod->MxText("nonExisteActes.versionCCAM",CCAM_VERSION);
}
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1)));
$mod->MxHidden("hidden2","actionPrec=$action&dejaDansLR=$listeActes&debCodeActePrec=$debCodeActe&listeActesPrec=$listeActesInvalides");

$this->af.=$mod->MxWrite("1");
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
