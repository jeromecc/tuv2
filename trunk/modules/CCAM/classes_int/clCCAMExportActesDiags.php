<?php
/* Titre  : Classe Cotation des diagnostics et des actes CCAM & NGAP
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 11 avril 2005

	Description : Affectation/Désaffectation des actes de la liste restreinte,
	ou associés au diagnostic en cours, pour le patient en cours
*/

include (MODULE_CCAM."ccam_define.php");

class clCCAMExportActesDiags{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;
private $debug;

function __construct($param){
global $session;
global $options;

$this->dateDebut=$param["dateDebut"]." 00:00:00";
$this->dateFin=$param["dateFin"]." 23:59:59";
/*$this->dateDebut="2006-04-06";
$this->dateFin="2006-04-07";*/
}

function initTableauActesDiag($exp){
/*$paramRq[table]=PSORTIS;
$paramRq[cw]="where (dt_examen between '".$this->dateDebut."' and '".$this->dateFin."')";
$paramRq[cw]="where idpatient between 30070 and 30156";
$req=new clResultQuery;
$exp=$req->Execute("Fichier","getPatients",$paramRq,"ResultQuery");*/
reset($exp);
for ($i=0;isset($exp["idpatient"][$i]);$i++){
  $idPatient=$exp["idpatient"][$i];
  $tabI[$idPatient]=$i;

}

$paramRq[cw]="(dateEvent between '".$this->dateDebut."' and '".$this->dateFin."') and idDomaine=".CCAM_IDDOMAINE;
//$paramRq[cw]="idEvent between 30070 and 30156";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
for ($i=0;isset($res["identifiant"][$i]);$i++){
	set_time_limit(30);
  $idPatient=$res["idEvent"][$i];
  $indice=$tabI[$idPatient];
  if ($indice!="" or $indice=="0"){
    //eko("indice:$indice-patient:$idPatient");
    if ($res["type"][$i]=="DIAG"){
      if ($nbDiag[$idPatient]) $maxDiagPatient=$nbDiag[$idPatient]+1; else $maxDiagPatient=1;
      if ($maxDiagPatient<10) $maxDiagPatientTxt="0".$maxDiagPatient; else $maxDiagPatientTxt=$maxDiagPatient;
      $libColonne="DIAG_".$maxDiagPatientTxt;
      $tabExport[$libColonne][$indice]=$res["codeActe"][$i]."-".$res["libelleActe"][$i];
      $nbDiag[$idPatient]++;
    }
    else{
      if (substr($res["codeActe"][$i],0,4)!="NGAP" and substr($res["codeActe"][$i],0,4)!="CONS"){
        if ($nbCCAM[$idPatient]) $maxCCAMPatient=$nbCCAM[$idPatient]+1; else $maxCCAMPatient=1;
        if ($maxCCAMPatient<10) $maxCCAMPatientTxt="0".$maxCCAMPatient; else $maxCCAMPatientTxt=$maxCCAMPatient;
        $libColonne="CCAM_".$maxCCAMPatientTxt;
        $tabExport[$libColonne][$indice]=$res["codeActe"][$i]."-".$res["libelleActe"][$i];
        $nbCCAM[$idPatient]++;
      }
      else{
        if ($nbNGAP[$idPatient]) $maxNGAPPatient=$nbNGAP[$idPatient]+1; else $maxNGAPPatient=1;
        if ($maxNGAPPatient<10) $maxNGAPPatientTxt="0".$maxNGAPPatient; else $maxNGAPPatientTxt=$maxNGAPPatient;
        $libColonne="NGAP_".$maxNGAPPatientTxt;
        $tabExport[$libColonne][$indice]=$res["libelleActe"][$i]."-".$res["cotationNGAP"][$i];
        $nbNGAP[$idPatient]++;
      }
    }
  }
}
ksort($tabExport);
$this->tabExport=$tabExport;
/*ksort($tabI);
eko($tabI);
while (list($key,$val)=each($tabExport)){ 
  eko("Tableau $key");
  ksort($tabExport[$key]);
  eko($tabExport[$key]);
}*/
}

function getActesDiagsPatient($i){
$tabExport=$this->tabExport;
$chaine="";
reset($tabExport);
while (list($key,$val)=each($tabExport)){
  if ($val[$i]) $chaine.=$val[$i]."\t"; else $chaine.="-\t";
}
$chaine=substr($chaine,0,-1)."\n";
return $chaine; 
}

function getTitreColonnes(){
$tabExport=$this->tabExport;
$chaine="";
reset($tabExport);
while (list($key,$val)=each($tabExport)) $chaine.="$key\t";
$chaine=substr($chaine,0,-1)."\n";
return $chaine; 
}

// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}
}
