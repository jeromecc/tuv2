<?php
/*
Licence et conditions d'utilisations-----------------------------------------------------------------------------

-English---------------------------------------------------------------------------------------------------------
Copyright (C) 2001  - AUTHOR ANDRE thierry

This library is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General
Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option)
any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
details.

You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to :

Free Software Foundation,
Inc., 59 Temple Place,
Suite 330, Boston,
MA 02111-1307, Etats-Unis.
------------------------------------------------------------------------------------------------------------------

-Français---------------------------------------------------------------------------------------------------------
ModeliXe est distribué sous licence LGPL, merci de laisser cette en-tête, gage et garantie de cette licence.
ModeliXe est un moteur de template destiné à être utilisé par des applications écrites en PHP.
ModeliXe peut être utilisé dans des scripts vendus à des tiers aux titres de la licence LGPL. ModeliXe n'en reste
pas moins OpenSource et libre de droits en date du 23 Août 2001.

Copyright (C) 2001  - Auteur ANDRE thierry

Cette bibliothèque est libre, vous pouvez la redistribuer et/ou la modifier selon les termes de la Licence Publique
Générale GNU Limitée publiée par la Free Software Foundation version 2.1 et ultérieure.

Cette bibliothèque est distribuée car potentiellement utile, mais SANS AUCUNE GARANTIE, ni explicite ni implicite,
y compris les garanties de commercialisation ou d'adaptation dans un but spécifique. Reportez-vous à la Licence
Publique Générale GNU Limitée pour plus de détails.

Vous devez avoir reçu une copie de la Licence Publique Générale GNU Limitée en même temps que cette bibliothèque;
si ce n'est pas le cas, écrivez à:

Free Software Foundation,
Inc., 59 Temple Place,
Suite 330, Boston,
MA 02111-1307, Etats-Unis.

Pour tout renseignements mailez à modelixe@free.fr ou thierry.andre@freesbee.fr
--------------------------------------------------------------------------------------------------------------------
*/
$incErrorManager = true;

class ErrorManager {

    public $errorCounter = Array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

    public $errorMessage = '';
    public $errorEscape = '';
    public $errorLog = '';
    public $errorAlarme = '';

    public $errorTrackingLevel = 1;
    public $numberError = 0;
    public $maxErrorReport = 0;

    public $errorManagerSystem = true;

    //Constructeur-----------------------------------------------------
    function ErrorManager($errorManagerSystem = '', $level = '', $escape = '', $file = '', $alarme = ''){
		$this -> SetErrorSystem($errorManagerSystem);
		$this -> SetErrorLevel($level);
		$this -> SetErrorEscape($escape);
		$this -> SetErrorAlarme($alarme);
		$this -> SetErrorLog($file);
        }

	//Setting ErrorManager---------------------------------------------------

	function SetErrorSystem($arg = ''){
		if (defined('ERROR_MANAGER_SYSTEM') && ! $arg) $arg = ERROR_MANAGER_SYSTEM;
        $this -> errorManagerSystem = $arg;

        if ($this -> errorManagerSystem != 'off') $this -> errorManagerSystem = true;
        else $this -> errorManagerSystem = false;
		}

	function SetErrorLevel($arg = ''){
		if (defined('ERROR_MANAGER_LEVEL') && ! $arg) $arg = ERROR_MANAGER_LEVEL;
		if ($arg) $this -> errorTrackingLevel = $arg;
		}

	function SetErrorEscape($arg = ''){
		if (defined('ERROR_MANAGER_ESCAPE') && ! $arg) $arg = ERROR_MANAGER_ESCAPE;
		if ($arg && ! $this -> SetErrorOut($arg)) $this -> errorEscape = '';
		}

    function SetErrorAlarme($arg = ''){
    	if (defined('ERROR_MANAGER_ALARME') && ! $arg) $arg = ERROR_MANAGER_ALARME;
    	if ($arg) $this -> errorAlarme = $arg;
    	}

	function SetErrorLog($arg = ''){
		if (defined('ERROR_MANAGER_LOG') && ! $arg) $arg = ERROR_MANAGER_LOG;
		if ($arg) $this -> errorLog = $arg;
		}

    //Paramétrage -----------------------------------------------------------

    function SetErrorLock($func){
        if (strtolower($func) == 'actived') $func = true;
        if (strtolower($func) == 'desactived') $func = false;

        $this -> errorManagerSystem = $func;
        return true;
        }

    function SetErrorOut($url){
        if (is_file($url) || ereg('http://', $url)) {
            $this -> errorEscape = $url;
            return true;
            }
        else return false;
        }

    //Gestionnaire -----------------------------------------------------------

    function ErrorTracker($warning, $message, $func = '', $file = '', $line = ''){

        switch ($warning){
            case 1:
                $type = "Low warning";
                break;
            case 2:
                $type = "Warning";
                break;
            case 3:
                $type = "Notification";
                break;
            case 4:
                $type = "Error";
                break;
            case 5:
                $type = "Emergency break";
                break;
            default:
                $type = "Unknown error";
                $warning = 0;
            }

        $this -> numberError ++;
        if (++ $this -> errorCounter[$warning] > 0 && $warning > $this -> maxErrorReport) $this -> maxErrorReport = $warning;

        if ($this -> numberError > 1) $pre = "\t<li>";
        else $pre = "\n<ul>\n\t<li>";

        $this -> errorMessage .= $pre.$type.' no '.$this -> errorCounter[$warning].' ';

        if ($func) $this -> errorMessage .= 'on <b>'.$func.'</b> ';
        if ($file) $this -> errorMessage .= 'in file <b>'.$file.'</b> ';
        if ($line) $this -> errorMessage .= 'on line <b>'.$line.'</b> ';

        $this -> errorMessage .= ': <br><ul><li><i>'.$message.'</i><br><br></ul>'."\n";
        $this -> ErrorChecker();
        }


    function ErrorChecker($level = ''){
        if ($level == '') $level = $this -> errorTrackingLevel;

        if ($this -> maxErrorReport >= $level) {
            $message = 'The '.date('<b>d/M/Y </b> H:i:s')."<br />\n".'ErrorManager report, you\'ve got '.$this -> numberError.' error(s), see below to correct:'."\n<br>\n".$this -> errorMessage."\n</ul>";

            if ($this -> errorManagerSystem) {

				if ($this -> errorAlarme) {
					$tab = explode(',', $this -> errorAlarme);
					while (list($key, $val) = each($tab)){
				        if (! preg_match('/^(.+)@(.+)\.(.+)$/s', $val)) {
				        	$message .= "<p style='color:red;'>Your ERROR_MANAGER_ALARME mails configurations has got a mistake and was disabled.</p>";
				        	$this -> errorAlarme = '';
				        	}
						}
                    $server = getenv("HTTP_HOST");
					$script = getenv("SCRIPT_NAME");
					$mess = 'ErrorMessage from "'.$server.'" server and "'.$script."\" script.\n\n".$message;

					if ($this -> errorAlarme) @mail($this -> errorAlarme, '[ErrorManager][Alarm]', strip_tags($mess));
	            	}

				if ($this -> errorLog) {
	                $ouv = @fopen($this -> errorLog, 'a');
	                @fputs($ouv, strip_tags($message));
	                @fclose($ouv);
	            	}

                if ($this -> errorEscape) header('location: '.$this -> errorEscape);
                else {
                    print($message);
                    exit;
                    }
                }
            else {
                if (strtoupper($level) == 'GET') return $message;
                else return false;
                }
            }
        else return true;
        }
    }
?>