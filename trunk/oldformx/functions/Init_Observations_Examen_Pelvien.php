<?php
function Init_Observations_Examen_Pelvien($formx) {

$item=utf8_decode($formx->getVar('L_Val_EABDOMINO_TR'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EABDOMINO_TR',"Champ Non Pr�cis�.");
  }
 
$item=utf8_decode($formx->getVar('L_Val_EABDOMINO_TV'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EABDOMINO_TV',"Champ Non Pr�cis�.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_EABDOMINO_Fosses'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EABDOMINO_Fosses',"Champ Non Pr�cis�.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_EABDOMINO_Rate'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EABDOMINO_Rate',"Champ Non Pr�cis�.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_EABDOMINO_Foie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EABDOMINO_Foie',"Champ Non Pr�cis�.");
  }

$item=utf8_decode($formx->getVar('L_Val_EABDOMINO_Signes'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EABDOMINO_Signes',"Champ Non Pr�cis�.");
  }  

return "O";
}
?>
