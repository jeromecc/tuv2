<?php
function Calcul_IMC($formx) {
$poids  = utf8_decode($formx->getFormVar('Val_ESIGNES_Poids'));
$taille = utf8_decode($formx->getFormVar('Val_ESIGNES_Taille'));
eko ($taille);
eko ($poids);
$taille = str_replace(",",".",$taille);
$poids  = str_replace(",",".",$poids);
$taille = str_replace("m",".",$taille);
eko ($taille);
eko ($poids);
if ( $taille > 0 && $poids > 0 ) 
  return number_format($poids/($taille*$taille),2);
return "0";
}
?>
