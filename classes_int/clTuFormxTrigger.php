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
		if( $this->getDxDefAttributeVal('autoStart') )
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
		if( $this->getDxDefAttributeVal('autostart') )
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
		if (  $this->isAutostart()  &&  $this->isValide() && ! $this->isActive() )
			return true ;
		return false ;
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
			if( $dateClose->earlierThan( clDate::getInstance() ) )
				return true ;
		}
		return false ;
	}



function getIdEnquete()
{
		$now = new clDate();
		$requete = "SELECT * FROM enquetes WHERE  id_trigger = '".$this->idTrigger."' AND date_debut <= '".$now->getDatetime()."' AND ( date_fin = '0000-00-00 00:00:00' OR  date_fin > '".$now->getDatetime()."' ) "   ;
		$obRequete = new clRequete(BDD,'enquetes');
		$res = $obRequete->exec_requete($requete, 'tab');
		if(count($res) > 0 )
			return $res[0]['id_enquete'] ;
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
		$this->setEndTime(clDate::getInstance());
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
 * @return <type>
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
		return (count(self::getTriggersActive())?true:false);
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




	/**
	 * exectute chaque minute,
	 * verifie si des triggers sont à cloturer ou à démarrer
	 */
	static function crontab()
	{
		foreach( self::getAllTriggers() as $trigger )
		{
			if( $trigger->mustBegin() )
            {
				$trigger->start();
            }
			if( $trigger->mustClose() )
			{
				$trigger->close();
			}
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


	function isNotElligibleOrCompleted( clTuFormxTrigger $trigger)
	{
		return  ! $this -> isElligible( $trigger,  array('checkLaunchers'=>true,'etatsFormx'=> array('F','H') ) ) ;
	}


	//Regarde si on peut déclencher le trigger pour le patient en cours
	//$options = array('checkLaunchers'=>true,'etatsFormxAcceptes'=> array('I','E','F','H') ) ;
	//ce sont les etats pour lesquels l'eligibilité est considéré comme nulle
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
            if($this->checkCond($cond,$options))
                return true ;
        }
        return false ;
    }

    function multiAnd($tabConds,$options)
    {
        foreach($tabConds as $cond)
        {
            if( ! $this->checkCond($cond,$options))
                return false ;
        }
        return true ;
    }




	private function checkCond(clTuFormxTriggerCondition $condition,$options)
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
			return ! $this->checkCond($condition->getFirstSubCond(),$options) ;
		case 'xor':
			return  $this->checkCond($condition->getFirstSubCond(),$options) XOR  $this->checkCond( $condition->getSecondSubCond() ,$options) ;
		case 'ccmu':
			//est-ce que le diagnostic du patient est concerné par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
            if($condition->hasCcmu($this->getPatient()->getCCMU()) &&  ! $this->getpatient()->hasFormxPassage($condition->getTrigger()->getIdFormx(),array('etat' => $options['etatsFormx'] ) ) )
			{
				return true ;
			}
			return false ;
		case 'diag':
			//est-ce que le diagnostic du patient est concerné par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
			if($condition->hasDiag($this->getPatient()->getCodeDiagnostic()) &&  ! $this->getpatient()->hasFormxPassage($condition->getTrigger()->getIdFormx(),array('etat' => $options['etatsFormx'] ) ) )
			{
				return true ;
			}
			return false ;
        case 'motif':
			//est-ce que le motif du patient est concerné par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
			if($condition->hasMotif($this->getPatient()->getCodeRecours()) &&  ! $this->getpatient()->hasFormxPassage($condition->getTrigger()->getIdFormx(),array('etat' => $options['etatsFormx'] ) ) )
			{
				return true ;
			}
			return false ;
		case 'acte':
			//est-ce que les actes du patient sont concernés par ce formulaire ? Est-ce que le patient n'a pas déjà le formulaire instancié pour le passage ?
			if($condition->hasActes($this->getPatient()->getTabActes()  )  &&  ! $this->getpatient()->hasFormxPassage($condition->getTrigger()->getIdFormx(),array('etat' => $options['etatsFormx'] ) )  )
			{
				return true ;
			}
			return false ;
		case 'medecin':
			//Est-ce que le patient a un médecin urgentiste affecté ? Est-ce que ce médecin n'a pas déjà un formulaire instancié pour ce passage ?
			if(	$this->getPatient()->getMatriculeMedecin()
				&&
				count(formxTools::exportsGetTabIdformFilterValue( $condition->getTrigger()->getIdFormx() , 'id_medecin', $this->getPatient()->getMatriculeMedecin(),array('etat' => $options['etatsFormx'] )   )  ) == 0 )
			{
				return true ;
			}
			return false ;
		default :
			return false ;
		}
	}

	//on regarde si le patient est elligible à des trigger diags, si c'est le cas on marque le déclenchement du trigger pour qu'il s'affiche des que l'affichage est dispo
	function launchTriggers($typeAppel='onPresent')
	{

		//est-ce qu'il y a des enquetes  en cours ?
		if ( !  clTuFormxTrigger::isTriggersActive() )
			return false ;
		//pour chaque enquete
		foreach( clTuFormxTrigger::getTriggersActive() as $trigger )
		{
			//le patient est il elligible ? Si en sortie, est-ce bien un trigger de sortie ?
			if( $this->isElligible($trigger)  and ( ! $trigger ->isOnOut() or ( $typeAppel == 'onOut' && $trigger ->isOnOut()  ) ) )
			{
				//on le marque comme à afficher
				$this->markLaunching($trigger->getIdTrigger() );
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
		foreach ( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'] as $index => $idTrigger )
		{
			if ( $idTriggerAsk == $idTrigger )
				unset($_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'][$index] );
		}

	}

	function isLaunching($idTriggerAsk)
	{
		foreach ( $_SESSION['tuFxTriggersWatcher'][$this->getPatient()->getIDU() ]['launchers'] as $idTrigger )
		{
			if ( $idTriggerAsk == $idTrigger )
				return true ;
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
			if( $this->isElligible($trigger)  &&  $trigger ->isOnOut()   )
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
			// le patient sort avec un trigger incomplet ?
			if( ! $this->isNotElligibleOrCompleted($trigger ) )
			{
				//envoi de mail à signalement
				$sujet = "Terminal des Urgences : Un formulaire d'enquête a été ignoré.";
				$message = "Le formulaire ".$trigger->getNomEnquete()." pour le patient ".$this->getPatient()->getNom()." a été occulté.";
				foreach($params['mailConflits'] as $destinataire)
				{
					clTools::sendMail($destinataire, $sujet, $message);
				}
			}
		}
	}

	static function getInstance($patient)
	{
		return new clTuFormxTriggerWatcher($patient) ;
	}
}



