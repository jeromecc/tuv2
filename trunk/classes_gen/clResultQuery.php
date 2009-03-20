<?php

class clResultQuery {

  public $resultats = array ( ) ;

  // extraction des valeurs XML
  function get_xml_values ( $balises, $content ) {
    while ( list ( $key, $val ) = each ( $balises ) ) {
      $a_pos = strpos ( $content, "<$val>" ) + strlen ( "<$val>" ) ;
      $b_pos = strpos ( $content, "</$val>" ) ;
      $length = $b_pos - $a_pos ;
      $xml_values[$val] = substr ( $content, $a_pos, $length ) ;
    }
    return $xml_values ;
  }

  // retourne le contenu du fichier $filename
  function get_file_content ( $filename ) {
    //$filename = strtolower ( $filename ) ;
    if ( file_exists ( "queries_int/".$filename ) ) {
      $fd = fopen ( "queries_int/".$filename, "r" ) ;
      $rep = "queries_int/" ;
      //echo $rep;
    } elseif ( file_exists ( "queries_gen/".$filename ) ) {
      $fd = fopen ( "queries_gen/".$filename, "r" ) ;
      $rep = "queries_gen/" ;
    } else  {
      //echo "pas trouvé";
      fopen ( "index.php", "r" ) ;
    }
    $content = @fread ( $fd, filesize ( $rep.$filename ) ) ;
    global $errs ;
    if ( $errs AND ! $content ) $errs -> addErreur ( "clResulQuery : Impossible d'ouvrir le fichier '$filename'." ) ;
    
    @fclose ( $fd ) ;
    //echo $content;
    return $content ;
  }


  function Execute ( $type_entree, $requete, $param, $type_sortie='ResultQuery', $forceBase='' ) {
    global $nbRequetes ;
    global $tpRequetes ;
    // Pour forcer la base à requêter en MySQL
    $this->forceBase = $forceBase ;
    $type_entree = strtolower ( $type_entree ) ;
    $debut = $this->temps();
    switch ( $type_entree ) {
    case 'file'    : $res = $this->UseFile   ( $requete, $param ) ;break ;
    case 'fichier' : $res = $this->UseFile   ( $requete, $param ) ; break ;
    case 'ccam'    : $res = $this->UseFileCCAM   ( $requete, $param ) ; break ;
    case 'query'   : $res = $this->UseQuery  ( $requete, $param ) ; break ;
    case 'requete' : $res = $this->UseQuery  ( $requete, $param ) ; break ;
    default : $res = $this->UseQuery  ( $requete, $param ) ;  break ;
    }
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL AND function_exists ( 'eko' ) ) eko ( "Temps d'exécution de la requête : $total ($requete)" ) ;
    $nbRequetes++ ;
    $tpRequetes += $total ;

    $type_sortie = strtolower ( $type_sortie ) ;
    switch ( $type_sortie ) {
    case 'xml':
      while ( list ( $key, $val ) = each ( $res ) ) {
	$$key = $val ;
	if ( $key != "INDIC_SVC" ) { $tags[] = $key ; }
      }
      if ( sizeof ( $tags ) > 0 ) {
	for ( $i = 0 ; $i < $INDIC_SVC[2] ; $i++ ) {
	  $xml_data .= "<element>" ;
	  while ( list ( $key, $val ) = each ( $tags ) ) {
	    $col = $$val ;
	    ( ! isset ( $col[$i] ) ) ? $col[$i] = "" : "" ;
	    $xml_data .= "<".$val.">".$col[$i]."</".$val.">" ; } $xml_data .= "</element>" ; reset ( $tags ) ;
	}
      }
      $lib_indic_svc = array ( 'num_err'=>$INDIC_SVC[0], 'lib_err'=>$INDIC_SVC[1], 'affected_rows'=>$INDIC_SVC[2], 'id_created'=>$INDIC_SVC[3] ) ;
      while ( list ( $cle_indic, $val_indic ) = each ( $lib_indic_svc ) ) { $xml_indic_svc .= "<".$cle_indic.">".$val_indic."</".$cle_indic.">" ; }
      $xml_indic_svc = "<indic_svc>".$xml_indic_svc."</indic_svc>" ;
      $xml_data = "<result num='".$INDIC_SVC[2]."'>".$xml_data.$xml_indic_svc."</result>" ;
      $xml_data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>".$xml_data ;
      return $xml_data ;
      break ;
    case 'txt' :
      reset ( $res ) ;
      while ( list ( $clef, $valeur ) = each ( $res ) ) { $txt .= "$clef\t"; } $txt .= "\n" ;
      for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
	reset ( $res ) ; while ( list ( $clef, $valeur ) = each ( $res ) ) { if ( $valeur[$i] ) $txt .= $valeur[$i]."\t"; else $txt .= "0\t" ; } $txt .= "\n" ;
      }
      return $txt ;
      break ;
    default :
      $res['INDIC_SVC']['temps'] = $total ;
      return $res ;
      break ;
    }

  }


  // Execute la requête avec les paramètres contenus dans le tabeau $Param
  function UseQuery ( $requete, $value_config ) {
    $value_query['id_config'] = 0 ;
    $value_query['colonnes']  = '' ;
    $value_query['code_sql']  = $requete ;
    $resultats = $this->execute_request ( $value_query, $value_config, Array() ) ;
    return $resultats ;
  }

  // fonction permettant de récupérer le temps écoulé depuis l'époque UNIX ( 1 - 1 1970 )
  function temps() {
    $time = microtime();
    $tableau = explode(" ",$time);
    return ($tableau[1] + $tableau[0]);
  }

  // Execute la requête avec les paramètres contenus dans le tabeau $Param
  function UseFile ( $requete, $Param, $find="NOM" ) {
    global $errs ;
    $query_file = $requete.".qry" ;
    //echo $query_file;
    $content_query = $this->get_file_content($query_file) ;
    //$content_query;
    $Balises_qry = array ( 0=>"id_config", 1=>"colonnes", 2=>"code_sql" ) ;
    $value_query = $this->get_xml_values ( $Balises_qry, $content_query ) ;
    $config_file = $value_query['id_config'].".cfg" ;
    $content_config = $this->get_file_content ( $config_file ) ;
    $Balises_cnf = array ( 0=>"type", 1=>"host", 2=>"login", 3=>"password", 4=>"db" ) ;
    $value_config = $this->get_xml_values ( $Balises_cnf, $content_config ) ;
    //eko ( $Balises_cnf ) ;
    // eko ( $value_config ) ;
    $resultats = $this->execute_request ( $value_query, $value_config, $Param ) ;
    if ( isset ( $errs ) AND $resultats['INDIC_SVC'][1] ) $errs -> addErreur (  "clResultQuery :<br>".$resultats['INDIC_SVC'][0]."<br>".$resultats['INDIC_SVC'][1]."<br>".$resultats['INDIC_SVC'][2]."<br>".$resultats['INDIC_SVC'][15] ) ;
    //eko ( $resultats[INDIC_SVC] ) ;
    return $resultats ;
  }

   // Execute la requète definie par son identifiat avec les paramètres contenus dans le tabeau $Param
  function Use_Query_Id ( $id_requete, $Param ) {
    $resultats = $this->Use_Query ( $id_requete, $Param, "ID" ) ;
    return $resultats ;
  }

  function get_xml ( $requete, $Param, $find="NOM" ) {
    /* Execute la requète definie par son nom ou son identifiant avec les paramètres contenus dans le tabeau $Param et retourne les résultats au format XML */
    if ( $find == "NOM" ) { $Resultats = $this->Use_Query ( $requete, $Param ) ; }
    else { $Resultats = $this->Use_Query_Id ( $requete, $Param ) ; }
    while ( list ( $key, $val ) = each ( $Resultats ) ) {
      $$key = $val ;
      if ( $key != "INDIC_SVC" ) { $tags[] = $key ; }
    }
    if ( sizeof ( $tags ) > 0 ) {
      for ( $i = 0 ; $i < $INDIC_SVC[2] ; $i++ ) {
	$xml_data .= "<element>" ;
	while ( list ( $key, $val ) = each ( $tags ) ) {
	  $col = $$val ;
	  ( ! isset ( $col[$i] ) ) ? $col[$i] = "" : "" ;
	  $xml_data .= "<".$val.">".$col[$i]."</".$val.">" ;
	}
	$xml_data .= "</element>" ;
	reset ( $tags ) ;
      }
    }

    // gestion des indicateurs de service
    $lib_indic_svc = array ( 'num_err'=>$INDIC_SVC[0], 'lib_err'=>$INDIC_SVC[1], 'affected_rows'=>$INDIC_SVC[2], 'id_created'=>$INDIC_SVC[3] ) ;
    while ( list ( $cle_indic, $val_indic ) = each ( $lib_indic_svc ) ) { $xml_indic_svc .= "<".$cle_indic.">".$val_indic."</".$cle_indic.">" ; }
    $xml_indic_svc = "<indic_svc>".$xml_indic_svc."</indic_svc>" ;
    // Fin de gestion des indicateurs de service
    $xml_data = "<result num='".$INDIC_SVC[2]."'>".$xml_data.$xml_indic_svc."</result>" ;
    $xml_data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>".$xml_data ;

    return $xml_data ;
  }

  function get_xml_Id ( $id_requete, $Param ) {
    /* Execute la requète definie par son identifiat avec les paramètres contenus dans le tabeau $Param et retourne les résultats au format XML */
    $xml_data = $this->get_xml ( $id_requete, $Param, "ID" ) ;
    return $xml_data ;
  }


  function write_get_xml ( $requete, $Param, $file_path, $find = "NOM" ) {
    	/* Execute la requète definie par son nom ou identifiant
        avec les paramètres contenus dans le tabeau $Param
    	et Ecrit les résultats dans le fichier xml $file_path
        */
    if ( $find == "NOM" ) { $xml_data = $this->get_xml ( $requete, $Param ) ; }
    else { $xml_data = $this->get_xml_Id ( $requete, $Param ) ; }
    $fp = fopen ( $file_path, "w" ) ;
    fwrite ( $fp, $xml_data ) ;
    fclose ( $fp ) ;
  }

  function write_get_xml_Id ( $requete, $Param, $file_path ) {
    /* Execute la requète definie par son  identifiant
        avec les paramètres contenus dans le tabeau $Param
    	et Ecrit les résultats dans le fichier xml $file_path
    */
    $xml_data = $this->write_get_xml ( $requete, $Param, $file_path, "ID" ) ;
  }

  function affiche_sql_request ( $Resultats ) {
    while ( list ( $key, $val ) = each ( $Resultats ) ) {
      $table_header .= "<th>".$key."</th>" ;
      while ( list ( $cle, $valeur ) = each ( $val ) ) { $table_part[$key] .= $valeur."<br>" ; }
      $table_content .= "<td valign=\"top\">".$table_part[$key]."</td>" ;
    }
    $table = "<table border=1 align=\"center\"><tr>$table_header</tr><tr>$table_content</tr></table>" ;
    return $table ;
  }

  function execute_request ( $value_query, $value_config, $Param ) {
	global $debug;
    //Constitution de la requete finale
    $requete = $value_query['code_sql'] ;
    
    if (! is_array($Param)) {
    	eko("pb d'argument pour requete $requete");
    	}
    
    while ( list ( $key, $val ) = each ( $Param ) ) {
      $searched_str = "$".$key ;
      $requete = str_replace ( $searched_str, $val, $requete ) ;
    }
    // echo $requete;
    // On place les colonnes à ramener dans un tableau $tab_cols
    if ( $value_query['colonnes'] != '*' & $value_query['colonnes'] != '' ) { $tab_cols = split ( ",", $value_query['colonnes'] ) ; }

    switch ( $value_config['type'] ) {
    case "MySQL":
      // Connexion au serveur Mysql & Execution de la requete
      // eko ( $value_config ) ;
      $conn = @mysql_connect ( $value_config['host'], $value_config['login'], $value_config['password'] ) ;
      if ( ! $conn ) {
	global $errs ;
	if ( $errs ) $errs -> addErreur ( "La connexion au serveur MySQL \"".$value_config['host']."\" avec l'utilisateur \"".$value_config['login']."\" et le mot de passe \"*******\" a échoué." ) ;
      }
      // eko ( "connexion : ".$value_config[login]."/".$value_config[password]."@". $value_config[host] ) ;
      $INDIC_SVC[0] = mysql_errno ( ) ;
      $INDIC_SVC[1] = mysql_error ( ) ;
      // pas de problème de connexion
      if ( ! $INDIC_SVC[0] ) {
	// execution de la raquete
	if ( ! $this->forceBase )
		$result = @mysql_select_db ( $value_config['db'] ) ;
	else $result = @mysql_select_db ( $this->forceBase ) ;
	$result = @mysql_query ( $requete ) ;
	//echo "<h1>REQUETE : $requete</h1>";
	$INDIC_SVC[0] = mysql_errno ( ) ;
	$INDIC_SVC[1] = mysql_error ( ) ;
	// pas de problème à l'execution de la requete
	if ( ! $INDIC_SVC[0] ) {
	  // Analyse du type de requete
	  if ( DEBUGSQL AND function_exists ( 'eko' ) ) eko ( $requete ) ;
	  $qry_type = explode (" ", $requete ) ;
	  switch ( strtoupper ( $qry_type[0] ) ) {
	  case "SELECT" :
	    $nrows = mysql_num_rows ( $result ) ;
	    if ( $nrows ) {
	      // le nom des colonnes a ramener est *
	      if ( ! isset ( $tab_cols ) ) { for ( $i = 0 ; $i < mysql_num_fields ( $result ) ; $i++ ) { $tab_cols[] = mysql_field_name ( $result, $i ) ; } }
	      // Maintenant On connait $tab_cols
	      while ( $record = mysql_fetch_array ( $result ) ) {
		// affectation dans les tableau de colonnes
		//jeton de debugage quand même colones
 		unset($jeton);
		while ( list ( $key, $val ) = each ( $tab_cols ) ) {
			if(! isset($jeton[$val])) $jeton[$val] = true ;
				else $jeton[$val] = false ;
			if ( isset ( $$val )  ) {
				if ( $jeton[$val] ) {
					$$val .= $record [ $val ] . "§" ;
					//if ($debug) eko($val."-".$$val);
					}
				}
			else 
				$$val = $record [ $val ] . "§" ; 
			}
		reset ( $tab_cols ) ;
	      }
	      //Construction des tableaux de colonnes
		unset($jeton);	
		//print "********************* Tableau **********************<br>".affTab ( $tab_cols ) ;
	      while ( list ( $key, $val ) = each ( $tab_cols ) ) {
		//print "/////////////////// Case ////////////////////<br>".affTab ( $jeton[$val] ) ;
		if(! isset($jeton[$val]))
			$jeton[$val] = true ;
		else
			$jeton[$val] = false ;
		
		if($jeton[$val]) {
			// on retire le dernier |
			//print "<br>dollardollarval : ".$$val ;
			$$val = substr ( $$val, 0, strlen ( $$val ) - 1 ) ;
			$resultats[$val] = explode ( "§", $$val ) ;
			}
		//if ($debug) eko($resultats[$val]);
	      }
	    }
	    $INDIC_SVC[2] = $nrows ;
	    break ;
	  case "INSERT" :
	    $INDIC_SVC[2] = mysql_affected_rows ( ) ;
	    $INDIC_SVC[3] = mysql_insert_id ( ) ;
	    break ;
	  case "UPDATE" :
	  case "DELETE" :
	    $INDIC_SVC[2] = mysql_affected_rows ( ) ;
	    break ;
	  }
	}
      }
      //mysql_close ( $conn ) ;
      break;

	case "MSSQL":
      // Connexion au serveur Mssql & Execution de la requete
      //print affTab ( $value_config ) ;
      //mssql_connect('galileo.ch-brignoles.fr','user_dim' ,'') || die ( 'hop' ) ;
      //mssql_connect("galileo","user_dim","") ||die ("Connexion impossible au serveur!"); 
      $conn = mssql_pconnect ( $value_config['host'], $value_config['login'], $value_config['password'] ) ;
      if ( ! $conn ) {
	  	global $errs ;
		if ( $errs ) $errs -> addErreur ( "La connexion au serveur MsSQL \"".$value_config['host']."\" avec l'utilisateur \"".$value_config['login']."\" et le mot de passe \"*******\" a échoué." ) ;
      }
      // eko ( "connexion : ".$value_config[login]."/".$value_config[password]."@". $value_config[host] ) ;
      //$INDIC_SVC[0] = mssql_get_last_message ( ) ;
      //$INDIC_SVC[1] = mssql_get_last_message ( ) ;
      
      // pas de problème de connexion
      if ( ! $INDIC_SVC[0] ) {
		// execution de la raquete
		$result = mssql_select_db ( $value_config['db'] ) ;
		
		$result = mssql_query ( $requete ) ;
		//echo "<h1>REQUETE : $requete</h1>";
		//$INDIC_SVC[0] = mssql_get_last_message ( ) ;
		//$INDIC_SVC[1] = mssql_get_last_message ( ) ;
		// pas de problème à l'execution de la requete
		if ( ! $INDIC_SVC[0] ) {
		  	// Analyse du type de requete
	  		if ( DEBUGSQL AND function_exists ( 'eko' ) ) eko ( $requete ) ;
	  		$qry_type = explode (" ", $requete ) ;
	  		switch ( strtoupper ( $qry_type[0] ) ) {
	  			case "SELECT" :
	    			
	    			$nrows = mssql_num_rows ( $result ) ;
	    			//eko ( "nombre de lignes : $nrows" ) ;
	    			if ( $nrows ) {
	      				// le nom des colonnes a ramener est *
	      				if ( ! isset ( $tab_cols ) ) { for ( $i = 0 ; $i < mssql_num_fields ( $result ) ; $i++ ) { $tab_cols[] = mssql_field_name ( $result, $i ) ; } }
	      				// Maintenant On connait $tab_cols
	      				while ( $record = mssql_fetch_array ( $result ) ) {
							// affectation dans les tableau de colonnes
							//jeton de debugage quand même colones
 							unset($jeton);
							while ( list ( $key, $val ) = each ( $tab_cols ) ) {
								if(! isset($jeton[$val])) $jeton[$val] = true ;
								else $jeton[$val] = false ;
								if ( isset ( $$val )  ) {
									if ( $jeton[$val] ) {
										$$val .= $record [ $val ] . "§" ;
										// eko ( $record[$val] ) ;
										//if ($debug) eko($val."-".$$val);
									}
								}
								else 
									$$val = $record [ $val ] . "§" ; 
							}
							reset ( $tab_cols ) ;
	      				}
	      				// Construction des tableaux de colonnes
						unset($jeton);	
	      				while ( list ( $key, $val ) = each ( $tab_cols ) ) {
							if(! isset($jeton[$val]))
								$jeton[$val] = true ;
							else
								$jeton[$val] = false ;
		
							if($jeton[$val]) {
								// on retire le dernier |
								//print "<br>dollardollarval : ".$$val ;
								$$val = substr ( $$val, 0, strlen ( $$val ) - 1 ) ;
								$resultats[$val] = explode ( "§", $$val ) ;
							}
							//if ($debug) eko($resultats[$val]);
	      				}
	    			}
	    		$INDIC_SVC[2] = $nrows ;
	    break ;
	  case "INSERT" :
	    //$INDIC_SVC[2] = mssql_affected_rows ( ) ;
	    //$INDIC_SVC[3] = mssql_insert_id ( ) ;
	    break ;
	  case "UPDATE" :
	  case "DELETE" :
	    //$INDIC_SVC[2] = mssql_affected_rows ( ) ;
	    break ;
	  }
	}
      }
      mssql_close ( $conn ) ;
      break;

    case "ORACLE" :
      //echo "oracle";
      // Connexion
      // eko ($value_config);
      //      $conn = @OCILogon ( $value_config['login'], $value_config['password'], $value_config['db'] ) ;
      $conn = @oci_pconnect ( $value_config['login'], $value_config['password'], $value_config['db'] ) ; 
      //global $conn ;
      $conn_error = ocierror ( ) ;
      // Problème à la connexion
      if ( $conn_error ) {

	$INDIC_SVC[0] = $conn_error['code'] ;
	$INDIC_SVC[1] = $conn_error['message'] ;
	// pas de problème de connexion
      } else { 	
     // print $requete;	
	// execution de la raquete
	$stmt = OCIParse ( $conn, $requete ) ;
	
	// print ( $requete ) ;
	if ( DEBUGSQL AND function_exists ( 'eko' ) ) eko ( $requete ) ;
	OCIExecute ( $stmt ) ;
	$INDIC_SVC[0] = $conn_error['code'] ;
	$INDIC_SVC[1] = $conn_error['message'] ;
	// pas de problème à l'execution de la requete
	if ( ! $INDIC_SVC[0] ) {
	  // Analyse du type de requete
	  $qry_type = OCIStatementType ( $stmt ) ;
	  switch ( $qry_type ) {
	  case "SELECT" :
	    $nrows = OCIFetchStatement ( $stmt, $results ) ;
	    // eko ( "<P>There are $nrows records containing your criteria. ($requete)</P>" ) ;
	    if ( $nrows ) {
	    // le nom des colonnes a ramener n'a pas été spécifié ou est *
	      if ( !isset ( $tab_cols ) ) {
	      $ncols = OCINumCols ( $stmt ) ;
	      for ( $k = 1 ; $k <= $ncols ; $k++ ) { $tab_cols[] = OCIColumnName ( $stmt, $k ) ; }
	      }
	      for ( $j = 0 ; $j < $nrows ; $j++ ) {
		if ( isset ( $tab_cols ) AND is_array ( $tab_cols ) ) 
		  while ( list ( $key, $val ) = each ( $tab_cols ) ) { 
		    if ( isset ( $$val ) ) $$val.=$results[$val][$j]."§" ; 
		    else $$val = $results[$val][$j]."§" ; 
		  }
		reset ( $tab_cols ) ;
	      }
	    }

	    //Construction des tableaux de colonnes
	    if ( isset ( $tab_cols ) AND is_array ( $tab_cols ) ) {
	      while ( list ( $key, $val ) = each ( $tab_cols ) ) {
		// on retire le dernier |
		$$val = substr ( $$val, 0, strlen ( $$val ) - 1 ) ;
		$resultats[$val] = explode ( "§", $$val ) ;
	      }
	    }
	    $INDIC_SVC[2] = $nrows ;
	    break ;

	  case "INSERT" :
	    $nrows = OCIRowCount ( $stmt ) ;
	    $INDIC_SVC[2] = $nrows ;
	    break ;
	  case "UPDATE" :
	  case "DELETE" :
	    $nrows = OCIRowCount ( $stmt ) ;
	    $INDIC_SVC[2] = $nrows ;
	    break ;
	  }
	}
      }
      oci_close ( $conn ) ;
      break ;

    case "LDAP" :
      // Connexion au serveur LDAP
      $ds = @ldap_connect ( $value_config['host'] ) ;
      $bind = @ldap_bind ( $ds ) ;
      if ( $ds ) {
	// On eclate les instructions LDAP dans un tableau
	$instructions_ldap = explode ( "##", $requete ) ;
	$chemin = $instructions_ldap[0] ;
	$filtre = $instructions_ldap[1] ;
	// Fin de gestion du code sql
	// Execution de la requete
	$sr = ldap_search ( $ds, $chemin, $filtre ) ;
	// le nom des colonnes a ramener n'a pas été spécifié ou est *
	if ( ! isset ( $tab_cols ) ) {
	  $entry = ldap_first_entry ( $ds, $sr ) ;
	  $attrs = ldap_get_attributes ( $ds, $entry ) ;
	  for ( $l = 0 ; $l < sizeof ( $attrs ) ; $l++ ) { $tab_cols[] = $attrs[$l] ; }
	}
	$result = ldap_get_entries ( $ds, $sr ) ;
	for ( $i = 0 ; $i < $result["count"] ; $i++ ) {
	  // test des attributs multivalués
	  while ( list ( $key, $val ) = each ( $tab_cols ) ) {
	    for ( $v = 0 ; $v < sizeof ( $result[$i][$val] ) ; $v++ ) {
	      if ( $result[$i][$val][$v] != "" ) {
		if ( $v == 0 ) { $separateur = "" ; }
		else { $separateur = "##" ; }
		$valeur_brut = explode ( ",", $result[$i][$val][$v] ) ;
		if ( $valeur_brut[1] ) { $valeur_pure = explode ( "cn=", $valeur_brut[0] ) ; }
		else { $valeur_pure[1] = $result[$i][$val][$v] ; }
		$$val .= $separateur.$valeur_pure[1] ;
	      }
	    }
	    $$val .= "," ;
	  }
	  reset ( $tab_cols ) ;
	}
	// Construction des tableaux de colonnes
	while ( list ( $key, $val ) = each ( $tab_cols ) ) {
	  // on retire la dernière virgule
	  $$val = substr ( $$val, 0, strlen ( $$val ) - 1 ) ;
	  $resultats[$val] = explode ( ",", $$val ) ;
	}
      }
      $INDIC_SVC[0] = ldap_errno ( $ds ) ;
      $INDIC_SVC[1] = ldap_error ( $ds ) ;
      $INDIC_SVC[2] = $result["count"] ;
      break ;
    }
    $INDIC_SVC[15] = $requete ;
    if ( isset ( $Param['RIFIFI'] ) ) { echo "indicsvc[0]:$INDIC_SVC[0]<br>indicsvc[1]:$INDIC_SVC[1]<br>indicsvc[2]:$INDIC_SVC[2]<br>indicsvc[3]:$INDIC_SVC[3]<br>indicsvc[15]:$INDIC_SVC[15]<br>\n"; }
    $resultats['INDIC_SVC'] = $INDIC_SVC ;
    return $resultats ;
  }
}


?>
