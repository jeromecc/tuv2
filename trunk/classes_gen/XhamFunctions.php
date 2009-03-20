<?php
/*
 * Created on 22 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */

if ( ! function_exists( 'getDefine') )  {
	// Retourne le contenu d'un define.
	function getDefine ( $name, $default='' ) {
 		$tmp = '' ;
 		if ( defined ( $name ) ) { eval ('$tmp = '.$name." ;" ) ; return $tmp ; }
 		elseif ( $default ) return $default ;
 		else return false ;
 	}
}

if ( ! function_exists( 'whereAmI') ) { 
	function whereAmI($mode='',$nivorecu=666,$debut=0) {
		$i=$debut;
		$j=0;
		$infoDebug = debug_backtrace();
		$info = '';
		while(isset($infoDebug[$i])) {
			$i++;
			if(isset($infoDebug[$i]["file"]) && $infoDebug[$i]["file"]) {
				$info .= $infoDebug[$i]["file"]." L".$infoDebug[$i]["line"].'<br/>';
				$j++;
			}
			if($j>=$nivorecu)
				break;
		}
		if($mode=='')
			print($info);
		else
			return  $info;
	}
}

if ( ! function_exists( 'affTab') ) { 
	//affiche récursivement un tableau en XTML valide ^^
	function affTab( $tab ) {
  		reset ( $tab ) ;
  		//$contenu = "<table align=\"center\" border =1><tr bgcolor=\"Silver\"><th>Index</th><th>Valeur</th></tr>" ;
  		$contenu = "<table>" ;
  		while ( list ( $key, $val ) = each ( $tab) ) {
  			if(is_array($val))
  				$contenu .= "<tr><td style='vertical-align:top;' >".$key."</td><td style=\"border: 1px white solid;\">".affTab($val)."</td></tr>";
  			else
    			$contenu .= "<tr><td>".$key."</td><td>".$val."</td></tr>" ;
  		}
  		$contenu .= "</table>";
  		return $contenu;
	}	
}

if ( ! function_exists( 'affTabSimple') ) { 
	//affiche récursivement un tableau en XTML valide ^^
	function affTabSimple( $tab ) {
  		reset ( $tab ) ;
  		//$contenu = "<table align=\"center\" border =1><tr bgcolor=\"Silver\"><th>Index</th><th>Valeur</th></tr>" ;
  		$contenu = "<table>" ;
  		while ( list ( $key, $val ) = each ( $tab) ) {
    			$contenu .= "<tr><td>".$key."</td><td>".$val."</td></tr>" ;
  		}
  		$contenu .= "</table>";
  		return $contenu;
	}	
}

if ( ! function_exists( 'eko') ) { 
	//affiche une console. le deuxieme argument force l'affichage
	//même pour un non-admin.
	function eko ( $message,$force =false) {
  		global $xham;
  		if ( is_array ( $message ) ) {
    		$message = " Affichage d'un tableau demandé<BR/>".affTab ( $message ) ;
  		}
  		if ( isset ( $xham ) )
    		$xham->errs->logThis ( $message,$force ) ;
	}
}


 if ( ! function_exists( 'printb') ) { 	
 	function printb($s){
 		print $s;
 		print "<br/>";
 		ob_flush();flush();
 	}
 }
?>
