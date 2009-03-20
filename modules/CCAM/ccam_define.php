<?php
// Titre  : Déclaration des constantes (pour la configuration...).
// Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
// Date   : 04 mars 2005
// Module CCAM

// Description : 
// Des constantes sont définies ici, puis utilisées dans le site.
// Ca permet d'externaliser les parties en dur dans le code pour
// une configuration plus facile.

define ('CCAM_URLQRY',MODULE_CCAM."queries/");
define ('CCAM_URLTMP',MODULE_CCAM."template/");
//define (CCAM_IDDOMAINE,getIdDomaine(IDAPPLICATION));

function getIdDomaine($idApplication){
$req=new clResultQuery;
$param[cw]="";
$res=$req->Execute("Fichier","CCAM_get1Domaine",$param,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
$idDomaine=$res[domaine][0];
return $idDomaine;
}
?>
