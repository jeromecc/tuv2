<?php
function Calcul_Delai_Traumatologie_Main($formx) {

global $session;
global $options;

$date_accident  = utf8_decode($formx->getFormVar('Val_Horaire_Date_Accident'));
$heure_accident = utf8_decode($formx->getFormVar('Val_Horaire_Heure_Accident'));
$date_examen    = utf8_decode($formx->getFormVar('Val_Horaire_Date_Examen'));
$heure_examen   = utf8_decode($formx->getFormVar('Val_Horaire_Heure_Examen'));

$posted  = explode("-",$date_accident);
$date_accident=$posted[2]."-".$posted[1]."-".$posted[0];
	
$posted  = explode("-",$date_examen);
$date_examen=$posted[2]."-".$posted[1]."-".$posted[0];
  
$accident=new clDate($date_accident." ".$heure_accident.":00");
$examen=new clDate($date_examen." ".$heure_examen);
$duree=new clDuree($examen->getDifference($accident));

//eko ($accident->getDatetime());
//eko ($examen->getDatetime());

return $duree->getDuree();
}
?>

