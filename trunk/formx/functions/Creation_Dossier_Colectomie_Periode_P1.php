<?php
function Creation_Dossier_Colectomie_Periode_P1($formx) {

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
$sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P1' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Colectomie_Periode_P1');

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

createItem($formx2,$etape,'Colectomie_Periode_P1_Actes','<span style="color:#CC3333;font-weight:bold;">ACTES</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Actes']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Pre_Med','<span style="color:#336666;font-weight:bold;">---- PRESCRIPTIONS MEDICALES</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pre_Med']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Bil_Com','<span style="color:green;">Bilan complémentaire</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Bil_Com']);
if ( $tabValeurs['Colectomie_Periode_P1_Bil_Com_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Bil_Com_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Bil_Com_C']);
createItem($formx2,$etape,'Colectomie_Periode_P1_Tra_Med','<span style="color:green;">Traitement médicamenteux</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Tra_Med']);
if ( $tabValeurs['Colectomie_Periode_P1_Tra_Med_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Tra_Med_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Tra_Med_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Soi','<span style="color:#336666;font-weight:bold;">---- SOINS</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Soi']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Pre_Col','<span style="color:green;">Préparation colique</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pre_Col']);
if ( $tabValeurs['Colectomie_Periode_P1_Pre_Col_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Pre_Col_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pre_Col_C']);
createItem($formx2,$etape,'Colectomie_Periode_P1_Pre_Par','<span style="color:green;">Préparation pariétale</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pre_Par']);
if ( $tabValeurs['Colectomie_Periode_P1_Pre_Par_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Pre_Par_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pre_Par_C']);
if ( $tabValeurs['Colectomie_Periode_P1_Poi'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Poi','<span style="color:green;">Poids en Kg</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Poi']);
if ( $tabValeurs['Colectomie_Periode_P1_Tai'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Tai','<span style="color:green;">Taille en mètre</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Tai']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Ali','<span style="color:#336666;font-weight:bold;">---- ALIMENTATION</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Ali']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Die_Hyd','<span style="color:green;">Diète hydrique</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Die_Hyd']);
if ( $tabValeurs['Colectomie_Periode_P1_Die_Hyd_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Die_Hyd_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Die_Hyd_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Aut','<span style="color:#336666;font-weight:bold;">---- AUTONOMIE</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Aut']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Apr_Eta_CM','<span style="color:green;">Appréciation de l\'état initial</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Apr_Eta_CM']);
if ( $tabValeurs['Colectomie_Periode_P1_Apr_Eta_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Apr_Eta_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Apr_Eta_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Pre','<span style="color:#336666;font-weight:bold;">---- PREPARATION DE LA SORTIE</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pre']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Dem_Soi','<span style="color:green;">Demande de soins de suite</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Dem_Soi']);
if ( $tabValeurs['Colectomie_Periode_P1_Dem_Soi_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Dem_Soi_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Dem_Soi_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Rec','<span style="color:#336666;font-weight:bold;">---- RECUEIL INFORMATION/CONTROLE DOSSIER</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Rec']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Rec_Res','<span style="color:green;">Recueil des résultats des examens prescrits</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Rec_Res']);
if ( $tabValeurs['Colectomie_Periode_P1_Rec_Res_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Rec_Res_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Rec_Res_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Heu_Int','<span style="color:green;">Heure d\'intervention connue</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Heu_Int']);
if ( $tabValeurs['Colectomie_Periode_P1_Heu_Int_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Heu_Int_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Heu_Int_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Vis','<span style="color:#336666;font-weight:bold;">---- VISITE MEDICALE</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Vis']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Vis_Pre','<span style="color:green;">Visite pré anesthésique</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Vis_Pre']);
if ( $tabValeurs['Colectomie_Periode_P1_Vis_Pre_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Vis_Pre_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Vis_Pre_C']);
createItem($formx2,$etape,'Colectomie_Periode_P1_Vis_Chi','<span style="color:green;">Visite Chirurgien</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Vis_Chi']);
if ( $tabValeurs['Colectomie_Periode_P1_Vis_Chi_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Vis_Chi_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Vis_Chi_C']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Res','<span style="color:#336666;font-weight:bold;">---- RESULTATS OBTENUS POUR LE PATIENT</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Res']);

createItem($formx2,$etape,'Colectomie_Periode_P1_Pat_Pre','<span style="color:green;">Patient prêt pour le bloc</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pat_Pre']);
if ( $tabValeurs['Colectomie_Periode_P1_Pat_Pre_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P1_Pat_Pre_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P1_Pat_Pre_C']);


//

//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
