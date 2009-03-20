<?php
function Init_Antecedents_Chirurgicaux_Orthopediques($formx) {

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Fracture_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Fracture_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Fracture'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Fracture',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Ptg_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Ptg_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Ptg_Choix'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Ptg_Choix',"Droite");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Ptg'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Ptg',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Pth_Com'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Pth_Com',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Pth_Choix'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Pth_Choix',"Droite");
  }

$item=utf8_decode($formx->getVar('L_Val_ORTHOPEDIQUES_Pth'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ORTHOPEDIQUES_Pth',"Non");
  }



























return "O";
}
?>
