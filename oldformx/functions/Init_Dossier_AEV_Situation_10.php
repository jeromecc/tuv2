<?php
function Init_Dossier_AEV_Situation_10($formx) {

// Efface les anciens fichiers Formulaire_AEV
//$AEV_Type = utf8_decode($formx->getFormVar('AEV_Type'));
//$formx->setVar('L_AEV_Situation',$situation);


$ids         = $formx->getIDS();
$id_instance = $formx->getIdInstance();
$idu         = $formx->getVar('ids');

eko ($ids);
eko ($id_instance);
eko ($idu);

// On va supprimer tous les fichiers Dossier_Colectomie_Periode_P1 de la table formx
// $requete=new clRequete(BDD,TABLEFORMX,$param);
// $sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P1' and ids='".$ids."'");

//$param[nomitem] = addslashes(stripslashes($this->getMedecin ( ))) ;
//$req = new clResultQuery ;
//$res = $req -> Execute ( "Fichier", "getMatriculeMedecin", $param, "ResultQuery" ) ;

$item=utf8_decode($formx->getVar('L_AEV_Date_Accident_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Date_Accident_Situation_10',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Heure_Accident_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Heure_Accident_Situation_10',date("H").":".date("i"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Date_Prophylaxie_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Date_Prophylaxie_Situation_10',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Heure_Prophylaxie_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Heure_Prophylaxie_Situation_10',date("H").":".date("i"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Accident_Travail_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Accident_Travail_Situation_10',"Non");
  }

$item=utf8_decode($formx->getVar('L_AEV_Accident_Travail_Hyeres_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Accident_Travail_Hyeres_Situation_10',"Oui");
  }

$item=utf8_decode($formx->getVar('L_AEV_Accident_Travail_Etablissement_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Accident_Travail_Etablissement_Situation_10',"");
  }

$item=utf8_decode($formx->getVar('L_AEV_Source_Nom_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Source_Nom_Situation_10',"");
  }

$item=utf8_decode($formx->getVar('L_AEV_Source_Prenom_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Source_Prenom_Situation_10',"");
  }

$item=utf8_decode($formx->getVar('L_AEV_Source_Jour_Naissance_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Source_Jour_Naissance_Situation_10',"");
  }

$item=utf8_decode($formx->getVar('L_AEV_Source_Mois_Naissance_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Source_Mois_Naissance_Situation_10',"");
  }

$item=utf8_decode($formx->getVar('L_AEV_Source_Annee_Naissance_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Source_Annee_Naissance_Situation_10',"");
  }

$item=utf8_decode($formx->getVar('L_AEV_Complement_Situation_10'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Complement_Situation_10',"");
  }

return "O";
}
?>
