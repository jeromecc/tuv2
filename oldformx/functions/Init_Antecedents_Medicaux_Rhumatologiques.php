<?php
function Init_Antecedents_Medicaux_Rhumatologiques($formx) {

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Rachidiennes_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Rachidiennes_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Rachidiennes'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Rachidiennes',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Chondrocalcinose_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Chondrocalcinose_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Chondrocalcinose'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Chondrocalcinose',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Goutte_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Goutte_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Goutte'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Goutte',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Rhumatoide_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Rhumatoide_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Rhumatoide'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Rhumatoide',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Osteoporotique_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Osteoporotique_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Osteoporotique'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Osteoporotique',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Rhumatismale_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Rhumatismale_Comm',"Champ Non Précisé.");
  }


$item=utf8_decode($formx->getVar('L_Val_RHUMATOLOGIQUES_Rhumatismale'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_RHUMATOLOGIQUES_Rhumatismale',"Non");
  }













return "O";
}
?>
