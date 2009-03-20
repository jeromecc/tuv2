<?php
function Creation_Dossier_AEV_Synthese($formx) {

global $session;
global $options;
global $patient;


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

// On va supprimer tous les fichiers Dossier_AEV_Synthese de la table formx
$requete=new clRequete(FX_BDD,TABLEFORMX,$param);
$sql=$requete->delRecord("idformx='Dossier_AEV_Synthese' and ids='".$ids."'");

$formx2 = new clFoRmX($ids,'NO_POST_THREAT');
$formx2->loadForm('Dossier_AEV_Synthese');

//ajout d 'une étape
$etape = createEtape($formx2,'1','DOSSIER DU PATIENT '.$tabValeurs['Val_IDENT_NomPatient'].' '.$tabValeurs['Val_IDENT_PrenomPatient'].'  DONT LE NUMERO DE SEJOUR EST:'.$tabValeurs['Val_IDENT_SEJPatient'],array('etat'=>'fini'));
createItem($formx2,$etape,'id1','<span style="color:green;">Nom</span>','TXT',   $tabValeurs['Val_IDENT_NomPatient']);
createItem($formx2,$etape,'id2','<span style="color:green;">Prénom</span>','TXT',$tabValeurs['Val_IDENT_PrenomPatient']);
createItem($formx2,$etape,'id4','<span style="color:green;">Date de naissance</span>','TXT',$tabValeurs['Val_IDENT_DateNPat2']);
createItem($formx2,$etape,'id5','<span style="color:green;">Age</span>','TXT',$tabValeurs['Val_IDENT_AgePat']);
if ($patient->getSexe() == "F") 
  createItem($formx2,$etape,'id6','<span style="color:green;">Sexe</span>','TXT',"Féminin");
else
  createItem($formx2,$etape,'id6','<span style="color:green;">Sexe</span>','TXT',"Masculin");
createItem($formx2,$etape,'id7','<span style="color:green;">Adresse</span>','TXT',$tabValeurs['Val_IDENT_AdressePat']);
createItem($formx2,$etape,'id8','<span style="color:green;">Code postal</span>','TXT',$tabValeurs['Val_IDENT_CodePPat']);
createItem($formx2,$etape,'id9','<span style="color:green;">Ville</span>','TXT',$tabValeurs['Val_IDENT_VillePat']);
createItem($formx2,$etape,'id10','<span style="color:green;">Telephone</span>','TXT',$tabValeurs['Val_IDENT_TelPat']);
createItem($formx2,$etape,'id11','<span style="color:green;">IDU</span>','TXT',$tabValeurs['Val_IDENT_IDUPatient']);
createItem($formx2,$etape,'id12','<span style="color:green;">ILP</span>','TXT',$tabValeurs['Val_IDENT_ILPPatient']);
createItem($formx2,$etape,'id13','<span style="color:green;">SEJ</span>','TXT',$tabValeurs['Val_IDENT_SEJPatient']);
createItem($formx2,$etape,'id14','<span style="color:green;">Etablissement</span>','TXT',$tabValeurs['Val_Etablissement_Etablissement']);

createItem($formx2,$etape,'d1','','TXT','');
createItem($formx2,$etape,'d2','','TXT','');
createItem($formx2,$etape,'d3','','TXT','');


// On selectionne tous les formulaires du patient en fonction de l'IDU (ids)
$requete        = new clResultQuery;
unset($paramRq);
$paramRq["ids"] = $ids;
$paramRq["base"]=FX_BDD;
$formulaireAEV  = $requete->Execute("Fichier","getAllFormulaireAEVfromIDS",$paramRq,"ResultQuery");

// On parcourt la liste des formulaires
for ( $i = 0 ; $i < $formulaireAEV["INDIC_SVC"][2] ; $i++ ) {
  
  $idstemp = $formx->getIDS();
  $tabValeurs=array();
  $formxtemp = new clFoRmX($idstemp,'NO_POST_THREAT');
  $formxtemp->loadInstance($formulaireAEV["id_instance"][$i]);
  $domtemp = $formxtemp->XMLDOM ;
  $listItemstemp = $domtemp->getElementsByTagName('ITEM');
  foreach($listItemstemp as $itemtemp) {
    $nomItemtemp = 	$itemtemp->getAttribute('id');
    $valItemtemp =  $itemtemp->getElementsByTagName('Val')->item(0)->nodeValue;
    $tabValeurstemp[$nomItemtemp] = utf8_decode($valItemtemp);
  }
  
  // En fonction des formulaires, on crée les items 
  // On sélectionne aussi les formulaires en fonction du séjour
  if ( $tabValeurstemp["Val_IDENT_SEJPatient"] == $patient->getNSej() ) {
    
    //file 1 = file 2 = file 19 = file 20
    //file 3 = file 10 = file 11 = file 12
    //file 4 = file 5 = file 6 = file 7 = file 8 = file 22 = file 23 = file 24 = file 25 = file 26
    //file 9 = file 21 = file 27
    //file 13 = file 14 = file 15 = file 16 = file 17 = file 18
    
    //file 28 (without message) = file 29 (without message)
    //file 30 () = 
    //file 31 () = file 32 () = file 36 ()
    //file 33 () = file 35 ()
    //file 34 ()
    
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_1" ) {
      include ("Creation_Dossier_AEV_Situation_1_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_2" ) {
      include ("Creation_Dossier_AEV_Situation_2_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_3" ) {
      include ("Creation_Dossier_AEV_Situation_3_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_4" ) {
      include ("Creation_Dossier_AEV_Situation_4_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_5" ) {
      include ("Creation_Dossier_AEV_Situation_5_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_6" ) {
      include ("Creation_Dossier_AEV_Situation_6_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_7" ) {
      include ("Creation_Dossier_AEV_Situation_7_Synthese.php");
      } 
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_8" ) {
      include ("Creation_Dossier_AEV_Situation_8_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_9" ) {
      include ("Creation_Dossier_AEV_Situation_9_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_10" ) {
      include ("Creation_Dossier_AEV_Situation_10_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_11" ) {
      include ("Creation_Dossier_AEV_Situation_11_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_12" ) {
      include ("Creation_Dossier_AEV_Situation_12_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_13" ) {
      include ("Creation_Dossier_AEV_Situation_13_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_14" ) {
      include ("Creation_Dossier_AEV_Situation_14_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_15" ) {
      include ("Creation_Dossier_AEV_Situation_15_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_16" ) {
      include ("Creation_Dossier_AEV_Situation_16_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_17" ) {
      include ("Creation_Dossier_AEV_Situation_17_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_18" ) {
      include ("Creation_Dossier_AEV_Situation_18_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_19" ) {
      include ("Creation_Dossier_AEV_Situation_19_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_20" ) {
      include ("Creation_Dossier_AEV_Situation_20_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_21" ) {
      include ("Creation_Dossier_AEV_Situation_21_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_22" ) {
      include ("Creation_Dossier_AEV_Situation_22_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_23" ) {
      include ("Creation_Dossier_AEV_Situation_23_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_24" ) {
      include ("Creation_Dossier_AEV_Situation_24_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_25" ) {
      include ("Creation_Dossier_AEV_Situation_25_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_26" ) {
      include ("Creation_Dossier_AEV_Situation_26_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_27" ) {
      include ("Creation_Dossier_AEV_Situation_27_Synthese.php");
      } 
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_28" ) {
      include ("Creation_Dossier_AEV_Situation_28_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_29" ) {
      include ("Creation_Dossier_AEV_Situation_29_Synthese.php");
      } 
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_30" ) {
      include ("Creation_Dossier_AEV_Situation_30_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_31" ) {
      include ("Creation_Dossier_AEV_Situation_31_Synthese.php");
      } 
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_32" ) {
      include ("Creation_Dossier_AEV_Situation_32_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_33" ) {
      include ("Creation_Dossier_AEV_Situation_33_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_34" ) {
      include ("Creation_Dossier_AEV_Situation_34_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_35" ) {
      include ("Creation_Dossier_AEV_Situation_35_Synthese.php");
      }
    if ( $formulaireAEV["idformx"][$i] == "Dossier_AEV_Situation_36" ) {
      include ("Creation_Dossier_AEV_Situation_36_Synthese.php");
      }                            
    
  } // if ( $tabValeurstemp["Val_IDENT_SEJPatient"] == $patient->getNSej() )
  
  } //for ( $i = 0 ; $i < $formulaireAEV["INDIC_SVC"][2] ; $i++ ) {



//enregistrement initial du formulaire formx
$formx2->initInstance();

//modification pour qu'il soit en etat 'Fini'
$formx2->makeBalVal($formx2->XMLDOM->documentElement,"STATUS",'F');
$formx2->close();

//eko( $patient->getTablePatient() );

if ( $patient->getTablePatient() == "patients_sortis" )
  {
  clFoRmX_manip::rangerDossMedAEV($patient);
  
  //echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">';
  
  //$af = $patient->HistoriqueDocs();
  //echo($af);
  //div historiquedocspatient
  
  //document.getElementById('historiquedocspatient').innerHTML = "contenu de ton div"

  //$val = $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2));
  //echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php?navi='.$val.'">';
  
  //$fichePatient = new clFichePatient ( "Sortis", "patients_sortis", $patient->getID() ) ;
  //$fichePatient->getAffichage ( ) ; 
  
  header ( 'Location:?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)) ) ;
    	
    
  }
}
?>
