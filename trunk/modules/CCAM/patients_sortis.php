<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>TUv2 : Patients sortis</title>
</head>

<body>
<form>
<?
function gen_Affiche_Tableau($tableau){
// Affiche le tableau à 1 dimension passé en paramètre
	reset($tableau);
	$contenu="<table align=\"center\" border =1><tr bgcolor=\"Silver\"><th>Index</th><th>Valeur</th></tr>";
	while (list($key,$val) = each($tableau)){
		$contenu .= "<tr><td>".$key."</td><td>".$val."</td></tr>";
	}
	$contenu .= "</table>";
	echo $contenu;
}

$relocate="/home/www/";
include_once("../../config.php");
include_once("ccam_define.php");

if (isset($valid)){
	list($jour,$mois,$annee)=explode("/",$date);
	$dateSql=$annee."-".$mois."-".$jour;
	unset($paramRq);
	$paramRq[cw]="sort.idpatient=cot.idEvent and sort.dt_sortie like '$dateSql%' and sort.manuel=0 and cot.idDomaine=".CCAM_IDDOMAINE;
	$paramRq[order]="cot.numSejour,cot.cotationNGAP";
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getListeSortis",$paramRq,"ResultQuery");
	//gen_Affiche_Tableau($res[INDIC_SVC]);
	if ($res[INDIC_SVC][2]){
		for ($i=0;isset($res[identifiant][$i]);$i++){
			$numSejour=$res[numSejour][$i];
			$entete[$numSejour]="$numSejour - ".$res[nomu][$i]." ".$res[pren][$i];
			if ($res[type][$i]=="DIAG") $diag[$numSejour].=$res[codeActe][$i].":".$res[libelleActe][$i]." + ";
			else{
				$libelle=$res[libelleActe][$i];
				$intervenant="Adeli ".$res[matriculeIntervenant][$i].": Dr ".$res[nomIntervenant][$i];
				if ($res[cotationNGAP][$i]!=""){
					$code=$res[cotationNGAP][$i];
					$ligneActe[$numSejour].="<li><i>$intervenant * $code - $libelle</i></li>";
				}
				else{
					$code=$res[codeActe][$i];
					$ligneActe[$numSejour].="<li>$intervenant * $code - $libelle</li>";
				}
			}
		}
		
		reset($entete);
		while (list($numSejour,$val)=each($entete)){
			$diag[$numSejour]=substr($diag[$numSejour],0,-3);
			$resultat.="<b>$val - $diag[$numSejour]</b><br>
				$ligneActe[$numSejour]<hr>";
		}
		$resultat.="Ecriture normale : Actes CCAM - <i>Ecriture italique : Actes NGAP</i><br>
			En cas d'intervenants multiples, le second représente l'anesthésiste associé";
	}
	else $resultat="<b><center>L'application ne trouve ni actes, ni diagnostics pour la journée du $date</center></b>";
}
if (!isset($date)){
	$date=date("d/m/Y",mktime(0,0,0,date("n"),date("j")-1,date("Y")));
}
$affichage="<center><h3>Actes effectués sur les patients sortis</h3>
	pour la journée du <input type=text name=date value=\"$date\" size=10> (jj/mm/yyyy)<p>
	<input type=submit name=valid value=OK><p>";
$affichage.="</center>";
$affichage.=$resultat;


echo $affichage;

?>
</form>
</body>
</html>
