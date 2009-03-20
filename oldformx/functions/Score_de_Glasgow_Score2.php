<?php
function Score_de_Glasgow_Score2($formx) {

$Y = utf8_decode($formx->getFormVar('fusion:Val_ENEURO_Con_Glasgow_Y'));
$V = utf8_decode($formx->getFormVar('fusion:Val_ENEURO_Con_Glasgow_V'));
$M = utf8_decode($formx->getFormVar('fusion:Val_ENEURO_Con_Glasgow_M'));
$Score = $Y+$V+$M;
return "Glasgow:".$Score." Y".$Y." V".$V." M".$M."";
}
?>

