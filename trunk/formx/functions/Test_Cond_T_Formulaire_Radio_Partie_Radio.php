<?php
function Test_Cond_T_Formulaire_Radio_Partie_Radio($formx) {

$Val_F_RADIO_CoteDroit  = utf8_decode($formx->getFormVar('Val_F_RADIO_CoteDroit'));
$Val_F_RADIO_CoteGauche = utf8_decode($formx->getFormVar('Val_F_RADIO_CoteGauche'));
$Val_F_RADIO_Centre     = utf8_decode($formx->getFormVar('Val_F_RADIO_Centre'));
$Val                    = utf8_decode($formx->getFormVar('Val_F_RADIO_Test_Validation'));

eko($Val_F_RADIO_CoteDroit);
eko($Val_F_RADIO_CoteGauche);
eko($Val_F_RADIO_Centre);
eko("id=".$Val);

if (  (strcmp($Val_F_RADIO_CoteDroit,"")==0 AND 
       strcmp($Val_F_RADIO_CoteGauche,"")==0 AND 
       strcmp($Val_F_RADIO_Centre,"")==0 ) )

{eko("true");
$formx->setVar('L_Val_F_RADIO_Test_Validation',"");
return true;}
else

{eko("false");
$formx->setVar('L_Val_F_RADIO_Test_Validation',"ok");
return "O";}
}
?>
