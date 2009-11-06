<?php
function Test_AEV_Situation_4($formx) {

// On determine dans quelle situation on se trouve
    $AEV_Type = utf8_decode($formx->getFormVar('AEV_Situation'));


    //if ( (int)$AEV_Type == 4 )
    //  {eko("1");return true;}
    //else
    //  {eko("0");return false;}
    //}
    return ((int)$AEV_Type == 4);
}
?>
