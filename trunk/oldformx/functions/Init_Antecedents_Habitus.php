<?php
function Init_Antecedents_Habitus($formx) {

$item=utf8_decode($formx->getVar('L_Val_HABITUS_Vie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Vie',"Actif");
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_Retraite'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Retraite',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_HABITUS_Prof'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Prof',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_Allergie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Allergie',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_ALCOOL_Test'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_ALCOOL_Test',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_HABITUS_TABAC_Paquet'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_TABAC_Paquet',"0 ");
  }
  
$item=utf8_decode($formx->getVar('L_Val_HABITUS_TABAC_Non_Sevre'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_TABAC_Non_Sevre',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_HABITUS_ALCOOL_LITRE'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_ALCOOL_LITRE',"50");
  }
  
$item=utf8_decode($formx->getVar('L_Val_HABITUS_ALCOOL_Non_Sevre'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_ALCOOL_Non_Sevre',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_ALCOOL_Sevre_Depuis'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_ALCOOL_Sevre_Depuis',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_Iode'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Iode',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_Medicament'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Medicament',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_HABITUS_Autre'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_Autre',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_HABITUS_TABAC_Sevre_Depuis'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_HABITUS_TABAC_Sevre_Depuis',date("d")."-".date("m")."-".date("Y"));
  }
  






  

  

  
  


  

  


return "O";
}
?>
