<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clTuFormTriggers
 *
 * @author ecervetti
 */
class clTuFormxTrigger {
    //put your code here
	

	//charge les triggers dans /formx/triggers
	function clTuFormxTrigger($idTrigger)
	{
		$this->idTrigger = addslashes(stripslashes($idTrigger)) ;
		$this -> sxDef = simplexml_load_file(formxSession::getInstance()->getFxTriggerPath().$idTrigger.'.xml');
	}

	function isActive()
	{
		return $this->getIdEnquete() ;
	}

/**
 * regarde si pas de restriction de p?riode de date
 * @return <type>
 */
	function isValide()
	{
		if( $this->getDxDefAttributeVal('dateStart') )
		{
			$dateStart = new clDate($this->getDxDefAttributeVal('dateStart'));
			if(clDate::getInstance()->earlierThan($dateStart))
			{
				return false ;
			}
		}
		if( $this->getDxDefAttributeVal('dateEnd') )
		{
			$dateEnd = new clDate($this->getDxDefAttributeVal('dateEnd'));
			if(clDate::getInstance()->laterThan($dateEnd))
			{
				return false ;
			}
		}
		return true ;
	}

	function hasFunc()
	{
		if( $this->getDxDefAttributeVal('func') )
			return true ;
		return false ;
	}

	function isBlocking()
	{
		if( $this->getDxDefAttributeVal('blocking') )
			return true ;
		return false ;
	}

	function launchFunc($objPatient)
	{
		$fonction = $this->getDxDefAttributeVal('func') ;
		try
		{
			require(URLLOCAL.'formx/triggers/functions/'.$fonction.'.php');
			eval(' $r = '.$fonction.'($objPatient);' );
		}
		catch(Exception $e)
		{
			//eko("Problème lors du chargement de la fonction $fonction ");
		}
		return false ;
	}


	function isOnOut()
	{
		if( $this->getDxDefAttributeVal('onOut') )
			return true ;
		return false ;
	}

	function isActivable()
	{
		//si pas bonne p?riode
		if( ! $this->isValide() )
			return false ;
		//si autostart
		if( $this->getDxDefAttributeVal('autoStart') or $this->getDxDefAttributeVal('autostart') )
			return false ;

		return true ;
	}

	function isClosable()
	{
		//si pas bonne p?riode
		if( ! $this->isValide() )
			return false ;
		//si autoclose
		if( $this->getDxDefAttributeVal('obligDays') )
			return false ;


		return true ;
	}
	
	function isAutostart()
	{
		if( $this->getDxDefAttributeVal('autostart') or $this->getDxDefAttributeVal('autoStart')  )
			return true ;

		return false ;
	}

	function isPassageLinked()
	{
		if( $this->getDxDefAttributeVal('passageLinked') )
			return true ;
		return false ;
	}

	function isAutoclose()
	{
		if( $this->getDxDefAttributeVal('autoclose') )
			return true ;
		if( $this->getDxDefAttributeVal('maxDays') )
			return true ;
		if( $this->getDxDefAttributeVal('obligDays') )
			return true ;

		return false ;
	}


	function getCondition()
	{
		foreach($this->getSxDef()->trigger as $sxTrigger)
		{
			foreach($sxTrigger->cond as $sxCond)
			{
				return new clTuFormxTriggerCondition($this,$sxCond) ;
			}
		}
		return null ;
		//throw new Exception("pas trouve de balise cond dans le trigger");
	}

	function mustBegin()
	{
		if (  $this->isAutostart()  &&  $this->isValide() && ! $this->isActive() && ! $this->hasBeenPreviouslyLaunched() )
			return true ;
		return false ;
	}

	function mustExportRegularly()
	{
		if (  $this->isActive() &&  $this->getDxDefAttributeVal('exportRegularly') )
			return true ;
	}

	function getExportRegularlyBackNumberDays()
	{
		return $this->getDxDefAttributeVal('exportRegularly');
	}

	function mustClose()
	{
		if (  $this->isAutoclose()  &&  $this->isActive() )
		{
			$dateClose =self::getDebutEnquete($this->getIdEnquete());
			if( $this->getDxDefAttributeVal('maxDays') )
				$dateClose->addDays($this->getDxDefAttributeVal('maxDays') ) ;
			else if( $this->getDxDefAttributeVal('obligDays') )
				$dateClose->addDays($this->getDxDefAttributeVal('obligDays') ) ;
		
			if ( $this->getDxDefAttributeVal('dateEnd') )
				$dateClose = new clDate($this->getDxDefAttributeVal('dateEnd')) ;

			if( $dateClose->earlierThan( clDate::getInstance() ) )
				return true ;
		}
		return false ;
	}


/**
 * renvoie l'id de l'enquete en cours si active
 * @return int
 */
function getIdEnquete()
{
	
		$now = new clDate();
		$requete = "SELECT * FROM enquetes WHERE  id_trigger = '".$this->idTrigger."' AND date_debut <= '".$now->getDatetime()."' AND ( date_fin = '0000-00-00 00:00:00' OR  date_fin > '".$now->getDatetime()."' )  ORDER BY date_debut DESC"   ;
		$obRequete = new clRequete(BDD,'enquetes');
		$res = $obRequete->exec_requete($requete, 'tab');

		if(count($res) > 0 )
			return $res[0]['id_enquete'] ;

		return false ;
}

//hasBeenPreviouslyLaunched
function hasBeenPreviouslyLaunched()
{
	$requete = "SELECT * FROM enquetes WHERE  id_trigger = '".$this->idTrigger."' "   ;
	$obRequete = new clRequete(BDD,'enquetes');
		$res = $obRequete->exec_requete($requete, 'tab');
		if(count($res) > 0 )
			return true ;
		return false ;
}







/**
 *
 * @param <type> $idEnquete
 * @return clDate
 */
static function getDebutEnquete($idEnquete)
{
	$tabEnquete = self::getTabEnquete($idEnquete);
	return new clDate($tabEnquete['date_debut']);
}

/**
 *
 * @param <type> $idEnquete
 * @return clDate
 */
static function getFinEnquete($idEnquete)
{
	$tabEnquete = self::getTabEnquete($idEnquete);
	return new clDate($tabEnquete['date_fin']);
}

	function getIdTrigger()
	{
		return $this->idTrigger ;
	}

/**
 * renvoie la definition simpleXml du trigger
 * @return SimpleXMLElement
 */
	function getSxDef()
	{
		return $this -> sxDef  ;
	}

	function getIdFormx()
	{
		return $this->getDxDefAttributeVal('idForm');
	}

	function start()
	{
		$this->setStartTime(clDate::getInstance());
	}

	function close()
	{
		//print "fermeture" ;
		//die ;

		$idEnquete = $this->getIdEnquete() ;
		
		$this->setEndTime( clDate::getInstance() );

		
		if( $this->getDxDefAttributeVal('upload') )
		{
			//print " enttre dans export" ;
			//die ;

			$this->export($idEnquete);
		}
		else
		{
			//print "pas export" ;
			//die ;
		}
	}

	function export($idEnquete)
	{
			global $options ;
			global $errs ;

			//eko("entree dans export ") ;
			//return ;
			//die ;

			//print "vraie entree dans export" ;
			//die ;

			//eko($this->getIdEnquete());
			//eko($this->getIdFormx());

			$dateD =  $this->getDebutEnquete($idEnquete) ;
			//eko($dateD->getSimpleDate());
			//print "date de debut pour id ".$idEnquete.": ".$dateD->getSimpleDate() ;
			//die ;

			$dateF = new clDate() ;
			//$idFormx = $this->getIdFormx();

			$data = array() ;
			$nomFic = '' ;
			self::getDataExport($this,$dateD,$dateF,$data,$nomFic);

			//set_time_limit(0);
			//ini_set('memory_limit','512M');
			//$strDate1 = str_replace(array(' ',':'), array('_','-'), $dateD->getDatetime());
			//$strDate2 = str_replace(array(' ',':'), array('_','-'), $dateF->getDatetime());
            //$nomFic = 'etab_'.$options->getOption('RPU_IdActeur').'_enquete_'.formxTools::strGetIdAtomiqueFx($idFormx).'_du_'.$strDate1.'_au_'.$strDate2.'.csv';

            //if( $this->isPassageLinked() )
            //    $tabOptions = array('firstColsFunc'=>'clGestFormxTriggers::genTabinfoIdPassage','firstColsFuncArgField'=>'id_passage');
            //else
            //    $tabOptions = array() ;

			//$data = clFoRmXtOoLs::exportsGetTabIdform($idFormx, $tabOptions + array( 'basic'=>true ,'cw' => " dt_creation <= '".$dateF->getDatetime()."' AND status IN ('F','H') AND dt_creation >= '".$dateD->getDatetime()."'   " ));
            //var_dump($data);
			//$tab = array('cw' => " dt_creation <= '".$dateF->getDatetime()."' AND status IN ('F','H') AND dt_creation >= '".$dateD->getDatetime()."'   " ) ;
			//eko($tab['cw'] );

			$localUrlFic = clFoRmXtOoLs::exportsGetCsvFromData($data,$nomFic,array('local_access'=>true)) ;

			try {
				XhamUpdater:: sendFtpData($localUrlFic,'enquetes');
				eko("depot ftp de $localUrlFic ok");
				//die ;
			} catch (Exception $e) {
				eko( "erreur".$e );
				$errs->addErreur($e);
			}
			//print "fin"; die ;
	}


	public function exportRegularly()
	{
			global $errs ;
			$dateD =  clDate::getInstance()->addDays('-'.$this->getExportRegularlyBackNumberDays());
			$dateF = new clDate() ;
			$data = array() ;
			$nomFic = '' ;
			self::getDataExport($this,$dateD,$dateF,$data,$nomFic);
			$localUrlFic = clFoRmXtOoLs::exportsGetCsvFromData($data,$nomFic,array('local_access'=>true)) ;
			try {
				XhamUpdater:: sendFtpData($localUrlFic,'enquetes');
				eko("depot ftp de $localUrlFic ok");
				//die ;
			} catch (Exception $e) {
				eko( "erreur".$e );
				$errs->addErreur($e);
			}
	}


	static public function getDataExport($trigger,$dateD,$dateF,& $data, &$nomFic)
	{
			global $options ;
			$idFormx = $trigger->getIdFormx();
			set_time_limit(0);
			ini_set('memory_limit','512M');
			$strDate1 = str_replace(array(' ',':'), array('_','-'), $dateD->getDatetime());
			$strDate2 = str_replace(array(' ',':'), array('_','-'), $dateF->getDatetime());
            $nomFic = 'etab_'.$options->getOption('RPU_IdActeur').'_enquete_'.formxTools::strGetIdAtomiqueFx($idFormx).'_du_'.$strDate1.'_au_'.$strDate2.'.csv';

            if( $trigger->isPassageLinked() )
                $tabOptions = array('firstColsFunc'=>'clGestFormxTriggers::genTabinfoIdPassage','firstColsFuncArgField'=>'id_passage');
            else
                $tabOptions = array() ;

            $data = clFoRmXtOoLs::exportsGetTabIdform($idFormx, $tabOptions + array( 'basic'=>true , 'cw' => " dt_creation <= '".$dateF->getDatetime()."' AND status IN ('F','H') AND dt_creation >= '".$dateD->getDatetime()."'   " ));
	}

	function getDxDefAttributeVal($id)
	{
		foreach( $this->getSxDef()->trigger as $trigger )
		{
			return (string) $trigger[$id] ;
		}
		return false ;
	}


	function getType()
	{
		return $this->getDxDefAttributeVal('type');
	}

	function getNomEnquete()
	{
		return $this->getDxDefAttributeVal('title');
	}

	function setStartTime($date)
	{
		if(  $this->isActive() ) return true ;
		$data = array('id_trigger' => $this->getIdTrigger() ,  'date_debut' =>$date->getDatetime() );
		$obRequete = new clRequete(BDD,'enquetes',$data);
		$obRequete->addRecord();
		return true ;
	}

	function setEndTime($date)
	{
		if( !  $this->isActive() ) return false ;
		$data = array('id_trigger' => $this->getIdTrigger() ,  'date_fin' =>$date->getDatetime() );
		$obRequete = new clRequete(BDD,'enquetes',$data);
		$obRequete->updRecord(' id_enquete = '. $this->getIdEnquete() ) ;
		return false ;
	}

/**
 *Attention, renvoie toutes les id, m?me celles qui sont expir?es
 * @return <type>
 */
	private static function listTriggers()
	{
		$tabTriggers = array() ;
		$di = new DirectoryIterator(  formxSession::getInstance()->getFxTriggerPath() );
		foreach($di as $file )
		{
			if($file->isFile() and preg_match('/^(.*).xml$/',$file->getFilename(),$matchTab) )
			{
				$tabTriggers[] =  $matchTab[1] ;
			}
		}
		return $tabTriggers ;
	}

/**
 *Retourne tous les triggers d'enquetes disponibles en ce moment
 * @return <type>
 */
	static function getTriggers()
	{
		$tabOut = array() ;
		foreach(self::listTriggers() as $idTrigger)
		{
			$trigger = new clTuFormxTrigger($idTrigger);
			if($trigger->isValide() )
				$tabOut[] = $trigger ;
		}
		return $tabOut ;
	}

	/**
	 * retourne tous les triggers d'enquetes existants hors tests quelconques
	 */
	static function getAllTriggers()
	{
		$tabOut = array() ;
		foreach(self::listTriggers() as $idTrigger)
		{
			$tabOut[] = new clTuFormxTrigger($idTrigger);
		}
		return $tabOut ;
	}



/**
 * renvoie tous les triggers d'enquetes en cours
 * @return clTuFormxTrigger[]
 */
	static function getTriggersActive()
	{
		$tabOut = array() ;
		foreach(self::listTriggers() as $idTrigger)
		{
			$trigger = new clTuFormxTrigger($idTrigger);
			if($trigger->isActive() )
				$tabOut[] = $trigger ;
		}
		return $tabOut ;
	}






	//renvoie tous les triggers activés d'un type particulier
	static function getTriggersFromType($type)
	{
		$res = array();
		foreach(self::getTriggers() as $trigger)
		{
			if($trigger->getType() ==$type)
			{
				$res[] = $trigger ;
			}
		}
		return $res ;
	}



	static function getTabEnquetesFinies()
	{
		$tab = array();
		$now = new clDate();
		$requete = "SELECT * FROM enquetes WHERE  date_fin <= '".$now->getDatetime()."' AND  date_fin != '0000-00-00 00:00:00'  ORDER BY date_fin DESC "   ;
		$obRequete = new clRequete(BDD,'enquetes');
		return $obRequete->exec_requete($requete, 'tab') ;
	}

	static function getTabEnquete($idEnquete)
	{
		$idEnquete = (int) $idEnquete ;
		$requete = "SELECT * FROM enquetes WHERE  id_enquete = $idEnquete "   ;
		$obRequete = new clRequete(BDD,'enquetes');
		$tabreq = $obRequete->exec_requete($requete, 'tab') ;
		return $tabreq[0];
	}




	//regarde s'il y a des triggers en sortie patient (et verifie s'ils ne sont pas déja remplis  pour ce passage )
	static function isTriggerOnOut($patient)
	{
		//test et mise en cache
		return false  ;
	}


	//regarde s'il y a des trigger de type diag activ?s
	static function isTriggersActive()
	{
		return (count(self::getTriggersActive())?true:false) ;
	}



	/**
	 * renvoie un objet watcher sur le patient
	 * @param <type> $patient
	 * @return clTuFormxTriggerWatcher
	 */
	static function getWatcher($patient)
	{
		return clTuFormxTriggerWatcher::getInstance($patient);
	}

	static function onceByDay()
	{
		$filename = URLLOCAL.'var/last_date_check_triggers.txt' ;
		$oldDate = null ;
		if ( file_exists($filename) )
		{
			$oldDate = new clDate( file_get_contents($filename) ) ;
		}
		else
		{
			$oldDate = clDate::getInstance()->addDays(-1);
		}
		$dateToday = clDate::makeDateToday() ;
		file_put_contents($filename, $dateToday->getDate());

		if( $dateToday->getDifference($oldDate) > 0 )
		{
			//eko()
			eko("une fois par jours");
			return true ;
		}
		return false ;
	}


	/**
	 * exectute chaque minute,
	 * verifie si des triggers sont à cloturer ou à démarrer
	 */
	static function crontab()
	{
		

		foreach( self::getAllTriggers() as $trigger )
		{
			// print "<br />analyse ".$trigger->idTrigger ;
			if( $trigger->mustBegin() )
			{
				$trigger->start();
			}
			if( $trigger->mustClose() )
			{
				$trigger->close();
			}
			if(  $trigger->mustExportRegularly() && self::onceByDay() )
			{
				$trigger->exportRegularly();
			}

			//clOptions::logEtatOptions() ;
		}
	}
	
}


class clTuFormxTriggerCondition
{

function __construct($trigger,$sxElement)
{
	$this->sxDef = $sxElement ;
	$this->trigger = $trigger ;
}

function getTrigger()
{
	return $this->trigger ;
}

function getSxDef()
{
	return $this->sxDef ;
}

function getType()
{
	return $this->sxDef['type'];
}

function getAttributeCond($att)
{
    return $this->sxDef[$att];
}

/**
 * regarde si la cond (supposée de type diag) contient ce code diag comme déclencheur
 * @param string $codeDiagPatient
 * @return bool
 */
	function hasDiag($codeDiagPatient)
	{
		foreach( $this->getSxDef()->diag as $diag )
		{
			$diag1 = str_replace(array('.','+'), array('',''), $diag['code']);
			$diag2 = str_replace(array('.','+'), array('',''), $codeDiagPatient);
			if( $diag1 == $diag2 || ( $diag2 && $diag1=='all' ) )
			{
				return true ;
			}
		}
		return false ;
	}


    function hasMotif($codeMotifPatient)
    {
		foreach( $this->getSxDef()->motif as $motif )
		{
			$motif1 = str_replace(array('.','+'), array('',''), $motif['code']);
			$motif2 = str_replace(array('.','+'), array('',''), $codeMotifPatient);
			if( $motif1 == $motif2 || ( $motif2 && $motif1=='all' ) )
			{
				return true ;
			}
		}
		return false ;
	}

	function hasCcmu($ccmuPatient)
	{
		foreach( $this->getSxDef()->ccmu as $ccmu )
		{
			if ( ( $ccmu['val'] == $ccmuPatient ) || ( $ccmuPatient && $ccmu['val'] == 'all' ) )
			{
				return true ;
			}
		}
		return false ;
	}

/**
 * regarde si la cond (supposée de type acte) contient ce code acte comme déclencheur
 * @param array $tabActesPatient
 * @return bool
 */
	function hasActes($tabActesPatient)
	{
		foreach( $this->getSxDef()->acte as $sxActe)
		{
			foreach($tabActesPatient as $codeActePatient)
			{
				if( $codeActePatient == $sxActe['code']  ||  ( $codeActePatient && $sxActe['code']  == 'all' ) )
				{
					return true ;
				}
			}
		}
		return false ;
	}

	function hasRegExp(clPatient $patient)
	{
	     $val = $patient->getInformation(  (string) $this->getAttributeCond('item') ) ;
	     $reg =  (string) $this->getSxDef() ;
	    if( $val and ereg($reg, $val ))
	    {
		return true ;
	    }
	    return false ;
	}

	function getFirstSubCond()
	{
		foreach( $this->getSxDef()->cond as $sxCond )
		{
			return new clTuFormxTriggerCondition($this->getTrigger(),$sxCond) ;
		}
	}

	function getSecondSubCond()
	{
		$isFirstLoop = true ;
		foreach( $this->getSxDef()->cond as $sxCond )
		{
			if( $isFirstLoop )
			{
				$isFirstLoop = false ;
				continue ;
			}
			return new clTuFormxTriggerCondition($this->getTrigger(),$sxCond) ;
		}
	}

    function getTabSubConds()
    {
        $tabConds = array() ;
        foreach( $this->getSxDef()->cond as $sxCond )
		{
            $tabConds[] = new clTuFormxTriggerCondition($this->getTrigger(),$sxCond) ;
        }
        return $tabConds ;
    }

	function getDxDefAttributeVal($id)
	{
		return $this->sxDef[$id] ;
	}
	
}


class clTuFormxTriggerWatcher
{
	function __construct($patient)
	{
		$this->patient = $patient ;
		//on instancie les variables session si c'est la toute toute première fois
		if( ! isset( $_SESSION['tuFxTriggersWatcher']   ))
			$_SESSION['tuFxTriggersWatcher'] = array() ;

		if( ! isset( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]   ))
			$_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ] = array() ;

		if( ! isset( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers']   ))
			$_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'] = array() ;

	}
/**
 *
 * @return clPatient
 */
	function getPatient()
	{
		return $this->patient ;
	}

	//négation de :
	//est ce que le patient est concerné par le formulaire sans que ce formulaire dans un etat F ou H
	//
	function isElligibleAndNotCompleted( clTuFormxTrigger $trigger)
	{
		return	$this -> isElligible( $trigger,  array('checkLaunchers'=>true,'etatsFormx'=> array('F','H') ) ) ;
	}


	//Regarde si on peut déclencher le trigger pour le patient en cours
	//$options = array('checkLaunchers'=>true,'etatsFormx'=> array('I','E','F','H') ) ;
	//ce sont les etats pour lesquels on considere que l'eligibilité est nulle
	function isElligible( clTuFormxTrigger $trigger,$options='')
	{
		if( ! $options )
		{
			$options = array('checkLaunchers'=>true,'etatsFormx'=> array('I','E','F','H') ) ;
		}
		//si  l'option checkLaunchers est active : est-ce que le trigger est déjà programmé ?
		if (  $options['checkLaunchers']  &&  $this->isLaunching($trigger->getIdTrigger() ) )
		{
			return false ;
		}

		return $this->checkCond($trigger->getCondition() , $options );	
	}

    function multiOr($tabConds,$options)
    {
        foreach($tabConds as $cond)
        {
			if($this->checkCondRecursive($cond,$options))
                return true ;
        }
        return false ;
    }

    function multiAnd($tabConds,$options)
    {
        foreach($tabConds as $cond)
        {
            if( ! $this->checkCondRecursive($cond,$options))
                return false ;
        }
        return true ;
    }


	private function checkCondRecursive(clTuFormxTriggerCondition $condition,$options)
	{
		if( ! $condition )
			return true ;

		switch($condition->getType())
		{
		case 'and':
			return $this->multiAnd($condition->getTabSubConds(), $options) ;
		case 'or':
            return $this->multiOr($condition->getTabSubConds(), $options) ;
		case 'not':
			return ! $this->checkCondRecursive($condition->getFirstSubCond(),$options) ;
		case 'xor':
			return  $this->checkCondRecursive($condition->getFirstSubCond(),$options) XOR  $this->checkCond( $condition->getSecondSubCond() ,$options) ;
		case 'ccmu':
			//est-ce que le diagnostic du patient est concerné par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
            if($condition->hasCcmu($this->getPatient()->getCCMU())  )
			{
				return true ;
			}
			return false ;
		case 'diag':
			//est-ce que le diagnostic du patient est concerné par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
			if($condition->hasDiag($this->getPatient()->getCodeDiagnostic()) )
			{
				return true ;
			}
			return false ;
		case 'motif':
			//est-ce que le motif du patient est concerné par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
			if($condition->hasMotif($this->getPatient()->getCodeRecours())  )
			{
				return true ;
			}
			return false ;
		case 'acte':
			//est-ce que les actes du patient sont concernés par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
			if( $condition->hasActes($this->getPatient()->getTabActes()  )   )
			{
				return true ;
			}
			return false ;
		case 'regexp':
			if( $condition->hasRegExp($this->getPatient()  )   )
			{
				return true ;
			}
			return false ;
		case 'medecin':

			//Est-ce que le patient a un médecin urgentiste affecté ? Est-ce que ce médecin n'a pas déjà un formulaire instancié pour ce passage ?
			if(	$this->getPatient()->getMatriculeMedecin()
				&&
				( count( formxTools::exportsGetTabIdformFilterValue	( $condition->getTrigger()->getIdFormx() , 'id_medecin', $this->getPatient()->getMatriculeMedecin(),array('etat' => array('F','H') ) )   )   == 0 )
				&&
				count(formxTools::exportsGetTabIdsIdformFilterValue	( $this->getPatient()->getIDU() , $condition->getTrigger()->getIdFormx() , 'id_medecin', $this->getPatient()->getMatriculeMedecin(), array('etat' => $options['etatsFormx'] )  ) ) == 0   )
			{
				
				//eko(count(formxTools::exportsGetTabIdformFilterValue( $condition->getTrigger()->getIdFormx() , 'id_medecin', $this->getPatient()->getMatriculeMedecin(),array('etat' => $options['etatsFormx'] )   )  ));
				return true ;
			}
			return false ;
		case 'ide':

			//Est-ce que le patient a un médecin urgentiste affecté ? Est-ce que ce médecin n'a pas déjà un formulaire instancié pour ce passage ?
			if(	$this->getPatient()->getIDE()
				&&
				( count( formxTools::exportsGetTabIdformFilterValue	( $condition->getTrigger()->getIdFormx() , 'id_ide', $this->getPatient()->getIDE(),array('etat' => array('F','H') ) )   )   == 0 )
				&&
				count(formxTools::exportsGetTabIdsIdformFilterValue	( $this->getPatient()->getIDU() , $condition->getTrigger()->getIdFormx() , 'id_ide', $this->getPatient()->getIDE(), array('etat' => $options['etatsFormx'] )  ) ) == 0   )
			{

				//eko(count(formxTools::exportsGetTabIdformFilterValue( $condition->getTrigger()->getIdFormx() , 'id_medecin', $this->getPatient()->getMatriculeMedecin(),array('etat' => $options['etatsFormx'] )   )  ));
				return true ;
			}
			return false ;
		default :
			return false ;
		}
	}


	private function checkCond(clTuFormxTriggerCondition $condition,$options)
	{
		if( ! $condition )
			return true ;

		switch($condition->getType())
		{
		case 'and':
		case 'or':
		case 'not':
		case 'xor':
		case 'ccmu':
		case 'diag':
		case 'motif':
		case 'acte':
		case 'regexp':
			if ( ! $this->getpatient()->hasFormxPassage($condition->getTrigger()->getIdFormx(),array('etat' => $options['etatsFormx'] ) ) )
			{
				//eko("formulaire non trouve, tests des conditions de declenchement");
				return $this->checkCondRecursive($condition , $options) ;
			}
			else
			{
				//eko("le formulaire ".$condition->getTrigger()->getIdFormx()." a été trouvé dans un état accepté dans les formx ".implode(',',$options['etatsFormx']) );
				return false ;
			}
		default :
			return $this->checkCondRecursive($condition , $options) ;
		}
	}

	//on regarde si le patient est elligible à des trigger diags, si c'est le cas on marque le déclenchement du trigger pour qu'il s'affiche des que l'affichage est dispo
	function launchTriggers($typeAppel='onPresent')
	{
		global $errs;
		//est-ce qu'il y a des enquetes  en cours ?
		if ( !  clTuFormxTrigger::isTriggersActive() )
			return false ;
		//pour chaque enquete
		foreach( clTuFormxTrigger::getTriggersActive() as $trigger )
		{
		    //eko("test si la session est concernée par le trigger ".$trigger->getIdTrigger());
			//le patient est il elligible ? Si en sortie, est-ce bien un trigger de sortie ?
			if(  ( ! $trigger ->isOnOut() or ( $typeAppel == 'onOut' && $trigger ->isOnOut()  ) ) and $this->isElligible($trigger) )
			{
			    //eko("oui");
				//appel de fonction
				if( $trigger->hasFunc() )
				{
					$trigger->launchFunc($this->getPatient()) ;
				}
				else
				{
					//on le marque comme à afficher
					//eko("on pose le marqueur ".$trigger->getIdTrigger()."type appel =$typeAppel " );
					//$errs->whereAmI();
					$this->markLaunching($trigger->getIdTrigger() );
				}
			}
			else
			{
			    //eko("non");
			}

		}
	}

	function launchTriggersOnOut()
	{
		$this->launchTriggers('onOut');
	}




	function markLaunching($idTriggerAsk)
	{
		$_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'][] = $idTriggerAsk ;
	}

	function delLaunching($idTriggerAsk)
	{
		//eko("on enleve le marqueur $idTriggerAsk ");

		foreach ( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'] as $index => $idTrigger )
		{
			if ( $idTriggerAsk == $idTrigger )
				unset($_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'][$index] );
		}

	}

	function isTriggersWaitingForLauch()
	{
	    return (bool)  count($_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers']);
	}

	function isLaunching($idTriggerAsk)
	{
		foreach ( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'] as $idTrigger )
		{
			if ( $idTriggerAsk == $idTrigger )
			{
			    return true ;
			}
				
		}
		return false ;
	}


	function getHtml()
	{

		//if( formxTools::manipIsFormPresent() )
		//	return '';
		foreach ( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'] as $idTrigger )
		{
			//quoi qu'il arrive par la suite, le formulaire ne sera plus à lancer
			$this->delLaunching($idTrigger);
			
			$trigger = new clTuFormxTrigger($idTrigger);

			//on reteste si entre temps la condition est toujours valide (autre acces parallele par exemple) , mais sans tester si le launcher est déjà activé cette fois ci
			if( $this->isElligible($trigger,array('checkLauchers' => false )))
			{
				//eko("affichage d'un trigger marqué $idTrigger");
				$formx = new clFoRmX_manip($this->getPatient());
				$formx->loadForm($trigger->getIdFormx());
				$formx->initInstance();
				$formx->genAffichage();
				return $formx->getAffichage();
			}
		}
		return '' ;
	}

	function isTriggersOnOut()
	{

		//est-ce qu'il y a des enquetes  en cours ?
		if ( !  clTuFormxTrigger::isTriggersActive() )
			return false ;
		//pour chaque enquete
		foreach( clTuFormxTrigger::getTriggersActive() as $trigger )
		{
			// Est-ce un trigger de sortie ?  Concerne-t-il le patient ?
			if( $trigger ->isOnOut() && $this->isElligible($trigger) )
			{
				return true ;
			}
		}
		return false ;
	}

	/**
	 * Aplique des actions à la sortie du patient
	 * @params array( 'mailConflits' => $tabMailConflits );
	 */
	function launchActionsOnOut($params)
	{
		//est-ce qu'il y a des enquetes  en cours ?
		if ( !  clTuFormxTrigger::isTriggersActive() )
			return false ;
		//pour chaque enquete
		foreach( clTuFormxTrigger::getTriggersActive() as $trigger )
		{
		    /*
			// le patient sort avec un trigger incomplet ?
			if( $this->isElligibleAndNotCompleted($trigger ) )
			{
				//envoi de mail à signalement
				$sujet = "Terminal des Urgences : Un formulaire d'enquête a été ignoré.";
				$message = "Le formulaire ".$trigger->getNomEnquete()." pour le patient ".$this->getPatient()->getNom()." a été occulté.";

				foreach($params['mailConflits'] as $destinataire)
				{
					clTools::sendMail($destinataire, $sujet, $message);
				}
			}*/
		}
	}

	public function checkOnOut()
	{
		


		if ( !  clTuFormxTrigger::isTriggersActive() )
			return false ;
		$tabLibEnquetesInachevees = array() ;
		//pour chaque enquete
		foreach( clTuFormxTrigger::getTriggersActive() as $trigger )
		{
			//eko("entree dans la boucle pour ".$trigger->getNomEnquete());

			// le patient sort avec un trigger incomplet ? Le trigger est défini comme bloquant ?
			if(  $this->isElligibleAndNotCompleted($trigger )  )
			{
			    //eko("l'enqueete ".$trigger->getNomEnquete()." n'est pas complétée");

				if( $trigger->isBlocking() )
					$tabLibEnquetesInachevees[] = $trigger->getNomEnquete() ;
			}

			//eko("sortie de la boucle ");
		}
		if( count($tabLibEnquetesInachevees) )
			return array( 'isContrainte' => true , 'titreContrainte' => "Enquete(s) non remplie(s)" , 'messageContrainte' => implode(',',$tabLibEnquetesInachevees) ) ;

		//eko("pas de blocage") ;
		return array( 'isContrainte' => false );
	}


	static function getInstance($patient)
	{
		return new clTuFormxTriggerWatcher($patient) ;
	}
}



