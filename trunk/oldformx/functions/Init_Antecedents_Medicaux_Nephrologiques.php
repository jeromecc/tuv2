<?php
function Init_Antecedents_Medicaux_Nephrologiques($formx) {


$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Renal_Dial_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Renal_Dial_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Renal_Dial_Depuis'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Renal_Dial_Depuis',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Renal_Dial'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Renal_Dial',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Renal_CL'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Renal_CL',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Renal_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Renal_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Renal'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Renal',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Pyelonephrites_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Pyelonephrites_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Pyelonephrites'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Pyelonephrites',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Infections_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Infections_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Infections'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Infections',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Hypertrophie_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Hypertrophie_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_NEPHROLOGIQUES_Hypertrophie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_NEPHROLOGIQUES_Hypertrophie',"Non");
  }













return "O";
}
?>
