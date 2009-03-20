<?php
function ListeFlechisseursCommunsSuperficiels($formx) {

$on_continue = 1;
$Presence_Doigt_Gauche = 0;
$Presence_Doigt_Droite = 0;

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_5'),"Gauche") == 0 )
  { $on_continue = 0; $Presence_Doigt_Gauche = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_5'),"Droite") == 0 )
  { $on_continue = 0; $Presence_Doigt_Droite = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_4'),"Gauche") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Gauche = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_4'),"Droite") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Droite = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_3'),"Gauche") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Gauche = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_3'),"Droite") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Droite = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_2'),"Gauche") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Gauche = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_2'),"Droite") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Droite = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_1'),"Gauche") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Gauche = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main_1'),"Droite") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Droite = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main'),"Gauche") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Gauche = 1; }

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Main'),"Droite") == 0 && $on_continue )
  { $on_continue = 0; $Presence_Doigt_Droite = 1; }


if ( $Presence_Doigt_Gauche && $Presence_Doigt_Droite ) {

$liste["Pas d'anomalie"]                    ="Pas d'anomalie";
$liste["Flexion V doigt Droit anormale"]    ="Flexion V doigt Droit anormale";
$liste["Flexion V doigt Gauche anormale"]   ="Flexion V doigt Gauche anormale";
$liste["Flexion IV doigt Droit anormale"]   ="Flexion IV doigt Droit anormale";
$liste["Flexion IV doigt Gauche anormale"]  ="Flexion IV doigt Gauche anormale";
$liste["Flexion III doigt Droit anormale"]  ="Flexion III doigt Droit anormale";
$liste["Flexion III doigt Gauche anormale"] ="Flexion III doigt Gauche anormale";
$liste["Flexion II doigt Droit anormale"]   ="Flexion II doigt Droit anormale";
$liste["Flexion II doigt Gauche anormale"]  ="Flexion II doigt Gauche anormale";
$liste["Flexion I doigt Droit anormale"]    ="Flexion I doigt Droit anormale";
$liste["Flexion I doigt Gauche anormale"]   ="Flexion I doigt Gauche anormale";
    
}

elseif ( $Presence_Doigt_Gauche ) {

$liste["Pas d'anomalie"]                    ="Pas d'anomalie";
$liste["Flexion V doigt Gauche anormale"]   ="Flexion V doigt Gauche anormale";
$liste["Flexion IV doigt Gauche anormale"]  ="Flexion IV doigt Gauche anormale";
$liste["Flexion III doigt Gauche anormale"] ="Flexion III doigt Gauche anormale";
$liste["Flexion II doigt Gauche anormale"]  ="Flexion II doigt Gauche anormale";
$liste["Flexion I doigt Gauche anormale"]   ="Flexion I doigt Gauche anormale";

}

elseif ( $Presence_Doigt_Droite ) {

$liste["Pas d'anomalie"]                    ="Pas d'anomalie";
$liste["Flexion V doigt Droit anormale"]    ="Flexion V doigt Droit anormale";
$liste["Flexion IV doigt Droit anormale"]   ="Flexion IV doigt Droit anormale";
$liste["Flexion III doigt Droit anormale"]  ="Flexion III doigt Droit anormale";
$liste["Flexion II doigt Droit anormale"]   ="Flexion II doigt Droit anormale";
$liste["Flexion I doigt Droit anormale"]    ="Flexion I doigt Droit anormale";
}
  
  
  //$liste[ utf8_encode($liste1)]= $liste2;
  
  
  
return ($liste);

}
?>
