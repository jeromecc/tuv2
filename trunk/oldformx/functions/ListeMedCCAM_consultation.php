<?php
function ListeMedCCAM_consultation($formx) {
global $listeMedCCAM;
//$listeMedCCAM = new clCCAMListesComplexes("ListeMédecins");

$liste = array(); 
foreach ( $listeMedCCAM->getListeListes("ListeMédecins", 1, 0, 0, 1 ) as $liste1 => $liste2 ){
  $liste[ utf8_encode($liste1)]= $liste2;
  //$liste[ $liste1]= $liste2;
  }
return ($liste);

}
?>
