<?php

// Titre  : Classe ListesPredefinies
// Auteur : Christophe Boulay <cboulay@ch-hyeres.fr>
// Date   : 14 mars 2005

// Description : 
// Cette classe gère les listes prédéfinies
// Elle permet d'afficher une liste seule de différentes façons.

class clCCAMListesPredefinies{
// Attribut contenant l'affichage généré par la classe.
private $af;
private $infos;
private $erreurs;
private $type;

// Constructeur de la classe.
function __construct($type=''){
if (!$type){
	$this->type="ListesCCAM";
 	$this->getListesPredefinies();
}
}

// Gestion des listes générales.
function getListesPredefinies(){
global $session ;
global $options ;
// Récupération et calcul du ratio pour le nombre de listes
// affichées par ligne.
$num=$options->getOption("CCAM_ListesParLigne");
$nli=$options->getOption("CCAM_LignesParListe");
$ratio=sprintf("%d",100/$num);
// Vérification du droit de lecture.
if ($session->getDroit("CCAM_ListesPredefinies","r")){
	// Réparation d'une liste d'items.
  	if ($session->getNavi(2)=="repListeItems" and $session->getDroit("CCAM_ListesPredefinies","a")){
		$this->repListe($session->getNavi(3));
	}
  	
	// Ajout d'un nouvel item à une liste.
  	if (($_POST['Valider'] or $_POST['Valider_x']) and $session->getNavi(2)=="ValiderAjouter" 
  			and $session->getDroit("CCAM_ListesPredefinies","w")){
		$this->addItem($session->getNavi(3));
  	}
  	
	// Suppression d'un item.
  	if (($_POST['Supprimer'] or $_POST['Supprimer_x']) and $session->getNavi(2)=="ValiderModifier" 
			and $session->getDroit("CCAM_ListesPredefinies","d")){
		$this->delItem($session->getNavi(3),$session->getNavi(4));
  	}
  
  	// Mise à jour d'un item.
  	if (($_POST['Modifier'] or $_POST['Modifier_x']) and $session->getNavi(2)=="ValiderModifier" 
		and $session->getDroit("CCAM_ListesPredefinies","m")){
			$this->modItem($session->getNavi(3),$session->getNavi(4));
  	}
  	// Récupération de toutes les listes.
  	$param[cw]="WHERE categorie=\"ListesCCAM\" ORDER BY nomliste";
  	$param[idDomaine]=CCAM_IDDOMAINE;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getListes",$param,"ResultQuery"); 
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	
	// Initialisation du template ModeliXe.
	$mod=new ModeliXe("CCAM_GestionDesListes.mxt");
	$mod->SetModeliXe();
	
	// Affichage ou non du champs d'informations.
	if ($this->infos) $mod->MxText("informations.infos",$this->infos);
	else $mod->MxBloc("informations","modify"," ");
	
	// Affichage ou non du champs d'erreurs.
	if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
	else $mod->MxBloc("erreurs","modify", " ");
	
	// Parcours des différentes listes.
	for ($i=0;isset($res[nomliste][$i]);$i++){
		// Affichage ou non d'un tr en fonction de la liste parcourue
		if ($i and (!($i%$num))) $mod->MxText("liste.tr","</tr><tr>");
		else $mod->MxText("liste.tr","");
		
		// Affichage du td à la bonne dimension.
		$mod->MxText("liste.td","<td width=\"$ratio%\">");
		
		// Affichage du nom de la liste.
		$mod->MxText("liste.nomListe",$res[nomliste][$i]);
		
		// Si le droit d'écriture est présent, alors on affiche le bouton d'ajout.
		if ($session->getDroit("CCAM_ListesPredefinies","w")){
			$mod->MxImage("liste.imgAjouter",URLIMGAJO,"Ajouter");
			$mod->MxUrl("liste.lienAjouter",URLNAVI.$session->genNavi($session->getNavi(0),
				$session->getNavi(1),"Ajouter",$res[nomliste][$i]));
		}
		
		// Si le droit d'administration est présent, alors on affiche le bouton de réparation.
		if ($session->getDroit("CCAM_ListesPredefinies","a")){
			$mod->MxImage("liste.imgReparer",URLIMGREP,"Reparer");
			$mod->MxUrl("liste.lienReparer",URLNAVI.$session->genNavi($session->getNavi(0),
				$session->getNavi(1),"repListeItems",$res[nomliste][$i]));
		}
		
		// Génération de la variable de navigation.
		$mod->MxHidden("liste.hidden","navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),"Modifier",$res[nomliste][$i]));
		
		// Préparation de la liste des items de la liste parcourue.
		$data=$this->getListeItems($res[nomliste][$i],1);
		$mod->MxSelect("liste.select","item",$_POST['item'],$data,'','',
			"size=\"$nli\" onChange=\"reload(this.form)\""); 
		
		// Si c'est nécessaire, on affiche le formulaire d'ajout d'un nouvel item.
		if ($session->getNavi(2)=="Ajouter" and $session->getNavi(3)==addslashes($res[nomliste][$i]) 
				and $session->getDroit("CCAM_ListesPredefinies","w"))
			$mod->MxText("formAjouter",$this->getFormAjouter($res[nomliste][$i]));
			
		// Si c'est nécessaire, on affiche le formulaire de modification d'un item.
		elseif ($session->getNavi(2)=="Modifier" and 
				$session->getNavi(3)==addslashes($res[nomliste][$i]) and 
				$session->getDroit ("CCAM_ListesPredefinies","m"))
			$mod->MxText("formAjouter",$this->getFormModifier($res[nomliste][$i]));

		// Sinon, on n'affiche pas la partie formulaire.
		else $mod->MxText("liste.form","");
		
		// Boucle sur le bloc liste.
		$mod->MxBloc("liste","loop");
  	}
	// Récupération de l'affichage généré par le template.
	$this->af.=$mod->MxWrite("1");
}
}

// Réparation d'une liste (de listes ou d'items).
function repListe($nomListe=''){
global $errs ;
// Réparation d'une liste d'items.
if ($nomListe){
	$_POST['liste']=$nomListe;
  	// Récupération des items de la liste à réparer.
  	$param[cw]="WHERE nomliste='$nomListe' AND nomitem!='LISTE' AND 
		categorie=\"".$this->type."\" ORDER BY rang";
  	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[order]="";
	$req=new clResultQuery;
  	$res=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	// Si au moins un item est présent, alors on commence la reconstruction.
  	if ($res[INDIC_SVC][2]){
		for ($i=0;isset($res[iditem][$i]);$i++){
			$data[rang]=$i+1;
			$requete=new clRequete(CCAM_BDD,"ccam_liste",$data);
			$requete->updRecord("iditem='".$res[iditem][$i]."' and idDomaine=".CCAM_IDDOMAINE);
		}
		// Message d'information.
		$this->infos.="La réparation de la liste \"".stripslashes($nomListe)."\" a été effectuée.";
  	}
	else{
		// Signalement des erreurs.
		$errs->addErreur ("La liste \"".stripslashes($nomListe)."\" n'existe pas ou ne contient 
			aucun item, la réparation est annulée.");
		$this->erreurs.="La liste \"".stripslashes($nomListe)."\" n'existe pas ou ne contient 
			aucun item, la réparation est annulée.";
  	}
}
// Réparation d'une liste de listes.
else{
	// Récupération des différentes listes.
  	$param[cw]="WHERE nomitem='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang";
  	$param[idDomaine]=CCAM_IDDOMAINE;
	$req=new clResultQuery;
  	$res=$req->Execute("Fichier","getListesItems",$param,"ResultQuery"); 
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
  	// Si au moins une liste est présente, on commence la reconstruction.
  	if ($res[INDIC_SVC][2]){
		for ($i=0;isset($res[iditem][$i]);$i++){
			$data[rang]=$i+1;
			$requete=new clRequete(CCAM_BDD,"ccam_liste",$data);
			$requete->updRecord("iditem='".$res[iditem][$i]."' and idDomaine=".CCAM_IDDOMAINE);
		}
		// Message d'information.
		$this->infos.="La réparation de la liste des catégories de ".$this->type." a été effectuée.";
  	}
	else{
		// Signalement des erreurs.
		$errs->addErreur("La liste des listes de catégories de ".$this->type." ne contient aucune 
			liste, la réparation est annulée.");
		$this->erreurs.="La liste des listes de catégories de ".$this->type." ne contient aucune 
			liste, la réparation est annulée.";
  	}
}
}

// Modification d'un item d'une liste.
function modItem($nomListe,$idItem){
global $errs;
$req=new clResultQuery;
//Récupération des anciennes informations de l'item à modifier.
$param[cw]="WHERE iditem='".$idItem."'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$res1=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// Récupération de tous les autres items.
$param[cw]="WHERE nomliste='$nomListe'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$res2=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// Vérification de la présence d'un item portant le nouveau nom.
$param[cw]="WHERE nomliste='$nomListe' and nomitem='".$_POST['nomItemF']."' and iditem!='$idItem'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$res3=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// Vérification que l'item existe.
if ($res1[INDIC_SVC]>0){
  	// Vérification d'un changement de nom.
	if ($res1[nomitem][0]!=$_POST['nomItemF']){
		// Nouveau nom libre ou pas.
		if ($res3[INDIC_SVC][2]==0){
		// Nouveau nom correct.
			if (eregi("[0-9a-zA-Z]",$_POST['nomItemF'])){
				// Mise à jour du nom de l'item dans la base.
				$data[nomitem]=$_POST['nomItemF'];
	 			$requete=new clRequete(CCAM_BDD,"ccam_liste", $data );
 				$requete->updRecord("iditem='".$idItem."' and idDomaine=".CCAM_IDDOMAINE);
				// Message d'information.
				$this->infos.="L'item \"".$res1[nomitem][0]."\" de la liste \"".
					stripslashes($nomListe)."\" a changé de nom : \"".$_POST['nomItemF']."\".<br/>";
			}
			else
 				// Message d'erreur.
				$this->erreurs .= "Le nom choisi ne doit pas être vide.";
		}
		else
			// Message d'erreur.
			$this->erreurs.="Le nom choisi pour l'item \"".$res1[nomitem][0]."\" de la liste \"".
				stripslashes($nomListe)."\" est déjà utilisé. La modification est annulée.<br/>";
	}
  	
	/*// On vérifie si le type de la destination attendue a changé.
  	if ($res1[localisation][0]!=$_POST['typeF']){
		$data2[localisation] = $_POST['typeF'] ;
		$requete=new clRequete(CCAM_BDD,"ccam_liste",$data2);
		$requete->updRecord("iditem='".$res1[iditem][0]."'");
		// Message d'information.
		$this->infos.="L'item \"".$res1[nomitem][0]."\" de la liste \"".stripslashes($nomListe).
			"\" a changé de type de destination.<br/>";
  	}*/
  	// On vérifie si l'item a changé de position ou non.
  	if ($res1[iditem][0]!=$_POST['placerF']){
		// Suppression du rang actuel et décalage du rang des autres items.	
		$rang=$res1[rang][0];
		$param[cw]="WHERE rang>'$rang' and nomliste='$nomListe'";
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[order]="";
		$res4=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		for ($i=0;isset($res4[iditem][$i]);$i++){
			$data3[rang] = $res4[rang][$i] - 1 ;
			$requete = new clRequete ( BDD, "listes", $data3 ) ;
			$requete->updRecord ( "iditem='".$res4[iditem][$i]."' and idDomaine=".CCAM_IDDOMAINE) ;
		}
		// Calcul du rang suivant.
		if ($_POST['placerF']){
			$param[cw]="WHERE iditem='".$_POST['placerF']."' and nomliste='$nomListe'";
			$param[idDomaine]=CCAM_IDDOMAINE;
			$res6=$req->Execute("Fichier","getListesItems",$param,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			$rang=$res6[rang][0]+1;
		} 
		else $rang=1;
		
		// Décalage de tous les items d'un rang.
		$param[cw]="WHERE rang>='$rang' and nomliste='$nomListe'";
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[order]="";
		$res5=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		for ($i=0;isset($res5[iditem][$i]);$i++){
			$data4[rang]=$res5[rang][$i]+1;
			$requete=new clRequete(CCAM_BDD,"ccam_liste",$data4);
			$requete->updRecord("iditem='".$res5[iditem][$i]."' and idDomaine=".CCAM_IDDOMAINE);
		}
		
		// Mise à jour du rang de l'item.
		if ($_POST['placerF']) $data5[rang]=$res6[rang][0]+1;
		else $data5[rang]=1;
		$requete=new clRequete(CCAM_BDD,"listes",$data5);
		$requete->updRecord("iditem='".$res1[iditem][0]."' and idDomaine=".CCAM_IDDOMAINE);
		// Message d'information.
		$this->infos.="L'item \"".$res1[nomitem][0]."\" de la liste \"".stripslashes($nomListe).
			"\" a changé de position.<br/>";
	}
}
else{
	// Signalement d'une erreur si l'item à modifier n'existe pas.
	$this->erreurs.="L'item ne peut pas être modifié (id=\"$idItem\") car il n'existe pas.";
	$errs->addErreur("clListesCCAM : L'item ne peut pas être modifié (id=\"$idIditem\") 
		car il n'existe pas.");
}
}

// Suppression d'un item d'une liste.
function delItem($nomListe,$idItem){
global $errs;
$req=new clResultQuery;
// Récupération des informations actuelles de l'item.
$param[cw]="WHERE iditem='".$idItem."'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$res=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// Récupération de la liste des items.
$param[cw]="WHERE nomliste='$nomListe'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$res2=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// On vérifie qu'on n'est pas en train de supprimer le dernier item.
if ($res2[INDIC_SVC][2]>1){
	// Vérification que l'item existe.
	if ($res[INDIC_SVC][2]>0){
		// Décalage des rangs des autres items.
		$rang=$res[rang][0];
		$param[cw]="WHERE rang>'$rang' and nomliste='$nomListe'";
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[order]="";
		$res3=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		for ($i=0;isset($res3[iditem][$i]);$i++){
			$data[rang]=$res3[rang][$i]-1;
			$requete=new clRequete(CCAM_BDD,"ccam_liste",$data);
			$requete->updRecord("iditem='".$res3[iditem][$i]."' and idDomaine=".CCAM_IDDOMAINE);
		}
		// Message d'information.
		$this->infos.="L'item \"".$res[nomitem][0]."\" a été supprimé de la liste \"".
			stripslashes($nomListe)."\".";
		// Suppression de l'item.
		$requete=new clRequete(CCAM_BDD,"ccam_liste");
		$requete->delRecord("iditem='$idItem' and idDomaine=".CCAM_IDDOMAINE);
	}
	else{
		// Signalement 
		$this->erreurs.="L'item ne peut pas être supprimé (id=\"$idItem\") car il n'existe pas.";
		$errs->addErreur("clListesCCAM : L'item ne peut pas être supprimé (id=\"$idIditem\") 
			car il n'existe pas.");
	}
}
else{
	$this->erreurs.="Impossible de supprimer le dernier item de la liste \"$nomListe\".";
}
}

// Modification d'un item d'une liste.
function getFormModifier($nomListe){
global $options;
global $session;
global $errs;
// Récupération des informations de l'item.
$param[cw]="WHERE iditem='".$_POST['item']."'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// Si l'item existe, on affiche le formulaire.
if ($res[INDIC_SVC][2]>0){
	// Chargement du template ModeliXe.
  	$mod=new ModeliXe("CCAM_ModifierListeItem.mxt");
	$mod->SetModeliXe();
  	
	// Affichage du bouton "Supprimer" si l'utilisateur a les droits.
  	if (!$session->getDroit("CCAM_ListesPredefinies","d"))
		$mod->MxBloc("supprimer","modify"," ");
  	
	// Nom de la liste.
  	$mod->MxText("nomListe",$res[nomliste][0]);
  	
	// Nom actuel de l'item.
  	$mod->MxText("oldNomItem",$res[nomitem][0]);
	
	// Champs texte de modification du nom de l'item.
  	$mod->MxText("nomItem","Valeur :");
  	$mod->MxFormField("nomItemF","text","nomItemF",$res[nomitem][0],"size=\"31\" maxlength=\"50\"");
  	// Affichage de la liste pour déplacer l'item si on est dans une à classement manuel.
  	if ($options->getOption($nomListe)=="Manuel"){
		$mod->MxText("placer","Placer :");
		$data=$this->getListeItems($nomListe,1,1,$res[nomitem][0]);
		$mod->MxSelect("placerF","placerF",$res[iditem][0],$data ,'','',"size=\"1\""); 
	}
	else
		$placerF="&placerF=".$res[iditem][0];
  	
	$mod->MxBloc("formType","modify"," ");
  	// Génération de la variable de navigation.
  	$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),
		"ValiderModifier",$nomListe,$res[iditem][0]).$placerF);
	
	return $mod->MxWrite("1");
}
else{
	// Envoi d'une erreur si l'item à modifier n'existe pas.
  	$errs->addErreur("clListesCCAM : L'item (id=".$_POST['item'].") n'existe pas.");
}
}

// Ajout d'un item à une liste.
function addItem($nomListe){
global $options ;
// On vérifie qu'un item ne porte pas déjà ce nom.
$param[cw]="WHERE nomitem='".$_POST['nomItemF']."' AND nomliste='$nomListe'";
$param[idDomaine]=CCAM_IDDOMAINE;
$param[order]="";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
// On signale l'erreur si le nom est déjà pris.
if ($res[INDIC_SVC][2]>0){
	$this->erreurs.="Dans la liste \"$nomListe\", un item portant ce nom 
		(\"".$_POST['nomItemF']."\") existe déjà. La création est annulée.";
}
else{
	// Nouveau nom correct.
  	if (eregi("[0-9a-zA-Z]",$_POST['nomItemF'])){
		// On positionne correctement le nouvel item et on déplace les autres.
		if($options->getOption( stripslashes($nomListe))!="Manuel") $rang=1;
		else{ 
			$param[cw]="WHERE iditem='".$_POST['placerF']."' and nomliste='$nomListe'";
			$param[idDomaine]=CCAM_IDDOMAINE;
			$param[order]="";
			$res2=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			$rang=$res2[rang][0]+1; 
		}
		$param[cw]="WHERE rang>='$rang' AND nomliste='$nomListe'";
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[order]="";
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		for ($i=0;isset($res[iditem][$i]);$i++){
			$data2[rang] = $res[rang][$i] + 1 ;
			$requete=new clRequete(CCAM_BDD,"ccam_liste",$data2);
			$requete->updRecord("iditem='".$res[iditem][$i]."' and idDomaine=".CCAM_IDDOMAINE);
		}
		// Insertion du nouveau item.
		$data[categorie]="ListesCCAM";
		$data[nomliste]=$nomListe;
		$data[nomitem]=$_POST['nomItemF'];
		$data[rang]=$rang;
		$data[idDomaine]=CCAM_IDDOMAINE;
		$requete=new clRequete(CCAM_BDD,"ccam_liste",$data);
		$requete->addRecord();
		// Message d'information.
		$this->infos.="L'item \"".$_POST['nomItemF']."\" a été ajouté dans la liste \"".
			stripslashes($nomListe)."\".";
	}
	else $this->erreurs.="Le nom choisi ne doit pas être vide.";
}
}

// Retourne le code HTML du formulaire d'ajout d'item.
function getFormAjouter($nomListe){
global $options;
global $session;
// Chargement du template.
$mod=new ModeliXe("CCAM_AjouterListeItem.mxt");
$mod->SetModeliXe();
// Nom de la liste.
$mod->MxText("nomListe",$nomListe);
// Champs texte pour entrer le nom de l'item.
$mod->MxText("nomItem","Valeur :");
$mod->MxFormField("nomItemF","text","nomItemF",$_POST['valeur'],"size=\"31\" maxlength=\"50\"");

// Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
if ($options->getOption($nomListe)=="Manuel"){
	$mod->MxText("placer","Placer :");
	$data=$this->getListeItems($nomListe,1,1);
	$mod->MxSelect("placerF","placerF",'',$data,'','',"size=\"1\""); 
}

$mod->MxBloc("formType","modify"," ");

// Génération de la variable de navigation.
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),
	"ValiderAjouter",$nomListe));

return $mod->MxWrite("1");
}

// Retourne la liste des items.
function getListeItems($nomListe,$modelixe='',$placement='',$nomItem=''){
global $options ;
// Préparation du type de classement pour la requête.
switch ($options->getOption($nomListe)){
	case 'Manuel':$order="ORDER BY rang";break;
	case 'Alphabétique':$order="ORDER BY nomitem";break;
	case 'Alphabétique inversé':$order="ORDER BY nomitem DESC";break;
	default:$order="";break;
}
$param[cw]="WHERE nomliste='".addslashes($nomListe)."'";
$param[order]=$order;
$param[idDomaine]=CCAM_IDDOMAINE;
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getListesItems",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);

// Affichage en cas de débugage.
if (DEBUGLISTES) newfct(gen_affiche_tableau,$res[INDIC_SVC]);

// Préparation du tableau à retourner pour un select de modelixe.
if ($modelixe){
	// Placement ou affichage simple.
  	if ($placement){ 
		$placer="Après "; 
		$tab[0]="En début de liste";
		$type="iditem";
		$val=0;
	}
	else $type = "iditem" ;
	
	// Fabrication du tableau.
	for ($i=0;isset($res[iditem][$i]);$i++){
		if ($nomItem==$res[nomitem][$i]){
			$rang=$res[$type][$i];
			$tab[$rang]="Position actuelle";
		}
		else $tab[($res[$type][$i]+$val)]=$placer.$res[nomitem][$i];
	}
  	// Retourne le tableau au format attendu par modelixe.
  	return $tab;
}
else
	// Retourne le tableau au format normal de ResultQuery.
  	return $res;
}

// Retourne l'affichage généré.
function getAffichage(){
return $this->af;
}
}