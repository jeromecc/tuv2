<?php
function getAge2($formx) {

global $patient ;
global $tool;

$dateToday = new clDate();
$dateNaiss  = new clDate($patient->getDateNaissance());

$ageSec = $dateToday->getDifference($dateNaiss);

return floor($ageSec/(365.25*24*3600)).' ans';


}

?>
