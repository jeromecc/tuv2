<?php
function Init_Fiche_Colectomie_Periode_P5($formx) {

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Bil_Pro'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Bil_Pro',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Bil_Pro_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Bil_Pro_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Abl_Fil'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Abl_Fil',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Abl_Fil_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Abl_Fil_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Eva_Aut'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Eva_Aut',"Indépendant");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Eva_Aut_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Eva_Aut_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Con_Die'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Con_Die',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Con_Die_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Con_Die_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Pla_Pos'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Pla_Pos',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Pla_Pos_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Pla_Pos_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Doc_Sor'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Doc_Sor',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Doc_Sor_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Doc_Sor_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Pat_Inf'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Pat_Inf',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P5_Pat_Inf_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P5_Pat_Inf_C',"");
  }

return "O";
}
?>
