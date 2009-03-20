<?php
function Init_Antecedents_Chirurgicaux_Pelviens($formx) {

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Chirg_CCom'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Chirg_CCom',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Chirg_Choix'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Chirg_Choix',"Hystérectomie");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Chirg'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Chirg',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Chirp_CCom'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Chirp_CCom',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Chirp_Choix'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Chirp_Choix',"Prostatectomie");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Chirp'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Chirp',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Gast_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Gast_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Gast'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Gast',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Hemi_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Hemi_Comm',"Droite");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Hemi'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Hemi',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Cure_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Cure_Comm',"Droite");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Cure'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Cure',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Cholo_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Cholo_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Cholo'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Cholo',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Append_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Append_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PELVIENS_Append'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PELVIENS_Append',"Non");
  }



























return "O";
}
?>
