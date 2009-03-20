<?php
function Init_Formulaire_Pneumopathie($formx) {

$item=utf8_decode($formx->getVar('L_Pneumopathie_Orientation_Motif'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Orientation_Motif',"Boronchite aigue");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Orientation_Traitement'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Orientation_Traitement',"Classes");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Orientation_Adresse'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Orientation_Adresse',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Tabagisme'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Tabagisme',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Tabagisme_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Tabagisme_C',"1");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Vie'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Vie',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Precarite'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Precarite',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Mauvaise'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Mauvaise',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Absorption'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Absorption',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Decompensation'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Decompensation',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Bpco'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Bpco',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Oxygeno'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Oxygeno',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Oxygeno_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Oxygeno_C',"1");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Hepatique'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Hepatique',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Neoplasique'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Neoplasique',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Renale'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Renale',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Cerebro'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Cerebro',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Diabete'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Diabete',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Diabete_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Diabete_C',"");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Comorbidites_Cardiaque'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Comorbidites_Cardiaque',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Toux'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Toux',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Dyspnee'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Dyspnee',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Douleur'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Douleur',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Expectoration'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Expectoration',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Fievre'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Fievre',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Tachycardie'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Tachycardie',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Polypnees'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Polypnees',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Matite'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Matite',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Foyer'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Foyer',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Autres',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Criteres_Autres_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Criteres_Autres_C',"");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_Trouble'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_Trouble',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_Frequence'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_Frequence',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_Systolique'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_Systolique',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_Diastolique'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_Diastolique',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_T1'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_T1',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_T2'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_T2',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_Cardiaque'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_Cardiaque',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Evaluation_Septique'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Evaluation_Septique',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Paraclinique_Pha'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Paraclinique_Pha',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Paraclinique_Ure'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Paraclinique_Ure',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Paraclinique_Na'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Paraclinique_Na',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Paraclinique_Hematocrique'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Paraclinique_Hematocrique',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Paraclinique_Pao'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Paraclinique_Pao',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Paraclinique_Pleural'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Paraclinique_Pleural',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Therapeutique_Anti'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Therapeutique_Anti',"Classes");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Therapeutique_Oxy'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Therapeutique_Oxy',"1");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Therapeutique_Aerosol'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Therapeutique_Aerosol',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Therapeutique_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Therapeutique_Autres',"Non");
  }

$item=utf8_decode($formx->getVar('L_Pneumopathie_Therapeutique_Autres_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Pneumopathie_Therapeutique_Autres_C',"");
  }

return "O";
}
?>
