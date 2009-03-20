<html>
<head>
	<title>Export données décès ch-hyeres</title>
</head>

<body>
<?
include "fonctions.inc";
$conn=Connect("REF");

//Constitution de la balise Entete au format XML
$nomForm="DECES";
/*$idActeur=475;
$cleDepot="UtXSgA";
$mail="cboulay@ch-hyeres.fr";*/
$idActeur=241;
$cleDepot="RYBEA8";
$mail="nrobert@ch-hyeres.fr";
$arRequis=1;
$cfg_expediteur_alerte="siou_ref";
$chemin="/home/batch/arh/to_be_treated/";

$date_jour=date("Ymd");
$jour=substr($date_jour,6,2);
$mois=substr($date_jour,4,2);
$annee=substr($date_jour,0,4);
$date_file=date("YmdHis");
$date_envoi=date("d/m/Y à H:i:s");
$date_event=date("d/m/Y", mktime(0, 0, 0,$mois,$jour-1,$annee));

$balise_element="\n<entete>";
$balise_element.="\n<idActeur>$idActeur</idActeur>";
$balise_element.="\n<cleActeur>$cleDepot</cleActeur>";
$balise_element.="\n<arRequis>$arRequis</arRequis>";
$balise_element.="\n<mail>$mail</mail>";
$balise_element.="\n</entete>";

//###################Recherche des patients décédés    ######### 	
$requete="select to_char(pat.dtnai,'yyyy') annee_nais,count(*) nb_deces
	from passage pas,patient pat
	where pat.idu=pas.idu 
		  and to_char(pas.dtsor,'dd/mm/yyyy')='$date_event'
		  and pas.modsor='05'
	group by to_char(pat.dtnai,'yyyy')";    
$stmt = OCIParse($conn,$requete); 	
OCIExecute($stmt); 	
$nrows = OCIFetchStatement($stmt,$results);  	
for ($j=0;$j<$nrows;$j++) 	  	
{	(date("Y")-$results[ANNEE_NAIS][$j]>=75)
		?$NbDecesSup75Ans=$NbDecesSup75Ans+$results[NB_DECES][$j]:""; 			
	$NbDeces=$NbDeces+$results[NB_DECES][$j];	
}
($NbDecesSup75Ans=="")?$NbDecesSup75Ans=0:"";
($NbDeces=="")?$NbDeces=0:"";

//Constition du format XML relatif au formulaire DECES
$balise_element.="\n<element>";
$balise_element.="\n<nomForm>$nomForm</nomForm>";
$balise_element.="\n<date_event>$date_event</date_event>";
$balise_element.="\n<NbDeces>$NbDeces</NbDeces>";
$balise_element.="\n<NbDecesSup75Ans>$NbDecesSup75Ans</NbDecesSup75Ans>";
$balise_element.="\n</element>";

//Ecriture du fichier d'export
$nom_fic=$idActeur."_".$date_file.".xml";
$nom_fic_export=$chemin.$nom_fic;
$xml_data="<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>
	\n<result>".$balise_element."\n</result>";
$fp=fopen($nom_fic_export,"w");
if (!fwrite ($fp, $xml_data))
{	echo "pb ecriture fichier xml";}
fclose($fp);

$affichage="<center><h2>Export des données Décès - CH-Hyères</h2></center>
		<u>Date d'export:</u> le $date_envoi<br>
		<u>Contenu exporté :</u> $balise_element<br>
		<u>Fichier d'export :</u> $nom_fic_export";
echo $affichage;
?>
</body>
</html>