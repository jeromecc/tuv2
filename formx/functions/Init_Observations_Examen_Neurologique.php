<?php
function Init_Observations_Examen_Neurologique($formx) {

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Epr'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Epr',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Mar'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Mar',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Sensp'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Sensp',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Senss'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Senss',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Coor'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Coor',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_RCP'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_RCP',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_RCA'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_RCA',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_ROT'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_ROT',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ENEURO_Mot'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Mot',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Ton'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Ton',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Pupi'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Pupi',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Pair'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Pair',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ENEURO_Fonc'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Fonc',"Normales");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Vig'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Vig',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Con_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Con_C',"Oui");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Con_Glasgow_Y'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Con_Glasgow_Y',"4");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Con_Glasgow_V'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Con_Glasgow_V',"5");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Con_Glasgow_M'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ENEURO_Con_Glasgow_M',"6");
  }

$item=utf8_decode($formx->getVar('L_Val_ENEURO_Score_Glasgow_W'));
if ( $item == "Afficher la (nouvelle) valeur" )
  {
  $formx->setVar('L_Val_ENEURO_Score_Glasgow_W',"Afficher la (nouvelle) valeur");
  }


return "O";
}
?>
