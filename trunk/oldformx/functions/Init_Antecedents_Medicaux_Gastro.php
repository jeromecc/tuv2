<?php
function Init_Antecedents_Medicaux_Gastro($formx) {

$item=utf8_decode($formx->getVar('L_Val_GASTRO_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_GASTRO_Hemorroidaire_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Hemorroidaire_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_GASTRO_Hemorroidaire'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Hemorroidaire',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_GASTRO_Colopathie_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Colopathie_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_GASTRO_Colopathie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Colopathie',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_GASTRO_Maladie_choix'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Maladie_choix',"Chronn");
  }

$item=utf8_decode($formx->getVar('L_Val_GASTRO_Maladie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Maladie',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_GASTRO_Ulcere_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Ulcere_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_GASTRO_Ulcere'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_GASTRO_Ulcere',"Non");
  }






  



  


  

  


return "O";
}
?>
