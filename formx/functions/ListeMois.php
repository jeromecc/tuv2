<?php
function ListeMois($formx) {

$liste = array(); 

$liste[ utf8_encode("Janvier")]= "Janvier";
$liste[ utf8_encode("F�vrier")]= "F�vrier";
$liste[ utf8_encode("Mars")]= "Mars";
$liste[ utf8_encode("Avril")]= "Avril";
$liste[ utf8_encode("Mai")]= "Mai";
$liste[ utf8_encode("Juin")]= "Juin";
$liste[ utf8_encode("Juillet")]= "Juillet";
$liste[ utf8_encode("Ao�t")]= "Ao�t";
$liste[ utf8_encode("Septembre")]= "Septembre";
$liste[ utf8_encode("Octobre")]= "Octobre";
$liste[ utf8_encode("Novembre")]= "Novembre";
$liste[ utf8_encode("D�cembre")]= "D�cembre";


return ($liste);
}
?>
