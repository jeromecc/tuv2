<?php
function ListeMois($formx) {

$liste = array(); 

$liste[ utf8_encode("Janvier")]= "Janvier";
$liste[ utf8_encode("Février")]= "Février";
$liste[ utf8_encode("Mars")]= "Mars";
$liste[ utf8_encode("Avril")]= "Avril";
$liste[ utf8_encode("Mai")]= "Mai";
$liste[ utf8_encode("Juin")]= "Juin";
$liste[ utf8_encode("Juillet")]= "Juillet";
$liste[ utf8_encode("Août")]= "Août";
$liste[ utf8_encode("Septembre")]= "Septembre";
$liste[ utf8_encode("Octobre")]= "Octobre";
$liste[ utf8_encode("Novembre")]= "Novembre";
$liste[ utf8_encode("Décembre")]= "Décembre";


return ($liste);
}
?>
