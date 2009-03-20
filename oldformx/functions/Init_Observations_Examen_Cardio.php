<?php
function Init_Observations_Examen_Cardio($formx) {


$item=utf8_decode($formx->getVar('L_Val_ECARDIO_Signes'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_Signes',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ECARDIO_Souffles'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_Souffles',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ECARDIO_Coeur'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_Coeur',"Réguliers");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ECARDIO_Pouls_I'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_Pouls_I',"Aucun Test");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ECARDIO_Pouls_S'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_Pouls_S',"Aucun Test");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ECARDIO_TA'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_TA',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_ECARDIO_SignesC'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECARDIO_SignesC',"Champ Non Précisé.");
  }
   

return "O";
}
?>
