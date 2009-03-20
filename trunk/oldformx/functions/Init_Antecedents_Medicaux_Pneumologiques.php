<?php
function Init_Antecedents_Medicaux_Pneumologiques($formx) {

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Autres',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_BK_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_BK_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_BK'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_BK',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Odeux_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Odeux_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Odeux_Depuis'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Odeux_Depuis',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Odeux'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Odeux',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Insuffisance'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Insuffisance',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_BPCO_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_BPCO_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_BPCO'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_BPCO',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Asthme_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Asthme_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PNEUMOLOGIQUES_Asthme'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PNEUMOLOGIQUES_Asthme',"Non");
  }







  


  


  

  


  

  


return "O";
}
?>
