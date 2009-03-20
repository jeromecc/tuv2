<?php
function Init_Antecedents_Gyneco_Obstetricaux($formx) {

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_Geu_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_Geu_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_Geu'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_Geu',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_Fcs_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_Fcs_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_Fcs'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_Fcs',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_Ivg_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_Ivg_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_Ivg'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_Ivg',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_P'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_P',"0 ");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSTETRICAUX_G'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSTETRICAUX_G',"0 ");
  }














return "O";
}
?>
