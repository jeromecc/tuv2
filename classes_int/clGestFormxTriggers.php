<?php

class clGestFormxTriggers
{

	//controleur
	function __construct()
	{
		global $options ;
		if( isset($_POST['idTrigger'])  )
		{
			$trigger = new clTuFormxTrigger($_POST['idTrigger']);
			if( isset($_POST['activate'])  )
			{
				$trigger->start();
			}
			else if  ( isset($_POST['desactivate'])  )
			{
				$trigger->close();
			}
		}
		else if ( isset($_POST['export'])  )
		{
			$tabEnquete = clTuFormxTrigger::getTabEnquete($_POST['export']);
			$trigger = new clTuFormxTrigger($tabEnquete['id_trigger']);
			$dateD = new clDate($tabEnquete['date_debut']);
			$dateF = new clDate($tabEnquete['date_fin']);
			$idFormx = $trigger->getIdFormx();
			//TODO préciser dates
			set_time_limit(0);
			ini_set('memory_limit','512M');
			$strDate1 = str_replace(array(' ',':'), array('_','-'), $dateD->getDatetime());
			$strDate2 = str_replace(array(' ',':'), array('_','-'), $dateF->getDatetime());
            $nomFic = 'etab_'.$options->getOption('RPU_IdActeur').'_enquete_'.formxTools::strGetIdAtomiqueFx($idFormx).'_du_'.$strDate1.'_au_'.$strDate2.'.csv';
		 
            if( $trigger->isPassageLinked() )
                $tabOptions = array('firstColsFunc'=>'clGestFormxTriggers::genTabinfoIdPassage','firstColsFuncArgField'=>'id_passage');
            else
                $tabOptions = array() ;
            $data = clFoRmXtOoLs::exportsGetTabIdform($idFormx, $tabOptions + array('cw' => " dt_creation <= '".$dateF->getDatetime()."' AND status IN ('F','H') AND dt_creation >= '".$dateD->getDatetime()."'   " ));

            $location = clFoRmXtOoLs::exportsGetCsvFromData($data,$nomFic);
            
			header('Location: '.$location);
		}
	}

    static public function genTabinfoIdPassage($idPassage)
    {
        return clPatient::getObjPatientFromIdPassage($idPassage)->getMiniExport() ;
    }
    


//templater
	function getAffichage()
	{

		global $session ;
		$af = '<h2>Gestion des enquetes</h2>';
		$link = 'index.php?navi='.$session->genNavi($session->getNaviFull());
		foreach(clTuFormxTrigger::getTriggers() as $trigger)
		{
			$af .= '<br />'.utf8_decode($trigger->getNomEnquete()).' ';
			if( $trigger->isActive() ) {
				if( $trigger->isClosable() )
					$af .= clTools::genLinkPost($link,"Cloturez l'enquête",array('desactivate'=>'y','idTrigger'=>$trigger->getIdTrigger()),array('alert'=>'Cloturer l\'enquête '.$trigger->getNomEnquete().'?'));
			}
			else if ( $trigger->isActivable() )
				$af .= clTools::genLinkPost($link,"Activez l'enquête",array('activate'=>'y','idTrigger'=>$trigger->getIdTrigger()),array('alert'=>'Commencer l\'enquête '.$trigger->getNomEnquete().'?'));

		}
		$af .= '<h2>Enquetes finies</h2>';
		$af .='L\'export peut parfois se révéler assez long. C\'est un comportement normal.<br />';

		foreach(clTuFormxTrigger::getTabEnquetesFinies() as $tabEnquete)
		{
			$trigger = new  clTuFormxTrigger($tabEnquete['id_trigger']) ;
			$af .= '<br />'.utf8_decode($trigger->getNomEnquete())." du ".clDate::getInstance($tabEnquete['date_debut'])->getSimpleDate();
			$af .=" au ".clDate::getInstance($tabEnquete['date_fin'])->getSimpleDate();
			$af .= ' '.clTools::genLinkPost($link,"Exporter",array('export'=>$tabEnquete['id_enquete']));
		}
		return $af ;
	}
}