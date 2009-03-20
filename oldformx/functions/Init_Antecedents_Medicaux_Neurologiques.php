<?php
function Init_Antecedents_Medicaux_Neurologiques($formx) {

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_E_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_E_C',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_E'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_E',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Avc_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Avc_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Avc_S'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Avc_S',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Avc_L'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Avc_L',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Avc_D'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Avc_D',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Avc_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Avc_C',"Ischémique");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Avc'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Avc',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Ait_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Ait_C',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Ait'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Ait',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Park_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Park_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Park'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Park',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Al_MMS'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Al_MMS',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Al_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Al_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEUROLOGIQUES_Al'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEUROLOGIQUES_Al',"Non");
  }
























return "O";
}
?>
