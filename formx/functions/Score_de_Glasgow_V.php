<?php
function Score_de_Glasgow_V($formx) {
$liste = array();
$liste[utf8_encode("5")]= utf8_encode("(V5) Orient&eacute;e");
$liste[utf8_encode("4")]= utf8_encode("(V4) Confuse (d&eacute;sorientation)");
$liste[utf8_encode("3")]= utf8_encode("(V3) Mots inappropri&eacute;s");
$liste[utf8_encode("2")]= utf8_encode("(V2) Soins incompr&eacute;hensibles (Grogne)");
$liste[utf8_encode("1")]= utf8_encode("(V1) Aucune r&eacute;ponse verbale");
return $liste;
}
?>

