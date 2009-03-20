<?php
function Creation_Dossier_Medicale_Colectomie($formx) {

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

$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Medical_Colectomie');

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



createItem($formx2,$etape,'a','<span style="font-weight:bold;">CONTEXTE</span>','TXT','');

if ( $tabValeurs['Val_COLECTOMIE_Score_ASA'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Score_ASA','<span style="color:green;">Score ASA</span>','TXT',$tabValeurs['Val_COLECTOMIE_Score_ASA']);
if ( $tabValeurs['Val_COLECTOMIE_Circonstances'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Circonstances','<span style="color:green;">Circonstances de diagnostic</span>','TXT',$tabValeurs['Val_COLECTOMIE_Circonstances']);
if ( $tabValeurs['Val_COLECTOMIE_Circonstances_C'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Circonstances_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Val_COLECTOMIE_Circonstances_C']);

createItem($formx2,$etape,'b','<span style="font-weight:bold;">ACTE CHIRURGICAL</span>','TXT','');

if ( $tabValeurs['Val_COLECTOMIE_Voie'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Voie','<span style="color:green;">Voie d\'abord</span>','TXT',$tabValeurs['Val_COLECTOMIE_Voie']);
if ( $tabValeurs['Val_COLECTOMIE_Type'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Type','<span style="color:green;">Type de colectomie</span>','TXT',$tabValeurs['Val_COLECTOMIE_Type']);
if ( $tabValeurs['Val_COLECTOMIE_Cond'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Cond','<span style="color:green;">Conditions de l\'intervention</span>','TXT',$tabValeurs['Val_COLECTOMIE_Cond']);
if ( $tabValeurs['Val_COLECTOMIE_Etiologique'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Etiologique','<span style="color:green;">Diagnostic étiologique</span>','TXT',$tabValeurs['Val_COLECTOMIE_Etiologique']);
if ( $tabValeurs['Val_COLECTOMIE_Exereses'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Exereses','<span style="color:green;">Exereses associées</span>','TXT',$tabValeurs['Val_COLECTOMIE_Exereses']);
if ( $tabValeurs['Val_COLECTOMIE_Exereses_Autres'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Exereses_Autres','<span style="color:green;">Exereses associées autres</span>','TXT',$tabValeurs['Val_COLECTOMIE_Exereses_Autres']);
if ( $tabValeurs['Val_COLECTOMIE_Exereses_Autres_C'] != "" && $tabValeurs['Val_COLECTOMIE_Exereses_Autres'] == "Oui")
  createItem($formx2,$etape,'Val_COLECTOMIE_Exereses_Autres_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Val_COLECTOMIE_Exereses_Autres_C']);
if ( $tabValeurs['Val_COLECTOMIE_Stomie'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Stomie','<span style="color:green;">Stomie</span>','TXT',$tabValeurs['Val_COLECTOMIE_Stomie']);
if ( $tabValeurs['Val_COLECTOMIE_Gestes'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Gestes','<span style="color:green;">Gestes pré-opératoires</span>','TXT',$tabValeurs['Val_COLECTOMIE_Gestes']);

createItem($formx2,$etape,'c','<span style="font-weight:bold;">LES COMPLICATIONS</span>','TXT','');


if ( $tabValeurs['Val_COLECTOMIE_Complications'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Complications','<span style="color:green;">Types de complications</span>','TXT',$tabValeurs['Val_COLECTOMIE_Complications']);
if ( $tabValeurs['Val_COLECTOMIE_Complications_Autres'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Complications_Autres','<span style="color:green;">Types de complications autres</span>','TXT',$tabValeurs['Val_COLECTOMIE_Complications_Autres']);
if ( $tabValeurs['Val_COLECTOMIE_Complications_Autres_C'] != "" && $tabValeurs['Val_COLECTOMIE_Complications_Autres'] == "Oui" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Complications_Autres_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Val_COLECTOMIE_Complications_Autres_C']);
if ( $tabValeurs['Val_COLECTOMIE_Intervention'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Intervention','<span style="color:green;">Ré-intervention</span>','TXT',$tabValeurs['Val_COLECTOMIE_Intervention']);
if ( $tabValeurs['Val_COLECTOMIE_Intervention_D'] != "" && $tabValeurs['Val_COLECTOMIE_Intervention'] == "Oui" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Intervention_D','<span style="color:#666699;">Ré-intervention date</span>','TXT',$tabValeurs['Val_COLECTOMIE_Intervention_D']);
if ( $tabValeurs['Val_COLECTOMIE_Intervention_C'] != "" && $tabValeurs['Val_COLECTOMIE_Intervention'] == "Oui" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Intervention_C','<span style="color:#666699;">Ré-intervention commentaires</span>','TXT',$tabValeurs['Val_COLECTOMIE_Intervention_C']);
if ( $tabValeurs['Val_COLECTOMIE_Deces'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Deces','<span style="color:green;">Decés</span>','TXT',$tabValeurs['Val_COLECTOMIE_Deces']);
if ( $tabValeurs['Val_COLECTOMIE_Deces_D'] != "" && $tabValeurs['Val_COLECTOMIE_Deces'] == "Oui" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Deces_D','<span style="color:#666699;">Decés date</span>','TXT',$tabValeurs['Val_COLECTOMIE_Deces_D']);
if ( $tabValeurs['Val_COLECTOMIE_Deces_C'] != "" && $tabValeurs['Val_COLECTOMIE_Deces'] == "Oui" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Deces_C','<span style="color:#666699;">Decés causes</span>','TXT',$tabValeurs['Val_COLECTOMIE_Deces_C']);
if ( $tabValeurs['Val_COLECTOMIE_Transfusion'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Transfusion','<span style="color:green;">Transfusion</span>','TXT',$tabValeurs['Val_COLECTOMIE_Transfusion']);

if ( $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'d','<span style="font-weight:bold;">LA TUMEUR</span>','TXT','');


if ( $tabValeurs['Val_COLECTOMIE_TumeurT'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_TumeurT','<span style="color:green;">T</span>','TXT',$tabValeurs['Val_COLECTOMIE_TumeurT']);
if ( $tabValeurs['Val_COLECTOMIE_TumeurN'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_TumeurN','<span style="color:green;">N</span>','TXT',$tabValeurs['Val_COLECTOMIE_TumeurN']);
if ( $tabValeurs['Val_COLECTOMIE_TumeurM'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_TumeurM','<span style="color:green;">M</span>','TXT',$tabValeurs['Val_COLECTOMIE_TumeurM']);

if ( $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" ) createItem($formx2,$etape,'e','<span style="font-weight:bold;">SUIVI CANCEROLOGIQUE A LA SORTIE</span>','TXT','');
	
if ( $tabValeurs['Val_COLECTOMIE_RCP'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_RCP','<span style="color:green;">RCP</span>','TXT',$tabValeurs['Val_COLECTOMIE_RCP']);
if ( $tabValeurs['Val_COLECTOMIE_Annonce'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Annonce','<span style="color:green;">Consultation d\'annonce</span>','TXT',$tabValeurs['Val_COLECTOMIE_Annonce']);
if ( $tabValeurs['Val_COLECTOMIE_Feuille'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Feuille','<span style="color:green;">Feuille de projet thérapeutique</span>','TXT',$tabValeurs['Val_COLECTOMIE_Feuille']);
if ( $tabValeurs['Val_COLECTOMIE_Spychiatrique'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Spychiatrique','<span style="color:green;">Consultation spychiatrique</span>','TXT',$tabValeurs['Val_COLECTOMIE_Spychiatrique']);
if ( $tabValeurs['Val_COLECTOMIE_Courrier'] != "" && $tabValeurs['Val_COLECTOMIE_Etiologique'] == "Cancer" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Courrier','<span style="color:green;">Courrier 100%</span>','TXT',$tabValeurs['Val_COLECTOMIE_Courrier']);


if ( $tabValeurs['Val_COLECTOMIE_Transfusion_2'] != "" )
  createItem($formx2,$etape,'Val_COLECTOMIE_Transfusion_2','<span style="color:green;">Transfusion</span>','TXT',$tabValeurs['Val_COLECTOMIE_Transfusion_2']);


//

//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
