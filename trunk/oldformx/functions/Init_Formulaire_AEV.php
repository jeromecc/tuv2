<?php
function Init_Formulaire_AEV($formx) {

// On determine dans quelle situation on se trouve

$AEV_Type                = utf8_decode($formx->getFormVar('AEV_Type'));
$AEV_Exposition_Sang     = utf8_decode($formx->getFormVar('AEV_Exposition_Sang'));
$AEV_Exposition_Sexuelle = utf8_decode($formx->getFormVar('AEV_Exposition_Sexuelle'));
$AEV_Exposition_Drogue   = utf8_decode($formx->getFormVar('AEV_Exposition_Drogue'));
$AEV_Source              = utf8_decode($formx->getFormVar('AEV_Source'));

//eko($AEV_Type);
//eko($AEV_Exposition_Sang);
//eko($AEV_Exposition_Sexuelle);
//eko($AEV_Exposition_Drogue);
eko($AEV_Source);

switch ( $AEV_Type ) {
case "Expositions au sang":
  {
  if ( $AEV_Exposition_Sang == "Important" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 1;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 10;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 19;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 28;
    } 
  if ( $AEV_Exposition_Sang == "Intermédiaire" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 2;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 11;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 20;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 29;  
    }
  if ( $AEV_Exposition_Sang == "Minime" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 3;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 12;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 21;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 30;
    }
  $dossier = $AEV_Type." Risque ".$AEV_Exposition_Sang." ".$AEV_Source;   
  }break;
case "Expositions sexuelles":
  {
  if ( $AEV_Exposition_Sexuelle == "Rapports anaux" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 4;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 13;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 22;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 31;
    } 
  if ( $AEV_Exposition_Sexuelle == "Rapports vaginaux" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 5;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 14;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 23;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 32;
    }
  if ( $AEV_Exposition_Sexuelle == "Fellation réceptive avec éjaculation" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 6;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 15;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 24;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 33;
    }
  $dossier = $AEV_Type." Avec ".$AEV_Exposition_Sexuelle." ".$AEV_Source;     
  }break;
case "Expositions chez les usagers de drogues":
  {
  if ( $AEV_Exposition_Drogue == "Important" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 7;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 16;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 25;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 34;
    } 
  if ( $AEV_Exposition_Drogue == "Intermédiaire" ) {
      if ( $AEV_Source == "Infecté par le VIH" )        $situation = 8;
      if ( $AEV_Source == "Infecté par le VHC" )        $situation = 17;
      if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 26;
      if ( $AEV_Source == "De sérologie inconnue" )     $situation = 35;
    }
  $dossier = $AEV_Type." Risque ".$AEV_Exposition_Drogue." ".$AEV_Source;   
  }break;
case "Autres situations":
  {
    if ( $AEV_Source == "Infecté par le VIH" )        $situation = 9;
    if ( $AEV_Source == "Infecté par le VHC" )        $situation = 18;
    if ( $AEV_Source == "Infecté par le VIH et VHC" ) $situation = 27;
    if ( $AEV_Source == "De sérologie inconnue" )     $situation = 36;
  $dossier = $AEV_Type." ".$AEV_Source;     
  }break;
}

eko($situation);

$formx->setVar('L_AEV_Situation',$situation);
$formx->setVar('L_AEV_Dossier',$dossier);

$item=utf8_decode($formx->getVar('L_AEV_Type'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Type',"");
  }
  
$item=utf8_decode($formx->getVar('L_AEV_Exposition_Sang'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Exposition_Sang',"Important");
  }

$item=utf8_decode($formx->getVar('L_AEV_Exposition_Sexuelle'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Exposition_Sexuelle',"Rapports anaux");
  }

$item=utf8_decode($formx->getVar('L_AEV_Exposition_Drogue'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Exposition_Drogue',"Important");
  }

$item=utf8_decode($formx->getVar('L_AEV_Source'));
if ( $item == "" )
  {
  $formx->setVar('L_AEV_Source',"Infecté par le VIH");
  }

return "O";
}
?>
