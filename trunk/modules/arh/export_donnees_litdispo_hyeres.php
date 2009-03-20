<html>
<head>
	<title>Export données lits dispo ch-hyeres</title>
</head>

<body>
<?
include "fonctions.inc";
$baseBD="TAGADA_V4";
$connexion1=mysql_connect("panisse.ch-hyeres.fr","phptagada","ta4chh");

//Constitution de la balise Entete au format XML
$nomForm="LITDISPO";
/*$idActeur=475;
$cleDepot="UtXSgA";
$mail="cboulay@ch-hyeres.fr";*/
$idActeur=241;
$cleDepot="RYBEA8";
$mail="lsimon@ch-hyeres.fr";
$arRequis=1;
$cfg_expediteur_alerte="tagada_v4";
$chemin="/home/batch/arh/to_be_treated/";

$date_jour=date("Ymd");
$jour=substr($date_jour,6,2);
$mois=substr($date_jour,4,2);
$annee=substr($date_jour,0,4);
$date_file=date("YmdHis");
$date_envoi=date("d/m/Y à H:i:s");
$date_event=date("d/m/Y", mktime(0, 0, 0,$mois,$jour,$annee));

$balise_element="\n<entete>";
$balise_element.="\n<idActeur>$idActeur</idActeur>";
$balise_element.="\n<cleActeur>$cleDepot</cleActeur>";
$balise_element.="\n<arRequis>$arRequis</arRequis>";
$balise_element.="\n<mail>$mail</mail>";
$balise_element.="\n</entete>";

//###################Recherche des lits disponibles par uf    ######### 	
$requete="SELECT uf.code, count(*) nb
	FROM tagref , uf, lit 
	WHERE tagref.iduf = uf.iduf and lit.idlit=tagref.idlit 
		and lit.officiel='O' and tagref.idpass='' 
		and uf.code<>'2702'	and uf.code<='3221'
	GROUP BY uf.code";    
$result=mysql($baseBD,$requete);
$Nb3021=$Nb3031=$Nb3043=$Nb3080=$Nb3101=$Nb3221=$Nb2702=$Nb3701=0;
while ($record=mysql_fetch_array($result))
{	$UF=$record[code];
	$NB=$record[nb];
	if ($UF==3021)
	{	$Nb3021=$Nb3021+$NB;}
	elseif ($UF==3031)
	{	$Nb3031=$Nb3031+$NB;}
	elseif ($UF==3043)
	{	$Nb3043=$Nb3043+$NB;}
	elseif ($UF==3080 or $UF==3090 or $UF==3041 or $UF==3061)
	{	$Nb3080=$Nb3080+$NB;}
	elseif ($UF==3101 or $UF==3111)
	{	$Nb3101=$Nb3101+$NB;}
	elseif ($UF==3201 or $UF==3221)
	{	$Nb3221=$Nb3221+$NB;}
}

//Constitution du format XML relatif au formulaire LITSDISPO
$balise_element.="\n<element>";
$balise_element.="\n<nomForm>$nomForm</nomForm>";
$balise_element.="\n<date_event>$date_event</date_event>";
$balise_element.="\n<Autres_disciplines_de_pediatrie_48>$Nb3021</Autres_disciplines_de_pediatrie_48>";
$balise_element.="\n<Reanimation_medico_chirurgicale_43>$Nb3031</Reanimation_medico_chirurgicale_43>";
$balise_element.="\n<Surveillance_continue_47>$Nb3043</Surveillance_continue_47>";
$balise_element.="\n<Medecine_8>$Nb3080</Medecine_8>";
$balise_element.="\n<Chirurgie_41>$Nb3101</Chirurgie_41>";
$balise_element.="\n<Gynecologie_obstetrique_15>$Nb3221</Gynecologie_obstetrique_15>";
$balise_element.="\n<UHCD_20>$Nb2702</UHCD_20>";
$balise_element.="\n<SLD_52>$Nb3701</SLD_52>";
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

$affichage="<center><h2>Export des données List disponibles - CH-Hyères</h2></center>
		<u>Date d'export:</u> le $date_envoi<br>
		<u>Contenu exporté :</u> $balise_element<br>
		<u>Fichier d'export :</u> $nom_fic_export";
echo $affichage;
?>
</body>
</html>
