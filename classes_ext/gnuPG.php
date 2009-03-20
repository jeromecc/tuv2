<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/


/**
 * Class to interact with the gnuPG.
 *
 * @package   gnuPG_class
 * @author    Enrique Garcia Molina <egarcia@egm.as>
 * @copyright Copyright (c) 2004, EGM :: Ingenieria sin fronteras
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since     Viernes, Enero 30, 2004
 * @version   $Id: gnuPG_class.inc,v 1.0.7 2004-07-15 16:09:00-05 egarcia Exp $
 */
class gnuPG
{
	/**
	* the path to gpg executable (default: /usr/bin/gpg)
	* @access private
	* @var string
	*/
	var $program_path;
	
	/**
	* The path to directory where personal gnupg files (keyrings, etc) are stored (default: ~/.gnupg)
	* @access private
	* @var string
	*/
	var $home_directory;
	
	/**
	* Error and status messages
	* @var string
	*/
	var $error;
	
	/**
	* Output message
	* @var string
	*/
	var $output;
	
	/**
	* Create the gnuPG object.
	*
	* Set the program path for the GNUPG and the home directory of the keyring.
	* If this parameters are not specified, according to the OS the function derive the values.
	*
	* @param  string $program_path   Full program path for the GNUPG
	* @param  string $home_directory Home directory of the keyring
	* @return void
	*/
	function gnuPG($program_path = false, $home_directory = false)
	{
		// if is empty then assume the path based in the OS
		if (empty($program_path)) {
			if ( strstr(PHP_OS, 'WIN') )
				$program_path = 'C:\gnupg\gpg';
			else
				$program_path = '/usr/bin/gpg';
		}
		$this->program_path = $program_path;
		
		// if is empty the home directory then assume based in the OS
		if (empty($home_directory)) {
			if ( strstr(PHP_OS, 'WIN') )
				$home_directory = 'C:\gnupg';
			else
				$home_directory = '~/.gnupg';
		}
		$this->home_directory = $home_directory;
	}
	
	/**
	* Call a subprogram redirecting the standard pipes
	*
	* @access private
	* @param  string $command The full command to execute
	* @param  string $input   The input data
	* @param  string $output  The output data
	* @return bool   true on success, false on error
	*/
	
	/*
	function _fork_process($command, $input = false, &$output)
	{
		// define the redirection pipes
		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w")   // stderr is a pipe that the child will write to
		);
		$pipes = null;
		//echo   $command."<br>";
		// calls the process
		
		$process = proc_open($command, $descriptorspec, $pipes,'',array('ENV'=>FORKPROFILE));
		if (is_resource($process)) {
			// writes the input
			if (!empty($input)) fwrite($pipes[0], $input);
			fclose($pipes[0]);
			
			// reads the output
			while (!feof($pipes[1])) {
				$data = fread($pipes[1], 1024);
				if (strlen($data) == 0) break;
				$output .= $data;
			}
			fclose($pipes[1]);
			
			// reads the error message
			$result = '';
			while (!feof($pipes[2])) {
				$data = fread($pipes[2], 1024);
				if (strlen($data) == 0) break;
				$result .= $data;
			}
			fclose($pipes[2]);
			//echo $result;
			// close the process
			$status = proc_close($process);
			
			// returns the contents
			$this->error = $result;
			if ( $this->error ) {
				global $errs ;
				if ( $errs )
					$errs->addErreur ( $this->error ) ;
			}
			return ($status == 0);
		} else {
			$this->error = 'Unable to fork the command';
			return false;
		}
	}
	
*/



function _fork_process($command, $input = false, &$output)
	{
		// define the redirection pipes
		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w")   // stderr is a pipe that the child will write to
		);
		$pipes = null;
		
		// calls the process
		$process = proc_open($command, $descriptorspec, $pipes);
		if (is_resource($process)) {
			// writes the input
			if (!empty($input)) fwrite($pipes[0], $input);
			fclose($pipes[0]);
			
			// reads the output
			while (!feof($pipes[1])) {
				$data = fread($pipes[1], 1024);
				if (strlen($data) == 0) break;
				$output .= $data;
			}
			fclose($pipes[1]);
			
			// reads the error message
			$result = '';
			while (!feof($pipes[2])) {
				$data = fread($pipes[2], 1024);
				if (strlen($data) == 0) break;
				$result .= $data;
			}
			fclose($pipes[2]);
			
			// close the process
			$status = proc_close($process);
			
			// returns the contents
			$this->error = $result;
			return ($status == 0);
		} else {
			$this->error = 'Unable to fork the command';
			return false;
		}
	}
























	/**
	* Encrypt and sign data.
	*
	* @param  string $KeyID          the key id used to encrypt
	* @param  string $Passphrase     the passphrase to open the key used to encrypt
	* @param  string $RecipientKeyID the recipient key id
	* @param  string $Text           data to encrypt
	* @return mixed  false on error, the encrypted data on success
	*/
	function Encrypt($KeyID, $Passphrase, $RecipientKeyID, $Text)
	{
		// initialize the output
		$contents = '';
        if ( $this->_fork_process($this->program_path . ' --homedir ' . $this->home_directory .
				'  --batch --force-v3-sigs ' .
				" --local-user $KeyID --default-key $KeyID --recipient $RecipientKeyID  --encrypt",$Text, $contents) )
			return $contents;
		else
           return false;
	}

    function EncryptFile($RecipientKeyID, $file)
	{
		// initialize the output
		$contents = '';
		//eko ( $this->program_path . ' --homedir ' . $this->home_directory .
		//		"  --batch --force-v3-sigs  --recipient $RecipientKeyID  --encrypt-files $file" ) ;
        //if ( $this->_fork_process($this->program_path . ' --homedir ' . $this->home_directory .
        if ( $this->_fork_process($this->program_path .' --homedir ' . $this->home_directory .
				"  --batch --force-v3-sigs  --recipient $RecipientKeyID  --encrypt-files $file","", $contents) )
			return $contents;
		else
           return false;
	}

    function Decrypt( $fileIn,$fileOut,$passPhrase=''){
		// initialize the output
		$contents = '';
		if ($passPhrase==''){
			// execute the GPG command without passphrase
	       	if ( $this->_fork_process($this->program_path . ' --homedir ' . $this->home_directory .
					" --output $fileOut --decrypt $fileIn",
				"", $contents) )
				return $contents;
			else
				return false;
		} else {
			// execute the GPG command with passphrase
			if ( $this->_fork_process(
	       		'cat '.$passPhrase.'|'.$this->program_path .
	       		" --batch --passphrase-fd 0 --homedir " . $this->home_directory .
	       		" --output $fileOut --decrypt $fileIn","", $contents) )
				return $contents;
			else
				return false;
		}

    }

}

?>