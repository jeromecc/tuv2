<?php
function Init_Formulaire_Anesthesie($formx) {

$item=utf8_decode($formx->getVar('L_Anesthesie_Intervention_Ambulatoire'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Intervention_Ambulatoire',"Non");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Hospitalisation_Lieu'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Hospitalisation_Lieu',"Consultations externes");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Hospitalisation_Autre'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Hospitalisation_Autre',"");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Hospitalisation_Chambre'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Hospitalisation_Chambre',"");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Hospitalisation_Lit'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Hospitalisation_Lit',"P");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Interrogatoire_Antecedent'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Interrogatoire_Antecedent',"Non");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Interrogatoire_Complications'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Interrogatoire_Complications',"Non");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Interrogatoire_Complications_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Interrogatoire_Complications_C',"Réaction allergique");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Interrogatoire_Dossier'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Interrogatoire_Dossier',"Non");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Transfusion'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Transfusion',"Non");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Transfusion_Date'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Transfusion_Date',"");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Transfusion_Intervention'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Transfusion_Intervention',"");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Transfusion_Anesthesie'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Transfusion_Anesthesie',"");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Transfusion_Complications'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Transfusion_Complications',"Non");
  }

$item=utf8_decode($formx->getVar('L_Anesthesie_Transfusion_Complications_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Anesthesie_Transfusion_Complications_C',"");
  }

















  




return "O";
}
?>
