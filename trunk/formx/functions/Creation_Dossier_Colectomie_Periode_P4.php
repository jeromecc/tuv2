<?php
function Creation_Dossier_Colectomie_Periode_P4($formx) {

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
$sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P4' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Colectomie_Periode_P4');

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


createItem($formx2,$etape,'Colectomie_Periode_P4_Actes','<span style="color:#CC3333;font-weight:bold;">ACTES</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Actes']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Pre_Med','<span style="color:#336666;font-weight:bold;">---- PRESCRIPTIONS MEDICALES</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Pre_Med']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Bil_Pro','<span style="color:green;">Bilan selon le protocole à definir</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Bil_Pro']);
if ( $tabValeurs['Colectomie_Periode_P4_Bil_Pro_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Bil_Pro_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Bil_Pro_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Pri_Cha','<span style="color:green;">Prise en charge de la douleur réajustement et relais per os de l\'antalgique</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Pri_Cha']);
if ( $tabValeurs['Colectomie_Periode_P4_Pri_Cha_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Pri_Cha_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Pri_Cha_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Sng','<span style="color:green;">Arrêt SNG</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Sng']);
if ( $tabValeurs['Colectomie_Periode_P4_Arr_Sng_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Sng_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Sng_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Son','<span style="color:green;">Arrêt sonde urinaire</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Son']);
if ( $tabValeurs['Colectomie_Periode_P4_Arr_Son_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Son_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Son_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Cat','<span style="color:green;">Arrêt cathéter de la péridurale</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Cat']);
if ( $tabValeurs['Colectomie_Periode_P4_Arr_Cat_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Cat_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Cat_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Voi','<span style="color:green;">Arrêt voie veineuse</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Voi']);
if ( $tabValeurs['Colectomie_Periode_P4_Arr_Voi_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Arr_Voi_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Arr_Voi_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Mob_Dra','<span style="color:green;">Mobilisation des drains</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Mob_Dra']);
if ( $tabValeurs['Colectomie_Periode_P4_Mob_Dra_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Mob_Dra_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Mob_Dra_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Mob_Lam','<span style="color:green;">Mobilisation de la lame</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Mob_Lam']);
if ( $tabValeurs['Colectomie_Periode_P4_Mob_Lam_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Mob_Lam_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Mob_Lam_C']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Soi','<span style="color:#336666;font-weight:bold;">---- SOINS</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Soi']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Ref_Pan','<span style="color:green;">Réfection du pansement</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ref_Pan']);
if ( $tabValeurs['Colectomie_Periode_P4_Ref_Pan_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Ref_Pan_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ref_Pan_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Rep_Tran','<span style="color:green;">Reprise du transit</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Rep_Tran']);
if ( $tabValeurs['Colectomie_Periode_P4_Rep_Tran_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Rep_Tran_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Rep_Tran_C']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Aut','<span style="color:#336666;font-weight:bold;">---- AUTONOMIE</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Aut_Fau','<span style="color:green;">Fauteuil</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut_Fau']);
if ( $tabValeurs['Colectomie_Periode_P4_Aut_Fau_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Aut_Fau_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut_Fau_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Aut_Mar','<span style="color:green;">Marche</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut_Mar']);
if ( $tabValeurs['Colectomie_Periode_P4_Aut_Mar_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Aut_Mar_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut_Mar_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Aut_Kin','<span style="color:green;">Kiné</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut_Kin']);
if ( $tabValeurs['Colectomie_Periode_P4_Aut_Kin_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Aut_Kin_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Aut_Kin_C']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Ali','<span style="color:#336666;font-weight:bold;">---- ALIMENTATION</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ali']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Eau_Bou','<span style="color:green;">Eau bouillon tisane</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Eau_Bou']);
if ( $tabValeurs['Colectomie_Periode_P4_Eau_Bou_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Eau_Bou_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Eau_Bou_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Com_Flo','<span style="color:green;">Compote Floraline</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Com_Flo']);
if ( $tabValeurs['Colectomie_Periode_P4_Com_Flo_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Com_Flo_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Com_Flo_C']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Ali_Sol','<span style="color:green;">Alimentation solide</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ali_Sol']);
if ( $tabValeurs['Colectomie_Periode_P4_Ali_Sol_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Ali_Sol_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ali_Sol_C']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Pre','<span style="color:#336666;font-weight:bold;">---- PREPARATION DE LA SORTIE</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Pre']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Org_Sor','<span style="color:green;">Organisation de la sortie</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Org_Sor']);
if ( $tabValeurs['Colectomie_Periode_P4_Org_Sor_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Org_Sor_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Org_Sor_C']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Inf','<span style="color:#336666;font-weight:bold;">---- INFORMATION EDUCATION</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Inf']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Ann_Sg','<span style="color:green;">Annonce du Diag à J7</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ann_Sg']);
if ( $tabValeurs['Colectomie_Periode_P4_Ann_Sg_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Ann_Sg_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Ann_Sg_C']);

createItem($formx2,$etape,'Colectomie_Periode_P4_Res','<span style="color:#336666;font-weight:bold;">---- RESULTAT OBTENUS POUR LE PATIENT</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Res']);
createItem($formx2,$etape,'Colectomie_Periode_P4_Pat_Par','<span style="color:green;">Le patient participe pour retrouver progressivement son autonomie antérieure</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Pat_Par']);
if ( $tabValeurs['Colectomie_Periode_P4_Pat_Par_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P4_Pat_Par_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P4_Pat_Par_C']);


//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
