<?php
function Formulaire_AEV_MAJ_Situation($formx) {

// On efface les formulaires de situation de la personne qui n'ont pas ete valide
// dans TU (presence dans la fenêtre des formulaires) )
$req = new clResultQuery ;
$param = array();

$req = new clRequete(FX_BDD,"formx",$param);
$res = $req->delRecord("ids='".$formx->getVar('ids')."' and idformx like 'Dossier_AEV_Situation%' and (status='I' or status='E')");

return "";
}
?>
