<?php
function Init_Observations_Signes_Generaux($formx) {

$item=utf8_decode($formx->getVar('L_Val_ESIGNES_Temp'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ESIGNES_Temp',"°C");
  }

$item=utf8_decode($formx->getVar('L_Val_ESIGNES_Taille'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ESIGNES_Taille',"0");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ESIGNES_Poids'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ESIGNES_Poids',"0");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ESIGNES_Etats'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ESIGNES_Etats',"Conservé");
  }

$item=utf8_decode($formx->getVar('L_Val_ESIGNES_IMC_W'));
if ( $item == "Afficher la (nouvelle) valeur" )
  {
  $formx->setVar('L_Val_ESIGNES_IMC_W',"Afficher la (nouvelle) valeur");
  }
  
  


  

  


return "O";
}
?>
