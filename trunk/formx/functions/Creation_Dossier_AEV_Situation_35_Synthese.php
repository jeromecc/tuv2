<?php
createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Libelle'.$i,'<span style="color:#CC3333;font-weight:bold;">*** DOSSIER ***</span>','TXT','<span style="color:#CC3333;font-weight:bold;">Exposition usagers de drogues Risque intermédiaire Source inconnue</span>');
createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'Val_Docteur_Consultation'.$i,'<span style="color:green;">Dossier saisi par le Docteur</span>','TXT',$tabValeurstemp['Val_Docteur_Consultation']);
createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'Val_Date_Saisie'.$i,'<span style="color:green;">Date de saisi du dossier</span>','TXT',$formulaireAEV["dt_modif"][$i]);
createItem($formx2,$etape,'d1'.$i,'','TXT','');
createItem($formx2,$etape,'d2'.$i,'','TXT','');
createItem($formx2,$etape,'d3'.$i,'','TXT','');

if ( $tabValeurstemp['AEV_Titre_Document'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Document'.$i,'<span style="color:#483D8B;font-weight:bold;">DOCUMENTS REMIS AU PATIENT</span>','TXT',$tabValeurstemp['AEV_Titre_Document']);

if ( $tabValeurstemp['AEV_Document_Fiche'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Document_Fiche'.$i,'','TXT','Fiche orientation victime');

if ( $tabValeurstemp['AEV_Titre_Dossier'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Dossier'.$i,'<span style="color:#483D8B;font-weight:bold;">DOSSIER DU PATIENT</span>','TXT',$tabValeurstemp['AEV_Titre_Dossier']);

if ( $tabValeurstemp['AEV_Titre_Accident'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Accident'.$i,'<span style="color:#336666;font-weight:bold;">---- ACCIDENT</span>','TXT',$tabValeurstemp['AEV_Titre_Accident']);

if ( $tabValeurstemp['AEV_Date_Accident'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Date_Accident'.$i,'<span style="color:green;">Date de l\'accident</span>','TXT',$tabValeurstemp['AEV_Date_Accident']);

if ( $tabValeurstemp['AEV_Heure_Accident'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Heure_Accident'.$i,'<span style="color:green;">Heure de l\'accident</span>','TXT',$tabValeurstemp['AEV_Heure_Accident']);

if ( $tabValeurstemp['AEV_Titre_Complement'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Complement'.$i,'<span style="color:#483D8B;font-weight:bold;">COMPLEMENTS D\'INFORMATION</span>','TXT',$tabValeurstemp['AEV_Titre_Complement']);

if ( $tabValeurstemp['AEV_Complement'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Complement'.$i,'<span style="color:green;">Commentaires</span>','TXT',$tabValeurstemp['AEV_Complement']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Complement'.$i,'<span style="color:green;">Commentaires</span>','TXT','Aucun commentaire saisie');

createItem($formx2,$etape,'d4'.$i,'','TXT','');
createItem($formx2,$etape,'d5'.$i,'','TXT','');
createItem($formx2,$etape,'d6'.$i,'','TXT','');
?>
