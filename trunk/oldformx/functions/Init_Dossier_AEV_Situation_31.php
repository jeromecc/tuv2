<?php
function Init_Dossier_AEV_Situation_31($formx) {

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

$item=utf8_decode($formx->getVar('L_AEV_Date_Accident_Situation_31'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Date_Accident_Situation_31',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Heure_Accident_Situation_31'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Heure_Accident_Situation_31',date("H").":".date("i"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Prophylaxie_Demande_31'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Prophylaxie_Demande_31',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_AEV_Date_Prophylaxie_Situation_31'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Date_Prophylaxie_Situation_31',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Heure_Prophylaxie_Situation_31'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Heure_Prophylaxie_Situation_31',date("H").":".date("i"));
  }

$item=utf8_decode($formx->getVar('L_AEV_Complement_Situation_31'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Complement_Situation_31',"");
  }


return "O";
}
?>