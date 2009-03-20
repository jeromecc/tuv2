<?php
function Init_Fiche_Colectomie_Periode_P1($formx) {

$item=utf8_decode($formx->getVar('L_Val_ENDOCRINOLOGIQUES_Dyslipidemie_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Bil_Com',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Bil_Com_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Bil_Com_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Tra_Med'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Tra_Med',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Tra_Med_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Tra_Med_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Pre_Col'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Pre_Col',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Pre_Col_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Pre_Col_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Pre_Par'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Pre_Par',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Pre_Par_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Pre_Par_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Die_Hyd'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Die_Hyd',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Die_Hyd_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Die_Hyd_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Apr_Eta_CM'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Apr_Eta_CM',"Indépendant");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Apr_Eta_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Apr_Eta_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Dem_Soi'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Dem_Soi',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Dem_Soi_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Dem_Soi_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Rec_Res'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Rec_Res',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Rec_Res_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Rec_Res_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Heu_Int'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Heu_Int',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Heu_Int_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Heu_Int_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Vis_Pre'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Vis_Pre',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Vis_Pre_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Vis_Pre_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Vis_Chi'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Vis_Chi',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Vis_Chi_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Vis_Chi_C',"");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Pat_Pre'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Pat_Pre',"Non");
  }

$item=utf8_decode($formx->getVar('L_Colectomie_Periode_P1_Pat_Pre_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Colectomie_Periode_P1_Pat_Pre_C',"");
  }

return "O";
}
?>
