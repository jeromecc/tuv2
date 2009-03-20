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
	$val = $objFormx->XMLDOM->createElement('Val',utf8_encode($val));
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
$etape = createEtape($formx2,'1','IDENTITE DU PATIENT',array('etat'=>'fini'));
createItem($formx2,$etape,'id1','Nom','TXT',   $tabValeurs['Val_IDENT_NomPatient']);
createItem($formx2,$etape,'id2','Prénom','TXT',$tabValeurs['Val_IDENT_PrenomPatient']);
//createItem($formx2,$etape,'id3','Sexe','TXT',  $tabValeurs['Val_IDENT_SexePat']);
createItem($formx2,$etape,'id4','Date de naissance','TXT',$tabValeurs['Val_IDENT_DateNPat2']);
createItem($formx2,$etape,'id5','Age','TXT',$tabValeurs['Val_IDENT_AgePat']);
createItem($formx2,$etape,'id6','Adresse','TXT',$tabValeurs['Val_IDENT_AdressePat']);
createItem($formx2,$etape,'id7','Code postal','TXT',$tabValeurs['Val_IDENT_CodePPat']);
createItem($formx2,$etape,'id8','Ville','TXT',$tabValeurs['Val_IDENT_VillePat']);
createItem($formx2,$etape,'id9','Telephone','TXT',$tabValeurs['Val_IDENT_TelPat']);
createItem($formx2,$etape,'id10','Profession','TXT',$tabValeurs['Val_Main_Profession']);
createItem($formx2,$etape,'id11','Statut profession','TXT',$tabValeurs['Val_Main_StatutProfession']);
createItem($formx2,$etape,'id12','IDU','TXT',$tabValeurs['Val_IDENT_IDUPatient']);
createItem($formx2,$etape,'id13','ILP','TXT',$tabValeurs['Val_IDENT_ILPPatient']);
createItem($formx2,$etape,'id14','Jour de consultation du patient','TXT',$tabValeurs['Val_Jour_Consultation']);
createItem($formx2,$etape,'id15','Heure de consultation du patient','TXT',$tabValeurs['Val_Heure_Consultation']);

createItem($formx2,$etape,'s1','','TXT','');
createItem($formx2,$etape,'s2','','TXT','');
createItem($formx2,$etape,'s3','','TXT','');

// LESION 1
createItem($formx2,$etape,'lesion1','<span style="color:red;">LESION 1</span>','TXT','');
createItem($formx2,$etape,'lesion1s1','','TXT','');
createItem($formx2,$etape,'lesion1s2','','TXT','');
createItem($formx2,$etape,'lesion1s3','','TXT','');

if ( $tabValeurs['Val_Main_Complication_Capillaires'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires','Anomalie des pouls capillaires ou du remplissage pulpaire','TXT',$tabValeurs['Anomalie_capillaires']);
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1','V doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1','V doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2','V doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2','V doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3','IV doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3','IV doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4','IV doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4','IV doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5','III doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5','III doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6','III doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6','III doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7','II doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7','II doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8','II doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8','II doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9','I doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9','I doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10','I doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10','I doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration','Anomalie de la coloration ou de la température cutanée','TXT',$tabValeurs['Anomalie_capillaires']);
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1','V doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1','V doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2','V doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2','V doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3','IV doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3','IV doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4','IV doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4','IV doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5','III doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5','III doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6','III doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6','III doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7','II doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7','II doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8','II doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8','II doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9','I doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9','I doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10','I doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10','I doigt Gauche','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus','Anomalie du tonus postual','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ca','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cb','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cc','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cd','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ce','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds','Flechisseurs communs profonds','TXT',$tabValeurs['Anomalie_capillaires']);
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels','Flechisseurs communs superficiels','TXT',$tabValeurs['Anomalie_capillaires']);
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs','Extenseurs','TXT',$tabValeurs['Anomalie_capillaires']);
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1','V doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1','V doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2','V doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2','V doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3','IV doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3','IV doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4','IV doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4','IV doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5','III doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5','III doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6','III doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6','III doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7','II doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7','II doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8','II doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8','II doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9','I doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9','I doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10','I doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10','I doigt Gauche','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main','Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx2,$etape,'Nerf_Ulnaire','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx2,$etape,'Nerf_Radial','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx2,$etape,'Nerf_Median','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi'] != "" ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes','Sensibilité des hémi-pulpes des doigts','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a','V doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b','V doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c','V doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d','V doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e','IV doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f','IV doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g','IV doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h','IV doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i','III doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j','III doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k','III doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l','III doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m','II doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n','II doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o','II doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p','II doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q','I doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r','I doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s','I doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t','I doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( $tabValeurs['lesionsup'] == "Oui" ) {
createItem($formx2,$etape,'s1_1','','TXT','');
createItem($formx2,$etape,'s2_1','','TXT','');
createItem($formx2,$etape,'s3_1','','TXT','');

// LESION 2
createItem($formx2,$etape,'lesion1_1','LESION 2','TXT','');
createItem($formx2,$etape,'lesion1s1_1','','TXT','');
createItem($formx2,$etape,'lesion1s2_1','','TXT','');
createItem($formx2,$etape,'lesion1s3_1','','TXT','');

}

if ( $tabValeurs['Val_Main_Complication_Capillaires_1'] != "Pas d'anomalie"
     && $tabValeurs['Val_Main_Complication_Capillaires_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_1','Anomalie des pouls capillaires ou du remplissage pulpaire','TXT',$tabValeurs['Anomalie_capillaires_1']);
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_1'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_1','V doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_1'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_1','V doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_1'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_1','V doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_1'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_1','V doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_1'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_1','IV doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_1'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_1','IV doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_1'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_1','IV doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_1'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_1','IV doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_1'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_1','III doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_1'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_1','III doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_1'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_1','III doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_1'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_1','III doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_1'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_1','II doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_1'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_1','II doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_1'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_1','II doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_1'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_1','II doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_1'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_1','I doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_1'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_1','I doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_1'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_1','I doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_1'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_1','I doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_1','Anomalie de la coloration ou de la température cutanée','TXT',$tabValeurs['Anomalie_capillaires_1']);
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_1'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_1','V doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_1'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_1','V doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_1'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_1','V doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_1'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_1','V doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_1'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_1','IV doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_1'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_1','IV doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_1'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_1','IV doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_1'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_1','IV doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_1'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_1','III doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_1'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_1','III doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_1'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_1','III doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_1'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_1','III doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_1'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_1','II doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_1'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_1','II doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_1'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_1','II doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_1'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_1','II doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_1'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_1','I doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_1'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_1','I doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_1'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_1','I doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_1'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_1','I doigt Gauche','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_1'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_1','Anomalie du tonus postual','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ca_1','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cb_1','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cc_1','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cd_1','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ce_1','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_1'] != "Pas d'anomalie"
     && $tabValeurs['Val_Main_Complication_Profonds_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_1','Flechisseurs communs profonds','TXT',$tabValeurs['Anomalie_capillaires_1']);
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_1','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_1','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_1','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_1'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_1','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_1','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_1','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_1','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_1','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_1','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_1'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_1','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_1','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_1'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_1','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_1','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_1'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_1','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_1','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_1'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_1','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_1','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_1'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_1','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_1','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_1'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_1','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Superficiels_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_1','Flechisseurs communs superficiels','TXT',$tabValeurs['Anomalie_capillaires_1']);
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_1','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_1','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_1','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_1'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_1','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_1','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_1','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_1','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_1','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_1','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_1'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_1','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_1','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_1'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_1','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_1','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_1'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_1','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_1','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_1'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_1','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_1','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_1'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_1','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_1','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_1'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_1','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Extenseurs_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_1','Extenseurs','TXT',$tabValeurs['Anomalie_capillaires_1']);
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_1'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_1','V doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_1'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_1','V doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_1'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_1','V doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_1'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_1','V doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_1'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_1','IV doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_1'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_1','IV doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_1'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_1','IV doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_1'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_1','IV doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_1'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_1','III doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_1'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_1','III doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_1'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_1','III doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_1'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_1','III doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_1'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_1','II doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_1'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_1','II doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_1'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_1','II doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_1'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_1','II doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_1'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_1','I doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_1'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_1','I doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_1'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_1','I doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_1'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_1','I doigt Gauche','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_1'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_1','Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_1']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx2,$etape,'Nerf_Ulnaire_1','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_1']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx2,$etape,'Nerf_Radial_1','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_1']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx2,$etape,'Nerf_Median_1','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_1'] != "" ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_1','Sensibilité des hémi-pulpes des doigts','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_1','V doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_1','V doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_1','V doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_1','V doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_1','IV doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_1','IV doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_1','IV doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_1','IV doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_1','III doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_1','III doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_1','III doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_1','III doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_1','II doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_1','II doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_1','II doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_1','II doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_1','I doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_1','I doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_1','I doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_1','I doigt Gauche','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_1'] == "Oui" ) {
createItem($formx2,$etape,'s1_2','','TXT','');
createItem($formx2,$etape,'s2_2','','TXT','');
createItem($formx2,$etape,'s3_2','','TXT','');

// LESION 3
createItem($formx2,$etape,'lesion1_2','LESION 3','TXT','');
createItem($formx2,$etape,'lesion1s1_2','','TXT','');
createItem($formx2,$etape,'lesion1s2_2','','TXT','');
createItem($formx2,$etape,'lesion1s3_2','','TXT','');

}



if ( $tabValeurs['Val_Main_Complication_Capillaires_2'] != "Pas d'anomalie"
     && $tabValeurs['Val_Main_Complication_Capillaires_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_2','Anomalie des pouls capillaires ou du remplissage pulpaire','TXT',$tabValeurs['Anomalie_capillaires_2']);
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_2'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_2','V doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_2'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_2','V doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_2'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_2','V doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_2'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_2','V doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_2'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_2','IV doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_2'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_2','IV doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_2'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_2','IV doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_2'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_2','IV doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_2'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_2','III doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_2'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_2','III doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_2'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_2','III doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_2'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_2','III doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_2'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_2','II doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_2'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_2','II doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_2'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_2','II doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_2'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_2','II doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_2'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_2','I doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_2'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_2','I doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_2'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_2','I doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_2'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_2','I doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Coloration_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_2','Anomalie de la coloration ou de la température cutanée','TXT',$tabValeurs['Anomalie_capillaires_2']);
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_2'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_2','V doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_2'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_2','V doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_2'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_2','V doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_2'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_2','V doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_2'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_2','IV doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_2'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_2','IV doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_2'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_2','IV doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_2'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_2','IV doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_2'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_2','III doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_2'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_2','III doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_2'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_2','III doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_2'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_2','III doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_2'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_2','II doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_2'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_2','II doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_2'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_2','II doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_2'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_2','II doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_2'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_2','I doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_2'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_2','I doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_2'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_2','I doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_2'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_2','I doigt Gauche','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_2'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_2','Anomalie du tonus postual','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ca_2','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cb_2','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cc_2','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cd_2','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ce_2','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Profonds_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_2','Flechisseurs communs profonds','TXT',$tabValeurs['Anomalie_capillaires_2']);
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_2','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_2'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_2','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_2','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_2','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_2','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_2','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_2','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_2','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_2','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_2'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_2','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_2','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_2'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_2','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_2','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_2'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_2','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_2','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_2'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_2','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_2','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_2'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_2','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_2','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_2'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_2','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Superficiels_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_2','Flechisseurs communs superficiels','TXT',$tabValeurs['Anomalie_capillaires_2']);
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_2','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_2'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_2','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_2','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_2','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_2','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_2','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_2','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_2','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_2','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_2'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_2','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_2','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_2'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_2','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_2','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_2'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_2','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_2','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_2'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_2','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_2','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_2'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_2','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_2','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_2'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_2','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Extenseurs_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_2','Extenseurs','TXT',$tabValeurs['Anomalie_capillaires_2']);
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_2'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_2','V doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_2'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_2','V doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_2'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_2','V doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_2'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_2','V doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_2'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_2','IV doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_2'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_2','IV doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_2'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_2','IV doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_2'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_2','IV doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_2'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_2','III doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_2'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_2','III doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_2'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_2','III doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_2'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_2','III doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_2'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_2','II doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_2'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_2','II doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_2'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_2','II doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_2'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_2','II doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_2'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_2','I doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_2'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_2','I doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_2'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_2','I doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_2'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_2','I doigt Gauche','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_2'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_2','Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_2']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx2,$etape,'Nerf_Ulnaire_2','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_2']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx2,$etape,'Nerf_Radial_2','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_2']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx2,$etape,'Nerf_Median_2','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_2'] != "" ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_2','Sensibilité des hémi-pulpes des doigts','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_2','V doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_2','V doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_2','V doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_2','V doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_2','IV doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_2','IV doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_2','IV doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_2','IV doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_2','III doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_2','III doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_2','III doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_2','III doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_2','II doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_2','II doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_2','II doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_2','II doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_2','I doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_2','I doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_2','I doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_2','I doigt Gauche','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_2'] == "Oui" ) {
createItem($formx2,$etape,'s1_3','','TXT','');
createItem($formx2,$etape,'s2_3','','TXT','');
createItem($formx2,$etape,'s3_3','','TXT','');

// LESION 4
createItem($formx2,$etape,'lesion1_3','LESION 4','TXT','');
createItem($formx2,$etape,'lesion1s1_3','','TXT','');
createItem($formx2,$etape,'lesion1s2_3','','TXT','');
createItem($formx2,$etape,'lesion1s3_3','','TXT','');

}



if ( $tabValeurs['Val_Main_Complication_Capillaires_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Capillaires_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_3','Anomalie des pouls capillaires ou du remplissage pulpaire','TXT',$tabValeurs['Anomalie_capillaires_3']);
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_3'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_3','V doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_3'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_3','V doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_3'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_3','V doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_3'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_3','V doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_3'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_3','IV doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_3'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_3','IV doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_3'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_3','IV doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_3'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_3','IV doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_3'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_3','III doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_3'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_3','III doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_3'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_3','III doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_3'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_3','III doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_3'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_3','II doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_3'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_3','II doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_3'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_3','II doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_3'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_3','II doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_3'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_3','I doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_3'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_3','I doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_3'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_3','I doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_3'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_3','I doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Coloration_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_3','Anomalie de la coloration ou de la température cutanée','TXT',$tabValeurs['Anomalie_capillaires_3']);
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_3'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_3','V doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_3'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_3','V doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_3'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_3','V doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_3'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_3','V doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_3'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_3','IV doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_3'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_3','IV doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_3'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_3','IV doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_3'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_3','IV doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_3'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_3','III doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_3'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_3','III doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_3'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_3','III doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_3'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_3','III doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_3'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_3','II doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_3'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_3','II doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_3'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_3','II doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_3'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_3','II doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_3'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_3','I doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_3'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_3','I doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_3'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_3','I doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_3'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_3','I doigt Gauche','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_3'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_3','Anomalie du tonus postual','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ca_3','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cb_3','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cc_3','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cd_3','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ce_3','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Profonds_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_3','Flechisseurs communs profonds','TXT',$tabValeurs['Anomalie_capillaires_3']);
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_3','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_3'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_3','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_3','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_3'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_3','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_3','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_3','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_3','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_3','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_3','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_3'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_3','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_3','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_3'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_3','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_3','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_3'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_3','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_3','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_3'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_3','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_3','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_3'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_3','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_3','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_3'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_3','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Superficiels_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_3','Flechisseurs communs superficiels','TXT',$tabValeurs['Anomalie_capillaires_3']);
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_3','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_3'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_3','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_3','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_3'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_3','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_3','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_3','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_3','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_3','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_3','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_3'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_3','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_3','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_3'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_3','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_3','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_3'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_3','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_3','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_3'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_3','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_3','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_3'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_3','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_3','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_3'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_3','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Extenseurs_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_3','Extenseurs','TXT',$tabValeurs['Anomalie_capillaires_3']);
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_3'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_3','V doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_3'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_3','V doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_3'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_3','V doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_3'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_3','V doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_3','IV doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_3'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_3','IV doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_3','IV doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_3'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_3','IV doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_3'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_3','III doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_3'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_3','III doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_3'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_3','III doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_3'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_3','III doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_3'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_3','II doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_3'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_3','II doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_3'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_3','II doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_3'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_3','II doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_3'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_3','I doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_3'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_3','I doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_3'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_3','I doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_3'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_3','I doigt Gauche','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_3'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_3','Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_3']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx2,$etape,'Nerf_Ulnaire_3','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_3']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx2,$etape,'Nerf_Radial_3','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_3']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx2,$etape,'Nerf_Median_3','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_3'] != "" ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_3','Sensibilité des hémi-pulpes des doigts','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_3','V doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_3','V doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_3','V doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_3','V doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_3','IV doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_3','IV doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_3','IV doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_3','IV doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_3','III doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_3','III doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_3','III doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_3','III doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_3','II doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_3','II doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_3','II doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_3','II doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_3','I doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_3','I doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_3','I doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_3','I doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( $tabValeurs['lesionsup_3'] == "Oui" ) {
createItem($formx2,$etape,'s1_4','','TXT','');
createItem($formx2,$etape,'s2_4','','TXT','');
createItem($formx2,$etape,'s3_4','','TXT','');

// LESION 5
createItem($formx2,$etape,'lesion1_4','LESION 5','TXT','');
createItem($formx2,$etape,'lesion1s1_4','','TXT','');
createItem($formx2,$etape,'lesion1s2_4','','TXT','');
createItem($formx2,$etape,'lesion1s3_4','','TXT','');

}



if ( $tabValeurs['Val_Main_Complication_Capillaires_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Capillaires_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_4','Anomalie des pouls capillaires ou du remplissage pulpaire','TXT',$tabValeurs['Anomalie_capillaires_4']);
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_4'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_4','V doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_4'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_4','V doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_4'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_4','V doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_4'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_4','V doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_4'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_4','IV doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_4'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_4','IV doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_4'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_4','IV doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_4'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_4','IV doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_4'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_4','III doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_4'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_4','III doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_4'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_4','III doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_4'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_4','III doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_4'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_4','II doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_4'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_4','II doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_4'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_4','II doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_4'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_4','II doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_4'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_4','I doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_4'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_4','I doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_4'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_4','I doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_4'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_4','I doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Coloration_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_4','Anomalie de la coloration ou de la température cutanée','TXT',$tabValeurs['Anomalie_capillaires_4']);
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_4'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_4','V doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_4'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_4','V doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_4'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_4','V doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_4'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_4','V doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_4'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_4','IV doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_4'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_4','IV doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_4'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_4','IV doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_4'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_4','IV doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_4'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_4','III doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_4'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_4','III doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_4'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_4','III doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_4'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_4','III doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_4'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_4','II doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_4'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_4','II doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_4'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_4','II doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_4'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_4','II doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_4'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_4','I doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_4'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_4','I doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_4'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_4','I doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_4'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_4','I doigt Gauche','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_4'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_4','Anomalie du tonus postual','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ca_4','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cb_4','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cc_4','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cd_4','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ce_4','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Profonds_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_4','Flechisseurs communs profonds','TXT',$tabValeurs['Anomalie_capillaires_4']);
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_4','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_4'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_4','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_4','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_4'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_4','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_4','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_4','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_4','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_4','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_4','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_4'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_4','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_4','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_4'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_4','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_4','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_4'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_4','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_4','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_4'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_4','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_4','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_4'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_4','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_4','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_4'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_4','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Superficiels_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_4','Flechisseurs communs superficiels','TXT',$tabValeurs['Anomalie_capillaires_4']);
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_4','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_4'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_4','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_4','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_4'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_4','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_4','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_4','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_4','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_4','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_4','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_4'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_4','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_4','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_4'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_4','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_4','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_4'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_4','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_4','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_4'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_4','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_4','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_4'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_4','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_4','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_4'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_4','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Extenseurs_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_4','Extenseurs','TXT',$tabValeurs['Anomalie_capillaires_4']);
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_4'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_4','V doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_4'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_4','V doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_4'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_4','V doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_4'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_4','V doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_4','IV doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_4'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_4','IV doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_4','IV doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_4'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_4','IV doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_4'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_4','III doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_4'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_4','III doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_4'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_4','III doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_4'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_4','III doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_4'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_4','II doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_4'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_4','II doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_4'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_4','II doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_4'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_4','II doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_4'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_4','I doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_4'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_4','I doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_4'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_4','I doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_4'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_4','I doigt Gauche','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_4'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_4','Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_4']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx2,$etape,'Nerf_Ulnaire_4','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_4']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx2,$etape,'Nerf_Radial_4','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_4']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx2,$etape,'Nerf_Median_4','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_4'] != "" ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_4','Sensibilité des hémi-pulpes des doigts','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_4','V doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_4','V doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_4','V doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_4','V doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_4','IV doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_4','IV doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_4','IV doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_4','IV doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_4','III doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_4','III doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_4','III doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_4','III doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_4','II doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_4','II doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_4','II doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_4','II doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_4','I doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_4','I doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_4','I doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_4','I doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( $tabValeurs['lesionsup_4'] == "Oui" ) {
createItem($formx2,$etape,'s1_5','','TXT','');
createItem($formx2,$etape,'s2_5','','TXT','');
createItem($formx2,$etape,'s3_5','','TXT','');

// LESION 6
createItem($formx2,$etape,'lesion1_5','LESION 6','TXT','');
createItem($formx2,$etape,'lesion1s1_5','','TXT','');
createItem($formx2,$etape,'lesion1s2_5','','TXT','');
createItem($formx2,$etape,'lesion1s3_5','','TXT','');

}



if ( $tabValeurs['Val_Main_Complication_Capillaires_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Capillaires_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_5','Anomalie des pouls capillaires ou du remplissage pulpaire','TXT',$tabValeurs['Anomalie_capillaires_5']);
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_5'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_5','V doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_5'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_5','V doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_5'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_5','V doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_5'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_5','V doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_5'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_5','IV doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_5'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_5','IV doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_5'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_5','IV doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_5'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_5','IV doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_5'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_5','III doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_5'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_5','III doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_5'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_5','III doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_5'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_5','III doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_5'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_5','II doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_5'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_5','II doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_5'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_5','II doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_5'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_5','II doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_5'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_5','I doigt Droit','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_5'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_5','I doigt Droit','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_5'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_5','I doigt Gauche','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_5'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_5','I doigt Gauche','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Coloration_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_5','Anomalie de la coloration ou de la température cutanée','TXT',$tabValeurs['Anomalie_capillaires_5']);
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_5'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_5','V doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_5'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_5','V doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_5'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_5','V doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_5'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_5','V doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_5'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_5','IV doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_5'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_5','IV doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_5'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_5','IV doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_5'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_5','IV doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_5'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_5','III doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_5'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_5','III doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_5'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_5','III doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_5'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_5','III doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_5'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_5','II doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_5'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_5','II doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_5'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_5','II doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_5'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_5','II doigt Gauche','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_5'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_5','I doigt Droit','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_5'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_5','I doigt Droit','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_5'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_5','I doigt Gauche','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_5'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_5','I doigt Gauche','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_5'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_5','Anomalie du tonus postual','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ca_5','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cb_5','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cc_5','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_cd_5','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_ce_5','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Profonds_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_5','Flechisseurs communs profonds','TXT',$tabValeurs['Anomalie_capillaires_5']);
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_5','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_5'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_5','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_5','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_5'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_5','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_5','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_5','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_5','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_5','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_5','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_5','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_5','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_5'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_5','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_5','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_5'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_5','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_5','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_5'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_5','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_5','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_5'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_5','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_5','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_5'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_5','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Superficiels_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_5','Flechisseurs communs superficiels','TXT',$tabValeurs['Anomalie_capillaires_5']);
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_5','V doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_5'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_5','V doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_5','V doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_5'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_5','V doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_5','IV doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_5','IV doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_5','IV doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_5','IV doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_5','III doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_5','III doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_5','III doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_5'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_5','III doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_5','II doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_5'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_5','II doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_5','II doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_5'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_5','II doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_5','I doigt Droit','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_5'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_5','I doigt Droit','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_5','I doigt Gauche','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_5'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_5','I doigt Gauche','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_Extenseurs_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_5','Extenseurs','TXT',$tabValeurs['Anomalie_capillaires_5']);
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_5'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_5','V doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_5'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_5','V doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_5'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_5','V doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_5'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_5','V doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_5'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_5','IV doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_5'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_5','IV doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_5'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_5','IV doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_5'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_5','IV doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_5'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_5','III doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_5'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_5','III doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_5'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_5','III doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_5'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_5','III doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_5'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_5','II doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_5'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_5','II doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_5'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_5','II doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_5'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_5','II doigt Gauche','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_5'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_5','I doigt Droit','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_5'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_5','I doigt Droit','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_5'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_5','I doigt Gauche','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_5'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_5','I doigt Gauche','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_5'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_5','Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_5']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx2,$etape,'Nerf_Ulnaire_5','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_5']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx2,$etape,'Nerf_Radial_5','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_5']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx2,$etape,'Nerf_Median_5','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_5'] != "" ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_5','Sensibilité des hémi-pulpes des doigts','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_5','V doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_5','V doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_5','V doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_5','V doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_5','IV doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_5','IV doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_5','IV doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_5','IV doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_5','III doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_5','III doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_5','III doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_5','III doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_5','II doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_5','II doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_5','II doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_5','II doigt Gauche','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_5','I doigt Droit','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_5','I doigt Droit','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_5','I doigt Gauche','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_5','I doigt Gauche','TXT','Pulpe cubitale anormale');
  }

//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();


}
?>

