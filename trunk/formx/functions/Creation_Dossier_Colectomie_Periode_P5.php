<?php
function Creation_Dossier_Colectomie_Periode_P5($formx) {

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
$sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P5' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Colectomie_Periode_P5');

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

createItem($formx2,$etape,'Colectomie_Periode_P5_Actes','<span style="color:#CC3333;font-weight:bold;">ACTES</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Actes']);

createItem($formx2,$etape,'Colectomie_Periode_P5_Pre_Med','<span style="color:#336666;font-weight:bold;">---- PRESCRIPTIONS MEDICALES</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Pre_Med']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Bil_Pro','<span style="color:green;">Bilan selon le protocole à definir</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Bil_Pro']);
if ( $tabValeurs['Colectomie_Periode_P5_Bil_Pro_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Bil_Pro_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Bil_Pro_C']);

createItem($formx2,$etape,'Colectomie_Periode_P5_Soi','<span style="color:#336666;font-weight:bold;">---- SOINS</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Soi']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Abl_Fil','<span style="color:green;">Ablation des fils (sur prescription médicale)</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Abl_Fil']);
if ( $tabValeurs['Colectomie_Periode_P5_Abl_Fil_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Abl_Fil_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Abl_Fil_C']);

createItem($formx2,$etape,'Colectomie_Periode_P5_Aut','<span style="color:#336666;font-weight:bold;">---- AUTONOMIE</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Aut']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Eva_Aut','<span style="color:green;">Evaluer l\'autonomie à la sortie</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Eva_Aut']);
if ( $tabValeurs['Colectomie_Periode_P5_Eva_Aut_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Eva_Aut_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Eva_Aut_C']);

createItem($formx2,$etape,'Colectomie_Periode_P5_Ali','<span style="color:#336666;font-weight:bold;">---- ALIMENTATION</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Ali']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Con_Die','<span style="color:green;">Conseil diététique</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Con_Die']);
if ( $tabValeurs['Colectomie_Periode_P5_Con_Die_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Con_Die_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Con_Die_C']);

createItem($formx2,$etape,'Colectomie_Periode_P5_Pre','<span style="color:#336666;font-weight:bold;">---- PREPARATION DE LA SORTIE</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Pre']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Pla_Pos','<span style="color:green;">Planification pose portacath réalisée</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Pla_Pos']);
if ( $tabValeurs['Colectomie_Periode_P5_Pla_Pos_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Pla_Pos_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Pla_Pos_C']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Doc_Sor','<span style="color:green;">Documents pour la sortie prêts</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Doc_Sor']);
if ( $tabValeurs['Colectomie_Periode_P5_Doc_Sor_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Doc_Sor_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Doc_Sor_C']);

createItem($formx2,$etape,'Colectomie_Periode_P5_Res','<span style="color:#336666;font-weight:bold;">---- RESULTAT OBTENUS POUR LE PATIENT</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Res']);
createItem($formx2,$etape,'Colectomie_Periode_P5_Pat_Inf','<span style="color:green;">Le patient informé et assuré par rapport à la sortie</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Pat_Inf']);
if ( $tabValeurs['Colectomie_Periode_P5_Pat_Inf_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P5_Pat_Inf_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P5_Pat_Inf_C']);


//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
