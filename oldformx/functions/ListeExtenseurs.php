<?php
function ListeExtenseurs($formx) {

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

$liste["Pas d'anomalie"]                      ="Pas d'anomalie";
$liste["Extension V doigt Droit anormale"]    ="Extension V doigt Droit anormale";
$liste["Extension V doigt Gauche anormale"]   ="Extension V doigt Gauche anormale";
$liste["Extension IV doigt Droit anormale"]   ="Extension IV doigt Droit anormale";
$liste["Extension IV doigt Gauche anormale"]  ="Extension IV doigt Gauche anormale";
$liste["Extension III doigt Droit anormale"]  ="Extension III doigt Droit anormale";
$liste["Extension III doigt Gauche anormale"] ="Extension III doigt Gauche anormale";
$liste["Extension II doigt Droit anormale"]   ="Extension II doigt Droit anormale";
$liste["Extension II doigt Gauche anormale"]  ="Extension II doigt Gauche anormale";
$liste["Extension I doigt Droit anormale"]    ="Extension I doigt Droit anormale";
$liste["Extension I doigt Gauche anormale"]   ="Extension I doigt Gauche anormale";
    
}

elseif ( $Presence_Doigt_Gauche ) {

$liste["Pas d'anomalie"]                      ="Pas d'anomalie";
$liste["Extension V doigt Gauche anormale"]   ="Extension V doigt Gauche anormale";
$liste["Extension IV doigt Gauche anormale"]  ="Extension IV doigt Gauche anormale";
$liste["Extension III doigt Gauche anormale"] ="Extension III doigt Gauche anormale";
$liste["Extension II doigt Gauche anormale"]  ="Extension II doigt Gauche anormale";
$liste["Extension I doigt Gauche anormale"]   ="Extension I doigt Gauche anormale";

}

elseif ( $Presence_Doigt_Droite ) {

$liste["Pas d'anomalie"]                      ="Pas d'anomalie";
$liste["Extension V doigt Droit anormale"]    ="Extension V doigt Droit anormale";
$liste["Extension IV doigt Droit anormale"]   ="Extension IV doigt Droit anormale";
$liste["Extension III doigt Droit anormale"]  ="Extension III doigt Droit anormale";
$liste["Extension II doigt Droit anormale"]   ="Extension II doigt Droit anormale";
$liste["Extension I doigt Droit anormale"]    ="Extension I doigt Droit anormale";

}
  
  
  //$liste[ utf8_encode($liste1)]= $liste2;
  
  
  
return ($liste);

}
?>
