<?php
function Init_Formulaire_Bio($formx) {

$item=utf8_decode($formx->getVar('L_Val_F_BIO_Liste_Choix'));
$val =utf8_decode($formx->getFormVar('Val_F_BIO_Liste_Choix'));
if ( $item == "" || $val == "")
  {
  $formx->setVar('L_Val_F_BIO_Liste_Choix',"Tout Décocher");
  }

return "O";
}
?>
