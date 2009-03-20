<?php
function Creation_Dossier_Colectomie_Periode_P6($formx) {

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
$sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P6' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Colectomie_Periode_P6');

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

createItem($formx2,$etape,'Colectomie_Periode_P6_Actes','<span style="color:#CC3333;font-weight:bold;">ACTES</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Actes']);
createItem($formx2,$etape,'Colectomie_Periode_P6_Mot_Sor','<span style="color:#336666;font-weight:bold;">---- MOTIFS DE LA SORTIE DIFFEREE</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Mot_Sor']);
createItem($formx2,$etape,'Colectomie_Periode_P6_Cau_Chi','<span style="color:green;">Causes chirurgicales</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Cau_Chi']);
if ( $tabValeurs['Colectomie_Periode_P6_Cau_Chi_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P6_Cau_Chi_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Cau_Chi_C']);
createItem($formx2,$etape,'Colectomie_Periode_P6_Cau_Med','<span style="color:green;">Causes médicales</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Cau_Med']);
if ( $tabValeurs['Colectomie_Periode_P6_Cau_Med_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P6_Cau_Med_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Cau_Med_C']);
createItem($formx2,$etape,'Colectomie_Periode_P6_Def_Aut','<span style="color:green;">Déficit d\'autonomie</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Def_Aut']);
if ( $tabValeurs['Colectomie_Periode_P6_Def_Aut_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P6_Def_Aut_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Def_Aut_C']);
createItem($formx2,$etape,'Colectomie_Periode_P6_Att_Mai','<span style="color:green;">Attente maison de repos</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Att_Mai']);
if ( $tabValeurs['Colectomie_Periode_P6_Att_Mai_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P6_Att_Mai_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Att_Mai_C']);
createItem($formx2,$etape,'Colectomie_Periode_P6_Cau_Soc','<span style="color:green;">Causes sociales</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Cau_Soc']);
if ( $tabValeurs['Colectomie_Periode_P6_Cau_Soc_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P6_Cau_Soc_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P6_Cau_Soc_C']);


//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
