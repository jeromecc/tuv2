<?PHP


/*
 *	$Id: ListMaker.php3,v 1.28 2003/10/29 15:55:49 marc Exp $
 *	Usage:		include file - Class:  ListMaker
 *	Authors:	Marc Druilhe <mdruilhe@w-agora.net>
 */

 if (defined('_LISTMAKER')) return;

 define('_LISTMAKER', 1);
 define('_LM_DIR', '.');

 // multi-line break
 define('BREAK_HORIZONTAL', 1);
 define('BREAK_VERTICAL', 2);

 // ORDER BY clause insert
 define('POSITION_BEFORE', 1);
 define('POSITION_AFTER', 2);

 // Error messages
 define ('MSG_WARNING', "<font color='red'><b>Attention!:</b></font> <tt>%s</tt><br>\n%s<br>");# text, reason
 define ('MSG_ERROR', "<b><font color='red'>erreur %s: </font></b><tt>%s</tt><br>\n%s<br>\n");# component, msg, reason
 define ('MSG_SESSION_HALTED', "session terminée.");
 define ('ERROR_CODE_WAS', "Le code erreur est: <tt>%s</tt>");

/**
 *
 * List template engine
 *
 * This class is designed to generate HTML listings (or any other format) using a templating
 * system. It handle auto-pagination, multi-columns, sortable column headers, ...
 *
 * @version		1.05
 * @author		Marc Druilhe (mdruilhe@w-agora.net)
 */
 class ListMaker  {

 public $lm_delimiter = "LM:";	// Delimiteur de bloc => <LM:name> ... </LM:name>

 // Private : Gestion interne de la liste
 public $children = array();	// array of keys
 public $entries = array();	// array in which notes are stored
 public $parent_field;
 public $keys;

 // private: Error handling
 public $Halt_On_Error = "yes";
 public $Error = "";
 public $Errno = "";

 public $variables = array();

 // Gestion traduction/remplacement automatique
 public $translations;				// table des valeurs à remplacer
 public $translations_options;		// options de remplacement

 public $self_url;
 public $template;

 public $level;

 public $_html;				// Buffer de la page a afficher

 // Gestion "pliage/depliage automatique
 public $collapsedKeys = "";	// Liste des items "repliés"
 public $expandedKeys = "";	// Liste des items "dépliés"
 public $expand_icon = "" ;	// Icone '+'
 public $collapse_icon = "" ;	// Icone '-'
 public $leaf_icon = "" ;		// Icone ' ' (feuille)
 public $level_padding = "" ;	// pattern d'indentation

 // Alternance des couleurs
 public $odd_color = "";
 public $even_color = "";

 // Multi-colonnes automatique
 public $auto_mc = false;
 public $mc_nb_columns = 1;		// Nombre de colonnes a afficher
 public $mc_break_string = "";		// Motif terminant une ligne (retour a la ligne)
 public $mc_break_direction;		// Sens d'affichage (HOR ou VERT)

 // Tri auto
 public $sort_headers = array();
 public $default_sort = "";
 public $order_by = "";
 public $order_position = POSITION_BEFORE;

 public $sort_arrow_none = "";
 public $sort_arrow_down = "";
 public $sort_arrow_up = "";

 // Couleur de la colonne selectionnée
 public $unselected_sort_color = "#ffffff";
 public $selected_sort_color = "#e0eaf6";

 // Pagination
 public $TotalCount = 0;	// Total number of items/records in the full query/list
 public $ItemCount = 0;	// Total number of items (records) displayed in the current page
 public $ItemNum = 0;		// Numero de l'item courant

 public $FirstItem = 0;
 public $LastItem = 0;
 public $PageCount = 0;
 public $PageNumber = 0;

 public $UrlFirstPage = "";
 public $UrlPrevPage = "";
 public $UrlNextPage = "";
 public $UrlLastPage = "";
 public $UrlPageNav = "";

 public $url_first_text = "";
 public $url_last_text = "";
 public $url_prev_text = "";
 public $url_next_text = "";
 public $show_inactive_nav_button = false;
 public $nb_result_per_page = 0;

// call back function
 public $plugin;

 public $cache_active ;

 /**
  * Constructeur
  *
  * Initialise les variables, gère les cookies, charge le template si défini
  *
  * @param	string	$tpl		Nom du fichier template à utiliser
  * @param	string	$self_url	URL à utiliser dans les liens générés par ListMaker
  * @return	void
  */
 function ListMaker ($tpl='', $self_url='', $use_cookies=false) {
	 global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $HTTP_SERVER_VARS;

	$mtime = explode(" ", microtime());
	$this->start_time = $mtime[1] + $mtime[0];

	$this->auto_mc = false;

	$this->sort_arrow_none = '&nbsp;<img src="'. _LM_DIR .'/images/none.gif" border="0" align="absmiddle" width="8" height="7">';
	$this->sort_arrow_down = '&nbsp;<img src="'. _LM_DIR .'/images/sort_down.gif" border="0" align="absmiddle" width="8" height="7">';
	$this->sort_arrow_up = '&nbsp;<img src="'. _LM_DIR .'/images/sort_up.gif" border="0" align="absmiddle" width="8" height="7">';

	$this->url_first_text = '<img src="'._LM_DIR .'/images/btn_begin.gif" width="24" height="22" border=0>';
	$this->url_last_text = '<img src="'._LM_DIR .'/images/btn_end.gif" width="24" height="22" border=0>';
	$this->url_prev_text = '<img src="'._LM_DIR .'/images/btn_prev.gif" width="24" height="22" border=0>';
	$this->url_next_text = '<img src="'._LM_DIR .'/images/btn_next.gif" width="24" height="22" border=0>';

	if(!empty($tpl)) {
 		$this->_parseTemplate ($tpl);
	}

	if(!empty($self_url)) {
 		$this->setSelfUrl ($self_url);
	} else {
		$this->setSelfUrl (basename($HTTP_SERVER_VARS['PHP_SELF']));
	}

	$this->LastItem = 0;
	$this->ItemNum = 0;
	$this->parent_field = '';
	$this->tree_view = false;

	// Handle cookies / expand / collapse stuff
	// ----------------------------------------
	if ($use_cookies) {
		
		if (@is_array($HTTP_COOKIE_VARS) ) {
			if (isset($HTTP_COOKIE_VARS['collapse'])) {
				$this->collapsedKeys = $HTTP_COOKIE_VARS['collapse'];
			}
			if (isset($HTTP_COOKIE_VARS['expand'])) {
				$this->expandedKeys = $HTTP_COOKIE_VARS['expand'];
			}
		}

		$setcookie=false;
		if (!empty($HTTP_GET_VARS['expand']) ) {
			$expand = $HTTP_GET_VARS['expand'];
			if (!preg_match("/([=+])$expand\+/", $this->expandedKeys, $match) ) {
				$this->expandedKeys .= "$expand+";
			}
			$this->collapsedKeys = preg_replace ("/([=+])$expand\+/", '\\1', $this->collapsedKeys);
			$setcookie=true;
		} elseif (!empty($HTTP_GET_VARS['collapse']) ) {
			$collapse = $HTTP_GET_VARS['collapse'];
			if (!preg_match("/([=+])$collapse\+/", $this->collapsedKeys, $match) ) {
				$this->collapsedKeys .= "$collapse+";
			}
			$this->expandedKeys = preg_replace ("/([=+])$collapse\+/", '\\1', $this->expandedKeys);
			$setcookie=true;
		}

		if ($setcookie) {
			if (!empty($this->expandedKeys)) {
				@setCookie ("expand", $this->expandedKeys, 0, '/');
			}
			if (!empty($this->collapsedKeys)) {
				@setCookie ("collapse", $this->collapsedKeys, 0, '/');
			}
		}
	}

 } // end func

/**
 * Returns the elapsed time since the object has been instantiated
 *
 * @access	public
 * @return	string	exec_time (formated)
 */
function getExecTime () {
	$mtime = explode(' ', microtime());
	$mtime = $mtime[1] + $mtime[0];
	return sprintf("%f sec.", $mtime - $this->start_time);
} // end func

/**
 * Positionne le flag "halt_on_error" et retourne l'ancienne valeur
 *
 * @param  string	$value	valeurs possibles:
 *							- "yes": affiche un message d'erreur et termine le programme
 *							- "no" : ignore les erreurs silencieusement
 *							- "report" : ignore les erreurs mais affiche un "warning"
 * @access    public
 * @return    boolean	l'ancienne valeur
 * @see	halt
 */
 function setHaltOnError ($value="yes") {
	$old = $this->Halt_On_Error;
	$this->Halt_On_Error = $value;
	return $old;
 }

/**
 * Gestion des erreurs
 *
 * Affiche un message d'erreur et arrete le programme ou continue en fonction du flag "halt_on_error"
 * @param	string $msg	message d'erreur a afficher
 * @access	public
 * @return	void
 * @see	setHaltOnError
 */
 function halt($msg) {
	if ($this->Halt_On_Error == "no")
		return;

	$reason =  $this->Error;
	if (!empty($this->Errno) ) {
	    $reason = '['.$this->Errno.'] '.$reason;
	}

	print '</td></tr></table></td></tr></table></td></tr></table><br><br>';
	if ($this->Halt_On_Error == "report") {
		printf(MSG_WARNING, $msg, $reason);
	} else {
		printf(MSG_ERROR, 'ListMaker', $msg, $reason);
	}

	if ($this->Halt_On_Error != "report")
		die(MSG_SESSION_HALTED);
 }

 /**
  * Initialize the current URL to be set in all generated URLs
  *
  * Detail description
  * @param	string $url	L'URL a positionner
  * @access	public
  * @return	void
  * @see	addUrlVar
  */
 function setSelfUrl ($url="") {

	$this->self_url = $url;
 } // end func


 /**
  * Ajoute un variable qui doit etre transmise sur les URLs générés
  *
  * @param	string $name	Nom de la variable
  * @param	string $value	Contenu de la variable
  * @access	public
  * @return	void
  * @see	setSelfUrl
  */
  function addUrlVar ($name, $value="") {

	if (strstr($this->self_url, '#')) {
		list ($url, $fragment) = explode('#', $this->self_url);
	} else {
		$url = $this->self_url;
		$frament = '';
	}

	if (preg_match("/$name=([^&]*)/i", $url, $match)) {
		$url = str_replace("$name=".$match[1], "$name=".urlencode($value), $url);
	} else {
		$url .= $this->_encodeVar ($name, $value);
	}

	if (!empty($fragment) ) {
		$url .= '#'.$fragment;
	}

	$this->self_url = $url;

 } // end func


 /**
  * Enable Cache mode
  *
  * @access	public
  * @see	disableCache
  */
 function enableCache () {
	global $HTTP_SESSION_VARS;

	@session_start();
	$this->cache_active = true;

 } // end func

 /**
  * Disable Cache mode
  *
  * @access	public
  * @see	enableCache
  * @return	void
  */
 function disableCache () {
	global $HTTP_SESSION_VARS;

	$this->cache_active = false;
	$this->clearCache();
 } // end func


 /**
  * Short description.
  *
  * Detail description
  * @param
  * @since	1.0
  * @access	private
  * @see
  * @return	void
  */
 function clearCache () {
	global $HTTP_SESSION_VARS;

	if (isset($HTTP_SESSION_VARS["lm_entries"])) {
		unset($HTTP_SESSION_VARS["lm_children"]);
		unset($HTTP_SESSION_VARS["lm_entries"]);
		unset($HTTP_SESSION_VARS["lm_ItemCount"]);
	}
 } // end func

 /**
  * Short description.
  *
  * Detail description
  * @param
  * @since	1.0
  * @access	private
  * @see
  * @return	void
  */
 function isCached () {
	global $HTTP_SESSION_VARS;

	return (isset($HTTP_SESSION_VARS["lm_entries"]));
 } // end func

 /**
  * Définit une fonction "callback" appelée pour chaque item
  *
  * Permet de définir le nom d'une fonction qui sera appelée par listMaker pour chaque item (ou ligne)
  * de la liste, avant son affichage. ListMaker passe l'item (tableau associatif) en paramètre à la
  * fonction : $item = function($item)
  * Celle-ci doit donc retourner l'enregistrement éventuellement modifié.
  * @param      string $function_name	Le nom de la fonction
  * @access     public
  * @return     void
  */
 function setPlugin ($function_name) {
 	$this->plugin = $function_name;
 }


 /**
  * Definit les champs servant de lien d'arborescence (pere -> fils)
  *
  * Définit le nom du champ qui servira de lien pour retrouver l'enregistrement parent
  * parmi les items déjà insérés par addItem().
  * @param      string $parent_fieldname
  * @param      string $link_to_parent_fieldname
  * @access     public
  * @return     void
  * @see		addItem
  */
 function setParent($parent_fieldname, $link_to_parent_fieldname='') {
 	$this->parent_field = $parent_fieldname;
 	$this->link_to_parent = $link_to_parent_fieldname;
	$this->tree_view = true;

	if (empty($this->expand_icon)) {
		$this->expand_icon = '<img src="'. _LM_DIR .'/images/expand.gif" width="11" height="11" border=0 alt="+">';
	}
	if (empty($this->collapse_icon)) {
		$this->collapse_icon = '<img src="'. _LM_DIR .'/images/collapse.gif" width="11" height="11" border=0 alt="-">';
	}
	if (empty($this->leaf_icon)) {
		$this->leaf_icon = '<img src="'. _LM_DIR .'/images/none.gif" width="11" height="11" border=0 alt="">';
	}
 }


 /**
  * Définition des entetes pour le tri
  *
  * @param      string $name		Nom du selecteur de tri => valeur passé en paramètre sur l'URL
  *									(ex: $name=u ajoute le parametre &sort=u)
  * @param      string $label		Le texte affiché (clickable)
  * @param      string $col_name	Nom de la (des) colonne(s) sur lequel s'effectue le tri (BD)
  * @param      string $sort_type	Type de tri (SORT_REGULAR = "standard" | SORT_NUMERIC = "numerique" | SORT_STRING = "ASCII")
  * @param      string $help		Le texte affiché en bulle d'aide
  * @access     public
  * @return     void
  * @see		setDefaultSort
  * @see		setOrderBy
  * @see		setSortColor
  * @see		setSortArrows
  */
 function setSortColumn($name, $label, $col_name, $sort_type=SORT_REGULAR, $help="") {

	if ( ($sort_type != SORT_REGULAR) && ($sort_type != SORT_NUMERIC) && ($sort_type != SORT_STRING) ) {
		$sort_type = SORT_REGULAR;
	}

 	$this->sort_headers[$name] = array (
		'label' => $label,
		'order_by' => $col_name,
		'help' => $help,
		'sort_type' => $sort_type
	);

	if (empty($this->default_sort) ) {
	 	$this->default_sort = $name;
	}

 	$this->auto_sort = true;

 }

 /**
  * Definit le selecteur de tri par defaut
  *
  * @param      string $name		Nom du selecteur de tri
  * @access     public
  * @return     void
  * @see		setSortColumn
  * @see		setOrderBy
  * @see		setSortColor
  * @see		setSortArrows
  */
 function setDefaultSort($name) {
 	$this->default_sort = $name;
 }


 /**
  * Ajoute une clause ORDER BY a la requete générée par dbGetList()
  *
  * Permet de forcer un ordre de tri constant indépendant des tris automatiques
  * Le paramètre $position permet d'insérer la clause ORDER BY avant ou apres les critères
  * de tris générés par ListMaker (setSortColumn)
  *
  * @param      string $order_by	Clause ORDER BY
  * @param		string	$position	valeurs possibles: POSITION_BEFORE, POSITION_AFTER
  * @access     public
  * @return     void
  * @see		setSortColumn
  * @see		setDefaultSort
  * @see		setSortColor
  * @see		setSortArrows
  */
 function setOrderBy ($order_by, $position='') {
	if ($position=='') {
		$position = POSITION_BEFORE;
	}
 	$this->order_by = trim(eregi_replace("order by", "", $order_by));
 	$this->order_position = $position;
 }


 /**
  * Définition des couleurs pour le tri
  *
  *
  * @param      string $selected		Couleur si colonne sélectionnée
  * @param      string $unselected		Couleur si colonne non sélectionnée
  * @access     public
  * @return     void
  * @see		setSortColumn
  * @see		setDefaultSort
  * @see		setOrderBy
  * @see		setSortArrows
  */

 function setSortColor ($selected="#e0eaf6", $unselected="#ffffff") {
	$this->selected_sort_color = $selected;
	$this->unselected_sort_color = $unselected;
 }

 /**
  * Définit les icones affichés pour les entetes de tri
  *
  * @param      string	$sort_up	Icone utilisé pour le tri décroissant
  * @param      string	$sort_down	icône utilsé pour le tri croissant
  * @param      string	$sort_none	icône (ou texte) utilisé pour tri inactif sur la colonne courante
  * @access     public
  * @return     void
  * @see		setSortColumn
  * @see		setDefaultSort
  * @see		setOrderBy
  * @see		setSortColor
  */
 function setSortArrows($sort_up, $sort_down, $sort_none) {
	$this->sort_arrow_up = $sort_up;
	$this->sort_arrow_down = $sort_down;
	$this->sort_arrow_none = $sort_none;
 }

 /**
  * Définit les couleurs pour l'alternance des lignes
  *
  * Permet de définir 2 chaines de caractères (couleur, classe) qui serviront a initialiser
  * la variable {_ItemColor}
  * @param      string	$odd_color
  * @param      string	$odd_color
  * @access     public
  * @return     void
  * @see	$_ItemColor
  */
 function setAlternateColor($odd_color="#ffffff", $even_color="#EDEFF3") {
	$this->odd_color = $odd_color;
	$this->even_color = $even_color;
 }

 /**
  * Définit les icones utilisés pour la navigation à l'intérieur de la pagination
  *
  * @param      string	$first	icône (ou texte) utilisé dans le lien généré vers la première page
  * @param      string	$prev	icône (ou texte) utilisé dans le lien généré vers la page précédente
  * @param      string	$next	icône (ou texte) utilisé dans le lien généré vers la page suivante
  * @param      string	$last	icône (ou texte) utilisé dans le lien généré vers la dernière page
  * @param      string	$showinactive	Indique si les icônes inactifs doivent être affichés ou non
  * @access     public
  * @return     void
  */
 function setNavIcons($first='', $prev='', $next='', $last='', $showinactive=false) {
	$this->url_first_text = $first;
	$this->url_prev_text = $prev;
	$this->url_next_text = $next;
	$this->url_last_text = $last;
	$this->show_inactive_nav_button = $showinactive;
 }

 /**
  * Définit les parametres d'affichage en multi-colonnage automatique
  *
  * Définit le nombre de colonnes, le retour à la ligne et le sens d'affichage dans le cas d'un
  * affichage multi-colonne
  *
  * @param	array $nb_columns		Nombre de colonnes
  * @param	array $break_string		Motif (contenu HTML a afficher) pour retour a la ligne
  * @param	array $break_direction	"sens de rupture" ( BREAK_HORIZONTAL (défaut) | BREAK_VERTICAL )
  * @param	integer parent
  * @access	public
  * @return	void
  */
 function setMultiColumnBreak ($nb_columns, $break_string = '<br>', $break_direction = BREAK_HORIZONTAL) {

	$this->auto_mc = true;
	$this->mc_nb_columns = $nb_columns;
	$this->mc_break_string = $break_string;
	$this->mc_break_direction = $break_direction;

 } // end func

 /**
  * Définit les icones utilisé pour le pliage/dépliage automatique
  *
  * @param      string	$expand		Icône utilisé pour déplier une liste
  * @param      string	$collapse	Icône utilisé pour replier une liste
  * @param      string	$none		Icône utilisé si l'item courant ne contient pas de d'items fils
  * @access     public
  * @return     void
  */
 function setExpandIcons ($expand='', $collapse='', $none='') {
	$this->expand_icon = $expand;
	$this->collapse_icon = $collapse;
	$this->leaf_icon = $none;
 }

 /**
  * Définit une table de substitution (remplacement automatique de valeurs)
  *
  * A utiliser lorsque l'on veut substituer une valeur (affichable) à la valeur présente
  * dans la base de données
  * Exemple :
  *		$genres = array ('F' => 'Femme', 'H', Homme');
  *		$list->addTranslation ('genre', $genres);
  *
  * @param	string	$name			Nom du champs dans lequel la substitution aura lieu
  * @param	array	$values			Tableau associatif des valeurs a remplacer
  * @param	boolean	$partial_match	substitution partielle (oui|non)
  * @param	boolean	$ignore_case	Ignore la casse (minuscule/majuscule)
  * @access	public
  * @return	boolean			true si OK
  */
 function addTranslation ($fieldname, $values, $partial_match=false, $ignore_case=false) {

	if (!is_array($values)) {
		$this->halt ("Invalid argument in addTranslation ($fieldname, $values)");
		Return false;
	}
	// $fieldname =strtolower($fieldname);
	$this->translations[$fieldname] = $values;
	$this->translations_options[$fieldname]['partial_match'] = $partial_match;
	$this->translations_options[$fieldname]['ignore_case'] = $ignore_case;
	Return true;
 } // end func


 /**
  * ajoute un item dans la liste
  *
  * @param	array item		enregistrement à insérer dans la liste
  * @param	integer parent	identifiant de l'enregistrement auquel se rattache l'item à insérer
  * @access	public
  * @return	integer	identifiant de l'enregistrement
  */
 function addItem ($item, $parent='') {

	settype($parent_key, 'integer');

	// retreive the parent key from already loaded items
	if (!empty($parent) && isset($this->keys[$parent]) ) {
		$parent_key = $this->keys[$parent];
	} else {
		$parent_key = 0;
	}

	// increments counter and store the item at the last place
	$this->ItemCount++;
	$key = $this->ItemCount;
	$this->entries[$key] = (@is_array ($this->translations)) ? $this->_translate($item) : $item;

	// Link this item to its parent
	$this->children[$parent_key][] = $key;

	// then add the reference to this item into the list of parent keys
	if (!empty($this->parent_field) && isset($item[$this->parent_field])) {
		$val = $item[$this->parent_field];
		$this->keys[$val] = $key;
	}
 	return $key;
 } // end func

 /**
  * Ajoute une variable globale accessible dans le template
  *
  * Permet de définir une variable globale accessible à l'intérieur de la liste.
  * Cette variable peut etre référencée dans le template par {var}
  *
  * @param	string $name	Nom de la variable
  *
  * @access	public
  * @return	void
  */
 function addGlobalVar ($var) {

	if (empty($var)) {
		$this->halt("variable name is not defined in addUserVar()");
	}

	if (!isset($GLOBALS[$var])) {
		$GLOBALS[$var] = '';
	}
	$this->globals[$var] = & $GLOBALS[$var];
 } // end func


 /**
  * Ajoute une variable utilisateur accessible dans le template
  *
  * Permet de définir une variable accessible à l'intérieur de la liste. Cette variable peut etre
  * référencée dans le template par {var}
  *
  * @param	string $name	Nom de la variable
  * @param	string $value	Contenu de la variable. Si ce parametre est omis, $name fera alors référence
  *							à la variable globale PHP: $name
  *
  * @access	public
  * @return	void
  */
 function addUserVar ($var, $value='') {

	if (empty($var)) {
		$this->halt("variable name is not defined in addUserVar()");
	}

	if (func_num_args()>1) {
		$this->variables[$var] = $value;
	} else {
		$this->addGlobalVar($var);
	}
 } // end func

 /**
  * ajoute un tableau d'items préalablement triés dans l'ordre d'affichage
  * Chaque item possède un champs "level" contenant le niveau d'affichage (commencant par 1)
  *
  * Exemple:
  *  -> item 1
  *  -> item 2
  *     |
  *     +-> item3
  *     |   |
  *     |   +-> item4
  *     |
  *     +-> item 5
  *
  * | l | item 1 ...
  * | 1 | item 2 ...
  * | 2 | item 3 ...
  * | 3 | item 4 ...
  * | 2 | item 5 ...
  *
  * @param	array items		enregistrement à insérer dans la liste
  * @access	public
  */
 function addItemsArray (&$items) {

	if (!is_array($items)) {
		$this->halt("addItemsArray: Items is not an array") ;
	}

	$parents[0] = 0;

	reset($items);
	while ( list(,$item) = each ($items) ) {
		$level = $item['level'];
		if ($level<1) {
			$this->halt("addItemsArray : invalid level '$level' ") ;
		}
		$parents[$level] = $this->addItem($item, $parents[$level-1]);
	}
 } // end func

 /**
  * Retourne la liste a afficher
  *
  * retourne la liste préalablement chargée par addItem ou addItemArray en utilisant le template
  * spécifié. Si template non renseigne -> utilisation du template par défaut.
  * @param	string	$tpl	Nom du fichier template à utiliser
  * @access	public
  * @return	mixed	La liste
  */
 function getList ($nb_result_per_page=0, $tpl='') {
	global $HTTP_GET_VARS;

	settype ($nb_result_per_page, 'integer');
	if ($nb_result_per_page == 0) {
		$nb_result_per_page = $this->nb_result_per_page;
	}

	if (empty ($HTTP_GET_VARS['sort']) ) {
		$current_sort = $this->default_sort;
	} else {
		$current_sort = urldecode($HTTP_GET_VARS['sort']);
	}

	if (!empty ($current_sort) && (count($this->entries)>1) ) {
		// get the current sort field name and sort order
		if (strstr($current_sort, ',')) {
			list ($name, $o) = explode(",", $current_sort);
			$order = ($o=='d') ? SORT_DESC : SORT_ASC;
		} else {
			$name = $current_sort;
			$order = SORT_ASC;
		}
		$column =  $this->sort_headers[$name]['order_by'];
		$sort_type =  (integer) $this->sort_headers[$name]['sort_type'];

		// Sort the entries according to the sort parameter
		foreach($this->entries as $k=>$entry){
			$sortarr[$k]=$entry[$column];
		}

		array_multisort($sortarr, $order, $sort_type, $this->entries);
		$this->array_multisort_done = true;
	}

	// get/save the total count of items
	$this->TotalCount = count($this->entries);	// Total number of entries 
	$item_count = count((isset($this->children[0])?$this->children[0]:array()));	// Total number of items or nodes (first level items)

	if ($item_count < 1) {
		$this->PageNumber = 0;
		$this->PageCount = 0;
		$this->FirstItem = 0;
		$this->LastItem = 0;

	} elseif ($nb_result_per_page>0) {

		if (isset($HTTP_GET_VARS['page']) && !empty($HTTP_GET_VARS['page']) ) {
			$this->PageNumber = $HTTP_GET_VARS['page'];
		} else {
			$this->PageNumber = 1;
		}

		$this->PageCount = ceil($item_count / $nb_result_per_page); // arrondi a l'entier superieur
		if ($this->PageNumber > $this->PageCount) {
			$this->PageNumber = $this->PageCount;
		}

		// compute first and last indice of the items to display
		$first = ($this->PageNumber - 1) * $nb_result_per_page;
		$last = $first + $nb_result_per_page;
		if ($last > $item_count) {
			$last = $item_count;
		}

		// rebuild the array with indices of items to display
		for ($i=$first; $i<$last; $i++) {
			$list[] = $this->children[0][$i];
		}
		$this->children[0] = $list;

		$this->FirstItem = $first+1;
		$this->LastItem = $last;
	} else {
		$this->PageNumber = 1;
		$this->PageCount = 1;
		$this->FirstItem = 1;
		$this->LastItem = $item_count;
	}

	$this->ItemCount = count((isset($this->children[0])?$this->children[0]:array()));

	if ($this->PageCount > 1) {

		if (!empty ($current_sort) ) {
			$this->addUrlVar('sort', $current_sort);
		}

		if ($this->PageNumber>1) {
			$this->addUserVar('page', $this->PageNumber);
		}

		$this->_setNavBar();
	}

	$this->_getList($tpl);
	Return $this->_html;
 } // end func


 /**
  * affiche la liste
  *
  * génère et affiche la liste construite à partir du template (lance getList()
  * puis affiche la liste obtenue)
  * @param	integer	$nb_result_par_page	Nombre d'items par page
  * @param	string	$tpl	Nom du fichier template à utiliser
  * @access	public
  * @return	void
  * @see	getList
  */
 function showList ($nb_result_per_page=0, $tpl='') {

	$this->getList($nb_result_per_page, $tpl);
 	print($this->_html);
 } // end func


 /**
  * Exécute une requête sur la base de données, génère la liste d'après les options courantes
  * (template, tri, etc…), et retoune le résultat paginé pour affichage ultérieur
  *
  * @param	string	$query				La requète SQL à exécuter
  * @param	integer	$nb_result_par_page	Nombre d'items par page
  * @param	string	$tpl				Nom (chemin d'accès) du fichier template
  * @access	public
  * @return	void
  * @see getList
  * @see dbShowList
  */
 function dbGetList ($query, $nb_result_per_page=0, $tpl='') {
	 global $HTTP_GET_VARS;

	settype ($nb_result_per_page, 'integer');
	if ($nb_result_per_page == 0) {
		$nb_result_per_page = $this->nb_result_per_page;
	}

	if ($nb_result_per_page>0) {

		if (isset($HTTP_GET_VARS['page']) && !empty($HTTP_GET_VARS['page']) ) {
			$this->PageNumber = $HTTP_GET_VARS['page'];
		} else {
			$this->PageNumber = 1;
		}

		// Suppression d'un LIMIT eventuel
		$query = preg_replace("/ LIMIT (.*)/mi", '', $query);

		// Récupere le nombre total d'enregistrements
		$this->TotalCount = $this->_getQueryRowCount ($query);

		if ($this->TotalCount == 0) {
			$this->_getList($tpl);
			return $this->_html;
		}

		$this->PageCount = ceil($this->TotalCount / $nb_result_per_page); // arrondi a l'entier superieur
		if ($this->PageNumber > $this->PageCount) {
			$this->PageNumber = $this->PageCount;
		}

		// Calcul des indices sur 1er et dernier item de la liste
		$this->FirstItem = ($this->PageNumber - 1) * $nb_result_per_page;
		$this->LastItem = $this->FirstItem + $nb_result_per_page;

		// Ajout du LIMIT calculé
		$query .= " LIMIT ".$this->FirstItem.",$nb_result_per_page";

		$this->FirstItem++;
		if ($this->LastItem > $this->TotalCount) {
			$this->LastItem = $this->TotalCount;
		}
	} else {
		$this->PageNumber = 1;
		$this->PageCount = 1;
		$this->FirstItem = 1;
	}

	// Add ORDER BY clause according to  URL sort parameter
	if (empty ($HTTP_GET_VARS['sort']) ) {
		$current_sort = $this->default_sort;
	} else {
		$current_sort = urldecode($HTTP_GET_VARS['sort']);
	}

	if (!empty ($current_sort) ) {

		// extract the limit clause
		// ------------------------
		if (preg_match("# LIMIT .*#mi", $query, $match)) {
			$limit_clause = $match[0];						// save it in order_clause...
		    $query = str_replace($match[0], "", $query);	// and remove it from the query
		} else {
			$limit_clause = "";
		}

		// Check if the query contains an ORDER BY clause
		if (preg_match("# ORDER BY .*#mi", $query, $match)) {
			$order_clause = $match[0];						// save it in order_clause...
		    $query = str_replace($match[0], "", $query);	// and remove it from the query
		} else {
			$order_clause = '';
		}

		// Add the ORDER BY parameter
		// --------------------------
		if (!empty($this->order_by) && ($this->order_position==POSITION_BEFORE) ) {
			$order_clause = "ORDER BY " . $this->order_by;
		}

		// Add the current sort to the SQL query string
		// --------------------------------------------
		if (strstr($current_sort, ',')) {
			list ($column, $order) = explode(",", $current_sort);
		} else {
			$column = $current_sort;
			$order = 'a';
		}
		if (!empty($column) && is_array($this->sort_headers[$column])) {
			$order_by =  $this->sort_headers[$column]['order_by'];
			if ($order == 'd') {
				$order_by .= " DESC";
			}
			$order_clause = (empty($order_clause)) ? "ORDER BY $order_by" : "$order_clause, $order_by";
		}

		// Add the Order By parameter
		// --------------------------
		if (!empty($this->order_by) && ($this->order_position==POSITION_AFTER) ) {
			$order_clause = (empty($order_clause)) ? "ORDER BY " : "$order_clause, ";
			$order_clause .= $this->order_by;
		}

		// rebuild the query
		// -----------------
		$query .= " $order_clause $limit_clause";
	}

	// Perform the SQL query
	$result = mysql_query ($query);
	if (!$result) {
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();
		$this->halt("Invalid SQL: $query");
	}

	// Store each row in the local array buffer
	while ($item = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($this->tree_view && isset($item[$this->link_to_parent]) ) {
			$parent = $item[$this->link_to_parent];
		} else {
			$parent = '';
		}

		$this->addItem($item, $parent);
	}

	if ($nb_result_per_page==0) {
		$this->TotalCount = mysql_num_rows($result);
		$this->LastItem = $this->TotalCount;
	}

	mysql_free_result($result);

    if ($this->PageCount > 1) {

		if (!empty ($current_sort) ) {
			$this->addUrlVar('sort', $current_sort);
		}

		if ($this->PageNumber>1) {
			$this->addUserVar('page', $this->PageNumber);
		}

		$this->_setNavBar();
	}

	$this->_getList($tpl);
	return $this->_html;

 }

 /**
  * Exécute une requête sur la base de données, génère la liste d'après les options courantes
  * (template, tri, etc…), et affiche le résultat paginé
  *
  * @param		$query				La requète SQL à exécuter
  * @param		$nb_result_par_page	Nombre d'item par page
  * @param		$tpl				Nom (chemin d'accès) du fichier template
  * @access	public
  * @return	mixed	La Liste
  * @see showList
  * @see dbGetList
  */
 function dbShowList ($query, $nb_result_per_page=0, $tpl='') {
	$this->dbGetList ($query, $nb_result_per_page, $tpl);
 	print($this->_html);
 }


#	-------------------------------------------------------------------------------
#				F O N C T I O N S   S P E C I F I Q U E S   C . H .   H Y E R E S
#	-------------------------------------------------------------------------------

  /**
  * Affiche une liste paginé à partir du résultat d'une requete (result_query::use_query)
  *
  * Spécifique Hopital d'Hyères
  *
  * @param		$result_query		Résultat de la fonction use_query
  * @param		$nb_result_par_page	Nombre d'item par page
  * @param		$tpl				Nom (chemin d'accès) du fichier template
  * @access	public
  * @return	mixed	La Liste
  * @see show_result_query
  */
 function get_result_query (&$result_query, $nb_result_per_page=0, $tpl='') {

	global $HTTP_GET_VARS, $HTTP_SESSION_VARS;

	if ( $this->cache_active && $this->isCached() ) {
		$this->entries   = $HTTP_SESSION_VARS["lm_entries"];
		$this->children  = $HTTP_SESSION_VARS["lm_children"];
		$this->ItemCount = $HTTP_SESSION_VARS["lm_ItemCount"];
	} else {

		if (!is_array($result_query) ) {
			$this->halt ("Invalid argument in get_result_query()");
		}

		$nb_items = $result_query['INDIC_SVC'][2];

		for ($i=0; $i<$nb_items; $i++) {
			reset($result_query);
			while(list($colname) = each($result_query)) {
				if ($colname != 'INDIC_SVC') {
					$item[$colname] = $result_query[$colname][$i];
				}
			}
			$this->addItem($item);
		}

		if ($this->cache_active ) {
			$HTTP_SESSION_VARS["lm_entries"] = $this->entries;
			$HTTP_SESSION_VARS["lm_children"] = $this->children;
			$HTTP_SESSION_VARS["lm_ItemCount"] = $this->ItemCount;
		}
	}


	return $this->getList($nb_result_per_page, $tpl);
 }


 /**
  * Affiche une liste paginé à partir du résultat d'une requete (result_query::use_query)
  *
  * Spécifique Hopital d'Hyères
  *
  * @param		$result_query		Résultat de la fonction use_query
  * @param		$nb_result_par_page	Nombre d'item par page
  * @param		$tpl				Nom (chemin d'accès) du fichier template
  * @access	public
  * @return	mixed	La Liste
  * @see get_result_query
  */
 function show_result_query ($result_query, $nb_result_per_page=0, $tpl='') {
	$this->get_result_query ($result_query, $nb_result_per_page, $tpl);
 	print($this->_html);
 }


# ------------------------------------------------------------
#				P R I V A T E   F U N C T I O N S
# ------------------------------------------------------------

 /**
  * Parse the configuration variables into the class variables
  *
  * @param      $config	The Configuration block to be parsed
  * @access     private
  * @return     void
  */
 function _getConfig($config) {

    $assigns = preg_split("/[\n]/ms", $config);

	reset($assigns);
	while(list($k,$assign) = each($assigns)) {
		if (preg_match("/\s*\\$?([\w]+)\s*[=:]+\s*(.*)/", $assign, $match)) {
			$var = $match[1];
			$val = preg_replace('/;?$/', '', trim($match[2]) );
			if (preg_match('/^(["\'])(.*)(\\1)$/', $val, $match)) {
				$quote = $match[1];
				$val = str_replace("\\{$quote}", "$quote", $match[2]);
			}
			$this->$var = $val;
//echo("\n<br> assign $var -> " .$this->$var );
		}
	}

	if (!empty ($this->last_row_padding) ) {
		$this->last_row_padding = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_]+)\}/ms", '$\\1', stripslashes($this->last_row_padding)));
	}

	if (!empty ($this->level_padding) ) {
		$this->level_padding = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_]+)\}/ms", '$\\1', stripslashes($this->level_padding)));
	}

 }


 /**
  * Parse the template file
  *
  * @param	string $file	nom (pathname) du fichier template
  * @access	private
  * @return	void
  */
 function _parseTemplate ($file) {

	$tab = explode ( "/", $file ) ;
	if ( isset ( $tab[1] ) AND $tab[1] ) $fichier = $tab[1] ;
	else $fichier = $file ;
	if ( file_exists ( "templates_int/".$fichier ) ) $file = "templates_int/".$fichier ;
	else $file = "templates_gen/".$fichier ;
	
	if (!file_exists($file) ) {
		$this->halt("Could not open file : $file");
	}

	$this->template = join ("", file(trim($file) ) );

	$delim = preg_quote($this->lm_delimiter, '/');

	// Gather all template tags
	preg_match_all ("/(<{$delim}([\w]+)[^>]*>)(.*)(<\/{$delim}\\2>)/imsU", $this->template, $matches);

	for ($i=0; $i< count($matches[0]); $i++) {
		switch(strtolower($matches[2][$i])) {
		  case 'config':
			$this->_getConfig($matches[3][$i]);
			break;
		  case 'php':
			$this->before_line  = $matches[3][$i];
			break;
		  case 'list':
			$list_block  = $matches[3][$i];
			break;
		}
	}

	// Split content by template tags to obtain non-template conten
    $text_blocks = preg_split("/(<{$delim}([\w]+)[^>]*>)(.*)(<\/{$delim}\\2>)/imsU", $list_block);
	$matches = count($text_blocks);
	if ($matches>0) {
		// Get starting block (list header)
		$pattern = preg_quote($text_blocks[0], '/');
		if (preg_match("/^{$pattern}/imsU", $list_block, $match)) {
			$this->begin_block = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_\\[\\]]+)\}/ms", '$\\1', trim ($text_blocks[0])));
		}
		if ($matches>1) {
			// Get ending block (list footer)
			$pattern = preg_quote($text_blocks[$matches-1], '/');
			if (preg_match("/{$pattern}$/imsU", $list_block, $match)) {
				$this->end_block = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_\\[\\]]+)\}/ms", '$\\1', trim ($text_blocks[$matches-1])));
			}
		}
	}

	// Now Gather inner blocks in list block
	preg_match_all ("/(<{$delim}([\w]+)[^>]*>)(.*)(<\/{$delim}\\2>)/imsU", $list_block, $matches);
	for ($i=0; $i< count($matches[0]); $i++) {
		switch(strtolower($matches[2][$i])) {
		  case 'begin_level':
			$this->begin_level = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_\\[\\]]+)\}/ms", '$\\1', trim ($matches[3][$i])));
			break;
		  case 'item':
			$this->item_block = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_\\[\\]]+)\}/ms", '$\\1', trim ($matches[3][$i])));
			break;
		  case 'no_item':
			$this->no_item_block = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_\\[\\]]+)\}/ms", '$\\1', trim ($matches[3][$i])));
			break;
		  case 'end_level':
			$this->end_level = str_replace('"','\"', preg_replace("/\{([a-zA-Z0-9_\\[\\]]+)\}/ms", '$\\1', trim ($matches[3][$i])));
			break;
		}
	}

/*
echo "<br><pre>template : " . htmlspecialchars($this->template);

echo "\n\n begin_block: ".htmlspecialchars($this->begin_block);
echo "\n\n begin_level: ".htmlspecialchars($this->begin_level);
echo "\n\n item_block: ".htmlspecialchars($this->item_block);
echo "\n\n end_level: ".htmlspecialchars($this->end_level);
echo "\n\n end_block: ".htmlspecialchars($this->end_block);
*/

 } // end func

 /**
  * Retourne le nombre de lignes renvoyées par une requete donnée
  *
  * @param      string	$query	La requete a executer
  * @since      1.0
  * @access     private
  * @return     integer	Le nombre de lignes ou FALSE si erreur
 */
 function _getQueryRowCount($query) {

	// Suppression d'un ORDER BY eventuel
	$query = preg_replace("/ ORDER BY (.*)/mi", '', $query);

	// extraction de la clause WHERE
	$where = "";
	if (preg_match("/ WHERE (.*)/mi", $query, $match)) {
		$where = trim($match[1]);
		$query = str_replace($match[0], '', $query);
	}

	// extraction du FROM et suppression de la clause
	if (!preg_match("/ FROM (.*)/mi", $query, $match)) {
		$this->halt("Invalid Query: $query");
		Return false;
	}
	$from = trim($match[1]);

	$newquery = "SELECT COUNT(*) AS cnt FROM $from";

	if(!empty($where)) {
		$newquery .= " WHERE $where";
	}

	$result = mysql_query ($newquery);
	if (!$result) {
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();
		$this->halt("Invalid SQL: $newquery");
	}

	$row = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);

	if (is_array($row)) {
		Return $row[0];
	} else {
		Return 0;
	}
 }


 /**
  * Retourne la liste a afficher
  *
  * retourne la liste préalablement chargée par addItem,  en utilisant le template
  * spécifié. Si template non renseigne -> utilisation du template par défaut.
  * @param	string	$tpl	Nom du fichier template à utiliser
  * @access	private
  * @return	mixed	La liste
  */
 function _getList ($tpl='') {
	 global $HTTP_GET_VARS;

	if(!empty($tpl)) {
 		$this->_parseTemplate ($tpl);
	}

	if (!empty($this->ItemCount) ) {
		$this->TotalCount = (empty ($this->TotalCount) ) ? $this->ItemCount : $this->TotalCount;
		$this->FirstItem  = (empty ($this->FirstItem) ) ? 1 : $this->FirstItem;
		$this->LastItem   = (empty ($this->LastItem) ) ? $this->ItemCount : $this->LastItem;
		$this->PageCount  = (empty ($this->PageCount) ) ? 1 : $this->PageCount;
		$this->PageNumber = (empty ($this->PageNumber) ) ? 1 : $this->PageNumber;

		$this->addUserVar ('_ItemCount',    $this->ItemCount);
		$this->addUserVar ('_TotalCount',   $this->TotalCount);
		$this->addUserVar ('_FirstItem',    $this->FirstItem);
		$this->addUserVar ('_LastItem',     $this->LastItem);
		$this->addUserVar ('_PageCount',    $this->PageCount);
		$this->addUserVar ('_PageNumber',   $this->PageNumber);
		$this->addUserVar ('_UrlFirstPage', $this->UrlFirstPage);
		$this->addUserVar ('_UrlPrevPage',  $this->UrlPrevPage);
		$this->addUserVar ('_UrlNextPage',  $this->UrlNextPage);
		$this->addUserVar ('_UrlLastPage',  $this->UrlLastPage);
		$this->addUserVar ('_UrlPageNav',   $this->UrlPageNav);
	}

	// Import User variables in current scope
	if (count($this->variables)>0) {
		extract($this->variables);
	}

	if (isset ( $this->globals ) AND count($this->globals)>0) {
		foreach ($this->globals as $var=>$val) {
			global $$var;
		}
	}

	$this->_html =  "<!--  BEGIN LIST -->";
	if (!empty($this->before_line)) {
		$is_expanded = 0;
		$children = 0;
		$item = array();
		eval ($this->before_line);
	}

	if (!empty($this->begin_block)) {
		// sortable headers
		if (isset ($this->auto_sort) AND $this->auto_sort) {
			if (empty ($HTTP_GET_VARS['sort']) ) {
				$current_sort = $this->default_sort;
			} else {
				$current_sort = urldecode($HTTP_GET_VARS['sort']);
			}

			if (strstr($current_sort, ',')) {
				list($cur_column, $cur_order) = explode(",", $current_sort);
			} else {
				$cur_column = $current_sort;
				$cur_order = 'a';
			}

			reset($this->sort_headers);
			while(list($column, $param) = each($this->sort_headers)) {
				$sort = $column;
				if ($column != $cur_column) {
					$sort_arrow = $this->sort_arrow_none;
					$_SortColor[$column] = $this->unselected_sort_color;
				} elseif ($cur_order == 'd'){
					$sort_arrow = $this->sort_arrow_down;
					$_SortColor[$column] = $this->selected_sort_color;
				} else {
					$sort = "$sort,d";
					$sort_arrow = $this->sort_arrow_up;
					$_SortColor[$column] = $this->selected_sort_color;
				}
				$this->addUrlVar('sort', $sort);
				$url   = $this->self_url;
				$title = str_replace('"', '&quot;', $param['help']);
				$label = str_replace('"', '&quot;', $param['label']);
				$_SortHeader[$column] = "<a href=\"$url\" title=\"$title\">$label</a>&nbsp; $sort_arrow";
			}
		}
		eval ("\$this->_html.= \"$this->begin_block\";");
	}

	if (@count((isset($this->children[0])?$this->children[0]:array())) < 1) {
		if (!empty($this->before_line)) {
			$item = array();
			eval ($this->before_line);
		}

		if (!empty($this->no_item_block)) {
			eval ("\$this->_html.= \"$this->no_item_block\";");
		}
	} else {
		$this->level = 0;
		if ($this->auto_mc) {
		   $this->_getMultiColumnEntries();
		} elseif ($this->tree_view) {
			$this->_getTreeNode(0);
		} else {
			$this->_getEntries();
		}	
	}

	$this->_html .= '<!-- END LIST -->';

	if (!empty($this->before_line)) {
		$item = array();
		eval ($this->before_line);
	}

	if (!empty($this->end_block)) {
		eval ("\$this->_html.= \"$this->end_block\";");
	}
	Return $this->_html;
 } // end func


  /**
   * retourne la liste sur plusieurs colonnes
   *
   * Detail description
   * @access	private
   * @see
   * @return	void
   */
  function _getMultiColumnEntries () {

	$nb_lines = ceil($this->ItemCount / $this->mc_nb_columns);

	// Import User variables in current scope
	if (count($this->variables)>0) {
		extract($this->variables);
	}

	if (count($this->globals)>0) {
		foreach ($this->globals as $var=>$val) {
			global $$var;
		}
	}

	if ($this->mc_break_direction == BREAK_HORIZONTAL) {
		// AFFICHAGE HORIZONTAL
		$line_no = 1;		// n° de la ligne courante
 	    $cellNo = 1;		// n° de la cellule courante
		reset($this->children[0]);
 		while (list(, $key) = each($this->children[0]) ) {
			if (@ $this->array_multisort_done) {
				$key = $key-1;
			}
			$item = & $this->entries[$key];
			$this->ItemNum++;
			$_ItemNum = $this->ItemNum;
			$_ItemColor = ($this->ItemNum % 2) ? $this->odd_color : $this->even_color;
			if (!empty($this->plugin)) {
				$f = $this->plugin;
				if (function_exists($f) ) {
					$item = $f($item);
				} else {
					$this->halt("$f() function does'nt exists!!");
				}
			}

 			extract($item);
 			$line = $this->item_block;
			if (!empty($this->before_line)) {
				eval ($this->before_line);
			}
 			eval ("\$this->_html.= \"$line\";");
 			$nl = $cellNo % $this->mc_nb_columns;
 			if (empty($nl)) {
 				if ($line_no != $nb_lines) {			// Pas de retour a la ligne pour la derniere ligne
				    $this->_html .= $this->mc_break_string;
				}
				$line_no++;
 			}
 			$cellNo++;
 		}

		// remplissage dernieres cellules
		$_Colspan = ($nb_lines * $this->mc_nb_columns) - $this->ItemCount;
		if (!empty($_Colspan)) {
		    eval ("\$this->_html.= \"$this->last_row_padding\";");
		}

 	} else {
		// AFFICHAGE VERTICAL

 		$i = 1;
 		while (list($key, $cur_item) = each($this->entries)) {
 			$items[$i] = $cur_item;
			$i++;
 		}

		$nb_dsp = 0;						// nb d'items deja affiches
		$col_no = 1;						// n° de la colonne courante
		$line_no = 1;						// n° de la ligne courante
		$nb_empty = ($nb_lines * $this->mc_nb_columns) - $this->ItemCount;
											// nb de cellules vides encore a remplir par du "blanc"
		reset($items);
		while ($nb_dsp < count($items)) {
			$this->ItemNum = $line_no + (($col_no - 1) * $nb_lines);
			$_ItemNum = $this->ItemNum;
			$_ItemColor = ($this->ItemNum % 2) ? $this->odd_color : $this->even_color;
			if (is_array($items[$this->ItemNum])) {
				$item = & $items[$this->ItemNum];
				if (!empty($this->plugin)) {
					$f = $this->plugin;
					if (function_exists($f) ) {
						$item = $f($item);
					} else {
						$this->halt("$f() function does'nt exists!!");
					}
				}
				extract($item);
				$line = $this->item_block;
				if (!empty($this->before_line)) {
					eval ($this->before_line);
				}
				eval ("\$this->_html.= \"$line\";");
				$nb_dsp++;
			} else {
				// remplissage toutes les lignes, derniere colonne
				$_Colspan = 1;
				eval ("\$this->_html.= \"$this->last_row_padding\";");
				$nb_empty--;
			}
			if ($line_no == $nb_lines && $nb_dsp == $this->ItemCount && !empty($nb_empty)) {
			    // Remplissage derniere colonne
				while (!empty($nb_empty)) {
					$_Colspan = 1;
					eval ("\$this->_html.= \"$this->last_row_padding\";");
				    $nb_empty--;
				}
			}

			$nl = $col_no % $this->mc_nb_columns;
 			if (empty($nl)) {
				if ($line_no != $nb_lines) {			// Pas de retour a la ligne pour la derniere ligne
				    $this->_html .= $this->mc_break_string;
				}
				$line_no++;
				$col_no = 1;
 			} else {
 			    $col_no++;
 			}
		}
 	}

  } // end func


 /**
  * Affiche une entrée de la liste et toutes entrées filles rattachées a cette entrée
  *
  * @param      Integer $key
  * @access     private
  * @return     void
  * @see	getList()
  */
 function _getTreeNode ($key) {

	$is_expanded = 1;
	// extract all variables then add the line to the local buffer
	if (@is_array($this->entries[$key]) ) {
		$item = & $this->entries[$key];
		$this->ItemNum ++;
		if (!empty($this->plugin)) {
			$f = $this->plugin;
			if (function_exists($f) ) {
				$item = $f($item);
			} else {
				$this->halt("$f() function does'nt exists!!");
			}
		}
		extract($item);

		$_ItemColor = ($this->ItemNum % 2) ? $this->odd_color : $this->even_color;
		$_ItemNum = $this->ItemNum;
		$_pad = str_repeat($this->level_padding, $this->level);

		// set expand_icon depending on collapsed or expanded:
		if (@is_array($this->children[$key]) ) {
			$children = count($this->children[$key]);
			if ($key==0) {
				$is_expanded = 1;
				$_ExpandIcon = $this->collapse_icon;
				$_ExpandUrl = '<a href="'.$this->self_url.$this->_encodeVar('collapse', $key).'">'.$_ExpandIcon.'</a>';
			} elseif (preg_match("/[=+]$key\+/", $this->collapsedKeys, $match) ) {
				$is_expanded = 0;
				$_ExpandIcon = $this->expand_icon;
				$_ExpandUrl = '<a href="'.$this->self_url.$this->_encodeVar('expand', $key).'">'.$this->expand_icon.'</a>';
			} elseif (preg_match("/[=+]$key\+/", $this->expandedKeys, $match) ) {
				$is_expanded = 1;
				$_ExpandIcon = $this->collapse_icon;
				$_ExpandUrl = '<a href="'.$this->self_url.$this->_encodeVar('collapse', $key).'">'.$this->collapse_icon.'</a>';
			} else {
				$is_expanded = 1;
				$_ExpandIcon = $this->collapse_icon;
				$_ExpandUrl = '<a href="'.$this->self_url.$this->_encodeVar('collapse', $key).'">'.$this->collapse_icon.'</a>';
			}
		} else {
			$children = 0;
			$is_expanded = 0;
			$_ExpandIcon = $this->leaf_icon;
			$_ExpandUrl = $_ExpandIcon;
		}

		// Import User variables in current scope
		if (count($this->variables)>0) {
			extract($this->variables);
		}

		if (count($this->globals)>0) {
			foreach ($this->globals as $var=>$val) {
				global $$var;
			}
		}

		if (!empty($this->before_line)) {
			eval ($this->before_line);
		}
		eval ("\$this->_html.= \"$this->item_block\";");

	}

	// gets all childs
	if ($is_expanded && @is_array($this->children[$key]) ) {
		reset($this->children[$key]);
		while ( list (,$child) = each ($this->children[$key]) ) {
			if ($key>0) {
				$this->level++;
			}
			if (!empty($this->begin_level)) {
				eval ("\$this->_html.= \"$this->begin_level\";");
			}

			$this->_getTreeNode($child);
			if (!empty($this->end_level)) {
				eval ("\$this->_html.= \"$this->end_level\";");
			}
			if ($key>0) {
				$this->level--;
			}
		}
	}
// $this->l--;
}

 /**
  * Affiche une entrée de la liste et toutes entrées filles rattachées a cette entrée
  *
  * @param      Integer $key
  * @access     private
  * @return     void
  * @see	getList()
  */
 function _getEntries () {

	// Import User variables in current scope
	if (count($this->variables)>0) {
		extract($this->variables);
	}

	if (isset ( $this->globals ) AND count($this->globals)>0) {
		foreach ($this->globals as $var=>$val) {
			global $$var;
		}
	}

	$plugin = false;
	if (!empty($this->plugin)) {
		$plugin_function = $this->plugin;
		if (function_exists($plugin_function) ) {
			$plugin = true;
		} else {
			$this->halt("$plugin() function does'nt exists!!");
		}
	}

	foreach($this->children[0] as $k=>$line) {
		// If array_multisort has been called then the array has been reorded starting at 0 !!!
		if (isset ( $this->array_multisort_done ) AND @ $this->array_multisort_done) {
			$line = $line-1;
		}
		// extract all variables then add the line to the local buffer
		$this->ItemNum ++;
		$item = & $this->entries[$line];

		if ($plugin) {
			$item = $plugin_function($item);
		}
		extract($item);

		$_ItemColor = ($this->ItemNum % 2) ? $this->odd_color : $this->even_color;
		$_ItemNum = $this->ItemNum;

		if (!empty($this->before_line)) {
			eval ($this->before_line);
		}
		eval ("\$this->_html.= \"$this->item_block\";");
	}
 }


 /**
  * Set the navigation bar
  *
  * @access	private
  * @return	void
  */
 function _setNavBar () {

	$url = $this->self_url;
	if ($this->PageNumber > 1) {
		$url_first = $this->self_url. $this->_encodeVar('page', 1);
		$this->UrlFirstPage = "<a href=\"$url_first\">".$this->url_first_text."</a>";

		$url_prev = $this->self_url. $this->_encodeVar('page', $this->PageNumber-1);
		$this->UrlPrevPage = "<a href=\"$url_prev\">".$this->url_prev_text."</a>";
	} elseif ($this->show_inactive_nav_button) {
		$this->UrlFirstPage = $this->url_first_text;
		$this->UrlPrevPage = $this->url_prev_text;
	}

	for ($pg=1; $pg <= $this->PageCount; $pg++) {
		if ($pg == $this->PageNumber) {
			$this->UrlPageNav .= "<b>$pg</b> ";
		} else {
			$url_nav = $this->self_url. $this->_encodeVar('page', $pg);
			$this->UrlPageNav .= "<a href=\"$url_nav\">$pg</a> ";
		}
	}

	if ( $this->PageNumber < $this->PageCount) {
		$url_next = $this->self_url. $this->_encodeVar('page', $this->PageNumber+1);
		$this->UrlNextPage = "<a href=\"$url_next\">".$this->url_next_text."</a>";

		$url_last = $this->self_url. $this->_encodeVar('page', $this->PageCount);
		$this->UrlLastPage = "<a href=\"$url_last\">".$this->url_last_text."</a>";
	} elseif ($this->show_inactive_nav_button) {
		$this->UrlNextPage = $this->url_next_text;
		$this->UrlLastPage = $this->url_last_text;
	}

 } // end func


 /**
  * substitution automatique
  *
  * Detail description
  * @param      array $record	L'enregistrement à traduire
  * @access     private
  * @return     The translated field
  */
 function _translate(&$record) {

	if (!@is_array ($this->translations)) {
		return $record;
	}

	$new_record = $record;
	reset($this->translations);
	// for each field needing translation
	while ( list($field) = each($this->translations) ) {
		if (!isset($record[$field])) {
			continue;
		}

		// This field must be translated
		if ($this->translations_options[$field]['partial_match']) {
			if ($this->translations_options[$field]['ignore_case']) {
				// partial field matching + case insensitive
				$i=0;
				foreach($this->translations[$field] as $p=>$r) {
					$patterns[$i] = "/$p/i";
					$replacements[$i] = "$r";
					$i++;
				}
				$new_record[$field] = preg_replace($patterns, $replacements, $record[$field]);
			} else {
				// partial field matching + case sensitive
				$new_record[$field] = strtr($record[$field], $this->translations[$field]);
			}

		} else {
			if ($this->translations_options[$field]['ignore_case']) {
				// full field matching + case insensitive
				$i=0;
				foreach($this->translations[$field] as $p=>$r) {
					$patterns[$i] = "/^$p\$/i";
					$replacements[$i] = "$r";
					$i++;
				}
				$new_record[$field] = preg_replace($patterns, $replacements, $record[$field]);
			} else {
				// full field matching + case sensitive
				$old_value = $record[$field];
				if (isset($this->translations[$field][$old_value])) {
					$new_record[$field] = $this->translations[$field][$old_value];
				}
			}
		}
	}
	Return $new_record;
 }


 /**
  * Retourne un parametre pouvant etre concaténé sur l'URL courant
  *
  * @param	string $name	Nom de la variable
  * @param	string $value	Contenu de la variable
  * @access	private
  * @see	addUrlVar
  * @return
  */
 function _encodeVar ($name, $value="") {

	if (strpos($this->self_url, '?')) {
		return "&amp;$name=".urlencode($value);
	} else {
		return "?$name=".urlencode($value);
	}
 } // end func


} // end class

?>
