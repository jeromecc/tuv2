<?php
function Formulaire_Colectomie1_MAJ_Table($formx) {

$id_instance = $formx->getIdInstance();
$ids         = $formx->getVar('ids');

// On va supprimer tous les fichiers Dossier_Medical_Colectomie de la table formx
$requete=new clRequete(BDD,TABLEFORMX,$param);
$sql=$requete->delRecord("idformx='Dossier_Medical_Colectomie' and ids='".$ids."'");

return "";
}
?>
