<?php
class clSyntheseFacturation
{
	function clSyntheseFacturation()
	{
		
	}



static function getNbPatient(clDate $date1,clDate $date2,$type,$cw=' 1 = 1 ')
{
		$strDate1 = $date1->getDate();
		$strDate2 = $date2->getDate();
		$instrRequete  = "SELECT  count( * ) nb FROM patients_sortis  WHERE dt_sortie  BETWEEN '$strDate1'
			AND '$strDate2'  AND  $cw ";
		$obRequete = new clRequete(BDD, 'patients_sortis', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
		$tabResult = $obRequete->exec_requete($instrRequete, 'tab');
		$nb = (int) $tabResult[0]['nb'] ;
		return $nb ;
}


	static function getStrRequete(clDate $date1,clDate $date2,$type,$reg,$mode)
	{
		$strDate1 = $date1->getDate();
		$strDate2 = $date2->getDate();
		$instrRequete  = "SELECT  count( * ) nb
			FROM `mbtv2`
			WHERE TYPE = '$type'
			AND `DTINS`
			BETWEEN '$strDate1'
			AND '$strDate2'
AND CONTENU LIKE '%$mode%'  " ;
		 if($reg) $instrRequete.=" AND CONTENU LIKE  '$reg'  ";
		return $instrRequete ;
	}


	static function getNb(clDate $date1,clDate $date2,$type,$reg='')
	{

		$obRequete = new clRequete(CCAM_BDD, 'mbtv2', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );

		
		$instrRequete = self::getStrRequete($date1,$date2,$type,$reg,'creation');
		$tabResult = $obRequete->exec_requete($instrRequete, 'tab');
		$nbCreation = (int)  $tabResult[0]['nb'] ;
		$instrRequete = self::getStrRequete($date1,$date2,$type,$reg,'suppression');
		$tabResult = $obRequete->exec_requete($instrRequete, 'tab');
		$nbSuppression = (int) $tabResult[0]['nb'] ;
		return $nbCreation - $nbSuppression ;

	}


	static function getIndicateurs(clDate $date1,clDate $date2)
	{
		$tabIndics = array();
		$tabIndics['nb_ngap_c'] =  self::getNb($date1,$date2,'NGAP','%|C|%');
		$tabIndics['nb_ngap_cs'] =  self::getNb($date1,$date2,'NGAP','%|CS|%');
		$tabIndics['nb_ngap_ami'] =  self::getNb($date1,$date2,'NGAP','%|AMI|%');

		$tabIndics['nb_ccam'] =  self::getNb($date1,$date2,'CCAM');

		$tabIndics['nb_ext'] =  self::getNbPatient($date1,$date2,'  type_destination NOT IN ("H")  ')  ;

		return $tabIndics ;
	}


	static function getDataTransfertsSamu(clDate $date1,clDate $date2)
	{
		$strDate1 = $date1->getDate();
		$strDate2 = $date2->getDate();
		$obRequete = new clRequete(BDD, 'patients_sortis', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
		$requete = "SELECT * FROM `patients_sortis` WHERE `dt_admission` >= '$strDate1' and dt_sortie <= '$strDate2' AND `type_destination` = 'T' AND moyen_transport LIKE '%SMUR%' ";
		eko($requete);
		return $obRequete->exec_requete($requete, 'tab');
	}

	static function getUrlCsvTransfertsSamu(clDate $date1,clDate $date2)
	{
		$data = self::getDataTransfertsSamu($date1, $date2) ;
		return  formxTools::exportsGetCsvFromData($data);
	}


	function getAffichage()
	{
		global $session ;
		$retour = "";
			if( isset( $_POST['ok'] ) )
			{
				$date1=new clDate($_POST['date1']);
				$date2=new clDate($_POST['date2']);
				$tabIndics = self:: getIndicateurs($date1,$date2);
				$retour ="";
				$retour.= "Du ".$date1->getSimpleDate()." au ".$date2->getSimpleDate();
				$retour.= "<br />NGAP C   : ".$tabIndics['nb_ngap_c'];
				$retour.= "<br />NGAP CS : ".$tabIndics['nb_ngap_cs'];
				$retour.= "<br />NGAP AMI : ".$tabIndics['nb_ngap_ami'];
				$retour.= "<br />CCAM : ".$tabIndics['nb_ccam'];
				$retour.= "<br />Sorties sans Hospi : ".$tabIndics['nb_ext'];
				$retour.= "<br /><a href='".'index.php?navi='.$session->genNavi($session->getNaviFull())."'>Retour</a>";
				$retour.= "<br />";
				$retour.= "<br /><a href='".self::getUrlCsvTransfertsSamu($date1, $date2)."'>Transferts SAMU</a>";
			}
			else
			{
				$mod = new ModeliXe ( "CCAM_choix.mxt" ) ;
				$mod -> SetModeliXe ( ) ;
				$mod ->MxText('titre', "Synthèse facturation");
				$mod ->MxAttribut('action', 'index.php?navi='.$session->genNavi($session->getNaviFull()));
				$retour = $mod->MxWrite('1');
			}
			
			return $retour ;
	}

}


?>