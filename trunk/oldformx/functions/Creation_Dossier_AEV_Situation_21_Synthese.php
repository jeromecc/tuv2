<?php
createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Libelle'.$i,'<span style="color:#CC3333;font-weight:bold;">*** DOSSIER ***</span>','TXT','<span style="color:#CC3333;font-weight:bold;">Exposition au sang Risque minime Source VIH et VHC</span>');
createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'Val_Docteur_Consultation'.$i,'<span style="color:green;">Dossier saisi par le Docteur</span>','TXT',$tabValeurstemp['Val_Docteur_Consultation']);
createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'Val_Date_Saisie'.$i,'<span style="color:green;">Date de saisi du dossier</span>','TXT',$formulaireAEV["dt_modif"][$i]);
createItem($formx2,$etape,'d1'.$i,'','TXT','');
createItem($formx2,$etape,'d2'.$i,'','TXT','');
createItem($formx2,$etape,'d3'.$i,'','TXT','');

if ( $tabValeurstemp['AEV_Titre_Document'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Document'.$i,'<span style="color:#483D8B;font-weight:bold;">DOCUMENTS REMIS AU PATIENT</span>','TXT',$tabValeurstemp['AEV_Titre_Document']);

if ( $tabValeurstemp['AEV_Document_Consentement'] != " " && $tabValeurstemp['AEV_Prophylaxie_Demande'] == "Oui" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Document_Consentement'.$i,'','TXT','Formulaire de consentement');

if ( $tabValeurstemp['AEV_Document_Note'] != " " && $tabValeurstemp['AEV_Prophylaxie_Demande'] == "Oui" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Document_Note'.$i,'','TXT','Note d &#146;information à la victime');

if ( $tabValeurstemp['AEV_Document_Fiche'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Document_Fiche'.$i,'','TXT','Fiche orientation victime');

if ( $tabValeurstemp['AEV_Titre_Dossier'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Dossier'.$i,'<span style="color:#483D8B;font-weight:bold;">DOSSIER DU PATIENT</span>','TXT',$tabValeurstemp['AEV_Titre_Dossier']);

if ( $tabValeurstemp['AEV_Titre_Accident'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Accident'.$i,'<span style="color:#336666;font-weight:bold;">---- ACCIDENT</span>','TXT',$tabValeurstemp['AEV_Titre_Accident']);

if ( $tabValeurstemp['AEV_Date_Accident'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Date_Accident'.$i,'<span style="color:green;">Date de l\'accident</span>','TXT',$tabValeurstemp['AEV_Date_Accident']);

if ( $tabValeurstemp['AEV_Heure_Accident'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Heure_Accident'.$i,'<span style="color:green;">Heure de l\'accident</span>','TXT',$tabValeurstemp['AEV_Heure_Accident']);

if ( $tabValeurstemp['AEV_Titre_Pro'] != " "  ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Pro'.$i,'<span style="color:#336666;font-weight:bold;">---- PROPHYLAXIE</span>','TXT',$tabValeurstemp['AEV_Titre_Pro']);

if ( $tabValeurstemp['AEV_Prophylaxie_Demande'] != " "  ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Prophylaxie_Demande'.$i,'<span style="color:green;">Décision de débuter un traitement antirétroviral prophylactique</span>','TXT',$tabValeurstemp['AEV_Prophylaxie_Demande']);

if ( $tabValeurstemp['AEV_Date_Prophylaxie'] != " " && $tabValeurstemp['AEV_Prophylaxie_Demande'] == "Oui" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Date_Prophylaxie'.$i,'<span style="color:#666699;">Date de la première prise de la prophylaxie</span>','TXT',$tabValeurstemp['AEV_Date_Prophylaxie']);

if ( $tabValeurstemp['AEV_Heure_Prophylaxie'] != " " && $tabValeurstemp['AEV_Prophylaxie_Demande'] == "Oui" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Heure_Prophylaxie'.$i,'<span style="color:#666699;">Heure de la première prise de la prophylaxie</span>','TXT',$tabValeurstemp['AEV_Heure_Prophylaxie']);

if ( $tabValeurstemp['AEV_Titre_Source'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Source'.$i,'<span style="color:#336666;font-weight:bold;">---- PATIENT SOURCE</span>','TXT',$tabValeurstemp['AEV_Titre_Source']);

if ( $tabValeurstemp['AEV_Source_Nom'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Non'.$i,'<span style="color:green;">Non</span>','TXT',$tabValeurstemp['AEV_Source_Nom']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Non'.$i,'<span style="color:green;">Non</span>','TXT','Non Renseigné');

if ( $tabValeurstemp['AEV_Source_Prenom'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Prenom'.$i,'<span style="color:green;">Prenom</span>','TXT',$tabValeurstemp['AEV_Source_Prenom']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Prenom'.$i,'<span style="color:green;">Prenom</span>','TXT','Non Renseigné');

if ( $tabValeurstemp['AEV_Source_Jour_Naissance'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Jour_Naissance'.$i,'<span style="color:green;">Jour de naissance</span>','TXT',$tabValeurstemp['AEV_Source_Jour_Naissance']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Jour_Naissance'.$i,'<span style="color:green;">Jour de naissance</span>','TXT','Non Renseigné');

if ( $tabValeurstemp['AEV_Source_Mois_Naissance'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Mois_Naissance'.$i,'<span style="color:green;">Mois de naissance</span>','TXT',$tabValeurstemp['AEV_Source_Mois_Naissance']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Mois_Naissance'.$i,'<span style="color:green;">Mois de naissance</span>','TXT','Non Renseigné');

if ( $tabValeurstemp['AEV_Source_Annee_Naissance'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Annee_Naissance'.$i,'<span style="color:green;">Année de naissance</span>','TXT',$tabValeurstemp['AEV_Source_Annee_Naissance']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Source_Annee_Naissance'.$i,'<span style="color:green;">Année de naissance</span>','TXT','Non Renseigné');

if ( $tabValeurstemp['AEV_Titre_Complement'] != " " ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Titre_Complement'.$i,'<span style="color:#483D8B;font-weight:bold;">COMPLEMENTS D\'INFORMATION</span>','TXT',$tabValeurstemp['AEV_Titre_Complement']);

if ( $tabValeurstemp['AEV_Complement'] != "" ) createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Complement'.$i,'<span style="color:green;">Commentaires</span>','TXT',$tabValeurstemp['AEV_Complement']);
else createItem($formx2,$etape,$formulaireAEV["id_instance"][$i].'AEV_Complement'.$i,'<span style="color:green;">Commentaires</span>','TXT','Aucun commentaire saisie');

createItem($formx2,$etape,'d4'.$i,'','TXT','');
createItem($formx2,$etape,'d5'.$i,'','TXT','');
createItem($formx2,$etape,'d6'.$i,'','TXT','');
?>
