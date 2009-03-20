<?
include_once("config.php");
// On instancie les objets globaux.
$options=new clOptions();
$logs=new clLogs();
$session=new clSession();
$errs=new clErreurs();
$req=new clResultQuery();

//Reprise des données externes et UHCD avec nbactes ccam >1
//CCAM_repriseBALccam
/*$res=$req->Execute("Fichier","CCAM_repriseBALccamUHCD",array(),"ResultQuery");
//print affTab($res[INDIC_SVC]);
for ($i=0;isset($res[DISCR][$i]);$i++){
  $paramCCAM[idEvent]=$res[DISCR][$i];
  $ccam=new clCCAMCotationActesDiags($paramCCAM);
  $cw="and type='ACTE' and codeActe not like 'NGAP%' and codeActe not like 'CONS%' and idEvent<=19493";
  $ccam->writeBALSorti($cw);
  echo "Fin de traitement pour discr=".$res[DISCR][$i]."<br>";
}*/

//Reprise des données pour les consultations spécialisées
$res=$req->Execute("Fichier","CCAM_repriseBALpatientCons",$paramRq,"ResultQuery");
//print affTab($res[INDIC_SVC]);
unset($tabIdEvent);
for ($i=0;isset($res[DISCR][$i]);$i++){
  $idEvent=$res[DISCR][$i];
  $id=$res[ID][$i];
  
  unset($paramCCAM);
  $paramCCAM[idEvent]=$idEvent;
  $ccam=new clCCAMCotationActesDiags($paramCCAM);
  $tabSpecialite=$ccam->tabSpecialite();
  
  //Mise à jour de l'UF de consult spécialisée dans ccam_cotation_actes
  unset($contenuMBTV2);
  $contenuMBTV2=explode("|",$res[CONTENU][$i]);
  $codeActe=$contenuMBTV2[11];
  $codeAdeli=$contenuMBTV2[18];
  
  unset($paramRq);
  $paramRq[cw]="categorie='ListeMédecins' and code='$codeAdeli' and idDomaine=".CCAM_IDDOMAINE;
  $res3=$req->Execute("Fichier","CCAM_getSpeMedecin",$paramRq,"ResultQuery");
  $libelleSpe=$res3[nomliste][0];
  //echo "rq:".$res3[INDIC_SVC][15]."<br>idEvent:$idEvent-codeActe:$codeActe-libSpe:$libelleSpe<br>";
  unset($param);
  if ($libelleSpe){
    if ($tabSpecialite["$libelleSpe"]) $param[numUFexec]=$tabSpecialite["$libelleSpe"];
    else $param[numUFexec]="3301";
  }
  else $param[numUFexec]="3301";
  $majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
  $sql=$majrq->updRecord("idEvent=$idEvent and codeActe='$codeActe'");
  
  /*//Ecriture dans la BAL
  $cw="and codeActe='$codeActe'";
  $ccam->writeBALSorti($cw);
  
  //Récupération de l'ID de la table MBTV2 d'origine et maj MBTV2
  unset($param);
  $param[ID]=$id;
  $majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
  $sql=$majrq->updRecord("discr='$idEvent' and contenu like '%|$codeActe|%'");*/
  
  echo "Fin de traitement pour discr=$idEvent<br>";
}
?>
