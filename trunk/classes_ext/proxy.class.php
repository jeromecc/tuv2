<?php

	#ENUM AccessMode
	abstract class AccessMode {
		const READ 		= 0;
		const APPEND 	= 1;
		const TRUNCATE	= 2;
		const UNKNOWN 	= 3;
	} 
	#END ENUM

	/**
	 * Interface de définition d'une classe de streaming
	 */	 	
	interface IStream {	  		  
	    function stream_open($path, $mode, $options, &$opened_path);
	    function stream_read($count);		
		function stream_stat();		
	    function stream_write($data);	
	    function stream_tell();
	    function stream_eof();	
	    function stream_seek($offset, $whence);	
	    function stream_flush();	
	} 

	/**
	 * Classe de base pour gérer un stream
	 */	 	
	abstract class BaseStream implements IStream {
		private $ptr;
		private $mode;
		private $position;
		private $filename;
		private $size;
		final public function getSize() {
			return $this->size;
		}
		final public function setSize($size) {
			$this->size = $size;
		}
		final public function getFileName() {
			return $this->filename;
		}
		final public function setFileName($file) {
			$this->filename = $file;
		}
		final public function getHandler() {
			return $this->ptr;
		}
		final public function setHandler($ptr) {
			$this->ptr = $ptr;
		}
		final public function getMode() {
			return $this->mode;
		}		
		final public function setMode($mode) {
			$this->mode = $mode;
		}
		final public function getPosition() {
			return $this->position;
		}
		final public function setPosition($position) {
			$this->position = $position;
		}
		public function stream_stat() {
			return array();
		}		
	    public function stream_tell() {
	        return $this->position;
	    }
	    public function stream_eof() {
	        return feof($this->getHandler());
	    }		
	    final public function getOpenedMode() {
	        switch ( strtolower($this->mode) ) {
	            case "r":
	            case "rb":
	                $OpenFlags = AccessMode::READ;
	                break;
	            case "r+":
	            case "a":
	            case "ab":
	            case "r+b":
	                $OpenFlags = AccessMode::APPEND;
	                break;
	            case 'w':
	            case 'w+':	            
	                $OpenFlags = AccessMode::TRUNCATE;
	                break;
	            default: 
	                $OpenFlags = AccessMode::UNKNOWN;
	                break;
	        }    
	        return $OpenFlags;
	    }	
	} 

	/**
	 * Classe de lecture HTTP
	 */	 	
	class HttpProxyStream extends BaseStream {
		
		// CONFIGURATION
	    public static $proxy_host = '';
	    public static $proxy_port = 0;
	    public static $proxy_user = '';
	    public static $proxy_pass = '';
	    
	    
	    private $buffer;
	    
	    /*
	    **    Wrapper interface
	    **
	    */
	    public function stream_open($path, $mode, $options, &$opened_path) {
			if (substr($path, 0, 5) == 'proxy') $path = 'http'.substr($path, 5);		
			$this->setPosition(0);
			$this->setFileName($path);
			$this->setMode($mode);
			$info = parse_url($this->getFileName());			
			if (!empty(self::$proxy_host) && self::$proxy_port > 0) {
	   			$ptr = fsockopen(self::$proxy_host, self::$proxy_port);
			} else {
				if (!isset($info['port'])) $info['port'] = 80;
				$ptr = fsockopen($info['host'], $info['port']);
			}
			if (!$ptr) {
				throw new Exception('Unable to load '.$path);
			} else {
				$this->setHandler($ptr);
			}
			
			if (!empty(self::$proxy_host) && self::$proxy_port > 0) {
				fputs($ptr, 'GET '.$path.' HTTP/1.0'."\r\n");
				fputs($ptr, 'Host: '.$info['host']."\r\n");
				if (!empty(self::$proxy_user)) {
					fputs($ptr, 'Proxy-Authorization: Basic '.base64_encode(self::$proxy_user.':'.self::$proxy_pass)."\r\n");					
				}
			} else {
				$uri = $info['path'];
				if (isset($info['query'])) {
					$uri .= '?'.$info['query'];
				}
				fputs($ptr, 'GET '.$uri.' HTTP/1.0'."\r\n");
				fputs($ptr, 'Host: '.$info['host']."\r\n");
				if (isset($info['user']) && isset($info['pass'])) {
					fputs($ptr, 'Authorization: Basic '.base64_encode($info['user'].':'.$info['pass'])."\r\n"); 					
				}			
			}
			fputs($ptr, "\r\n");	
			$header = '';
			while(!feof($ptr)) {				
				$header .= fread($ptr, 4096);
				if(strpos($header, "\r\n\r\n") > 0) {
					$this->buffer = substr($header, strpos($header, "\r\n\r\n") + 4);
					$header = substr($header, 0, strpos($header, "\r\n\r\n"));
					break;
				}
			}								
			return true;
	    }
	
	    public function stream_read($count) {
			$pos = $this->getPosition() + $count;
			$this->setPosition($pos);
			$ret = '';
			if (!empty($this->buffer)) {
				$ret = $this->buffer;
				$this->buffer = '';
			}
			$ret .= fread($this->getHandler(), $count);
			return $ret;
			
	    }
				
	    public function stream_write($data) {
	        return false;
	    }
	
	    public function stream_seek($offset, $whence) {
	    	return false;	        
	    }
	
	    public function stream_flush() {    
	        return false;
	    }	    
	}
	stream_wrapper_register("proxy", "HttpProxyStream") or die("Failed to register protocol");

?>