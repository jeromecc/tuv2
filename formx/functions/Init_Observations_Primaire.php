<?php
function Init_Observations_Primaire($formx) {

$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_TH'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_TH',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_HM'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_HM',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_MH'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_MH',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_MA_AE'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_MA_AE',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_MA_AS'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_MA_AS',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_MA'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_MA',"Domicile");
  }

$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_PRIMAIRES_DH'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_PRIMAIRES_DH',date("d")."-".date("m")."-".date("Y"));
  }


return "O";
}
?>
