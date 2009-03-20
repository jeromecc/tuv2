<?php
function Init_Antecedents_Medicaux_Infectieux($formx) {

$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Autres',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Paludisme_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Paludisme_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Paludisme'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Paludisme',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Hepatites_Detail'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Hepatites_Detail',"A");
  }
  
$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Hepatites'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Hepatites',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Tuberculose_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Tuberculose_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_INFECTIEUX_Tuberculose'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_INFECTIEUX_Tuberculose',"Non");
  }

  

  

  
  
  
  

  


  

  

  



return "O";
}
?>
