<?php
function Init_Fiche_Colectomie_Periode_P2($formx) {

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Soi_Pre'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Soi_Pre',"Non Fait");
  }
  
$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Soi_Pre_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Soi_Pre_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Sur_Rap'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Sur_Rap',"Non Fait");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Sur_Rap_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Sur_Rap_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Sng'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Sng',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Sng_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Sng_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Sur_Diu'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Sur_Diu',"Non Fait");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Sur_Diu_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Sur_Diu_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Pri_Cha'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Pri_Cha',"Autres");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Pri_Cha_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Pri_Cha_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Dou_Mai'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Dou_Mai',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Dou_Mai_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Dou_Mai_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Dou_Eva'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Dou_Eva',"Echelle numérique");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Dou_Eva_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Dou_Eva_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Tra_Ant'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Tra_Ant',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Tra_Ant_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Tra_Ant_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Aut_Pre'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Aut_Pre',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Aut_Pre_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Aut_Pre_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Vis_Med'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Vis_Med',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Vis_Med_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Vis_Med_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Ren_Fam'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Ren_Fam',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Ren_Fam_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Ren_Fam_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Ret_Ser'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Ret_Ser',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P2_Ret_Ser'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P2_Ret_Ser',"");
  }


return "O";
}
?>
