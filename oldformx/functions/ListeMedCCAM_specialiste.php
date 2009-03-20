<?php
function ListeMedCCAM_specialiste($formx) {
global $listeMedCCAM;
//$listeMedCCAM = new clCCAMListesComplexes("ListeMédecins");

$consultation = utf8_decode($formx->getFormVar('Val_F_CS_Con'));
//eko ("consultation = ".utf8_encode($consultation));
return $listeMedCCAM->getListeItems($consultation, 1, 0,"*AUCUNITEM*",1) ;
}
?>

