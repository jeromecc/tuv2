<?php
function Init_Fiche_Traumatologie_Main($formx) {

$item=utf8_decode($formx->getVar('L_Val_Horaire_Date_Accident'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Horaire_Date_Accident',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_Horaire_Heure_Accident'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Horaire_Heure_Accident',date("H").":".date("i"));
  }

$item=utf8_decode($formx->getVar('L_Val_Horaire_Delai_Calcul'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Horaire_Delai_Calcul',"Afficher la (nouvelle) valeur");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Presence_Bague_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Presence_Bague_C',"-**font color=red**-Enlever ou couper toutes les bagues de la main blessée.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Vaccin_Anti_Date')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Vaccin_Anti_Date',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Coupure_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Coupure_Message',"-**font color=red**-Les plaies par verres sont le plus souvent profondes et doivent être explorées chirurgicalement.-**/font**-");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Injection_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Injection_Message',"-**font color=red**-Envoi très rapide au centre spécialisé après accord téléphonique. Les lésions sont le plus souvent très sévères mais très peu symptomatiques initialement.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Morsure_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Morsure_Message',"-**font color=red**-Penser à gerer l'accident d'exposition au virus et à la prescription d'antibiotique.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Traction')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Traction',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Traction_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Traction_Message',"-**font color=red**-Les lésions constatés \"Ring Finger\" sont le plus souvent très sévères. Vérifier la vascularisation. Envoi rapide au centre spécialisé.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Grante_Main_M')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Grante_Main_M',"-**font color=red**-Adressez sans delai dans un centre spécialisé.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Grante_Amputation_M')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Grante_Amputation_M',"-**font color=red**-Adressez sans delai dans un centre spécialisé.-**/font**-");
  }








$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Face_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Face_Message',"-**font color=red**-Les plaies de la face palmaire sont d'une exploration difficile - adresser facilement au chirurgien spécialisé.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_Mralenti')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_Mralenti',"-**font color=red**-Avis chirurgical spécialisé.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_Mabsent')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_Mabsent',"-**font color=red**-Envoi rapide au centre spécialisé après accord téléphonique.-**/font**-");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_Mviolet')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_Mviolet',"-**font color=red**-Avis chirurgical spécialisé.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_Mblanc')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_Mblanc',"-**font color=red**-Envoi rapide au centre spécialisé après accord téléphonique.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_Mlent')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_Mlent',"-**font color=red**-Avis chirurgical spécialisé.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_Mfroid')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_Mfroid',"-**font color=red**-Envoi rapide au centre spécialisé après accord téléphonique.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_MessageG')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_MessageG',"-**font color=red**-La mobilité doit être indolore pour être considérée comme normale.-**/font**-");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_AucuneS')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_AucuneS',"-**font color=red**-Aucune saisie à effectuer sur cette partie du formulaire.-**/font**-");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_MessageG')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_MessageG',"-**font color=red**-La sensibilité est anormale quand il existe une dysesthésie ou une anesthésie.-**/font**-");
  }
  
$formx->setVar('L_valeur_titre_aucune_saisie',"Aucune saisie effectuée sur cette partie du formulaire.");
  
//eko($formx->getFormVar('Val_Main_Lesion_Zone')."<br>");
//eko($formx->getFormVar('Val_Main_Lesion_Zone_NewIntance1')."<br>");
//eko($formx->getFormVar('Val_Main_Lesion_Zone_NewIntance2')."<br>");
//eko($formx->getFormVar('Val_Main_Lesion_Zone_NewIntance3')."<br>");
//eko($formx->getFormVar('Val_Main_Lesion_Zone_NewIntance4')."<br>");
//eko($formx->getFormVar('Val_Main_Lesion_Zone_NewIntance5')."<br>");

if ( strcmp($formx->getFormVar('Val_Main_Lesion_Zone'),"Poignet") == 0 || 
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_1'),"Poignet") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_2'),"Poignet") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_3'),"Poignet") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_4'),"Poignet") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_5'),"Poignet") == 0 )
  $Presence_Poignet = 1;
else
  $Presence_Poignet = 0;
  
if ( strcmp($formx->getFormVar('Val_Main_Lesion_Zone'),"Doigt") == 0 || 
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_1'),"Doigt") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_2'),"Doigt") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_3'),"Doigt") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_4'),"Doigt") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_5'),"Doigt") == 0 )
  $Presence_Doigt = 1;
else
  $Presence_Doigt = 0;
  
if ( strcmp($formx->getFormVar('Val_Main_Lesion_Zone'),"Paume") == 0 || 
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_1'),"Paume") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_2'),"Paume") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_3'),"Paume") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_4'),"Paume") == 0 ||
     strcmp($formx->getFormVar('Val_Main_Lesion_Zone_5'),"Paume") == 0 )
  $Presence_Paume = 1;
else
  $Presence_Paume = 0;
  

//eko($Presence_Poignet."<br>");
//eko($Presence_Doigt."<br>");

$formx->setVar('L_Val_Main_Lesion_Zone_Presence_Poignet',$Presence_Poignet);
$formx->setVar('L_Val_Main_Lesion_Zone_Presence_Doigt',$Presence_Doigt);
$formx->setVar('L_Val_Main_Lesion_Zone_Presence_Paume',$Presence_Paume);




eko($formx->getVar('L_Val_Main_Lesion_Zone_Presence_Poignet')."<br>");
eko($formx->getVar('L_Val_Main_Lesion_Zone_Presence_Doigt')."<br>");






return "O";
}
?>
