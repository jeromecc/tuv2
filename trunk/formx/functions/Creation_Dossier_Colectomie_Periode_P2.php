<?php
function Creation_Dossier_Colectomie_Periode_P2($formx) {

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
$sql=$requete->delRecord("idformx='Dossier_Colectomie_Periode_P2' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Colectomie_Periode_P2');

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

createItem($formx2,$etape,'Colectomie_Periode_P2_Actes','<span style="color:#CC3333;font-weight:bold;">ACTES</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Actes']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Soi','<span style="color:#336666;font-weight:bold;">---- SOINS</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Soi']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Soi_Pre','<span style="color:green;">Soins pré-bloc immédiat</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Soi_Pre']);
if ( $tabValeurs['Colectomie_Periode_P2_Soi_Pre_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Soi_Pre_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Soi_Pre_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Sur_Rap','<span style="color:green;">Surveillance rapprochée</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Sur_Rap']);
if ( $tabValeurs['Colectomie_Periode_P2_Sur_Rap_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Sur_Rap_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Sur_Rap_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Sng','<span style="color:green;">SNG</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Sng']);
if ( $tabValeurs['Colectomie_Periode_P2_Sng_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Sng_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Sng_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Sur_Diu','<span style="color:green;">Surveillance diurèse sur 24 H</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Sur_Diu']);
if ( $tabValeurs['Colectomie_Periode_P2_Sur_Diu_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Sur_Diu_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Sur_Diu_C']);

createItem($formx2,$etape,'Colectomie_Periode_P2_Pre','<span style="color:#336666;font-weight:bold;">---- PRESCRIPTIONS MEDICALES</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Pre']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Pri_Cha','<span style="color:green;">Prise en charge de la douleur</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Pri_Cha']);
if ( $tabValeurs['Colectomie_Periode_P2_Pri_Cha_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Pri_Cha_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Pri_Cha_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Dou_Mai','<span style="color:green;">Douleur Maîtrisée Inférieur à 4</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Dou_Mai']);
if ( $tabValeurs['Colectomie_Periode_P2_Dou_Mai_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Dou_Mai_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Dou_Mai_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Dou_Eva','<span style="color:green;">Douleur évaluée par</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Dou_Eva']);
if ( $tabValeurs['Colectomie_Periode_P2_Dou_Eva_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Dou_Eva_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Dou_Eva_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Tra_Ant','<span style="color:green;">Traitement anti-thromboembolique</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Tra_Ant']);
if ( $tabValeurs['Colectomie_Periode_P2_Tra_Ant_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Tra_Ant_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Tra_Ant_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Aut_Pre','<span style="color:green;">Autre prescription</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Aut_Pre']);
if ( $tabValeurs['Colectomie_Periode_P2_Aut_Pre_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Aut_Pre_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Aut_Pre_C']);

createItem($formx2,$etape,'Colectomie_Periode_P2_Vis','<span style="color:#336666;font-weight:bold;">---- VISITE MEDICALE</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Vis']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Vis_Med','<span style="color:green;">Visite médicale</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Vis_Med']);
if ( $tabValeurs['Colectomie_Periode_P2_Vis_Med_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Vis_Med_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Vis_Med_C']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Ren_Fam','<span style="color:green;">Rencontre avec la famille</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Ren_Fam']);
if ( $tabValeurs['Colectomie_Periode_P2_Ren_Fam_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Ren_Fam_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Ren_Fam_C']);

createItem($formx2,$etape,'Colectomie_Periode_P2_Res','<span style="color:#336666;font-weight:bold;">---- RESULTATS OBTENUS POUR LE PATIENT</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Res']);
createItem($formx2,$etape,'Colectomie_Periode_P2_Ret_Ser','<span style="color:green;">Retour dans le service d\'un patient cadré et non algique</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Ret_Ser']);
if ( $tabValeurs['Colectomie_Periode_P2_Ret_Ser_C'] != "" )
  createItem($formx2,$etape,'Colectomie_Periode_P2_Ret_Ser_C','<span style="color:#666699;">Commentaires</span>','TXT',$tabValeurs['Colectomie_Periode_P2_Ret_Ser_C']);




//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>
