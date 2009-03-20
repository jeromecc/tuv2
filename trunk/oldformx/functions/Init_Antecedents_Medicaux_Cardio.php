<?php
function Init_Antecedents_Medicaux_Cardio($formx) {

$item=utf8_decode($formx->getVar('L_Val_CARDIO_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Autres',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Insuffisance_Fevg_Depuis'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Insuffisance_Fevg_Depuis',date("d")."-".date("m")."-".date("Y"));
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Insuffisance_Fevg'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Insuffisance_Fevg',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Insuffisance'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Insuffisance',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Valvulopathies_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Valvulopathies_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Valvulopathies'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Valvulopathies',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Maladie_Stent_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Maladie_Stent_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Maladie_Stent'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Maladie_Stent',"Non");
  }
  
$item=utf8_decode($formx->getVar('Val_CARDIO_Maladie_Coro_Comm'));
if ( $item == "" )
  {
  $formx->setVar('Val_CARDIO_Maladie_Coro_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Maladie_Coro'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Maladie_Coro',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_MaladieA_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_MaladieA_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Maladie_Ask'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Maladie_Ask',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_MaladieA'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_MaladieA',"Angor");
  }
  
$item=utf8_decode($formx->getVar('L_Val_CARDIO_Troubles_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Troubles_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_CARDIO_Troubles'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_Troubles',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_CARDIO_HTA'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CARDIO_HTA',"Non");
  }
  

  



  

  

  



  


  

  

  

  







  

  

  
  


  

  


return "O";
}
?>
