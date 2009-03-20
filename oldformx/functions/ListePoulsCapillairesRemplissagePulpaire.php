<?php
function ListePoulsCapillairesRemplissagePulpaire($formx) {


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

$liste["Pas d'anomalie"]   ="Pas d'anomalie";
$liste["V doigt Droit"]    ="V doigt Droit";
$liste["V doigt Gauche"]   ="V doigt Gauche";
$liste["IV doigt Droit"]   ="IV doigt Droit";
$liste["IV doigt Gauche"]  ="IV doigt Gauche";
$liste["III doigt Droit"]  ="III doigt Droit";
$liste["III doigt Gauche"] ="III doigt Gauche";
$liste["II doigt Droit"]   ="II doigt Droit";
$liste["II doigt Gauche"]  ="II doigt Gauche";
$liste["I doigt Droit"]    ="I doigt Droit";
$liste["I doigt Gauche"]   ="I doigt Gauche";
		
}
elseif ( $Presence_Doigt_Gauche ) {
$liste["Pas d'anomalie"]   ="Pas d'anomalie";
$liste["V doigt Gauche"]   ="V doigt Gauche";
$liste["IV doigt Gauche"]  ="IV doigt Gauche";
$liste["III doigt Gauche"] ="III doigt Gauche";
$liste["II doigt Gauche"]  ="II doigt Gauche";
$liste["I doigt Gauche"]   ="I doigt Gauche";
}
elseif ( $Presence_Doigt_Droite ) {
$liste["Pas d'anomalie"]   ="Pas d'anomalie";
$liste["V doigt Droit"]    ="V doigt Droit";
$liste["IV doigt Droit"]   ="IV doigt Droit";
$liste["III doigt Droit"]  ="III doigt Droit";
$liste["II doigt Droit"]   ="II doigt Droit";
$liste["I doigt Droit"]    ="I doigt Droit";
}
  
  
  //$liste[ utf8_encode($liste1)]= $liste2;
  
  
  
return ($liste);

}
?>
