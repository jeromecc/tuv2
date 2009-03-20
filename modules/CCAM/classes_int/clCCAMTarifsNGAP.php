<?php
/* Titre  : Classe tarifsNGAP
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : septembre 2005

	Description : Gestion des lettres-clé NGAP au niveua des tarifs et des correspondances nationales
*/

class clCCAMTarifsNGAP{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;

function __construct(){
global $session;
global $options;
$this->anneeMiniAffichage=$options->getOption("AnneeMiniAffichage");
$this->anneeMiniCalcul=$options->getOption("AnneeMiniCalcul");

$this->tarifsNGAP();
}

// Gestion des niveaux et des Reseaux de dépenses
function tarifsNGAP(){
global $session ;
//On a lancer le rapatriement des tarifs depuis Pastel
if ($_POST['imgCalcul_x'] and $session->getDroit("CCAM_TarifsNGAP","w")){
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","Tarifs_getTarifNGAP",array(),"ResultQuery");
	eko($res);
	$mail="";
	for ($i=0;isset($res[LC][$i]);$i++){
		$lc=$res[LC][$i];
		unset($param);
		//if ($res[TARIF][$i]!="0"){ // code de Christophe Boulet
		if ($res[TARIF][$i] >=0 ) { // modification François Derock
			$param[tarif]=str_replace(",",".",$res[TARIF][$i]);
					
			$req=new clResultQuery;
			unset($paramRq);
			$paramRq[cw]="lc='$lc'";
			$res2=$req->Execute("Fichier","CCAM_getLC",$paramRq,"ResultQuery");
			//eko($res2[INDIC_SVC]);
      if ($res2[INDIC_SVC][2]){
				$requete=new clRequete(CCAM_BDD,"ccam_lettres_cle",$param);
				$retourRequete=$requete->updRecord("lc='$lc'");
			}
			else{
				$param[lc]=$param[lcNat]=$lc;
				$requete=new clRequete(CCAM_BDD,"ccam_lettres_cle",$param);
				$retourRequete=$requete->addRecord();
			}
		}
		else $mail.="$lc, ";
	}
	if ($mail){
		$mail=substr($mail,0,-2);
		$content_type="Content-Type: text/html; charset=\"iso-8859-1\"";
		$head="From: ".Erreurs_MailApp."\n".$content_type."\n";
		$sujetMail="Terminal des urgences : Mise à jour des tarifs des lettres-clé NGAP";
		$txtMsg="<b>Anomalie : </b>La mise à jour des tarifs (source PASTEL) renvoit '0' pour les lettres-clé suivantes : $mail<br>
			==> Les tarifs n'ont pas été mis à jour pour ces lettres-clés<p>
			<i>Ce mail est envoyé automatiquement</i>";
		mail(Erreurs_Mail.",".Erreurs_Mail_WebMaster,$sujetMail,$txtMsg,$head);//
		$retourInfos[erreur]="Certains tarifs sont à '0' dans PASTEL. Un mail de synthèse a été envoyé. Leur mise à jour a été annulée.";
	}
	else {
  $content_type="Content-Type: text/html; charset=\"iso-8859-1\"";
	$head="From: ".Erreurs_MailApp."\n".$content_type."\n";
	$sujetMail="Terminal des urgences : Mise à jour des tarifs des lettres-clé NGAP";
	$txtMsg="Les correspondances nationales et les tarifs ont été mis à jour à partir des données PASTEL<p>
		<i>Ce mail est envoyé automatiquement</i>";
	mail(Erreurs_Mail.",".Erreurs_Mail_WebMaster,$sujetMail,$txtMsg,$head);//
  $retourInfos[infos]="Les correspondances nationales et les tarifs ont été mis à jour à partir des données PASTEL";
  }
}

//On a validé les modifications
if ($_POST['imgValider_x'] and $session->getDroit("CCAM_TarifsNGAP","w")){
	$req=new clResultQuery;
	unset($paramRq);
	$paramRq[cw]="lc!=''";
	$res=$req->Execute("Fichier","CCAM_getLC",$paramRq,"ResultQuery");
	//eko($res[INDIC_SVC]);
	for ($i=0;isset($res[lc][$i]);$i++){
		$lc=$res[lc][$i];
		$varLC="LC_".$lc;
		$tarifLC="tarif_".$lc;
		
		unset($param);
		$param[lcNat]=$_POST[$varLC];
		$param[tarif]=str_replace(",",".",$_POST[$tarifLC]);
		$requete=new clRequete(CCAM_BDD,"ccam_lettres_cle",$param);
		$retourRequete=$requete->updRecord("lc='$lc'");
	}
	$retourInfos[infos]="Les correspondances nationales et les tarifs ont été modifiés";
}

// Appel du template
$mod=new ModeliXe("CCAM_TarifsNGAP.mxt");
$mod->SetModeliXe();

if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];

if ($session->getDroit("CCAM_TarifsNGAP","w")){
	if (!$_POST['imgModifier_x']){
		$mod->MxFormField("modifier.imgModifier","image","imgModifier","","src=\"".URLIMG."modifier2.gif\" align=\"abscenter\" 
			title=\"Modifier les correspondances nationales et les tarifs\"");
		$mod->MxFormField("maj.imgCalcul","image","imgCalcul","","src=\"".URLIMG."calculer.gif\" align=\"abscenter\" 
			title=\"Rapatrier les tarifs depuis PASTEL\"");
		$mod->MxBloc("valider","delete");
	}
	else{
		$mod->MxFormField("valider.imgValider","image","imgValider","","src=\"".URLIMG."Ok.gif\" align=\"abscenter\" 
			title=\"Valider les modifications\"");
		$mod->MxFormField("valider.imgAnnuler","image","imgAnnuler","","src=\"".URLIMG."annuler2.gif\" align=\"abscenter\" 
			title=\"Annuler les modifications\"");
		$mod->MxBloc("maj","delete");
		$mod->MxBloc("modifier","delete");
	}
}
else{
	$mod->MxBloc("modifier","delete");
	$mod->MxBloc("valider","delete");
	$mod->MxBloc("maj","delete");
}

$req=new clResultQuery;
unset($paramRq);
$paramRq[cw]="lc!=''";
$res=$req->Execute("Fichier","CCAM_getLC",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
for ($i=0;isset($res[lc][$i]);$i++){
	$lc=$res[lc][$i];
	$mod->MxText("ligneLC.LC",$lc);
	if (!$_POST['imgModifier_x']){
		$mod->MxText("ligneLC.LCnat",$res[lcNat][$i]);
		$mod->MxText("ligneLC.tarif",$res[tarif][$i]);
	}
	else{
		$varLC="LC_".$lc;
		$mod->MxFormField("ligneLC.LCnat","text",$varLC,$res[lcNat][$i],"size=\"10\"");
		
		$tarifLC="tarif_".$lc;
		$mod->MxFormField("ligneLC.tarif","text",$tarifLC,$res[tarif][$i],"size=\"10\"");
	}
	$mod->MxBloc("ligneLC","loop");
}

//Ne jamais afficher les boutons suivants

// Affichage ou non du champs d'informations.
if ($this->infos) $mod->MxText("informations.infos",$this->infos);
else $mod->MxBloc("informations","delete");

// Affichage ou non du champs d'erreurs.
if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
else $mod->MxBloc("erreurs","delete");
		
$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1)));

$this->af.=$mod->MxWrite("1");     
}

//======================================================================================================================================
// Retourne l'affichage de la classe.
function getAffichage(){
//eko ("test");
return $this->af ;
}
}
