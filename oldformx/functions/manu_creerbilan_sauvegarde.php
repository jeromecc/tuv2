<?php
function manu_creerbilan($formx) {

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
	$val = $objFormx->XMLDOM->createElement('Val',$val);
	$item->appendChild($val);
}

//-----------------------------------
//Exemples d'utilisation
//-----------------------------------

//Creation d'un nouveau formulaire de type 'coin'
$ids = $formx->getIDS();
$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('pouik.xml');

//ajout d 'une étape
$etape = createEtape($formx2,'1','Mon libelle',array('etat'=>'fini'));
createItem($formx2,$etape,'id1','Libelle du premier item','TXT','Valeur du premier item');
createItem($formx2,$etape,'id2','Libelle du second item','TXT','Valeur du deuxieme item');


// creation d'une seconde etape si Val_Main_Conclusion_Coloration_choix3Ma = Couleur blanc
if ( $tabValeurs['Val_Main_Conclusion_Coloration_choix3Ma'] == 'Couleur blanc') {
	$etape = createEtape($formx2,'2','Mon libelle 2',array('etat'=>'fini'));
	createItem($formx2,$etape,'id3','Libelle du premier item','TXT','Valeur du premier item');
	createItem($formx2,$etape,'id4','Libelle du second item','TXT','Valeur du deuxieme item');
}
//

//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>

