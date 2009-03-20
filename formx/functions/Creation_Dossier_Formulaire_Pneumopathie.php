<?php
function Creation_Dossier_Formulaire_Pneumopathie($formx) {

global $session;
global $options;


//recuperation de toutes les variables du formulaire dans un tableau pour que ça aille plus vite
$dom = $formx->XMLDOM ;

$listItems = $dom->getElementsByTagName('ITEM');
foreach($listItems as $item) {
	$nomItem = 	$item->getAttribute('id');
	$valItem =  $item->getElementsByTagName('Val')->item(0)->nodeValue;
	$tabValeurs[$nomItem] = utf8_decode($valItem) ;
}


//-------------------------------------------
//fonctions qui utililsent DOM
//-------------------------------------------

function createEtape($objFormx,$idEtape,$libelleEtape,$tabAttributs) {
	$neudFormx = $objFormx->XMLDOM->getElementsByTagName('FORMX')->item(0);
	$etapeTemp = $objFormx->XMLDOM->createElement('ETAPE');
	$etape = $neudFormx->appendChild($etapeTemp);
	foreach($tabAttributs as $key => $value) {
		$etape->setAttribute($key,utf8_encode($value));	
	}
	$libelle = $objFormx->XMLDOM->createElement('Libelle',utf8_encode($libelleEtape));
	$etape->appendChild($libelle);
	return $etape;
}

function createItem($objFormx,$etape,$id,$libelle,$type,$val) {
	$item = $objFormx->XMLDOM->createElement('ITEM');
	$item = $etape->appendChild($item);
	//Atributs de l'item
	$item->setAttribute('id',$id);
	$item->setAttribute('type',$type);
	//libelle de l'item
	$libelle = $objFormx->XMLDOM->createElement('Libelle',utf8_encode($libelle));
	$item->appendChild($libelle);
	//creation et affectation de la balise Val
	$val = $objFormx->XMLDOM->createElement('Val',utf8_encode($val));
	$item->appendChild($val);
}

//-----------------------------------
//Exemples d'utilisation
//-----------------------------------

//Creation d'un nouveau formulaire de type 'coin'
$ids = $formx->getIDS();


// On va supprimer tous les fichiers Dossier_Colectomie_Periode_P1 de la table formx
$requete=new clRequete(BDD,TABLEFORMX,$param);
$sql=$requete->delRecord("idformx='Dossier_Formulaire_Pneumopathie' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Formulaire_Pneumopathie');

//ajout d 'une étape
$etape = createEtape($formx2,'1','IDENTITE DU PATIENT',array('etat'=>'fini'));
createItem($formx2,$etape,'id1','<span style="color:green;">Nom</span>','TXT',   $tabValeurs['Val_IDENT_NomPatient']);
createItem($formx2,$etape,'id2','<span style="color:green;">Prénom</span>','TXT',$tabValeurs['Val_IDENT_PrenomPatient']);
//createItem($formx2,$etape,'id3','Sexe','TXT',  $tabValeurs['Val_IDENT_SexePat']);
createItem($formx2,$etape,'id4','<span style="color:green;">Date de naissance</span>','TXT',$tabValeurs['Val_IDENT_DateNPat2']);
createItem($formx2,$etape,'id5','<span style="color:green;">Age</span>','TXT',$tabValeurs['Val_IDENT_AgePat']);
createItem($formx2,$etape,'id6','<span style="color:green;">Adresse</span>','TXT',$tabValeurs['Val_IDENT_AdressePat']);
createItem($formx2,$etape,'id7','<span style="color:green;">Code postal</span>','TXT',$tabValeurs['Val_IDENT_CodePPat']);
createItem($formx2,$etape,'id8','<span style="color:green;">Ville</span>','TXT',$tabValeurs['Val_IDENT_VillePat']);
createItem($formx2,$etape,'id9','<span style="color:green;">Telephone</span>','TXT',$tabValeurs['Val_IDENT_TelPat']);
createItem($formx2,$etape,'id10','<span style="color:green;">Profession</span>','TXT',$tabValeurs['Val_Main_Profession']);
createItem($formx2,$etape,'id11','<span style="color:green;">Statut profession</span>','TXT',$tabValeurs['Val_Main_StatutProfession']);
createItem($formx2,$etape,'id12','<span style="color:green;">IDU</span>','TXT',$tabValeurs['Val_IDENT_IDUPatient']);
createItem($formx2,$etape,'id13','<span style="color:green;">ILP</span>','TXT',$tabValeurs['Val_IDENT_ILPPatient']);
createItem($formx2,$etape,'id14','<span style="color:green;">Jour de consultation du patient</span>','TXT',$tabValeurs['Val_Jour_Consultation']);
createItem($formx2,$etape,'id15','<span style="color:green;">Heure de consultation du patient</span>','TXT',$tabValeurs['Val_Heure_Consultation']);

createItem($formx2,$etape,'d1','','TXT','');
createItem($formx2,$etape,'d2','','TXT','');
createItem($formx2,$etape,'d3','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Orientation','<span style="color:#CC3333;font-weight:bold;">ORIENTATION DIAGNOSTIC</span>','TXT',$tabValeurs['Pneumopathie_Orientation']);
createItem($formx2,$etape,'Pneumopathie_Orientation_Motif','<span style="color:green;">Motif d\'admission</span>','TXT',$tabValeurs['Pneumopathie_Orientation_Motif']);
createItem($formx2,$etape,'Pneumopathie_Orientation_Traitement','<span style="color:green;">Traitement antibiotique avant la prise en charge</span>','TXT',$tabValeurs['Pneumopathie_Orientation_Traitement']);
createItem($formx2,$etape,'Pneumopathie_Orientation_Adresse','<span style="color:green;">Adressé par un médecin libéral</span>','TXT',$tabValeurs['Pneumopathie_Orientation_Adresse']);

createItem($formx2,$etape,'d4','','TXT','');
createItem($formx2,$etape,'d5','','TXT','');
createItem($formx2,$etape,'d6','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Comorbidites','<span style="color:#CC3333;font-weight:bold;">COMORBIDITES</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Tabagisme','<span style="color:green;">Tabagisme</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Tabagisme']);
if ( $tabValeurs['Pneumopathie_Comorbidites_Tabagisme'] != "Non" ) createItem($formx2,$etape,'Pneumopathie_Comorbidites_Tabagisme_C','<span style="color:green;">Nombre de paquets année</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Tabagisme_C']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Vie','<span style="color:green;">Vit en institution</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Vie']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Precarite','<span style="color:green;">Précarité sociale</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Precarite']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Mauvaise','<span style="color:green;">Mauvaise observance</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Mauvaise']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Absorption','<span style="color:green;">Absorption traitement per os impossible</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Absorption']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Decompensation','<span style="color:green;">Décompensation d\'une comordité existante</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Decompensation']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Bpco','<span style="color:green;">BPCO</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Bpco']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Oxygeno','<span style="color:green;">Oxygeno thérapie à domicile</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Oxygeno']);
if ( $tabValeurs['Pneumopathie_Comorbidites_Oxygeno'] != "Non" ) createItem($formx2,$etape,'Pneumopathie_Comorbidites_Oxygeno_C','<span style="color:green;">Quantité en l/mn</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Oxygeno_C']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Hepatique','<span style="color:green;">Maladie hépatique</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Hepatique']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Neoplasique','<span style="color:green;">Maladie néoplasique</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Neoplasique']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Renale','<span style="color:green;">Maladie rénale</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Renale']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Cerebro','<span style="color:green;">Maladie Cérébro-vasculaire</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Cerebro']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Diabete','<span style="color:green;">Diabète</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Diabete']);
if ( $tabValeurs['Pneumopathie_Comorbidites_Diabete'] != "Non" ) createItem($formx2,$etape,'Pneumopathie_Comorbidites_Diabete_C','<span style="color:green;">Traitement ?</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Diabete_C']);
createItem($formx2,$etape,'Pneumopathie_Comorbidites_Cardiaque','<span style="color:green;">Insuffisance cardiaque congestive</span>','TXT',$tabValeurs['Pneumopathie_Comorbidites_Cardiaque']);


createItem($formx2,$etape,'d10','','TXT','');
createItem($formx2,$etape,'d11','','TXT','');
createItem($formx2,$etape,'d12','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Criteres','<span style="color:#CC3333;font-weight:bold;">CRITERES DIAGNOSTIQUES</span>','TXT',$tabValeurs['Pneumopathie_Criteres']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Toux','<span style="color:green;">Toux</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Toux']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Dyspnee','<span style="color:green;">Dyspnée</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Dyspnee']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Douleur','<span style="color:green;">Douleur lathérothoracique</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Douleur']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Expectoration','<span style="color:green;">Expectoration</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Expectoration']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Fievre','<span style="color:green;">Fièvre</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Fievre']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Tachycardie','<span style="color:green;">Tachycardie</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Tachycardie']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Polypnees','<span style="color:green;">Polypnées</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Polypnees']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Matite','<span style="color:green;">Matité localisé</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Matite']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Foyer','<span style="color:green;">Foyer de crépitants</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Foyer']);
createItem($formx2,$etape,'Pneumopathie_Criteres_Autres','<span style="color:green;">Autres</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Autres']);
if ( $tabValeurs['Pneumopathie_Criteres_Autres'] != "Non" ) createItem($formx2,$etape,'Pneumopathie_Criteres_Autres_C','<span style="color:green;">Précisions</span>','TXT',$tabValeurs['Pneumopathie_Criteres_Autres_C']);


createItem($formx2,$etape,'d13','','TXT','');
createItem($formx2,$etape,'d14','','TXT','');
createItem($formx2,$etape,'d15','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Evaluation','<span style="color:#CC3333;font-weight:bold;">EVALUATION CRITERE DE GRAVITE</span>','TXT',$tabValeurs['Pneumopathie_Evaluation']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_Trouble','<span style="color:green;">Trouble des fonctions supérieures</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_Trouble']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_Frequence','<span style="color:green;">Fréquence respiratoire Sup à 30</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_Frequence']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_Systolique','<span style="color:green;">TA Systolique Inf à 90</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_Systolique']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_Diastolique','<span style="color:green;">TA Diastolique Inf ou égal à 60</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_Diastolique']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_T1','<span style="color:green;">T° Inf à 35</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_T1']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_T2','<span style="color:green;">T° Sup à 40</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_T2']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_Cardiaque','<span style="color:green;">Fréquence cardiaque Inf ou égal à 125</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_Cardiaque']);
createItem($formx2,$etape,'Pneumopathie_Evaluation_Septique','<span style="color:green;">Choc septique</span>','TXT',$tabValeurs['Pneumopathie_Evaluation_Septique']);


createItem($formx2,$etape,'d16','','TXT','');
createItem($formx2,$etape,'d17','','TXT','');
createItem($formx2,$etape,'d18','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Paraclinique','<span style="color:#CC3333;font-weight:bold;">DONNEES PARACLINIQUES</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique']);
createItem($formx2,$etape,'Pneumopathie_Paraclinique_Pha','<span style="color:green;">pHA Inf à 7,53</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique_Pha']);
createItem($formx2,$etape,'Pneumopathie_Paraclinique_Ure','<span style="color:green;">Urée Inf ou Egal à 11 mmol par litre</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique_Ure']);
createItem($formx2,$etape,'Pneumopathie_Paraclinique_Na','<span style="color:green;">Na+ Inf 130 mmol par litre</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique_Na']);
createItem($formx2,$etape,'Pneumopathie_Paraclinique_Hematocrique','<span style="color:green;">Hématocrique Inf 30%</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique_Hematocrique']);
createItem($formx2,$etape,'Pneumopathie_Paraclinique_Pao','<span style="color:green;">Pa02 Inf 60 mmHg ou saturation en O2 Inf 90%</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique_Pao']);
createItem($formx2,$etape,'Pneumopathie_Paraclinique_Pleural','<span style="color:green;">Epanchement pleural</span>','TXT',$tabValeurs['Pneumopathie_Paraclinique_Pleural']);


createItem($formx2,$etape,'d19','','TXT','');
createItem($formx2,$etape,'d20','','TXT','');
createItem($formx2,$etape,'d21','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Therapeutique','<span style="color:#CC3333;font-weight:bold;">THERAPEUTIQUE</span>','TXT',$tabValeurs['Pneumopathie_Therapeutique']);
createItem($formx2,$etape,'Pneumopathie_Therapeutique_Anti','<span style="color:green;">Antibiothérapie</span>','TXT',$tabValeurs['Pneumopathie_Therapeutique_Anti']);
createItem($formx2,$etape,'Pneumopathie_Therapeutique_Oxy','<span style="color:green;">Oxygeno-Thérapie</span>','TXT',$tabValeurs['Pneumopathie_Therapeutique_Oxy']);
createItem($formx2,$etape,'Pneumopathie_Therapeutique_Aerosol','<span style="color:green;">Aérosols</span>','TXT',$tabValeurs['Pneumopathie_Therapeutique_Aerosol']);
createItem($formx2,$etape,'Pneumopathie_Therapeutique_Autres','<span style="color:green;">Autres</span>','TXT',$tabValeurs['Pneumopathie_Therapeutique_Autres']);
if ( $tabValeurs['Pneumopathie_Therapeutique_Autres'] != "Non" ) createItem($formx2,$etape,'Pneumopathie_Therapeutique_Autres_C','<span style="color:green;">Traitement ?</span>','TXT',$tabValeurs['Pneumopathie_Therapeutique_Autres_C']);


createItem($formx2,$etape,'d22','','TXT','');
createItem($formx2,$etape,'d23','','TXT','');
createItem($formx2,$etape,'d24','','TXT','');

createItem($formx2,$etape,'Pneumopathie_Devenir','<span style="color:#CC3333;font-weight:bold;">DEVENIR</span>','TXT',$tabValeurs['Pneumopathie_Devenir']);
createItem($formx2,$etape,'Pneumopathie_Devenir_Orientation','<span style="color:green;">Orientation</span>','TXT',$tabValeurs['Pneumopathie_Devenir_Orientation']);


//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
