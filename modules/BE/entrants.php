<?php
//Retourne les entrants de  moins de 30 jours du Terminal des Urgences
// L. SIMON 16/03/2004


$rep_totem_inc="../../totem_inc/";
include ($rep_totem_inc."function_loader.inc");
include ($rep_totem_inc."listmaker.php3");
include ($rep_totem_inc."result_query.php");
//////////////////////////////////////////////////
$now=date("d/m/Y H:i:s");
$param[]="";
$O_entrants=new Result_query;
$R_entrants=$O_entrants -> Use_Query("Patients 2702 sortis urgences non sortis pastel",$param);
$liste_anomalies = new ListMaker('/home/www/terminal_urgences/template/tpl_be.html');
$liste_anomalies->addUserVar("TITRE","Anomalies constatées sur l'UF 2702");
$liste_anomalies->addUserVar("STRING","Aucune anomalie constatée pour l'UF 2702");
$anomalies=$liste_anomalies->get_result_query($R_entrants);
//Newfct(gen_affiche_tableau,$R_entrants[NOMU]);

$O_2702=new Result_query;
$R_2702=$O_2702 -> Use_Query("Patients 2702 présents aux urgences",$param);
$liste_2702 = new ListMaker('/home/www/terminal_urgences/template/tpl_be.html');
$liste_2702->addUserVar("TITRE","Patients présents en UF 2702 ($now)");
$liste_2702->addUserVar("STRING","Aucun patient présent en UF 2702 pour le moment");


$show_2702=$liste_2702->get_result_query($R_2702);


//Newfct(gen_affiche_tableau,$R_entrants[NOMU]);

echo "<table>
             <tr>
                  <td>$show_2702 <hr></td>
	   		 </tr>

	   		 <tr>
                  <td>$anomalies</td>
	   		 </tr>

	   		 </table>" ;


?>
