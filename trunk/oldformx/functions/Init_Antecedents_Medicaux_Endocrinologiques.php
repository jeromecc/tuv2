<?php
function Init_Antecedents_Medicaux_Endocrinologiques($formx) {

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Dyslipidemie_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Dyslipidemie_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Dyslipidemie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Dyslipidemie',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Dysthyroidie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Dysthyroidie',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Com'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Com',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Equi'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Equi',"Equilibré");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Ttt'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Ttt',"(ttt).");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Insu'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Insu',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Type'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Diabete_Type',"Type 1");
  }

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Diabete'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENDOCRINOLOGIQUES_Diabete',"Non");
  }






  



return "O";
}
?>
