<?php
function Init_Fiche_Traumatologie_Main_sauv($formx) {

$item=utf8_decode($formx->getVar('L_Val_Main_Profession'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Profession',"Manuel");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_StatutProfession'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_StatutProfession',"Salarié");
  }

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

$item=utf8_decode($formx->getVar('L_Val_Horaire_Date_Examen'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Horaire_Date_Examen',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_Horaire_Heure_Examen'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Horaire_Heure_Examen',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_Horaire_Delai_Calcul'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Horaire_Delai_Calcul',"Afficher la (nouvelle) valeur");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Main_Dominante'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Main_Dominante',"Droitier");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Presence_Bague'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Presence_Bague',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Presence_Bague_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Presence_Bague_C',"Enlever ou couper toutes les bagues de la main blessée");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Anticoagulants'));
if ( $item == "" )                                               
  {                                                                                                                          
  $formx->setVar('L_Val_Main_Anticoagulants',"Non");                                                                           
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Anticoagulants_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Anticoagulants_C',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Troubles_Vasculaires')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Troubles_Vasculaires',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Troubles_Vasculaires_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Troubles_Vasculaires_C',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Tabagisme')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Tabagisme',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Tabagisme_Paquet')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Tabagisme_Paquet',"1");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Tabagisme_Annee')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Tabagisme_Annee',date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Diabete')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Diabete',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Diabete_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Diabete_C',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Allergies')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Allergies',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Allergies_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Allergies_C',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Vaccin_Anti')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Vaccin_Anti',"Oui");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Vaccin_Anti_Date')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Vaccin_Anti_Date',date("d")."-".date("m")."-".date("Y"));
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Accident_Trav')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Accident_Trav',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesions')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesions',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesions_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesions_C',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Contexte_Accident')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Contexte_Accident',"Accident de la voie publique - piéton");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Contexte_Accident_Sport')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Contexte_Accident_Sport',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Contexte_Accident_Sport_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Contexte_Accident_Sport_C',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Premier_Geste')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Premier_Geste',"Aucun");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Premier_Geste_Autre')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Premier_Geste_Autre',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Premier_Geste_Autre_C')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Premier_Geste_Autre_C',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Coupure')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Coupure',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Coupure_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Coupure_G',"Autre");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Coupure_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Coupure_Message',"Les plaies par verres sont le plus souvent profondes et doivent être explorées chirurgicalement");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Contusion')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Contusion',"Non");
  } 
  
$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Contusion_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Contusion_G',"Rixe");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Torsion')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Torsion',"Non");
  } 

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Torsion_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Torsion_G',"Champ Non Précisé.");
  } 

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Ecrasement')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Ecrasement',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Ecrasement_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Ecrasement_G',"Champ Non Précisé.");
  } 

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Arrachement')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Arrachement',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Arrachementt_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Arrachement_G',"Champ Non Précisé.");
  }  

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Injection')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Injection',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Injection_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Injection_G',"Huile");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Injection_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Injection_Message',"Envoi rapide au centre spécialisé après accord téléphonique");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Morsure')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Morsure',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Morsure_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Morsure_G',"Humaine");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Morsure_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Morsure_Message',"Penser à gerer l'accident d'exposition au virus");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Traction')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Traction',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Traction_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Traction_Message',"Les lésions constatés Ring Finger sont le plus souvent très sévères et peu symptomatiques initialement - avis chirurgical spécialisé justifié");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Autre')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Autre',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Mecanisme_Autre_G')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Mecanisme_Autre_G',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Type')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Type',"Plaie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Type_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Type_Message',"Envoi rapide au centre spécialisé après accord téléphonique");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Main')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Main',"Droite");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Rayon')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Rayon',"1");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Face')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Face',"Palmaire");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Face_Message')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Face_Message',"Les plaies de la face palmaire sont d'une exploration difficile - adresser facilement au chirurgien spécialisé");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Segment')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Segment',"Metacarpe");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Lesion_Associees')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Lesion_Associees',"Contusion");
  }










$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Demande_Reload')); 
if ( $item == "Rechargement de la page" )
  {
  $formx->setVar('L_Val_Main_Complication_Demande_Reload',"Rechargement de la page");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_Mralenti')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_Mralenti',"Avis chirurgical spécialisé");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_Mabsent')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_Mabsent',"Envoi rapide au centre spécialisé après accord téléphonique");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix1',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix2',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix3',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix4',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix5',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix6',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix7',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix8',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix9',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Capillaires_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Capillaires_choix10',"Normal");
  }
  
















$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_Mviolet')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_Mviolet',"Avis chirurgical spécialisé");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_Mblanc')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_Mblanc',"Envoi rapide au centre spécialisé après accord téléphonique");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix1',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix2',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix3',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix4',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix5',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix6',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix7',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix8',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix9',"Rose");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Coloration_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Coloration_choix10',"Rose");
  }













$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_Ask',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_Mlent')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_Mlent',"Avis chirurgical spécialisé");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix1',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix2',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix3',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix4',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix5',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix6',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix7',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix8',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix9',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Pulpaire_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Pulpaire_choix10',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_Mfroid')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_Mfroid',"Envoi rapide au centre spécialisé après accord téléphonique");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee',"Pas d'anomalie");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix1',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix2',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix3',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix4',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix5',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix6',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix7',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix8',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix9',"Normal");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Cutanee_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Cutanee_choix10',"Normal");
  }














  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_2_Demande_Reload')); 
if ( $item == "Rechargement de la page" )
  {
  $formx->setVar('L_Val_Main_Complication_2_Demande_Reload',"Rechargement de la page");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_3_Demande_Reload')); 
if ( $item == "Rechargement de la page" )
  {
  $formx->setVar('L_Val_Main_Complication_3_Demande_Reload',"Rechargement de la page");
  }
  





$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_Mdouloureuse')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_Mdouloureuse',"La flexion doit être indolore pour être considérée comme normale.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_Mdoigttendu')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_Mdoigttendu',"Un doigt tendu signe la section des deux fléchisseurs");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds',"Pas d'anomalie");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix1',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix2',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix3',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix4',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix5',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix6',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix7',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix8',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix9',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Profonds_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Profonds_choix10',"Douloureuse");
  }





$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_Mdouloureuse')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_Mdouloureuse',"La flexion doit être indolore pour être considérée comme normale.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix1',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix2',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix3',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix4',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix5',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix6',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix7',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix8',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix9',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Superficiels_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Superficiels_choix10',"Douloureuse");
  } 







$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_Mdouloureuse')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_Mdouloureuse',"L'extension doit être indolore pour être considérée comme normale.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix1',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix2',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix3',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix4',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix5',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix6',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix7',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix8',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix9',"Douloureuse");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_Extenseurs_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_Extenseurs_choix10',"Douloureuse");
  }






$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_Ask')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_Ask',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_choix1',"Anesthesie de tout le territoire");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_choix1Ma_M')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_choix1Ma_M',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_choix2',"Anesthesie de tout le territoire");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_choix2Ma_M')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_choix2Ma_M',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_choix3',"Anesthesie de tout le territoire");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteMain_choix3Ma_M')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteMain_choix3Ma_M',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi',"Pas d'anomalie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix1')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix1',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix2')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix2',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix3')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix3',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix4')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix4',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix5')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix5',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix6')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix6',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix7')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix7',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix8')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix8',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix9')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix9',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix10')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix10',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix11')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix11',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix12')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix12',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix13')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix13',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix14')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix14',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix15')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix15',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix16')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix16',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix17')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix17',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix18')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix18',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix19')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix19',"Anesthesie");
  }

$item=utf8_decode($formx->getVar('L_Val_Main_Complication_SensibiliteHemi_choix20')); 
if ( $item == "" )
  {
  $formx->setVar('L_Val_Main_Complication_SensibiliteHemi_choix20',"Anesthesie");
  }

return "O";
}
?>
