<?php
function Formulaire_AEV_MAJ_Formulaire($formx) {

// On efface les formulaires principaux qui n'ont pas ete valide
// dans TU (presence dans la fenêtre des formulaires)
$req = new clResultQuery ;
$param = array();

$req = new clRequete(FX_BDD,"formx",$param);
$res = $req->delRecord("id_instance!='".$formx->getIdInstance()."' and ids='".$formx->getVar('ids')."' and idformx='Formulaire_AEV' and (status='I' or status='E')");

return "";
}
?>
