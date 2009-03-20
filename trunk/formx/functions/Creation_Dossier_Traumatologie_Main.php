<?php
function Creation_Dossier_Traumatologie_Main($formx) {

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


$requete=new clRequete(BDD,TABLEFORMX,$param);
$sql=$requete->delRecord("idformx='Dossier_Traumatologie_Main_Resume' and ids='".$ids."'");

$requete=new clRequete(BDD,TABLEFORMX,$param);
$sql=$requete->delRecord("idformx='Dossier_Traumatologie_Main' and ids='".$ids."'");



$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_Traumatologie_Main_Resume.xml');

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

if ( $tabValeurs['Val_Main_Presence_Bague'] == "Oui" ) {
  createItem($formx2,$etape,'Val_Main_Conclusion_Presence_Bague','<span style="color:green;">Présence de bague(s)</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Coupure'] == "Déchiquetée" 
     && $tabValeurs['Val_Main_Mecanisme_Coupure_G'] == "Verre" ) {
  createItem($formx2,$etape,'Val_Main_Conlusion_Coupure','<span style="color:green;">Coupure par Verre</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Injection'] == "Oui" ) {
  createItem($formx2,$etape,'Val_Main_Conlusion_Injection','<span style="color:green;">Injection sous pression</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Traction'] == "Oui" ) {
  createItem($formx2,$etape,'Val_Main_Conclusion_Traction','<span style="color:green;">Traction sur une bague</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Grante'] == "Main multilesée avec évidence chirurgicale" ) {
  createItem($formx2,$etape,'Val_Main_Conclusion_Grante_Main','<span style="color:green;">Recherche de signe de gravité immédiat</span>','TXT','Main multilesée avec évidence chirurgicale');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Grante'] == "Amputation compléte ou partielle avec dévascularisation" ) {
  createItem($formx2,$etape,'Val_Main_Conclusion_Grante_Amputation','<span style="color:green;">Recherche de signe de gravité immédiat</span>','TXT','Amputation compléte ou partielle avec dévascularisation');
  }
  
createItem($formx2,$etape,'s1','','TXT','');
createItem($formx2,$etape,'s2','','TXT','');
createItem($formx2,$etape,'s3','','TXT','');

// LESION 1
createItem($formx2,$etape,'lesion1','<span style="color:red;font-weight:bold;">LESION 1</span>','TXT','');
createItem($formx2,$etape,'lesion1s1','','TXT','');
createItem($formx2,$etape,'lesion1s2','','TXT','');
createItem($formx2,$etape,'lesion1s3','','TXT','');

if ( $tabValeurs['Val_Main_Lesion_Face'] == "Les deux" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMa','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face'] == "Palmaire" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMb','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face'] == "Transfixiante" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMc','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
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
  createItem($formx2,$etape,'Complication_Profonds','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
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
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( $tabValeurs['lesionsup'] == "Oui" ) {
createItem($formx2,$etape,'s1_1','','TXT','');
createItem($formx2,$etape,'s2_1','','TXT','');
createItem($formx2,$etape,'s3_1','','TXT','');

// LESION 2
createItem($formx2,$etape,'lesion1_1','<span style="color:red;font-weight:bold;">LESION 2</span>','TXT','');
createItem($formx2,$etape,'lesion1s1_1','','TXT','');
createItem($formx2,$etape,'lesion1s2_1','','TXT','');
createItem($formx2,$etape,'lesion1s3_1','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_1'] == "Les deux" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMa_1','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_1'] == "Palmaire" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMb_1','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_1'] == "Transfixiante" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMc_1','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_1','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_1'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_1'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_1'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_1'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_1'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_1'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_1'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_1'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_1'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_1'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_1'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_1'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_1'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_1'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_1'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_1'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_1'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_1'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_1'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_1'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_1','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_1'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_1'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_1'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_1'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_1'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_1'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_1'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_1'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_1'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_1'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_1'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_1'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_1'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_1'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_1'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_1'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_1'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_1'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_1'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_1'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_1'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_1','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
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
  createItem($formx2,$etape,'Complication_Profonds_1','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_1'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_1'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_1'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_1'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_1'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_1'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_1'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_1','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_1'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_1'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_1'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_1'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_1'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_1'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_1'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_1'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_1','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_1'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_1'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_1'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_1'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_1'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_1'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_1'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_1'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_1'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_1'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_1'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_1'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_1'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_1'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_1'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_1'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_1'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_1'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_1'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_1'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_1'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_1','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
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
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_1','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_1','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_1','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_1','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_1','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_1','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_1','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_1','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_1','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_1','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_1','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_1','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_1','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_1','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_1','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_1','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_1','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_1','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_1','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_1'] == "Oui" ) {
createItem($formx2,$etape,'s1_2','','TXT','');
createItem($formx2,$etape,'s2_2','','TXT','');
createItem($formx2,$etape,'s3_2','','TXT','');

// LESION 3
createItem($formx2,$etape,'lesion1_2','<span style="color:red;font-weight:bold;">LESION 3</span>','TXT','');
createItem($formx2,$etape,'lesion1s1_2','','TXT','');
createItem($formx2,$etape,'lesion1s2_2','','TXT','');
createItem($formx2,$etape,'lesion1s3_2','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_2'] == "Les deux" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMa_2','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_2'] == "Palmaire" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMb_2','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_2'] == "Transfixiante" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMc_2','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_2','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_2'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_2'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_2'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_2'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_2'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_2'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_2'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_2'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_2'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_2'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_2'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_2'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_2'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_2'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_2'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_2'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_2'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_2'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_2'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_2'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_2','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_2'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_2'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_2'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_2'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_2'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_2'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_2'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_2'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_2'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_2'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_2'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_2'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_2'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_2'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_2'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_2'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_2'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_2'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_2'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_2'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_2'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_2','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
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

if ( $tabValeurs['Val_Main_Complication_Profonds_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_2','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_2'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_2'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_2'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_2'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_2'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_2'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_2'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_2','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_2'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_2'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_2'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_2'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_2'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_2'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_2'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_2'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_2','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_2'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_2'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_2'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_2'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_2'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_2'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_2'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_2'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_2'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_2'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_2'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_2'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_2'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_2'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_2'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_2'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_2'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_2'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_2'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_2'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_2'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_2','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
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
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_2','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_2','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_2','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_2','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_2','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_2','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_2','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_2','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_2','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_2','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_2','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_2','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_2','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_2','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_2','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_2','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_2','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_2','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_2','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_2'] == "Oui" ) {
createItem($formx2,$etape,'s1_3','','TXT','');
createItem($formx2,$etape,'s2_3','','TXT','');
createItem($formx2,$etape,'s3_3','','TXT','');

// LESION 4
createItem($formx2,$etape,'lesion1_3','<span style="color:red;font-weight:bold;">LESION 4</span>','TXT','');
createItem($formx2,$etape,'lesion1s1_3','','TXT','');
createItem($formx2,$etape,'lesion1s2_3','','TXT','');
createItem($formx2,$etape,'lesion1s3_3','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_3'] == "Les deux" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMa_3','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_3'] == "Palmaire" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMb_3','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_3'] == "Transfixiante" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMc_3','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_3','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_3'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_3'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_3'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_3'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_3'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_3'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_3'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_3'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_3'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_3'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_3'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_3'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_3'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_3'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_3'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_3'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_3'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_3'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_3'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_3'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_3','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_3'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_3'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_3'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_3'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_3'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_3'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_3'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_3'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_3'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_3'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_3'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_3'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_3'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_3'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_3'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_3'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_3'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_3'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_3'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_3'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_3'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_3','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
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

if ( $tabValeurs['Val_Main_Complication_Profonds_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_3','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_3'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_3'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_3'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_3'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_3'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_3'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_3'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_3'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_3','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_3'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_3'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_3'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_3'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_3'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_3'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_3'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_3'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_3'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_3','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_3'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_3'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_3'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_3'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_3'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_3'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_3'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_3'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_3'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_3'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_3'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_3'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_3'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_3'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_3'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_3'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_3'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_3'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_3'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_3','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
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
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_3','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_3','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_3','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_3','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_3','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_3','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_3','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_3','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_3','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_3','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_3','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_3','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_3','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_3','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_3','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_3','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_3','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_3','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_3','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_3'] == "Oui" ) {
createItem($formx2,$etape,'s1_4','','TXT','');
createItem($formx2,$etape,'s2_4','','TXT','');
createItem($formx2,$etape,'s3_4','','TXT','');

// LESION 5
createItem($formx2,$etape,'lesion1_4','<span style="color:red;font-weight:bold;">LESION 5</span>','TXT','');
createItem($formx2,$etape,'lesion1s1_4','','TXT','');
createItem($formx2,$etape,'lesion1s2_4','','TXT','');
createItem($formx2,$etape,'lesion1s3_4','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_4'] == "Les deux" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMa_4','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_4'] == "Palmaire" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMb_4','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_4'] == "Transfixiante" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMc_4','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_4','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_4'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_4'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_4'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_4'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_4'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_4'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_4'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_4'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_4'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_4'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_4'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_4'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_4'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_4'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_4'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_4'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_4'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_4'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_4'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_4'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_4','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_4'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_4'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_4'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_4'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_4'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_4'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_4'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_4'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_4'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_4'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_4'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_4'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_4'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_4'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_4'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_4'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_4'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_4'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_4'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_4'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_4'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_4','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
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

if ( $tabValeurs['Val_Main_Complication_Profonds_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_4','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_4'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_4'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_4'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_4'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_4'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_4'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_4'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_4'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_4','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_4'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_4'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_4'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_4'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_4'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_4'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_4'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_4'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_4'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_4','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_4'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_4'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_4'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_4'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_4'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_4'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_4'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_4'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_4'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_4'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_4'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_4'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_4'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_4'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_4'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_4'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_4'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_4'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_4'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_4','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
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
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_4','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_4','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_4','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_4','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_4','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_4','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_4','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_4','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_4','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_4','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_4','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_4','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_4','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_4','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_4','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_4','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_4','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_4','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_4','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_4'] == "Oui" ) {
createItem($formx2,$etape,'s1_5','','TXT','');
createItem($formx2,$etape,'s2_5','','TXT','');
createItem($formx2,$etape,'s3_5','','TXT','');

// LESION 6
createItem($formx2,$etape,'lesion1_5','<span style="color:red;font-weight:bold;">LESION 6</span>','TXT','');
createItem($formx2,$etape,'lesion1s1_5','','TXT','');
createItem($formx2,$etape,'lesion1s2_5','','TXT','');
createItem($formx2,$etape,'lesion1s3_5','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_5'] == "Les deux" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMa_5','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_5'] == "Palmaire" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMb_5','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_5'] == "Transfixiante" ) {
  createItem($formx2,$etape,'Val_Main_Lesion_Face_MessageMc_5','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Capillaires_5','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_5'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_5'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_5'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_5'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_5'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_5'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_5'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_5'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_5'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_5'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_5'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_5'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_5'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_5'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_5'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_5'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_5'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_5'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_5'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_5'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx2,$etape,'Complication_Capillaires_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Coloration_5','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_5'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_5'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_5'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_5'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_5'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_5'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_5'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_5'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_5'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_5'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_5'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_5'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_5'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_5'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_5'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_5'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_5'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_5'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_5'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_5'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx2,$etape,'Complication_Coloration_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_5'] == "Oui" ) {
  createItem($formx2,$etape,'Complication_orthopedique_tonus_5','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
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

if ( $tabValeurs['Val_Main_Complication_Profonds_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Profonds_5','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_5'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_5'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_5'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_5'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_5'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_5'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_5'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx2,$etape,'Complication_Profonds_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Superficiels_5','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_5'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_5'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_5'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_5'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_5'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_5'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_5'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx2,$etape,'Complication_Superficiels_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_5'] != "" ) {
  createItem($formx2,$etape,'Complication_Extenseurs_5','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_5'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_5'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_5'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_5'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_5'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_5'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_5'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_5'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_5'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_5'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_5'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_5'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_5'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_5'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_5'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_5'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_5'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_5'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_5'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_5'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx2,$etape,'Complication_Extenseurs_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_5'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx2,$etape,'Sensibilite_Main_5','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
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
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_5','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_a_5','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_b_5','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_c_5','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_d_5','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_e_5','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_f_5','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_g_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_h_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_i_5','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_j_5','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_k_5','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_l_5','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_m_5','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_n_5','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_o_5','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_p_5','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_q_5','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_r_5','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_s_5','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx2,$etape,'Sensibilite_Hemi_Pulpes_t_5','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


$ids = $formx->getIDS();
$formx3 = new clFoRmX($ids,'NO_POST_THREAT');
$formx3->loadForm('Dossier_Traumatologie_Main.xml');

//ajout d 'une étape
$etape = createEtape($formx3,'1','IDENTITE DU PATIENT',array('etat'=>'fini'));
createItem($formx3,$etape,'id1','<span style="color:green;">Nom</span>','TXT',   $tabValeurs['Val_IDENT_NomPatient']);
createItem($formx3,$etape,'id2','<span style="color:green;">Prénom</span>','TXT',$tabValeurs['Val_IDENT_PrenomPatient']);
//createItem($formx2,$etape,'id3','Sexe','TXT',  $tabValeurs['Val_IDENT_SexePat']);
createItem($formx3,$etape,'id4','<span style="color:green;">Date de naissance</span>','TXT',$tabValeurs['Val_IDENT_DateNPat2']);
createItem($formx3,$etape,'id5','<span style="color:green;">Age</span>','TXT',$tabValeurs['Val_IDENT_AgePat']);
createItem($formx3,$etape,'id6','<span style="color:green;">Adresse</span>','TXT',$tabValeurs['Val_IDENT_AdressePat']);
createItem($formx3,$etape,'id7','<span style="color:green;">Code postal</span>','TXT',$tabValeurs['Val_IDENT_CodePPat']);
createItem($formx3,$etape,'id8','<span style="color:green;">Ville</span>','TXT',$tabValeurs['Val_IDENT_VillePat']);
createItem($formx3,$etape,'id9','<span style="color:green;">Telephone</span>','TXT',$tabValeurs['Val_IDENT_TelPat']);
createItem($formx3,$etape,'id10','<span style="color:green;">Profession</span>','TXT',$tabValeurs['Val_Main_Profession']);
createItem($formx3,$etape,'id11','<span style="color:green;">Statut profession</span>','TXT',$tabValeurs['Val_Main_StatutProfession']);
createItem($formx3,$etape,'id12','<span style="color:green;">IDU</span>','TXT',$tabValeurs['Val_IDENT_IDUPatient']);
createItem($formx3,$etape,'id13','<span style="color:green;">ILP</span>','TXT',$tabValeurs['Val_IDENT_ILPPatient']);
createItem($formx3,$etape,'id14','<span style="color:green;">Jour de consultation du patient</span>','TXT',$tabValeurs['Val_Jour_Consultation']);
createItem($formx3,$etape,'id15','<span style="color:green;">Heure de consultation du patient</span>','TXT',$tabValeurs['Val_Heure_Consultation']);

createItem($formx3,$etape,'s1','','TXT','');
createItem($formx3,$etape,'s2','','TXT','');
createItem($formx3,$etape,'s3','','TXT','');

createItem($formx3,$etape,'id16','<span style="font-weight:bold;">URGENTISTE</span>','TXT','');
createItem($formx3,$etape,'id17','<span style="color:green;">Consultation du patient par le Docteur</span>','TXT',$tabValeurs['Val_Docteur_Consultation']);
createItem($formx3,$etape,'id18','<span style="color:green;">Etablissement</span>','TXT',$tabValeurs['Val_Etablissement_Etablissement']);

createItem($formx3,$etape,'s4','','TXT','');
createItem($formx3,$etape,'s5','','TXT','');
createItem($formx3,$etape,'s6','','TXT','');

createItem($formx3,$etape,'id19','<span style="font-weight:bold;">HORAIRE</span>','TXT','');
createItem($formx3,$etape,'id20','<span style="color:green;">Date de l\'accident</span>','TXT',$tabValeurs['Val_Horaire_Date_Accident']);
createItem($formx3,$etape,'id21','<span style="color:green;">Heure de l\'accident</span>','TXT',$tabValeurs['Val_Horaire_Heure_Accident']);
createItem($formx3,$etape,'id22','<span style="color:green;">Date de l\'examen</span>','TXT',$tabValeurs['Val_Horaire_Date_Examen']);
createItem($formx3,$etape,'id23','<span style="color:green;">Heure de l\'examen</span>','TXT',$tabValeurs['Val_Horaire_Heure_Examen']);
createItem($formx3,$etape,'id24','<span style="color:green;">Delai</span>','TXT',$tabValeurs['Val_Horaire_Delai']);

createItem($formx3,$etape,'s7','','TXT','');
createItem($formx3,$etape,'s8','','TXT','');
createItem($formx3,$etape,'s9','','TXT','');

createItem($formx3,$etape,'id25','<span style="font-weight:bold;">ANTECEDENTS</span>','TXT','');
createItem($formx3,$etape,'id26','<span style="color:green;">Main Dominante</span>','TXT',$tabValeurs['Val_Main_Main_Dominante']);
if ( $tabValeurs['Val_Main_Presence_Bague'] == "Oui") {
  createItem($formx3,$etape,'id27','<span style="color:green;">Présence de bague(s)</span>','TXT',$tabValeurs['Val_Main_Presence_Bague']);
	createItem($formx3,$etape,'id28','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Presence_Bague_C']);
	}
if ( $tabValeurs['Val_Main_Anticoagulants'] == "Oui") {
  createItem($formx3,$etape,'id29','<span style="color:green;">Anticoagulants</span>','TXT',$tabValeurs['Val_Main_Anticoagulants']);
	}
if ( $tabValeurs['Val_Main_Anticoagulants'] == "Oui" && $tabValeurs['Val_Main_Anticoagulants_C'] != "" ) {
	createItem($formx3,$etape,'id30','Detail(s)','TXT',$tabValeurs['Val_Main_Anticoagulants_C']);
	}
if ( $tabValeurs['Val_Main_Troubles_Vasculaires'] == "Oui") {
  createItem($formx3,$etape,'id31','<span style="color:green;">Troubles vasculaires</span>','TXT',$tabValeurs['Val_Main_Troubles_Vasculaires']);
	}
if ( $tabValeurs['Val_Main_Troubles_Vasculaires'] == "Oui" && $tabValeurs['Val_Main_Troubles_Vasculaires_C'] != "" ) {
	createItem($formx3,$etape,'id32','Detail(s)','TXT',$tabValeurs['Val_Main_Troubles_Vasculaires_C']);
	}
if ( $tabValeurs['Val_Main_Tabagisme'] == "Oui") {
  createItem($formx3,$etape,'id33','<span style="color:green;">Tabagisme</span>','TXT',$tabValeurs['Val_Main_Tabagisme']);
	}
if ( $tabValeurs['Val_Main_Tabagisme'] == "Oui") {
  createItem($formx3,$etape,'id34','Nombre de paquet année','TXT',$tabValeurs['Val_Main_Tabagisme_Paquet']);
	}
if ( $tabValeurs['Val_Main_Diabete'] == "Oui") {
  createItem($formx3,$etape,'id35','<span style="color:green;">Diabète</span>','TXT',$tabValeurs['Val_Main_Diabete']);
	}
if ( $tabValeurs['Val_Main_Diabete'] == "Oui" && $tabValeurs['Val_Main_Diabete_C'] != "" ) {
	createItem($formx3,$etape,'id36','Traitement(s) ?','TXT',$tabValeurs['Val_Main_Diabete_C']);
	}
if ( $tabValeurs['Val_Main_Allergies'] == "Oui") {
  createItem($formx3,$etape,'id37','<span style="color:green;">Allergies</span>','TXT',$tabValeurs['Val_Main_Allergies']);
	}
if ( $tabValeurs['Val_Main_Allergies'] == "Oui" && $tabValeurs['Val_Main_Allergies_C'] != "" ) {
	createItem($formx3,$etape,'id38','Detail(s)','TXT',$tabValeurs['Val_Main_Allergies_C']);
	}
if ( $tabValeurs['Val_Main_Vaccin_Anti'] == "Non") {
  createItem($formx3,$etape,'id39','<span style="color:green;">Vaccin antitétanique à jour</span>','TXT',$tabValeurs['Val_Main_Vaccin_Anti']);
	}
if ( $tabValeurs['Val_Main_Vaccin_Anti'] == "Non") {
  createItem($formx3,$etape,'id40','Date','TXT',$tabValeurs['Val_Main_Vaccin_Anti_Date']);
	}
	
createItem($formx3,$etape,'s10','','TXT','');
createItem($formx3,$etape,'s11','','TXT','');
createItem($formx3,$etape,'s12','','TXT','');

createItem($formx3,$etape,'id41','<span style="font-weight:bold;">CONTEXTE DE SURVENUE</span>','TXT','');

if ( $tabValeurs['Val_Main_Accident_Trav'] == "Oui") {
  createItem($formx3,$etape,'id42','<span style="color:green;">Accident du travail</span>','TXT',$tabValeurs['Val_Main_Accident_Trav']);
	}
if ( $tabValeurs['Val_Main_Lesions'] == "Oui") {
  createItem($formx3,$etape,'id43','<span style="color:green;">Autre(s) urgence(s) associée(s)</span>','TXT',$tabValeurs['Val_Main_Lesions']);
	}
if ( $tabValeurs['Val_Main_Lesions'] == "Oui" && $tabValeurs['Val_Main_Lesions_C'] != "" ) {
  createItem($formx3,$etape,'id44','Lesquelles ?','TXT',$tabValeurs['Val_Main_Lesions_C']);
	}
createItem($formx3,$etape,'id45','<span style="color:green;">Contexte accident</span>','TXT',$tabValeurs['Val_Main_Contexte_Accident']);
if ( $tabValeurs['Val_Main_Contexte_Accident_Sport'] == "Oui" ) {
  createItem($formx3,$etape,'id46','<span style="color:green;">Accident de sport</span>','TXT',$tabValeurs['Val_Main_Contexte_Accident_Sport']);
	}
if ( $tabValeurs['Val_Main_Contexte_Accident_Sport'] == "Oui" && $tabValeurs['Val_Main_Contexte_Accident_Sport_C'] != "" ) {
  createItem($formx3,$etape,'id47','Detail(s)','TXT',$tabValeurs['Val_Main_Contexte_Accident_Sport_C']);
	}
createItem($formx3,$etape,'id48','<span style="color:green;">Premiers gestes avant la prise en charge</span>','TXT',$tabValeurs['Val_Main_Premier_Geste']);
if ( $tabValeurs['Val_Main_Premier_Geste_Autre'] == "Oui") {
  createItem($formx3,$etape,'id49','<span style="color:green;">Premiers gestes avant la prise en charge autre</span>','TXT',$tabValeurs['Val_Main_Premier_Geste_Autre']);
	}
if ( $tabValeurs['Val_Main_Premier_Geste_Autre'] == "Oui" && $tabValeurs['Val_Main_Premier_Geste_Autre_C'] != "" ) {
  createItem($formx3,$etape,'id50','Detail(s)','TXT',$tabValeurs['Val_Main_Premier_Geste_Autre_C']);
	}

createItem($formx3,$etape,'s13','','TXT','');
createItem($formx3,$etape,'s14','','TXT','');
createItem($formx3,$etape,'s15','','TXT','');

createItem($formx3,$etape,'id51','<span style="font-weight:bold;">MECANISME DU TRAUMATISME</span>','TXT','');

if ( $tabValeurs['Val_Main_Mecanisme_Coupure'] == "Oui") {
  createItem($formx3,$etape,'id52','<span style="color:green;">Coupure</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Coupure']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Coupure'] == "Déchiquetée") {
  createItem($formx3,$etape,'id53','Coupure par','TXT',$tabValeurs['Val_Main_Mecanisme_Coupure_G']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Coupure'] == "Déchiquetée" && $tabValeurs['Val_Main_Mecanisme_Coupure_G'] == "Verre" ) {
  createItem($formx3,$etape,'id54','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Coupure_Message']);
	}
createItem($formx3,$etape,'id55','<span style="color:green;">Facteur(s) aggravant(s)</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Facteur']);
if ( $tabValeurs['Val_Main_Mecanisme_FacteurC'] != "" && (eregi("Ecrasement",$tabValeurs['Val_Main_Mecanisme_Facteur']) || eregi("Arrachement",$tabValeurs['Val_Main_Mecanisme_Facteur'])) ) {
  createItem($formx3,$etape,'id56','Description(s)','TXT',$tabValeurs['Val_Main_Mecanisme_FacteurC']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Injection'] == "Oui") {
  createItem($formx3,$etape,'id57','<span style="color:green;">Injection sous pression</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Injection']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Injection'] == "Oui") {
  createItem($formx3,$etape,'id58','Injection à','TXT',$tabValeurs['Val_Main_Mecanisme_Injection_G']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Injection'] == "Oui") {
  createItem($formx3,$etape,'id59','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Injection_Message']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Morsure'] == "Oui") {
  createItem($formx3,$etape,'id60','<span style="color:green;">Morsure</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Morsure']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Morsure'] == "Oui") {
  createItem($formx3,$etape,'id61','Type de morsure','TXT',$tabValeurs['Val_Main_Mecanisme_Morsure_G']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Morsure'] == "Oui") {
  createItem($formx3,$etape,'id62','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Morsure_Message']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Traction'] == "Oui") {
  createItem($formx3,$etape,'id63','<span style="color:green;">Traction sur une bague</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Traction']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Traction'] == "Oui") {
  createItem($formx3,$etape,'id64','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Traction_Message']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Autre'] == "Oui") {
  createItem($formx3,$etape,'id65','<span style="color:green;">Autre</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Autre']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Autre'] == "Oui" && $tabValeurs['Val_Main_Mecanisme_Autre_G'] != "" ) {
  createItem($formx3,$etape,'id66','Detail(s)','TXT',$tabValeurs['Val_Main_Mecanisme_Autre_G']);
	}
createItem($formx3,$etape,'id67','<span style="color:green;">Recherche de signe de gravité immédiat</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Grante']);
if ( $tabValeurs['Val_Main_Mecanisme_Grante'] == "Main multilesée avec évidence chirurgicale" ) {
  createItem($formx3,$etape,'id68','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Grante_Main']);
	}
if ( $tabValeurs['Val_Main_Mecanisme_Grante'] == "Amputation compléte ou partielle avec dévascularisation" ) {
  createItem($formx3,$etape,'id69','<span style="color:red;">Message</span>','TXT',$tabValeurs['Val_Main_Mecanisme_Grante_Amputation']);
	}	

createItem($formx3,$etape,'s16','','TXT','');
createItem($formx3,$etape,'s17','','TXT','');
createItem($formx3,$etape,'s18','','TXT','');

createItem($formx3,$etape,'id70','<span style="font-weight:bold;">EXAMEN CLINIQUE AVANT TOUTE ANESTHESIE - DESCRIPTION DE(S) PLAIE(S)</span>','TXT','');


createItem($formx3,$etape,'d1','','TXT','');
createItem($formx3,$etape,'d2','','TXT','');
createItem($formx3,$etape,'d3','','TXT','');

if ( $tabValeurs['Val_Main_Presence_Bague'] == "Oui" ) {
  createItem($formx3,$etape,'Val_Main_Conclusion_Presence_Bague','<span style="color:green;">Présence de bague(s)</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Coupure'] == "Déchiquetée" 
     && $tabValeurs['Val_Main_Mecanisme_Coupure_G'] == "Verre" ) {
  createItem($formx3,$etape,'Val_Main_Conlusion_Coupure','<span style="color:green;">Coupure par Verre</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Injection'] == "Oui" ) {
  createItem($formx3,$etape,'Val_Main_Conlusion_Injection','<span style="color:green;">Injection sous pression</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Traction'] == "Oui" ) {
  createItem($formx3,$etape,'Val_Main_Conclusion_Traction','<span style="color:green;">Traction sur une bague</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Grante'] == "Main multilesée avec évidence chirurgicale" ) {
  createItem($formx3,$etape,'Val_Main_Conclusion_Grante_Main','<span style="color:green;">Recherche de signe de gravité immédiat</span>','TXT','Main multilesée avec évidence chirurgicale');
  }

if ( $tabValeurs['Val_Main_Mecanisme_Grante'] == "Amputation compléte ou partielle avec dévascularisation" ) {
  createItem($formx3,$etape,'Val_Main_Conclusion_Grante_Amputation','<span style="color:green;">Recherche de signe de gravité immédiat</span>','TXT','Amputation compléte ou partielle avec dévascularisation');
  }
  
createItem($formx3,$etape,'s1','','TXT','');
createItem($formx3,$etape,'s2','','TXT','');
createItem($formx3,$etape,'s2','','TXT','');

// LESION 1
createItem($formx3,$etape,'lesion1','<span style="color:red;font-weight:bold;">LESION 1</span>','TXT','');
createItem($formx3,$etape,'lesion1s1','','TXT','');
createItem($formx3,$etape,'lesion1s2','','TXT','');
createItem($formx3,$etape,'lesion1s3','','TXT','');

if ( $tabValeurs['Val_Main_Lesion_Face'] == "Les deux" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMa','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face'] == "Palmaire" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMb','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face'] == "Transfixiante" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMc','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires'] != "" ) {
  createItem($formx3,$etape,'Complication_Capillaires','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration'] != "" ) {
  createItem($formx3,$etape,'Complication_Coloration','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus'] == "Oui" ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ca','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cb','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cc','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cd','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ce','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds'] != "" ) {
  createItem($formx3,$etape,'Complication_Profonds','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels'] != "" ) {
  createItem($formx3,$etape,'Complication_Superficiels','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs'] != "" ) {
  createItem($formx3,$etape,'Complication_Extenseurs','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx3,$etape,'Sensibilite_Main','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx3,$etape,'Nerf_Ulnaire','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx3,$etape,'Nerf_Radial','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet'] == "1") {
  createItem($formx3,$etape,'Nerf_Median','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi'] != "" ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_a','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_b','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_c','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_d','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_e','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_f','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_g','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_h','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_i','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_j','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_k','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_l','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_m','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_n','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_o','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_p','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_q','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_r','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_s','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_t','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( $tabValeurs['lesionsup'] == "Oui" ) {
createItem($formx3,$etape,'s1_1','','TXT','');
createItem($formx3,$etape,'s2_1','','TXT','');
createItem($formx3,$etape,'s3_1','','TXT','');

// LESION 2
createItem($formx3,$etape,'lesion1_1','<span style="color:red;font-weight:bold;">LESION 2</span>','TXT','');
createItem($formx3,$etape,'lesion1s1_1','','TXT','');
createItem($formx3,$etape,'lesion1s2_1','','TXT','');
createItem($formx3,$etape,'lesion1s3_1','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_1'] == "Les deux" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMa_1','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_1'] == "Palmaire" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMb_1','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_1'] == "Transfixiante" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMc_1','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_1'] != "" ) {
  createItem($formx3,$etape,'Complication_Capillaires_1','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_1'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_1'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_1'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_1'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_1'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_1'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_1'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_1'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_1'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_1'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_1'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_1'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_1'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_1'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_1'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_1'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_1'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_1'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_1'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_1'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_1'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_1'] != "" ) {
  createItem($formx3,$etape,'Complication_Coloration_1','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_1'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_1'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_1'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_1'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_1'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_1'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_1'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_1'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_1'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_1'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_1'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_1'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_1'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_1'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_1'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_1'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_1'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_1'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_1'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_1'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_1'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_1'] == "Oui" ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_1','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ca_1','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cb_1','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cc_1','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cd_1','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_1'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ce_1','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_1'] != "" ) {
  createItem($formx3,$etape,'Complication_Profonds_1','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_1'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_1'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_1'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_1'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_1'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_1'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_1'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_1'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_1'] != "" ) {
  createItem($formx3,$etape,'Complication_Superficiels_1','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_1'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_1'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_1'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_1'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_1'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_1'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_1'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_1'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_1'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_1'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_1'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_1'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_1'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_1'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_1'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_1'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_1'] != "" ) {
  createItem($formx3,$etape,'Complication_Extenseurs_1','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_1'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_1'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_1','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_1'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_1'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_1','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_1'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_1'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_1','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_1'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_1'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_1'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_1'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_1','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_1'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_1'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_1','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_1'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_1'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_1','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_1'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_1'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_1','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_1'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_1'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_1','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_1'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_1'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_1'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_1','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_1'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx3,$etape,'Sensibilite_Main_1','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_1']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx3,$etape,'Nerf_Ulnaire_1','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_1']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx3,$etape,'Nerf_Radial_1','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_1']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_1'] == "1") {
  createItem($formx3,$etape,'Nerf_Median_1','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_1'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_1'] != "" ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_1','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_a_1','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_b_1','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_c_1','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_d_1','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_e_1','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_f_1','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_g_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_h_1','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_i_1','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_j_1','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_k_1','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_l_1','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_m_1','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_n_1','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_o_1','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_p_1','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_q_1','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_r_1','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_s_1','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_1']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_t_1','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_1'] == "Oui" ) {
createItem($formx3,$etape,'s1_2','','TXT','');
createItem($formx3,$etape,'s2_2','','TXT','');
createItem($formx3,$etape,'s3_2','','TXT','');

// LESION 3
createItem($formx3,$etape,'lesion1_2','<span style="color:red;font-weight:bold;">LESION 3</span>','TXT','');
createItem($formx3,$etape,'lesion1s1_2','','TXT','');
createItem($formx3,$etape,'lesion1s2_2','','TXT','');
createItem($formx3,$etape,'lesion1s3_2','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_2'] == "Les deux" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMa_2','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_2'] == "Palmaire" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMb_2','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_2'] == "Transfixiante" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMc_2','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_2'] != "" ) {
  createItem($formx3,$etape,'Complication_Capillaires_2','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_2'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_2'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_2'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_2'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_2'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_2'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_2'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_2'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_2'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_2'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_2'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_2'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_2'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_2'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_2'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_2'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_2'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_2'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_2'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_2'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_2'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_2'] != "" ) {
  createItem($formx3,$etape,'Complication_Coloration_2','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_2'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_2'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_2'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_2'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_2'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_2'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_2'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_2'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_2'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_2'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_2'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_2'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_2'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_2'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_2'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_2'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_2'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_2'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_2'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_2'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_2'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_2'] == "Oui" ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_2','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ca_2','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cb_2','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cc_2','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cd_2','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_2'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ce_2','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_2'] != "" ) {
  createItem($formx3,$etape,'Complication_Profonds_2','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_2'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_2'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_2'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_2'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_2'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_2'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_2'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_2'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_2'] != "" ) {
  createItem($formx3,$etape,'Complication_Superficiels_2','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_2'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_2'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_2'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_2'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_2'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_2'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_2'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_2'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_2'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_2'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_2'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_2'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_2'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_2'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_2'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_2'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_2'] != "" ) {
  createItem($formx3,$etape,'Complication_Extenseurs_2','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_2'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_2'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_2','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_2'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_2'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_2','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_2'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_2'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_2','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_2'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_2'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_2'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_2'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_2','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_2'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_2'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_2','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_2'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_2'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_2','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_2'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_2'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_2','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_2'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_2'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_2','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_2'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_2'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_2'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_2','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_2'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx3,$etape,'Sensibilite_Main_2','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_2']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx3,$etape,'Nerf_Ulnaire_2','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_2']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx3,$etape,'Nerf_Radial_2','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_2']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_2'] == "1") {
  createItem($formx3,$etape,'Nerf_Median_2','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_2'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_2'] != "" ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_2','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_a_2','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_b_2','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_c_2','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_d_2','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_e_2','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_f_2','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_g_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_h_2','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_i_2','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_j_2','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_k_2','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_l_2','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_m_2','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_n_2','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_o_2','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_p_2','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_q_2','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_r_2','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_s_2','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_2']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_t_2','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_2'] == "Oui" ) {
createItem($formx3,$etape,'s1_3','','TXT','');
createItem($formx3,$etape,'s2_3','','TXT','');
createItem($formx3,$etape,'s3_3','','TXT','');

// LESION 4
createItem($formx3,$etape,'lesion1_3','<span style="color:red;font-weight:bold;">LESION 4</span>','TXT','');
createItem($formx3,$etape,'lesion1s1_3','','TXT','');
createItem($formx3,$etape,'lesion1s2_3','','TXT','');
createItem($formx3,$etape,'lesion1s3_3','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_3'] == "Les deux" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMa_3','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_3'] == "Palmaire" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMb_3','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_3'] == "Transfixiante" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMc_3','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_3'] != "" ) {
  createItem($formx3,$etape,'Complication_Capillaires_3','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_3'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_3'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_3'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_3'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_3'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_3'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_3'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_3'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_3'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_3'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_3'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_3'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_3'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_3'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_3'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_3'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_3'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_3'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_3'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_3'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_3'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_3'] != "" ) {
  createItem($formx3,$etape,'Complication_Coloration_3','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_3'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_3'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_3'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_3'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_3'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_3'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_3'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_3'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_3'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_3'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_3'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_3'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_3'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_3'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_3'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_3'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_3'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_3'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_3'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_3'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_3'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_3'] == "Oui" ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_3','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ca_3','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cb_3','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cc_3','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cd_3','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_3'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ce_3','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_3'] != "" ) {
  createItem($formx3,$etape,'Complication_Profonds_3','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_3'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_3'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_3'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_3'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_3'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_3'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_3'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_3'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_3'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_3'] != "" ) {
  createItem($formx3,$etape,'Complication_Superficiels_3','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_3'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_3'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_3'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_3'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_3'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_3'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_3'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_3'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_3'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_3'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_3'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_3'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_3'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_3'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_3'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_3'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_3'] != "" ) {
  createItem($formx3,$etape,'Complication_Extenseurs_3','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_3'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_3'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_3','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_3'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_3'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_3','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_3'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_3','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_3'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_3'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_3'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_3'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_3','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_3'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_3'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_3','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_3'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_3'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_3','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_3'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_3'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_3','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_3'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_3'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_3','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_3'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_3'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_3'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_3','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_3'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx3,$etape,'Sensibilite_Main_3','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_3']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx3,$etape,'Nerf_Ulnaire_3','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_3']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx3,$etape,'Nerf_Radial_3','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_3']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_3'] == "1") {
  createItem($formx3,$etape,'Nerf_Median_3','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_3'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_3'] != "" ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_3','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_a_3','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_b_3','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_c_3','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_d_3','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_e_3','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_f_3','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_g_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_h_3','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_i_3','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_j_3','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_k_3','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_l_3','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_m_3','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_n_3','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_o_3','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_p_3','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_q_3','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_r_3','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_s_3','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_3']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_t_3','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_3'] == "Oui" ) {
createItem($formx3,$etape,'s1_4','','TXT','');
createItem($formx3,$etape,'s2_4','','TXT','');
createItem($formx3,$etape,'s3_4','','TXT','');

// LESION 5
createItem($formx3,$etape,'lesion1_4','<span style="color:red;font-weight:bold;">LESION 5</span>','TXT','');
createItem($formx3,$etape,'lesion1s1_4','','TXT','');
createItem($formx3,$etape,'lesion1s2_4','','TXT','');
createItem($formx3,$etape,'lesion1s3_4','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_4'] == "Les deux" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMa_4','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_4'] == "Palmaire" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMb_4','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_4'] == "Transfixiante" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMc_4','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_4'] != "" ) {
  createItem($formx3,$etape,'Complication_Capillaires_4','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_4'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_4'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_4'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_4'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_4'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_4'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_4'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_4'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_4'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_4'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_4'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_4'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_4'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_4'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_4'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_4'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_4'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_4'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_4'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_4'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_4'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_4'] != "" ) {
  createItem($formx3,$etape,'Complication_Coloration_4','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_4'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_4'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_4'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_4'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_4'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_4'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_4'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_4'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_4'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_4'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_4'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_4'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_4'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_4'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_4'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_4'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_4'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_4'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_4'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_4'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_4'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_4'] == "Oui" ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_4','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ca_4','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cb_4','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cc_4','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cd_4','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_4'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ce_4','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_4'] != "" ) {
  createItem($formx3,$etape,'Complication_Profonds_4','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_4'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_4'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_4'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_4'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_4'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_4'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_4'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_4'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_4'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_4'] != "" ) {
  createItem($formx3,$etape,'Complication_Superficiels_4','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_4'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_4'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_4'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_4'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_4'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_4'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_4'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_4'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_4'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_4'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_4'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_4'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_4'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_4'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_4'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_4'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_4'] != "" ) {
  createItem($formx3,$etape,'Complication_Extenseurs_4','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_4'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_4'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_4','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_4'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_4'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_4','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_4'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_4','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_4'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_4'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_4'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_4'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_4','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_4'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_4'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_4','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_4'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_4'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_4','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_4'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_4'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_4','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_4'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_4'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_4','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_4'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_4'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_4'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_4','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_4'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx3,$etape,'Sensibilite_Main_4','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_4']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx3,$etape,'Nerf_Ulnaire_4','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_4']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx3,$etape,'Nerf_Radial_4','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_4']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_4'] == "1") {
  createItem($formx3,$etape,'Nerf_Median_4','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_4'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_4'] != "" ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_4','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_a_4','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_b_4','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_c_4','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_d_4','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_e_4','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_f_4','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_g_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_h_4','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_i_4','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_j_4','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_k_4','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_l_4','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_m_4','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_n_4','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_o_4','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_p_4','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_q_4','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_r_4','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_s_4','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_4']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_t_4','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }


if ( $tabValeurs['lesionsup_4'] == "Oui" ) {
createItem($formx3,$etape,'s1_5','','TXT','');
createItem($formx3,$etape,'s2_5','','TXT','');
createItem($formx3,$etape,'s3_5','','TXT','');

// LESION 6
createItem($formx3,$etape,'lesion1_5','<span style="color:red;font-weight:bold;">LESION 6</span>','TXT','');
createItem($formx3,$etape,'lesion1s1_5','','TXT','');
createItem($formx3,$etape,'lesion1s2_5','','TXT','');
createItem($formx3,$etape,'lesion1s3_5','','TXT','');

}

if ( $tabValeurs['Val_Main_Lesion_Face_5'] == "Les deux" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMa_5','<span style="color:green;">Lésion zone paume sur la face Palmaire et Dorsale</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_5'] == "Palmaire" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMb_5','<span style="color:green;">Lésion zone paume sur la face Palmaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Lesion_Face_5'] == "Transfixiante" ) {
  createItem($formx3,$etape,'Val_Main_Lesion_Face_MessageMc_5','<span style="color:green;">Lésion zone paume sur la face Transfixiante</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Capillaires_5'] != "" ) {
  createItem($formx3,$etape,'Complication_Capillaires_5','<span style="font-weight:bold;">Anomalie des pouls capillaires ou du remplissage pulpaire</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_5'] == "Ralenti" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix1_5'] == "Absent" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_5'] == "Ralenti" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix2_5'] == "Absent" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_5'] == "Ralenti" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix3_5'] == "Absent" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_5'] == "Ralenti" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix4_5'] == "Absent" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_5'] == "Ralenti" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix5_5'] == "Absent" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_5'] == "Ralenti" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix6_5'] == "Absent" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_5'] == "Ralenti" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix7_5'] == "Absent" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_5'] == "Ralenti" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix8_5'] == "Absent" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_5'] == "Ralenti" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix9_5'] == "Absent" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_5'] == "Ralenti" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Ralenti');
  }

if ( $tabValeurs['Val_Main_Complication_Capillaires_choix10_5'] == "Absent" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Capillaires_5'] ) ) {
  createItem($formx3,$etape,'Complication_Capillaires_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Absent');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Coloration_5'] != "" ) {
  createItem($formx3,$etape,'Complication_Coloration_5','<span style="font-weight:bold;">Anomalie de la coloration ou de la température cutanée</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_5'] == "Blanc" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix1_5'] == "Violet" &&
     eregi( "V doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_5'] == "Blanc" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix2_5'] == "Violet" &&
     eregi( "V doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_5'] == "Blanc" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix3_5'] == "Violet" &&
     eregi( "IV doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_5'] == "Blanc" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix4_5'] == "Violet" &&
     eregi( "IV doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_5'] == "Blanc" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix5_5'] == "Violet" &&
     eregi( "III doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_5'] == "Blanc" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix6_5'] == "Violet" &&
     eregi( "III doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_5'] == "Blanc" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix7_5'] == "Violet" &&
     eregi( "II doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_5'] == "Blanc" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix8_5'] == "Violet" &&
     eregi( "II doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_5'] == "Blanc" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix9_5'] == "Violet" &&
     eregi( "I doigt Droit",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Couleur violet');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_5'] == "Blanc" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur blanc');
  }

if ( $tabValeurs['Val_Main_Complication_Coloration_choix10_5'] == "Violet" &&
     eregi( "I doigt Gauche",$tabValeurs['Val_Main_Complication_Coloration_5'] ) ) {
  createItem($formx3,$etape,'Complication_Coloration_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Couleur violet');
  }
  
if ( $tabValeurs['Val_Main_Complication_orthopedique_tonus_5'] == "Oui" ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_5','<span style="font-weight:bold;">Anomalie du tonus postual</span>','TXT','');
  }

if ( eregi( "1° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ca_5','','TXT','1° Doigt');
  }

if ( eregi( "2° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cb_5','','TXT','2° Doigt');
  }

if ( eregi( "3° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cc_5','','TXT','3° Doigt');
  }

if ( eregi( "4° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_cd_5','','TXT','4° Doigt');
  }

if ( eregi( "5° Doigt",$tabValeurs['Val_Main_Complication_orthopedique_tonus_c_5'] ) ) {
  createItem($formx3,$etape,'Complication_orthopedique_tonus_ce_5','','TXT','5° Doigt');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Profonds_5'] != "" ) {
  createItem($formx3,$etape,'Complication_Profonds_5','<span style="font-weight:bold;">Flechisseurs communs profonds</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix1_5'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix2_5'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix3_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix4_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix5_5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix6_5'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix7_5'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix8_5'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix9_5'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Profonds_choix10_5'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Profonds_5'] ) ) {
  createItem($formx3,$etape,'Complication_Profonds_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Superficiels_5'] != "" ) {
  createItem($formx3,$etape,'Complication_Superficiels_5','<span style="font-weight:bold;">Flechisseurs communs superficiels</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix1_5'] == "Impossible" &&
     eregi( "Flexion V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_5'] == "Douloureuse" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix2_5'] == "Impossible" &&
     eregi( "Flexion V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix3_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_5'] == "Douloureuse" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix4_5'] == "Impossible" &&
     eregi( "Flexion IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix5_5'] == "Impossible" &&
     eregi( "Flexion III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_5'] == "Douloureuse" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix6_5'] == "Impossible" &&
     eregi( "Flexion III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix7_5'] == "Impossible" &&
     eregi( "Flexion II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_5'] == "Douloureuse" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix8_5'] == "Impossible" &&
     eregi( "Flexion II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix9_5'] == "Impossible" &&
     eregi( "Flexion I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_5'] == "Douloureuse" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Superficiels_choix10_5'] == "Impossible" &&
     eregi( "Flexion I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Superficiels_5'] ) ) {
  createItem($formx3,$etape,'Complication_Superficiels_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Flexion anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_5'] != "Pas d'anomalie" 
     && $tabValeurs['Val_Main_Complication_Extenseurs_5'] != "" ) {
  createItem($formx3,$etape,'Complication_Extenseurs_5','<span style="font-weight:bold;">Extenseurs</span>','TXT','');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_5'] == "Douloureuse" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix1_5'] == "Impossible" &&
     eregi( "Extension V doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix1_5','<span style="color:green;">V doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_5'] == "Douloureuse" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix2_5'] == "Impossible" &&
     eregi( "Extension V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix2_5','<span style="color:green;">V doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_5'] == "Douloureuse" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix3_5'] == "Impossible" &&
     eregi( "Extension IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix3_5','<span style="color:green;">IV doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_5'] == "Douloureuse" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix4_5'] == "Impossible" &&
     eregi( "Extension IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix4_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_5'] == "Douloureuse" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix5_5'] == "Impossible" &&
     eregi( "Extension III doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix5_5','<span style="color:green;">III doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_5'] == "Douloureuse" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix6_5'] == "Impossible" &&
     eregi( "Extension III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix6_5','<span style="color:green;">III doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_5'] == "Douloureuse" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix7_5'] == "Impossible" &&
     eregi( "Extension II doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix7_5','<span style="color:green;">II doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_5'] == "Douloureuse" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix8_5'] == "Impossible" &&
     eregi( "Extension II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix8_5','<span style="color:green;">II doigt Gauche</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_5'] == "Douloureuse" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix9_5'] == "Impossible" &&
     eregi( "Extension I doigt Droit anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix9_5','<span style="color:green;">I doigt Droit</span>','TXT','Extension anormale Impossible');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_5'] == "Douloureuse" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Douloureuse');
  }

if ( $tabValeurs['Val_Main_Complication_Extenseurs_choix10_5'] == "Impossible" &&
     eregi( "Extension I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_Extenseurs_5'] ) ) {
  createItem($formx3,$etape,'Complication_Extenseurs_choix10_5','<span style="color:green;">I doigt Gauche</span>','TXT','Extension anormale Impossible');
  }
  
if ( $tabValeurs['Val_Main_Complication_SensibiliteMain_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteMain_5'] != "" &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx3,$etape,'Sensibilite_Main_5','<span style="font-weight:bold;">Sensibilité de la main (lésion au niveau du poignet). Sensibilité anormale sur</span>','TXT','');
  }

if ( eregi ("Sensibilité territoire du nerf ulnaire anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_5']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx3,$etape,'Nerf_Ulnaire_5','','TXT','Territoire du nerf ulnaire');
  }

if ( eregi ("Sensibilité territoire du nerf radial anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_5']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx3,$etape,'Nerf_Radial_5','','TXT','Territoire du nerf radial');
  }

if ( eregi ("Sensibilité territoire du nerf median anormale",$tabValeurs['Val_Main_Complication_SensibiliteMain_5']) &&
     $tabValeurs['Val_Main_Lesion_Zone_Presence_Poignet_5'] == "1") {
  createItem($formx3,$etape,'Nerf_Median_5','','TXT','Territoire du nerf median');
  }

if ( $tabValeurs['Val_Main_Complication_SensibiliteHemi_5'] != "Pas d'anomalie" &&
     $tabValeurs['Val_Main_Complication_SensibiliteHemi_5'] != "" ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_5','<span style="font-weight:bold;">Sensibilité des hémi-pulpes des doigts</span>','TXT','');
  }

if ( eregi ("Pulpe radiale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_a_5','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_b_5','<span style="color:green;">V doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_c_5','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale V doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_d_5','<span style="color:green;">V doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_e_5','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_f_5','<span style="color:green;">IV doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_g_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale IV doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_h_5','<span style="color:green;">IV doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_i_5','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_j_5','<span style="color:green;">III doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_k_5','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale III doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_l_5','<span style="color:green;">III doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_m_5','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_n_5','<span style="color:green;">II doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_o_5','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale II doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_p_5','<span style="color:green;">II doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_q_5','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Droit anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_r_5','<span style="color:green;">I doigt Droit</span>','TXT','Pulpe cubitale anormale');
  }

if ( eregi ("Pulpe radiale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_s_5','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe radiale anormale');
  }

if ( eregi ("Pulpe cubitale I doigt Gauche anormale",$tabValeurs['Val_Main_Complication_SensibiliteHemi_5']) ) {
  createItem($formx3,$etape,'Sensibilite_Hemi_Pulpes_t_5','<span style="color:green;">I doigt Gauche</span>','TXT','Pulpe cubitale anormale');
  }

	



//enregistrement initial du formulaire formx
$formx3->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx3->makeBalVal($formx3->XMLDOM->documentElement,"STATUS",'F');
$formx3->close();


}
?>

