<?php
function Creation_Dossier_Colectomie_Periode_P3($formx) {

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
$sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P3' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Colectomie_Periode_P3');

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

createItem($formx2,$etape,'Colectomie_Periode_P3_Actes','<span style="color:#CC3333;font-weight:bold;">ACTES</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Actes']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Med','<span style="color:#336666;font-weight:bold;">---- PRESCRIPTIONS MEDICALES</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Med']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Bil_San','<span style="color:green;">Bilan sanguin et urinaire</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Bil_San']);
if ( $tabValeurs['Colectomie_Periode_P3_Bil_San_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Bil_San_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Bil_San_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Pri_Cha','<span style="color:green;">Prise en charge de la douleur réajustement</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pri_Cha']);
if ( $tabValeurs['Colectomie_Periode_P3_Pri_Cha_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Pri_Cha_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pri_Cha_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Abl_Per','<span style="color:green;">Ablation KT péridural</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Abl_Per']);
if ( $tabValeurs['Colectomie_Periode_P3_Abl_Per_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Abl_Per_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Abl_Per_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Rec_Trai','<span style="color:green;">Reconduction du traitement antérieur</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Rec_Trai']);
if ( $tabValeurs['Colectomie_Periode_P3_Rec_Trai_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Rec_Trai_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Rec_Trai_C']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Soi','<span style="color:#336666;font-weight:bold;">---- SOINS</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Soi']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Ref_Pan','<span style="color:green;">Réfection du pansement</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Ref_Pan']);
if ( $tabValeurs['Colectomie_Periode_P3_Ref_Pan_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Ref_Pan_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Ref_Pan_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Son','<span style="color:green;">Présence de la sonde nasogastrique</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Son']);
if ( $tabValeurs['Colectomie_Periode_P3_Pre_Son_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Son_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Son_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Soi_Dra','<span style="color:green;">Soins sur les drains</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Soi_Dra']);
if ( $tabValeurs['Colectomie_Periode_P3_Soi_Dra_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Soi_Dra_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Soi_Dra_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Sonb','<span style="color:green;">Présence de la sonde à demeure</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Sonb']);
if ( $tabValeurs['Colectomie_Periode_P3_Pre_Sonb_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Sonb_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Sonb_C']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Aut','<span style="color:#336666;font-weight:bold;">---- AUTONOMIE</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Aut']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Lev','<span style="color:green;">Premier levé</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Lev']);
if ( $tabValeurs['Colectomie_Periode_P3_Pre_Lev_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Pre_Lev_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre_Lev_C']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Vis','<span style="color:#336666;font-weight:bold;">---- VISITE MEDICALE</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Vis']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Ren_Fam','<span style="color:green;">Rencontre avec la famille faite</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Ren_Fam']);
if ( $tabValeurs['Colectomie_Periode_P3_Ren_Fam_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Ren_Fam_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Ren_Fam_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Vis_Ane','<span style="color:green;">Visite anesthésiste faite</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Vis_Ane']);
if ( $tabValeurs['Colectomie_Periode_P3_Vis_Ane_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Vis_Ane_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Vis_Ane_C']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Pre','<span style="color:#336666;font-weight:bold;">---- PREPARATION DE LA SORTIE</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pre']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Sas_Mod','<span style="color:green;">S\'assurer que les modalités de sortie sont en cours</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Sas_Mod']);
if ( $tabValeurs['Colectomie_Periode_P3_Sas_Mod_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Sas_Mod_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Sas_Mod_C']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Res','<span style="color:#336666;font-weight:bold;">---- RESULTATS OBTENUS POUR LE PATIENT</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Res']);

createItem($formx2,$etape,'Colectomie_Periode_P3_Pat_Alg','<span style="color:green;">Patient non algique</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pat_Alg']);
if ( $tabValeurs['Colectomie_Periode_P3_Pat_Alg_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Pat_Alg_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Pat_Alg_C']);
createItem($formx2,$etape,'Colectomie_Periode_P3_Mai_Fon','<span style="color:green;">Maintien des fonctions physiologiques assuré</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Mai_Fon']);
if ( $tabValeurs['Colectomie_Periode_P3_Mai_Fon_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P3_Mai_Fon_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P3_Mai_Fon_C']);


//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
