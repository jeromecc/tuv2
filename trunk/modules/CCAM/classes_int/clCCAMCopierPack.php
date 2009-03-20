<?php
/* Titre  : Classe ListeRestreinte
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 04 mars 2005

	Description : Création, modification, suppression de parties anatomiques
	Affectation/Désaffectation des actes de la liste restreinte pour la partie anatomique en cours
*/
class clCCAMCopierPack{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct(){
global $session ;
$this->copierPacks();
}

// Gestion des parties anatomiques
function copierPacks(){
global $session ;
/*(!$_POST['action'])?$action="ccam":$action=$_POST['action'];
($action=="affectation_ngap")?$nomForm="CCAM_AffectationNGAP.mxt":
	$nomForm="CCAM_ListeRestreinte.mxt";*/
// Appel du template
$mod=new ModeliXe("CCAM_CopierPack.mxt");
$mod->SetModeliXe();

$mod->MxText("titreFormulaire","Dupliquer les actes affectés à d'autres diagnostics");

//Initialisation des valeurs
(!$_POST['idListeSelection0'])?$idListeSelection0="aucun#":
	$idListeSelection0=$_POST['idListeSelection0'];
(!$_POST['idListeSelection1'])?$idListeSelection1="aucun#":
	$idListeSelection1=$_POST['idListeSelection1'];
(!$_POST['idListeSelection2'])?$idListeSelection2="tous":
	$idListeSelection2=$_POST['idListeSelection2'];

//Duplication des actes pour les diagnostics sélectionnés
if ($_POST['OK_x']) $this->infos=$this->copierActes();

//Récupération des valeurs pour Selection0
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$tabListeSelection0=$this->tableauValeurs("CCAM_getListeCatDiag",$param,
	"Choisir une catégorie de diagnostics");

//Récupération des valeurs pour Selection1
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idListeSelection0]=addslashes(stripslashes($idListeSelection0));
$param[order]="libelle";
$tabListeSelection1=$this->tableauValeurs("CCAM_getListeDiags",$param,"Choisir un diagnostic");

//Récupération des valeurs pour Selection2
unset($param);
$param[cw]="";
$tabListeSelection2=$this->tableauValeurs("CCAM_getFamilles",$param,
	"Choisir une famille d'actes");
$tabListeSelection2[NGAP]="Actes NGAP";
$tabListeSelection2[PACK]="Packs d'actes";
	
//Récupération des actes pour la liste de gauche en ignorant les valeurs de la liste de droite
//en fonction de la famille sélectionnéee dans Selection1
if ($idListeSelection1 and $idListeSelection1!="aucun#"){
	//Récupération des actes affectés au diagnostic sélectionné
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[idListeSelection1]=$idListeSelection1;
	$param[cw]="";
	$param[order]="identifiant";
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getActesDiags",$param,"ResultQuery");
	//eko($res[INDIC_SVC]);
	$listeGauche="";
	for ($i=0;isset($res[identifiant][$i]);$i++){ 
		$listeGauche.=$res[identifiant][$i]."-".$res[libelle][$i]."<br>";
	}
	$mod->MxText("listeGauche",$listeGauche);
	
	//Récupération des diagnostics de la liste de droite
	unset($param);
	$param[cw]="and nomliste='$idListeSelection0' and idDomaine=".CCAM_IDDOMAINE." and code<>'$idListeSelection1'";
	$tabListeDroite=$this->tableauValeurs("CCAM_getListeAutresDiags",$param,"Choisir un diagnostic");
}
else	
	$tabListeDroite[0]="Choisir un diagnostic";

//Gestion du template
$mod->MxText("titreSelection0","Catégories de diagnostics :");
$mod->MxSelect("idListeSelection0","idListeSelection0",stripslashes($idListeSelection0),
	$tabListeSelection0,'','',"onChange=reload(this.form)"); 

$mod->MxText("titreSelection1","Diagnostics :");
$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
	$tabListeSelection1,'','',"onChange=reload(this.form)"); 
	
$mod->MxText("titreDispo","Actes affectés au diagnostic sélectionné");
$mod->MxText("titreAffecte","Diagnostics vers lequels les actes seront copiés");

//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Decoupage","w")){
	$mod->MxBloc("validerCopier","delete");
}

// Affichage ou non du champs d'informations.
if ($this->infos) $mod->MxText("informations.infos",$this->infos);
else $mod->MxBloc("informations","delete");

// Affichage ou non du champs d'erreurs.
if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
else $mod->MxBloc("erreurs","delete");

$mod->MxSelect("listeDroite","listeDroite[]",'',$tabListeDroite,'','',
	"size=\"15\" multiple=\"yes\" class=\"selectngap\"");
	
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
	$session->getNavi(1)));

$this->af.=$mod->MxWrite("1");     
}

//Ajout des actes de la liste de gauche aux diagnostics sélectionnés
function copierActes(){
global $session ;
//Récupération des actes affectés au diagnostic sélectionné
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idListeSelection1]=$_POST[idListeSelection1];
$param[cw]="";
$param[order]="identifiant";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiags",$param,"ResultQuery");
//eko($res[INDIC_SVC]);
unset($actes);
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$actes[]=$res[identifiant][$i];
}

if (is_array($_POST['listeDroite'])){
	while (list($key,$val)=each($_POST['listeDroite'])){ 
		if ($val and $val!="aucun#"){
			reset($actes);
			for ($i=0;$i<count($actes);$i++){ 
				unset($param);
				$param[idDomaine]=CCAM_IDDOMAINE;
				$param[idListeSelection1]=$val;
				$param[cw]="and rel.idActe='$actes[$i]'";
				$param[order]="identifiant";
				$req=new clResultQuery;
				$res=$req->Execute("Fichier","CCAM_getActesDiags",$param,"ResultQuery");
				//eko($res[INDIC_SVC]);
				if ($res[INDIC_SVC][2]==0){
					unset($param);
					$param[idDiag]=$val;
					$param[idDomaine]=CCAM_IDDOMAINE;
					$param[idActe]=$actes[$i];
					$majrq=new clRequete(CCAM_BDD,"ccam_actes_diagnostic",$param);
					$majrq->addRecord();
				}
			}
		}
	}
	$retourInfos="Les actes rattachés au diagnostic '".$_POST['idListeSelection1']."' ont été affectés aux diagnostics sélectionnés";
	return $retourInfos;
}
}

//Fabrication d'une liste de valeurs à partir d'une requête
function tableauValeurs($requete,$param="",$lignePresentation=""){
// Récupération de la liste de valeurs
if ( ! isset ( $param['order'] ) ) $param['order'] = 'nomliste' ;
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requete,$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
($requete=="CCAM_getFamilles")?$tab[tous]="Tous les actes de la liste restreinte":
	$tab["aucun#"]=$lignePresentation;
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$libelle=strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
	if ($res[title][$i]=="" ){
		if ($res[cotationNGAPvide][$i]=="" and substr($res[identifiant][$i],0,4)=="NGAP"){
			$tab[$res[identifiant][$i]].="*";
		}
		$tab[$res[identifiant][$i]].=$res[identifiant][$i];
		if ($res[cotationNGAP][$i]!=""){
			$tab[$res[identifiant][$i]].=" - ".$res[cotationNGAP][$i];
		}
		if ($libelle!=""){
			$tab[$res[identifiant][$i]].=" - ".ucfirst(strtolower($libelle));
		}
	}
	else{
		$title=strtr($res[title][$i],"ÉÈÊÀ","éèêà");
		$identifiant=$res[identifiant][$i]."\" onmouseover=\"window.status='Voir cet événement'; 
			show(event, 'id".$res[identifiant][$i]."'); return true;\" 
			onmouseout=\"hide('id".$res[identifiant][$i]."'); return true;\"";
		if ($res[cotationNGAPvide][$i]=="" and substr($res[identifiant][$i],0,4)=="NGAP"){
			$tab[$identifiant].="*";
		}
		$tab[$identifiant].=$res[identifiant][$i];
		if ($res[cotationNGAP][$i]!=""){
			$tab[$identifiant].=" - ".$res[cotationNGAP][$i];
		}
		if ($libelle!=""){
			$tab[$identifiant].=" - ".ucfirst(strtolower($libelle));
		}
		$mod = new ModeliXe ( "CCAM_InfoBulleActes.mxt" ) ;
		$mod -> SetModeliXe ( ) ;
		$mod -> MxText ( "iddiv", "id".$res[identifiant][$i] ) ;
		$mod -> MxText ( "libelleActe", $libelle ) ;
		// Récupération du code HTML généré.
		$this->af .= $mod -> MxWrite ( "1" ) ;
	}
}
return $tab;
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
