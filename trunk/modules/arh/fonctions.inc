<?php
################        Paramètres de connexions
function Connect($database)
{
	switch ($database)
		{
			#Connection REF #
			case "REF" :
				$type="Oracle";
				$cfg_db = "REF";
			    $cfg_user = "SIOU";
			    $cfg_passwd = "SIOU";
			break;
			
			case "HOPI" :
				$type="Oracle";
				$cfg_db = "HOPI";
			    $cfg_user = "HOPI";
			    $cfg_passwd = "HOPI";
			break;
			
			case "REF_DEV" :
				$type="Oracle";
				$cfg_db = "REF";
			    $cfg_user = "DEV";
			    $cfg_passwd = "DEV";
			break;
			
			case "PC_LOLO" :
				$type="MySQL";
				$cfg_Host="102.1.0.23";
			    $cfg_user = "root";
			    $cfg_passwd = "";
			break;
			
			case "BIGFOOT" :
				$type="MySQL";
				$cfg_Host="Bigfoot.ch-hyeres.fr";
			    $cfg_user = "root";
			    $cfg_passwd = "";
			break;
			
		}
		
	switch ($type)
		{
			case "Oracle" :#Connection Oracle #
				$conn=OCILogon($cfg_user, $cfg_passwd, $cfg_db);
			break;
			
			case "MySQL" :#Connection MySQL #
				$conn=mysql_connect($cfg_Host,$cfg_user,$cfg_passwd);
			break;
		}
	return $conn;

}
################################  Dates   #
function age($d1,$d2)
	{

		// retourne un tableau $delta indiquant l'écart entre d1 et d2 avec d1 < à d2
		$t_d1=explode('/',$d1);
		$t_d2=explode('/',$d2);
		
		//jours
		
		if ($t_d1[0]>$t_d2[0])
			{
				$t_d2[0]=$t_d2[0]+30;
				$t_d1[1]=$t_d1[1]+1;
				if ($t_d1[1]>=13)
					{
						$t_d1[2]=$t_d1[2]+1;
					}
			}
		$delta[0]=$t_d2[0]-$t_d1[0];

		// Mois
		if ($t_d1[1]>$t_d2[1])
			{
				$t_d2[1]=$t_d2[1]+12;
				$t_d1[2]=$t_d1[2]+1;
			}
		$delta[1]=$t_d2[1]-$t_d1[1];

		//Ans
		$delta[2]=$t_d2[2]-$t_d1[2];
		//affiche_tableau($delta);
		return $delta;

         /*
        $t_d1=explode('/',$d1);
        $t_d2=explode('/',$d2);
        $diff=  mktime (0,0,0,$t_d2[1],$t_d2[0],$t_d2[2])-mktime (0,0,0,$t_d1[1],$t_d1[0],$t_d1[2]);
        $diff=explode('/',date('d/m/Y',$diff));
        $delta[0]=  $diff[0]-1;
        $delta[1]=$diff[1]-1;
        $delta[2]=$diff[2]-1970;
        //affiche_tableau($delta);
        return $delta;
        */
	}



#################  DEV  #
function Affiche_Tableau($tableau)
{
	reset($tableau);
	$contenu="<table align=\"center\" border =1><tr bgcolor=\"Silver\"><th>Index</th><th>Valeur</th></tr>";
	while (list($key,$val) = each($tableau)) 
	{ 
		$contenu .="<tr><td>".$key."</td><td>".$val."</td></tr>";
	}
	$contenu .= "</table>";
	 echo $contenu; 
} 

function transform_mysql_date_to_date($date_origine)
{ 
	// exploser la date dans un tableau en fonction du motif '/' 
	$tmpdate = explode( "-", $date_origine); 
		
	// on surf le tableau dans l'ordre inverse en admetant que l'année est toujours au début
    for($i=(count($tmpdate)-1);$i>=0;$i--) 
    { 
		// si la valeur est d'un seul chiffre ça rajoute un 0 devant (utile pour les mois ou les jours d'un chiffre)
		 if(strlen($tmpdate[$i])<2) 
            $tmpdate[$i] = "0".$tmpdate[$i]; 
		// on met tout ca dans $tmp_final_date 
        $tmp_final_date .= $tmpdate[$i]; 
		// si on passe un rang on place les séparateurs '-' cher à MySQL 
		// mais pas au dernier rang (0) 
		        if($i>0) 
		            $tmp_final_date .= "/"; 
    } 
	// enfin retourner le résultat 
	return $tmp_final_date; 
} 	

##################   Formulaires    #
//menu("DEST_ATT", $data, $DEST_ATT,0,$reload);
function menu($rub, $items, $defaut="",$size=0,$reload="NON")
{
	if (isset($items))
	{
		reset($items);
		($reload!="NON")?$javascript="onChange=\"SetAction( this.form )\"":$javascript="";
		$menu="\n<select name=\"$rub\" $javascript  size=\"$size\">";
		while(list($cle, $valeur) = each ($items))
		{
			//echo "cle: $cle  -->  $defaut <br>";
			$selected= (stripslashes($cle) == $defaut) ? "selected" : "";
			$menu .="<option value=\"$cle\" $selected>$valeur\n";
		}
		$menu .="\n</select>";
		return $menu;
	}
}

Function destinataires_maltraitance($conn){
// Recherche des destinataires du message
$requete="	select 	VALEUR from URG_PARAM where LISTE='Dest_maltraitance' order by RANG";	
$stmt = OCIParse($conn,$requete);
OCIExecute($stmt);
$nrows = OCIFetchStatement($stmt,$results);
for ($j=0;$j<$nrows;$j++)
	  	{	
			$dest_aff.=$results[VALEUR][$j]."<br>";
			($j!=($nrows-1))?$dest_mail.=$results[VALEUR][$j].",":$dest_mail.=$results[VALEUR][$j];
		}
	$destinataires["AFFICHAGE"]=$dest_aff;
	$destinataires["MAIL"]=$dest_mail;
	return $destinataires;
}

Function geriat_destinataires_maltraitance($conn){
// Recherche des destinataires du message
$requete="	select 	VALEUR from URG_PARAM where LISTE='Geriat_dest_maltraitance' order by RANG";	
$stmt = OCIParse($conn,$requete);
OCIExecute($stmt);
$nrows = OCIFetchStatement($stmt,$results);
for ($j=0;$j<$nrows;$j++)
	  	{	
			$dest_aff.=$results[VALEUR][$j]."<br>";
			($j!=($nrows-1))?$dest_mail.=$results[VALEUR][$j].",":$dest_mail.=$results[VALEUR][$j];
		}
	$destinataires["AFFICHAGE"]=$dest_aff;
	$destinataires["MAIL"]=$dest_mail;
	return $destinataires;
}
function passage($conn,$IDU,$nb_passage,$nb_jours){
	// retourne 1 si plus de $nb_passage dans les $nb_jours derniers jours
	$requete="select count (*) NB_PASS 
				from URG_MAIN_COURANTE 
				where IDU='$IDU'
				and DT_ADM >= SYSDATE-$nb_jours
				";
	$stmt = OCIParse($conn,$requete);
	OCIExecute($stmt);
	$nrows = OCIFetchStatement($stmt,$results);
	($results[NB_PASS][0]>$nb_passage)?$resultat=1:$resultat=0;
	return $resultat;
}

function code_signalement($conn,$CODE_RECOURS){
	// retourne 1 si code recours est dans la liste des codes à surveiller
	$requete="select ID_PARAM
				from URG_PARAM
				where LISTE='Codes_maltraitance'
				and VALEUR='$CODE_RECOURS'
				";
	$stmt = OCIParse($conn,$requete);
	OCIExecute($stmt);
	$nrows = OCIFetchStatement($stmt,$results);
	($results[ID_PARAM][0]>0)?$resultat=1:$resultat=0;
	return $resultat;
}

function geriat_code_signalement($conn,$CODE_RECOURS){
	// retourne 1 si code recours est dans la liste des codes à surveiller pour la gériatrie
	$requete="select ID_PARAM
				from URG_PARAM
				where LISTE='Geriat_codes_maltraitance'
				and VALEUR='$CODE_RECOURS'
				";
	$stmt = OCIParse($conn,$requete);
	OCIExecute($stmt);
	$nrows = OCIFetchStatement($stmt,$results);
	($results[ID_PARAM][0]>0)?$resultat=1:$resultat=0;
	return $resultat;
}

function redac_message($conn,$ID){
	// Retourne un tableau  contenant les différents éléments du message
	// Recherche des infos patient
	$requete="	select 		ID,
							U.IDU,
							U.IDPASS,
							U.ILP,
							NOMU,
							PREN,
							to_char(DTNAI,'dd/mm/yyyy') DTNAI,
							SEXE,
							MODE_ADM,
							to_char(DT_ADM,'dd/mm/yy') DT_ADM,
							to_char(DT_ADM,'HH24:MI') H_ADM,
							MED_URG,
							CATEGORIE_RECOURS,
							MOTIF_RECOURS,
							CODE_GRAV,
							to_char(DT_SORT,'dd/mm/yy HH24:MI') DT_SORT,
							to_char(DT_EXAM,'dd/mm/yy HH24:MI') DT_EXAM,
							MED_TRAIT
					from URG_MAIN_COURANTE U, PATIENT P
					where 	U.ID=$ID
						and	P.IDU=U.IDU
					";	
		
		
	   	$stmt = OCIParse($conn,$requete);
		OCIExecute($stmt);
		$nrows = OCIFetchStatement($stmt,$results);
		
			for ($j=0;$j<$nrows;$j++)
		  	{	// Préparation de l'affichage
				$ID=$results[ID][$j];
				$IDU=$results[IDU][$j];
				$IDPASS=$results[IDPASS][$j];
				$ILP=$results[ILP][$j];
				$NOMU=$results[NOMU][$j];
				$PREN=ucfirst(strtolower($results[PREN][$j]));
				$DTNAI=$results[DTNAI][$j];
				$AGE=age($DTNAI,date("d/m/Y"));
				$SEXE=$results[SEXE][$j];
				$MODE_ADM=strtolower($results[MODE_ADM][$j]);
				$DT_ADM=$results[DT_ADM][$j];
				$H_ADM=$results[H_ADM][$j];
				$MED_URG=$results[MED_URG][$j];
				$MOTIF_RECOURS=$results[MOTIF_RECOURS][$j];
				$CATEGORIE_RECOURS=$results[CATEGORIE_RECOURS][$j];
				$DT_SORT=$results[DT_SORT][$j];
				$DT_EXAM=$results[DT_EXAM][$j];
				$MED_TRAIT=str_replace ("|", chr(13), $results[MED_TRAIT][$j]);
				
				
				#####  Mise en forme des variables d'affichage et gestion des couleurs
				($AGE[2]>=1)?$AGE="$AGE[2] Ans $AGE[1] Mois":$AGE="$AGE[1] Mois $AGE[0] jours";
				
				#######################################################################
		
			}
	// Message
	$message[IDU]=$IDU;
	$message[from]="Terminal_urgences@ch-hyeres.fr";
	$destinataires= destinataires_maltraitance($conn);
	$message[dest_aff]=$destinataires["AFFICHAGE"];
	$message[dest_mail]=$destinataires["MAIL"];
	$message[reply]="lsimon@ch-hyeres.fr";
	
	$message[contenu]="Médecin Urgences : ".$MED_URG." <br> Concerne le patient : "
			.$NOMU." ".$PREN." (".$AGE.") (".$SEXE
			.")<br>	 N° Séjour: ".$IDPASS."<br>IPP: ".$ILP."<br>	Médecin traitant : "
			.$MED_TRAIT
			."<hr>Admis aux urgences le :"
			.$DT_ADM
			." à "
			.$H_ADM
			."<br>Pour "
			.$MOTIF_RECOURS
			." ("
			.$CATEGORIE_RECOURS
			.")<br>Heure Examen: "
			.$DT_EXAM;
	$message[comment]="<i>Ce message a été envoyé automatiquement par un serveur !<br>Pour toute remarque ecrivez à "
				.$message[reply]
				."</i> ";
	// Fin message
	return $message;
}

function geriat_redac_message($conn,$ID){
	// Retourne un tableau  contenant les différents éléments du message
	// Recherche des infos patient
	$requete="	select 		ID,
							U.IDU,
							U.IDPASS,
							U.ILP,
							NOMU,
							PREN,
							to_char(DTNAI,'dd/mm/yyyy') DTNAI,
							SEXE,
							MODE_ADM,
							to_char(DT_ADM,'dd/mm/yy') DT_ADM,
							to_char(DT_ADM,'HH24:MI') H_ADM,
							MED_URG,
							CATEGORIE_RECOURS,
							MOTIF_RECOURS,
							CODE_GRAV,
							to_char(DT_SORT,'dd/mm/yy HH24:MI') DT_SORT,
							to_char(DT_EXAM,'dd/mm/yy HH24:MI') DT_EXAM,
							MED_TRAIT
					from URG_MAIN_COURANTE U, PATIENT P
					where 	U.ID=$ID
						and	P.IDU=U.IDU
					";	
		
		
	   	$stmt = OCIParse($conn,$requete);
		OCIExecute($stmt);
		$nrows = OCIFetchStatement($stmt,$results);
		
			for ($j=0;$j<$nrows;$j++)
		  	{	// Préparation de l'affichage
				$ID=$results[ID][$j];
				$IDU=$results[IDU][$j];
				$IDPASS=$results[IDPASS][$j];
				$ILP=$results[ILP][$j];
				$NOMU=$results[NOMU][$j];
				$PREN=ucfirst(strtolower($results[PREN][$j]));
				$DTNAI=$results[DTNAI][$j];
				$AGE=age($DTNAI,date("d/m/Y"));
				$SEXE=$results[SEXE][$j];
				$MODE_ADM=strtolower($results[MODE_ADM][$j]);
				$DT_ADM=$results[DT_ADM][$j];
				$H_ADM=$results[H_ADM][$j];
				$MED_URG=$results[MED_URG][$j];
				$MOTIF_RECOURS=$results[MOTIF_RECOURS][$j];
				$CATEGORIE_RECOURS=$results[CATEGORIE_RECOURS][$j];
				$DT_SORT=$results[DT_SORT][$j];
				$DT_EXAM=$results[DT_EXAM][$j];
				$MED_TRAIT=str_replace ("|", chr(13), $results[MED_TRAIT][$j]);
				
				
				#####  Mise en forme des variables d'affichage et gestion des couleurs
				($AGE[2]>=1)?$AGE="$AGE[2] Ans $AGE[1] Mois":$AGE="$AGE[1] Mois $AGE[0] jours";
				
				#######################################################################
		
			}
	// Message
	$message[IDU]=$IDU;
	$message[from]="Terminal_urgences@ch-hyeres.fr";
	$destinataires= geriat_destinataires_maltraitance($conn);
	$message[dest_aff]=$destinataires["AFFICHAGE"];
	$message[dest_mail]=$destinataires["MAIL"];
	$message[reply]="lsimon@ch-hyeres.fr";
	
	$message[contenu]="Médecin Urgences : ".$MED_URG." <br> Concerne le patient : "
			.$NOMU." ".$PREN." (".$AGE.") (".$SEXE
			.")<br>	 N° Séjour: ".$IDPASS."<br>IPP: ".$ILP."<br>	Médecin traitant : "
			.$MED_TRAIT
			."<hr>Admis aux urgences le :"
			.$DT_ADM
			." à "
			.$H_ADM
			."<br>Pour "
			.$MOTIF_RECOURS
			." ("
			.$CATEGORIE_RECOURS
			.")<br>Heure Examen: "
			.$DT_EXAM;
	$message[comment]="<i>Ce message a été envoyé automatiquement par un serveur !<br>Pour toute remarque ecrivez à "
				.$message[reply]
				."</i> ";
	// Fin message
	return $message;
}



?>