<?php
function Init_Fiche_Colectomie_Periode_P6($formx) {

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Cau_Chi'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Cau_Chi',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Cau_Chi_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Cau_Chi_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Cau_Med'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Cau_Med',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Cau_Med_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Cau_Med_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Def_Aut'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Def_Aut',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Def_Aut_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Def_Aut_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Att_Mai'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Att_Mai',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Att_Mai_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Att_Mai_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Cau_Soc'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Cau_Soc',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P6_Cau_Soc_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P6_Cau_Soc_C',"");
  }

return "O";
}
?>
