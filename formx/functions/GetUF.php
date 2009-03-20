<?php
function GetUF($formx) {
$uf = $formx->getFormVar('Val_IDENT_UF');
$req = new clResultQuery ;
$param = array();
$param['liste']=$uf;
$res = $req -> Execute ( "Fichier", "struct", $param, "ResultQuery" ) ;
return utf8_encode($res['LIBSER'][0]);
}
?>
