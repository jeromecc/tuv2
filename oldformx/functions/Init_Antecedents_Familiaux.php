<?php
function Init_Antecedents_Familiaux($formx) {

$item=utf8_decode($formx->getVar('L_Val_FAMILLIAUX_Grandparent'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_FAMILLIAUX_Grandparent',"Champ Non Pr�cis�.");
  }

$item=utf8_decode($formx->getVar('L_Val_FAMILLIAUX_Pere'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_FAMILLIAUX_Pere',"Champ Non Pr�cis�.");
  }

$item=utf8_decode($formx->getVar('L_Val_FAMILLIAUX_Mere'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_FAMILLIAUX_Mere',"Champ Non Pr�cis�.");
  }

$item=utf8_decode($formx->getVar('L_Val_FAMILLIAUX_Fratrie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_FAMILLIAUX_Fratrie',"Champ Non Pr�cis�.");
  }

$item=utf8_decode($formx->getVar('L_Val_FAMILLIAUX_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_FAMILLIAUX_Autres',"Champ Non Pr�cis�.");
  }



return "O";
}
?>
