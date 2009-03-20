<?php
function Formulaire_Radio_DEL_Table($formx) {

$id_instance = $formx->getIdInstance();
$idu         = $formx->getVar('ids');

//eko ($id_instance);
//eko ($idu);

if ( $id_instance ) {
	$requete = new clRequete ( BDD, 'radios' ) ;
	$requete->delRecord ( 'id_instance='.$id_instance ) ;
}

return "";
}
?>
