<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
 class XhamTools {

 	// fonction permettant de récupérer le temps écoulé depuis l'époque UNIX ( 1 - 1 1970 )
 	static function temps ( ) {
   		$time = microtime();
  		$tableau = explode(" ",$time);
  		return ($tableau[1] + $tableau[0]);
	}

	// Enlève tous les accents d'une chaine de caractères.
	static function sansAccent ( $chaine )	{
   		$accent   = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ" ;
   		$noaccent = "aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby" ;
   		return strtr ( trim ( $chaine ), $accent, $noaccent ) ;
	} 

	// Chiffrement réversible d'un message avec une clé passée en paramètre.
	static function chiffre ( $texte, $cle='' ) {
  		// Récupération de la clé si elle n'est pas passée en paramètre.
  		if ( ! $cle ) {
  			// On la récupère depuis une constante si possible.
  			if ( DEFINED ( "CLEAPPLI" ) ) $cle = CLEAPPLI ;
  			// Sinon, on utilise la clé par défaut.
  			else $cle = "leJambonCtresbon" ;
  		}
  		// A améliorer..........
  		$temp = base64_encode ( $texte ) ;
  		return $temp;
  	}
  	
  	// Déchiffrement du message chiffré avec la fonction chiffrement.
  	static function dechiffre ( $texte, $cle='' ) {
  		// Récupération de la clé si elle n'est pas passée en paramètre.
  		if ( ! $cle ) {
  			// On la récupère depuis une constante si possible.
  			if ( DEFINED ( "CLEAPPLI" ) ) $cle = CLEAPPLI ;
  			// Sinon, on utilise la clé par défaut.
  			else $cle = "leJambonCtresbon" ;
  		}
		// A améliorer...........
  		$temp = base64_decode ( $texte ) ;
  		return $temp;
  	}

	// Petite fonction qui génère une clé aléatoire sur N caractères.
  	static function getAlea ( $taille='16' ) {
    	$lettres = "23456789abcdefghijkmnopqrstuvwxyz23456789ABCDEFGHIJKLMNPQRSTUVWXYZ23456789";
    	$sid = '';
    	srand ( strrchr ( microtime ( ) , " " ) ) ;
    	for ( $i = 0 ; $i < $taille ; $i++ ) {
    	  	$sid .= substr ( $lettres, ( rand ( ) % ( strlen ( $lettres ) ) ), 1 ) ;      
    	}
    	return $sid ;
  	}

	//Teste si une adresse mail est correctement formée
	static function isCorrectMail($adr) {
		if ( ereg('^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$',$adr) )
			return true ;
		return false ;
	}

	//Envoie un mail
	static function sendMail($destinataire,$sujet,$message) {
		$content_type="Content-Type: text/html; charset=\"iso-8859-1\"";
		$head="From :".MAIL_INFOS."\n".$content_type."\n";
		if( defined('SENDONLYMAILSTO') && SENDONLYMAILSTO )
			$dest = SENDONLYMAILSTO ;
		else 
			$dest = $destinataire ;
		if( defined('ALWAYSBCCTO') && ALWAYSBCCTO ) {
			$head .= "bcc:".ALWAYSBCCTO."\n";
		}
		$dest = strtr($dest, "; ", ",,");
		return mail($dest,$sujet,$message,$head);
	}


	//récupère les groupes xhams définis pour l'application en cours	
	static function   getListeGroupes ( ) {
    	$param['aw'] = " AND idapplication=".IDAPPLICATION." ORDER BY nomgroupe" ;
    	$req = new clResultQuery ;
    	$res = $req -> Execute ( "Fichier", "getGroupesApplication", $param, "ResultQuery" ) ;
    	return $res ; 
  	}
  	
  	// Génère un bout de javascript pour appeler des fonctions AJAX.
  	//setDivFromAjax ( url, iddiv, data, message )
  	static function genAjaxDiv ( $url, $iddiv, $data, $message='Chargement en cours...', $type='', $js='', $noWait='' ) {
  		//eko ( htmlentities ( " $type=\"setDivFromAjax('$url','$iddiv','$data','$message','$js','$noWait')\" " ) ) ;
  		if ( $type ) return " $type=\"setDivFromAjax('$url','$iddiv','$data','$message','$js','$noWait')\" " ;
  		else return "setDivFromAjax('$url','$iddiv','$data','$message','$js','$noWait'); " ;
  	}
  	
  	// Génère un bout de javascript pour appeler des fonctions AJAX.
  	//onChange="request('test.html',null,'alertContents')"
  	static function genAjax ( $type, $function, $param='', $url="index.php" ) {
  		if ( $type ) return " $type=\"request('$url?$param',null,'$function')\" " ;
  		else return "request('$url?$param',null,'$function'); " ;
  	}
  	
  	// Génère un bout de javascript pour appeler des fonctions AJAX avec une temporisation à la saisie.
  	static function genAjaxWithTempo ( $function, $param='', $url="index.php" ) {
		return "onKeyDown=\"clearTimeout(tempo);\" onKeyUp=\"tempo=setTimeout(function(){request('$url?$param',null,'$function')},400)\" " ;
  	}
  	
  //genere les bons javascripts, le premier argument attendu est un tableau d'id de balises
  //le deuxieme l'url ou les donnes seront envoyées en post,
  //le troisieme l'id du div qui contiendra les données renvoyées par
  //la requete ajax	
  
  	static function genAjaxForm ( $tab_idchp,$url,$iddiv,$sansScr='') {
  		if ( ! $sansScr ) $aff="<script type='text/javascript'>";
  		else $aff = '' ;
  		foreach($tab_idchp as $idchamp) {
  			$aff.=" document.getElementById('$idchamp').onkeyup=function () { requestForm('$url',getFormParent('$idchamp'),'$iddiv' );};";
  			$aff.="\n";
  			// Damien : Petit ajout à supprimer en me prévenant si ça pose problème.
  			$aff.="if(document.getElementById('$idchamp').nodeName == 'SELECT' )";
  			$aff.=" { document.getElementById('$idchamp').onchange=function () { requestForm('$url',getFormParent('$idchamp'),'$iddiv' );}; }";
  			//$aff.="\n";	
  		}
  		if ( ! $sansScr ) $aff.="</script>";
  		return $aff;
  	}
  	
  	// Exécute les requetes SQL contenues dans un fichier $file dans la base $bdd
  	static function execSQLFromFile ( $file, $bdd, $host, $user, $pass ) {
  		$sql = utf8_decode(file_get_contents ( $file )) ;
  		$tabSQL = explode ( ';', $sql ) ;
  		$req = new XhamRequete ( $bdd, '', '', $host, $user, $pass ) ;
  		while ( list ( $key, $val ) = each ( $tabSQL ) ) {
  			$rql = trim($val) ;
  			if ( $rql ) {
  				//print "REQUEEEEEEEEEEEEEEEETE".$rql."<br/><br/>" ;
  				$res = $req -> exec_requete ( $rql, 'resultquery' ) ;
  			}
  		}
  		return $res ; 
  	}
  	
  	// print AJAX
  	
  	static function printAJAX ( $val, $js ) {
  		global $xham ;
  		$af = "<?xml version=\"1.0\" ?>\n<root>" ;
  		$af .= "<droit>".$xham->getDroit('Configuration','a')."</droit>\n" ;
  		$af .= "<text><![CDATA[$val]]></text>\n" ;
  		if ( $js ) $af .= "<js><![CDATA[$js]]></js>\n" ;
  		//$af .= "</root>" ;
  		return $af ;
  	}
  	
  	// Génère une aide contenant le texte passé en paramètre.
  	static function genHelpFromText ( $contenu, $size='400', $bgcolor='', $fgcolor='' ) {
  		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "help.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	if ( getDefine ( 'HELPBGCOLOR' ) AND ! $bgcolor )
    		$bgcolor = getDefine ( 'HELPBGCOLOR' ) ;
    	if ( getDefine ( 'HELPFGCOLOR' ) AND ! $fgcolor )
    		$fgcolor = getDefine ( 'HELPFGCOLOR' ) ;    		
    	$text  = preg_replace("/(\r\n|\n|\r)/", " ", nl2br($contenu) ) ;
    	$textF = str_replace("'","\'",$text) ;
		$text  = str_replace('"','\"',$textF) ;
		$mod -> MxText ( "infoBulle", "onClick=\"return overlib('$text', " .
				"WIDTH, $size, BGCOLOR,'$bgcolor', FGCOLOR, '$fgcolor', STICKY, " .
				"CAPTION, '".NOMAPPLICATION." : Aide', CLOSECLICK, HAUTO, VAUTO, SHADOW );\" " .
				"onClick=\"return nd();\"" ) ;
    	return $mod -> MxWrite ( "1" ) ;
  	}
  	
  	// Génère une aide à partir du contenu d'un fichier dont le nom est passé en paramètre.
  	static function genHelpFromFile ( $file, $size='400', $bgcolor='', $fgcolor='' ) {
  		if ( file_exists( "templates_int/$file" ) ) $contenu = file_get_contents ( "templates_int/$file" ) ;
  		else if ( file_exists( "templates_gen/$file" ) ) $contenu = file_get_contents ( "templates_gen/$file" ) ;
  		else $contenu = 'Aucune aide associée à cet élément.' ;
  		return XhamTools::genHelpFromText ( $contenu, $size, $bgcolor, $fgcolor ) ;
  	}
  	
  	// Génère le header en XML (utilisé pour les retours AJAX)
  	static function genHeaderXML ( $contenu ) {
 		header ( "Cache-Control: no-cache, must-revalidate" ) ; // HTTP/1.1
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ) ;
		header ( "Content-type: application/xhtml+xml" ) ;
		print ( '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . utf8_encode ( "<resultats>".$contenu."</resultats>" ) ) ;
 	}
 	
 	// Vérification de la validité d'un mot de passe.
 	static function verifPassword ( $pwd, $recupTexte = '' ) {
 		global $xham ;
 		$err  = 0 ;
 		$errs = '' ;
 		$lp   = $xham -> getOption ( 'LongueurPassword' ) ;
 		$cp   = $xham -> getOption ( 'ComplexitePassword' ) ;
 		if ( strlen ( $pwd ) < $lp ) {
 			$err++ ;
 			$errs .= "Le mot de passe n'est pas assez long (minimum $lp caractère".($lp>1?'s':'').").<br/>" ;
 		}
 		$p = stripslashes ( $pwd ) ;
 		switch ( $cp ) {
 			case 'Paranoïaque':
 				//$errs .= "Le mot de passe ne doit pas être basé sur un mot du dictionnaire." ; 
 				//$errs .= "Trop de caractères se répètent." ;
 				if ( ! ereg ( '[A-Z]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère en majuscule.<br/>" ;
 				} elseif ( ! ereg ( '[]!:;,?./§²&~"#\'{([|`_\\^@)=}+°*\$£%¤-]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère spécial (ex: !?;:.).<br/>" ;
 				} elseif ( ! ereg ( '[a-z]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère alphabétique (ex: a,b,c,d,e...).<br/>" ;
 				} elseif ( ! ereg ( '[0-9]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère numérique (ex: 1,2,3...).<br/>" ;
 				}
 			break;
 			case 'Elevé':
 				if ( ! ereg ( '[A-Z]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère en majuscule.<br/>" ;
 				} elseif ( ! ereg ( '[]!:;,?./§²&~"#\'{([|`_\\^@)=}+°*\$£%¤-]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère spécial (ex: !?;:.).<br/>" ;
 				} elseif ( ! ereg ( '[a-z]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère alphabétique (ex: a,b,c,d,e...).<br/>" ;
 				} elseif ( ! ereg ( '[0-9]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère numérique (ex: 1,2,3...).<br/>" ;
 				}
 			break;
 			case 'Normal':
 				if ( ! ereg ( '[a-z]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère alphabétique (ex: a,b,c,d,e...).<br/>" ;
 				} elseif ( ! ereg ( '[0-9]', $p ) ) {
 					$err++ ;
 					$errs .= "Le mot de passe doit contenir au moins un caractère numérique (ex: 1,2,3...).<br/>" ;
 				}
 				break;
 			default:
 			
 			break;
 		}
 		if ( $recupTexte ) return $errs ;
 		else return $err ;
 	}
 	
 	static function calcUidFromLdapString($srt,$mark='uid') {
		$matches = array();
		if( preg_match('/^'.$mark.'=([^,]+),/', $srt, $matches  ))
			return $matches[1];
		return false;
	}
	
	// Génération d'une fenêtre bloquante (à valider).
	static function genFenetreBloquante ( $template, $navi='' ) {
		global $xham ;
		global $session ;
		// On détecte si c'est de la V1 ou de la V2.
		if ( is_object ( $xham ) ) $n = $xham ;
		else $n = $session ;
		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( $template ) ;
    	$mod -> SetModeliXe ( ) ;
    	if ( $navi ) $mod -> MxHidden ( "hidden", "navi=$navi" ) ;
    	else $mod -> MxHidden ( "hidden", "navi=".$n->genNavi($n->getNavi(0),$n->getNavi(1),$n->getNavi(2),$n->getNavi(3))) ;
    	$res = $mod -> MxWrite ( "1" ) ;
    	
    	// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "bloquant.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$ras  = preg_replace("/(\r\n|\n|\r)/", " ", nl2br($res) ) ;
	    $mod -> MxText ( "div", "'".$ras."'" ) ;
    	return $mod -> MxWrite ( "1" ) ;
	}
	
	static function genFormBarre ( $nom, $val=0, $max=100 ) {
		/*return '
		<div class="slider" id="'.$nom.'-div" tabIndex="1">
   			<input class="slider-input" id="'.$nom.'" name="'.$nom.'" value="'.$valeur.'"/>
   		</div>
		<script type="text/javascript">
				'.XhamTools::genFormBarreJS ( $nom ).'
   		</script>
   		' ;*/
   		return '
   		<table style="width: 200px; "  summary="Mise en forme"><tbody><tr>                  
        <td><div class="horizontal dynamic-slider-control slider" id="'.$nom.'Slider" style="height: 13px; width: 200px;">
        <input class="slider-input" id="'.$nom.'" name="'.$nom.'">
        </div>
        </div></td>
        <td style="vertical-align: middle;"><input id="valeur'.$nom.'" style="width: 20px;"></td>
        </tr></tbody></table>
		<script type="text/javascript">
				'.XhamTools::genFormBarreJS ( $nom, $val, $max ).'
   		</script>' ;
	}
	
	static function genFormBarreJS ( $nom, $val=0, $max=100 ) {
		/*return '
		var sliderEl = document.getElementById ? document.getElementById("'.$nom.'-div") : null;
		var inputEl = document.forms[0]["'.$nom.'"];
		var s = new Slider(sliderEl, inputEl);
		' ;*/
		
		return '
		var '.$nom.'s = new Slider(document.getElementById("'.$nom.'Slider"), document.getElementById("'.$nom.'"));
		'.$nom.'s.setMaximum('.$max.');
		var '.$nom.'b = document.getElementById("valeur'.$nom.'");
		'.$nom.'s.setValue('.$val.');
		'.$nom.'b.value = '.$nom.'s.getValue ( ) ;
		'.$nom.'s.onchange = function () {
			'.$nom.'b.value = '.$nom.'s.getValue();
		};
		'.$nom.'b.onchange = function () {
            '.$nom.'s.setValue ( '.$nom.'b.value ) ;
		}
		' ;
	}
	
	static function genFormText ( $nom, $contenu ) {
		return '<textarea id=formText'.$nom.' name='.$nom.'>'.$contenu.'</textarea>' ;
	}
	
	// get affichage var
	static function getAV ( $val, $type='' ) {
		if ( $type ) {
			if ( $val == '0000-00-00 00:00:00' ) return VIDEDEFAUT ;
			else {
				$date = new clDate ( $val ) ;
				return $date -> getDate ( $type ) ;	
			}			
		} else {
			if ( $val ) return $val ; else return VIDEDEFAUT ;
		}
	}
	
	// Génération d'un post-it'
  	static function genInfoBulle ( $contenu ) {
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "OverLib.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Ajout des informations dans l'info-bulle.
    $text = preg_replace("/(\r\n|\n|\r)/", " ", nl2br($contenu) ) ;
    // eko ( $contenu ) ;
    $mod -> MxText ( "libelle", str_replace("'","\'", str_replace('"',"'",$text) ) ) ;
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }
	
	// Affichage d'un texte en échappant les éventuels caractères spéciaux.
	static function affichage ( $texte ) {
		return nl2br(htmlentities($texte)) ;
	}

	  	/**
  	 * genLinkPost
  	 * génère à l'affichage un lien, mais qui va envoyer des données post à l'affichage
  	 * @link string url action du formulaire
  	 * @contenu HTML contenu du lien
  	 * @params array tableau des données post à passer
  	 * @options string options à passer dans la balise a
  	 */



 static function genLinkPost($link,$contenu,$params,$options='') {
	global $autoLinkFormIndex ;
	if(! is_array($params))
	$params = array();
	if( ! $autoLinkFormIndex ) $autoLinkFormIndex = 0 ;
	$autoLinkFormIndex++ ;
	$idLink = "autolink_$autoLinkFormIndex";
	$idForm = "autolinkform_$autoLinkFormIndex";

	$js = " document.getElementById(\"$idForm\").submit();return false; " ;

	if(isset($options['confirm']))
		    $js = " if (  confirm('".addslashes($options['confirm'])."')  ) { $js } else { return false ; }  " ;


	$af= "<a  onclick='$js'  id='$idLink'  class='formlink' href='$link'  >$contenu</a>";
	$af .= "<form style='display:none;' id='$idForm' method='post' action='$link' ><fieldset>";
	foreach($params as $key => $val) {
		$af.= "<input type='hidden' name='$key' value='$val' />";
	}
	$af.= "</fieldset></form>";
	return $af;
}
  	

	
	
	// Génération d'un bouton de sauvegarde AJAX
	static function genSaveForm ( $nom, $url, $param, $reload='', $js='', $jssup='' ) {
		$nomsave = 'save'.$nom ;
		$js = "onclick=\"SaveForm ( '$nomsave', 'form$nom', '".URLNAVI."$url', '$param', '$reload', '$js', '$jssup' );return false;\"" ;
		//$img = '<img src="images/load.gif" alt="'.$nomsave.'load" id="'.$nomsave.'load" style="visibility:hidden;" />' ;
		$img = '' ;
		return '<input class="submit" type="submit" value="Enregistrer" disabled name="'.$nomsave.'" id="'.$nomsave.'" '.$js.' />'.$img ;
	} 
	
	// Génération d'un bouton qui change le bouton de sauvegarde
	static function genSaveFormJS ( $nom, $evt1='', $evt2='' ) {
		$nomsave = 'save'.$nom ;
		$js = "setActiveSave ( '$nomsave' );" ;
		if ( $evt1 ) {
			$js1 = "$evt1=\"$js\"" ;
		} else $js1 = '' ;
		if ( $evt2 ) {
			$js2 = "$evt2=\"$js\"" ;
		} else $js2 = '' ;
		if ( !$js1 AND !$js2 )
			return $js ;
		else return "$js1 $js2" ;
	}
}
?>