<?php
function Formulaire_Radio_MAJ_Table($formx) {

$id_instance = $formx->getIdInstance();
$idu         = $formx->getVar('ids');

//eko ($id_instance);
//eko ($idu);


$req = new clResultQuery ;
$param = array();

$param['cw']="*";
$param['idu']=$idu;
$res = $req -> Execute ( "Fichier", "getInfoPatientFromIDU", $param, "ResultQuery" ) ;
$idpatient = $res['idpatient'][0];
//eko($res);

$param = array();
$param['etat'] = "a";
$param['idpatient'] = $idpatient;
$param['idapplication'] = IDAPPLICATION ;
$param['id_instance'] = $id_instance;
//$param['dt_creation'] = date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":00";

list($jour,$mois,$annee)=explode("-",utf8_decode($formx->getFormVar('Val_F_RADIO_Date')));

$param['dt_creation'] = $annee."-".$mois."-".$jour." ".utf8_decode($formx->getFormVar('Val_F_RADIO_Heure')).":".utf8_decode($formx->getFormVar('Val_F_RADIO_Minute')).":00";

//eko ( utf8_decode($formx->getFormVar('Val_F_RADIO_Heure'))  ); 
//eko ( utf8_decode($formx->getFormVar('Val_F_RADIO_Minute')) );

//eko ( utf8_decode($formx->getFormVar('Val_F_RADIO_Date')) ); 


//eko ( $annee."-".$mois."-".$jour." ".utf8_decode($formx->getFormVar('Val_F_RADIO_Heure')).":".utf8_decode($formx->getFormVar('Val_F_RADIO_Minute')).":00");

//eko ($param);
$req = new clRequete(BDD,"radios",$param);
$res = $req->addRecord();


//$res = $req -> Execute ( "Fichier", "putFormulaireRadioData", $param, "ResultQuery" ) ;
//eko($res);

return "";
}
?>
