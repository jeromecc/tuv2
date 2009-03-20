<?php
function Init_Suivi_Medical_Colectomie($formx) {

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Score_ASA'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Score_ASA',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Circonstances'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Circonstances',"");
  }



$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Circonstances_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Circonstances_C',"");
  }

  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Voie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Voie',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Type'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Type',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Cond'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Cond',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Etiologique'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Etiologique',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Exereses'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Exereses',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Exereses_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Exereses_Autres',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Exereses_Autres_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Exereses_Autres_C',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Stomie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Stomie',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Gestes'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Gestes',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Complications'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Complications',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Complications_Autres_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Complications_Autres_C',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Complications_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Complications_Autres',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Intervention'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Intervention',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Intervention_D'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Intervention_D',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Intervention_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Intervention_C',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Deces'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Deces',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Deces_D'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Deces_D',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Deces_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Deces_C',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Transfusion'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Transfusion',"");
  }
  
$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_TumeurT'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_TumeurT',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_TumeurN'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_TumeurN',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_TumeurM'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_TumeurM',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_RCP'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_RCP',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Annonce'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Annonce',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Feuille'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Feuille',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Spychiatrique'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Spychiatrique',"");
  }

$item=utf8_decode($formx->getVar('L_Val_COLECTOMIE_Courrier'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_COLECTOMIE_Courrier',"");
  }
  
return "O";
}
?>
