<?php
function Init_Antecedents_Medicaux_Oncologiques($formx) {

$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_Trait'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_Trait',"Chirurgie");
  }

$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_TraitQ'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_TraitQ',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_Nature'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_Nature',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_Localisation_Secondaire'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_Localisation_Secondaire',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_Localisation_Primaire'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_Localisation_Primaire',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_Localisation'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_Localisation',"Primaire");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ONCOLOGIQUES_Diagnostic'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ONCOLOGIQUES_Diagnostic',"Champ Non Précisé.");
  }
  

  

  



return "O";
}
?>
