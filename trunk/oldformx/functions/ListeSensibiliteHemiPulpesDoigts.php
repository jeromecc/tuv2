<?php
function ListeSensibiliteHemiPulpesDoigts($formx) {

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


$liste["Pas d'anomalie"]="Pas d'anomalie";
$liste["Pulpe radiale V doigt Droit anormale"]    ="Pulpe radiale V doigt Droit anormale";
$liste["Pulpe cubitale V doigt Droit anormale"]   ="Pulpe cubitale V doigt Droit anormale";
$liste["Pulpe radiale V doigt Gauche anormale"]   ="Pulpe radiale V doigt Gauche anormale";
$liste["Pulpe cubitale V doigt Gauche anormale"]  ="Pulpe cubitale V doigt Gauche anormale";
$liste["Pulpe radiale IV doigt Droit anormale"]   ="Pulpe radiale IV doigt Droit anormale";
$liste["Pulpe cubitale IV doigt Droit anormale"]  ="Pulpe cubitale IV doigt Droit anormale";
$liste["Pulpe radiale IV doigt Gauche anormale"]  ="Pulpe radiale IV doigt Gauche anormale";
$liste["Pulpe cubitale IV doigt Gauche anormale"] ="Pulpe cubitale IV doigt Gauche anormale";
$liste["Pulpe radiale III doigt Droit anormale"]  ="Pulpe radiale III doigt Droit anormale";
$liste["Pulpe cubitale III doigt Droit anormale"] ="Pulpe cubitale III doigt Droit anormale";
$liste["Pulpe radiale III doigt Gauche anormale"] ="Pulpe radiale III doigt Gauche anormale";
$liste["Pulpe cubitale III doigt Gauche anormale"]="Pulpe cubitale III doigt Gauche anormale";
$liste["Pulpe radiale II doigt Droit anormale"]   ="Pulpe radiale II doigt Droit anormale";
$liste["Pulpe cubitale II doigt Droit anormale"]  ="Pulpe cubitale II doigt Droit anormale";
$liste["Pulpe radiale II doigt Gauche anormale"]  ="Pulpe radiale II doigt Gauche anormale";
$liste["Pulpe cubitale II doigt Gauche anormale"] ="Pulpe cubitale II doigt Gauche anormale";
$liste["Pulpe radiale I doigt Droit anormale"]    ="Pulpe radiale I doigt Droit anormale";
$liste["Pulpe cubitale I doigt Droit anormale"]   ="Pulpe cubitale I doigt Droit anormale";
$liste["Pulpe radiale I doigt Gauche anormale"]   ="Pulpe radiale I doigt Gauche anormale";
$liste["Pulpe cubitale I doigt Gauche anormale"]  ="Pulpe cubitale I doigt Gauche anormale";

    
}

elseif ( $Presence_Doigt_Gauche ) {

$liste["Pas d'anomalie"]="Pas d'anomalie";
$liste["Pulpe radiale V doigt Gauche anormale"]   ="Pulpe radiale V doigt Gauche anormale";
$liste["Pulpe cubitale V doigt Gauche anormale"]  ="Pulpe cubitale V doigt Gauche anormale";
$liste["Pulpe radiale IV doigt Gauche anormale"]  ="Pulpe radiale IV doigt Gauche anormale";
$liste["Pulpe cubitale IV doigt Gauche anormale"] ="Pulpe cubitale IV doigt Gauche anormale";
$liste["Pulpe radiale III doigt Gauche anormale"] ="Pulpe radiale III doigt Gauche anormale";
$liste["Pulpe cubitale III doigt Gauche anormale"]="Pulpe cubitale III doigt Gauche anormale";
$liste["Pulpe radiale II doigt Gauche anormale"]  ="Pulpe radiale II doigt Gauche anormale";
$liste["Pulpe cubitale II doigt Gauche anormale"] ="Pulpe cubitale II doigt Gauche anormale";
$liste["Pulpe radiale I doigt Gauche anormale"]   ="Pulpe radiale I doigt Gauche anormale";
$liste["Pulpe cubitale I doigt Gauche anormale"]  ="Pulpe cubitale I doigt Gauche anormale";

}

elseif ( $Presence_Doigt_Droite ) {

$liste["Pas d'anomalie"]="Pas d'anomalie";
$liste["Pulpe radiale V doigt Droit anormale"]    ="Pulpe radiale V doigt Droit anormale";
$liste["Pulpe cubitale V doigt Droit anormale"]   ="Pulpe cubitale V doigt Droit anormale";
$liste["Pulpe radiale IV doigt Droit anormale"]   ="Pulpe radiale IV doigt Droit anormale";
$liste["Pulpe cubitale IV doigt Droit anormale"]  ="Pulpe cubitale IV doigt Droit anormale";
$liste["Pulpe radiale III doigt Droit anormale"]  ="Pulpe radiale III doigt Droit anormale";
$liste["Pulpe cubitale III doigt Droit anormale"] ="Pulpe cubitale III doigt Droit anormale";
$liste["Pulpe radiale II doigt Droit anormale"]   ="Pulpe radiale II doigt Droit anormale";
$liste["Pulpe cubitale II doigt Droit anormale"]  ="Pulpe cubitale II doigt Droit anormale";
$liste["Pulpe radiale I doigt Droit anormale"]    ="Pulpe radiale I doigt Droit anormale";
$liste["Pulpe cubitale I doigt Droit anormale"]   ="Pulpe cubitale I doigt Droit anormale";

}
  
  
  //$liste[ utf8_encode($liste1)]= $liste2;
  
  
  
return ($liste);

}
?>
