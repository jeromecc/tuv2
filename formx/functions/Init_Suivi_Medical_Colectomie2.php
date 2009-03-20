<?php
function Init_Suivi_Medical_Colectomie2($formx) {

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Score_ASA_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Score_ASA_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Circonstances_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Circonstances_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Circonstances_C_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Circonstances_C_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Voie_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Voie_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Type_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Type_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Cond_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Cond_2',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Etiologique_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Etiologique_2',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Exereses_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Exereses_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Exereses_Autres_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Exereses_Autres_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Exereses_Autres_C_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Exereses_Autres_C_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Stomie_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Stomie_2',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Gestes_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Gestes_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Complications_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Complications_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Complications_Autres_C_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Complications_Autres_C_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Complications_Autres_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Complications_Autres_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Intervention_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Intervention_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Intervention_D_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Intervention_D_2',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Intervention_C_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Intervention_C_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Deces_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Deces_2',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Deces_D_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Deces_D_2',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Deces_C_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Deces_C_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Transfusion_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Transfusion_2',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_TumeurT_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_TumeurT_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_TumeurN_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_TumeurN_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_TumeurM_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_TumeurM_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_RCP_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_RCP_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Annonce_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Annonce_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Feuille_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Feuille_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Spychiatrique_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Spychiatrique_2',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Courrier_2'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Courrier_2',"");
  }
  
return "O";
}
?>
