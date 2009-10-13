<?php
/*23 janv. 2006, modified by Emmanuel Cervetti ecervetti@ch-hyeres.fr */


//classe gerant les variales globales
class formxGlobals
  {
	public $dom;
	public function	formxGlobals($ids)
	{
		$this->ids = $ids ;
		$this->isModif = false ;
		$this->dom = new DomDocument ("1.0", "UTF-8");
		$obReq = formxSession::getInstance()->getObjRequeteGlobals();
		$tabReq = $obReq->getGen(" ids = '$ids' ",'tab');
		//on crée l'objet DOM
		if ( count($tabReq) >= 1 ) {
   			$dataxml = formxTools::decodeFromBdd($tabReq[0]['data']) ;
  			$this->dom->loadXML($dataxml);
   		} else { //sinon ben va bien falloir le créer
   			$bal = $this->dom->createElement('GLOBVARS');
			$this->dom->appendChild($bal);
			$this->set('ids',$ids);
			$this->save(true);
   		}
	}
	public function set($var,$val,$isSave = false)
	{
		$this->isModif = true ;
		foreach ( formxTools::domSearch(formxTools::domGetRoot($this->dom),'ITEM' ) as  $item)
		{
    			if ($item->getAttribute('id') == $var )
    			{
				try
				{
					formxTools::domSetTagValue( $item, 'Val', $val );
					if ( $isSave ) $this->save() ;
					return true ;
				}
				catch ( Exception $e )
				{
					eko("attention");
				}

			}
		}
		//eko('pas trouve');
		//si on est encore là c'est qu'on a pas trouvé la balise.on va la créer
  		//TODO découpler ci dessous
  		$bal = $this->dom->createElement('ITEM');
		$this->dom->getElementsByTagName('GLOBVARS')->item(0)->appendChild($bal);
		$bal -> setAttribute('id',$var);
  		formxTools::createTagValue($bal, 'Val', $val, $this->dom);
  		if ( $isSave ) $this->save() ;
  		return true ;
	}
	public function get( $var )
	{
		foreach ( formxTools::domSearch(formxTools::domGetRoot( $this->dom ),'ITEM' ) as  $item)
  		{
    			if ($item->getAttribute('id') == $var )
    			{
				return formxTools::getValueDomItem($item);
			}
   		}

  		return '';

	}
	public function save($isNew = false)
	{
		$vals = array();
		$vals['data']=$this->dom->saveXML();
		$vals['ids']=$this->ids;
		$obReq = formxSession::getInstance()->getObjRequeteGlobals($vals);
		if( $isNew )
		  	$resu = $obReq->addRecord () ;
		else {
			if ( $this->isModif )
			$resu = $obReq->updRecord (" ids='".$this->ids."' ");
		}
	}
	public function del($var)
	{
		$this->set($var,'');
	}
  }
  



class clFoRmXtOoLs {
  private $af;
//  	function __construct() {
//  
//  	}
 
 
 
 //obtension d'une instance de session
 static function getCurrentFormxSession() {
 global $formxSession ;
 if(! is_object($formxSession))
 	$formxSession = new clFoRmXSession() ;
 return $formxSession;
}
 
 /*
  * gestion de l'upload d'un fichier
  */
static function gestUpload($name,$dest='',$rename='',$maxfilesize=10,$verifExtension='') {
global $errs;
global $options;
if(!$dest) $dest=$_SESSION['informations']['home'];
if(!$rename) $rename=stripslashes($_FILES[$name]['name']);
if(! $_FILES[$name]) {
  //Pas de formulaire upload present pour $name
  eko("pas de fichier pour ".$name);
  return false;
  }
if($verifExtension) {
	$reg=array();
	if(! ereg("^.*\.".$verifExtension."$",stripslashes($_FILES[$name]['name']),$reg)) {	
		$errs->addErreur("Le fichier uploadé ne respecte pas l'extension demandïée");
		return false;
	}
}
if ($_FILES[$name]['error']) {
          switch ($_FILES[$name]['error']){
                   case 1: // UPLOAD_ERR_INI_SIZE
                   $errs->addErreur("Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !");
                   break;
                   case 2: // UPLOAD_ERR_FORM_SIZE
                   $errs->addErreur("Le fichier dïépasse la limite autorisïée dans le formulaire HTML !");
                   break;
                   case 3: // UPLOAD_ERR_PARTIAL
                   $errs->addErreur("L'envoi du fichier a ïétïé interrompu pendant le transfert !");
                   break;
                   case 4: // UPLOAD_ERR_NO_FILE
                   $errs->addErreur("Le fichier envoyïé a une taille nulle !");
                   break;
                   default:
                   $errs->addErreur("erreur d'envoi de type :".$_FILES[$name]['error'].".");
          }
          return false;
}
elseif (   $_FILES[$name]['size'] > 1000000*$maxfilesize ) {
  $errs->addErreur("Le fichier envoyïé depasse la taille specifiïée dans les options"); 
 
} else {
move_uploaded_file($_FILES[$name]['tmp_name'], $dest.$rename);
 // $_FILES['nom_du_fichier']['error'] vaut 0 soit UPLOAD_ERR_OK
 // ce qui signifie qu'il n'y a eu aucune erreur
 return true;
 }
}


static function string_to_array($string) {
   $retval = array();
   $string = urldecode($string);
   $tmp_array = explode('||', $string);
   $null_value = urlencode(base64_encode("^^^"));
   foreach ($tmp_array as $tmp_val) {
       list($index, $value) = explode('|', $tmp_val);
       $decoded_index = base64_decode(urldecode($index));
       if($value != $null_value){
           $val= base64_decode(urldecode($value));
           if(substr($val,0,8)=='^^array^') $val=string_to_array(substr($val,8));
           $retval[$decoded_index]=$val;
         }
       else
           $retval[$decoded_index] = NULL;
   }
   return $retval;
}

static function genHtmlArray($listeitems,$nbcols) {
 $af = "<table class='formx_item_tab' >";
 $af.= "<tr>";
 $j = 0;
 for($i = 0;$i < $nbcols;$i++) {
	$j++;
	$af.="<td>".$listeitems[$j]."</td>";	
 }
 $af.= "</tr>";
 $af.= "</table>";
}

//-----------------------------------------------------------------------------------------------------------
// CHAINES DE CARACTERES
//-----------------------------------------------------------------------------------------------------------

//prend latin ou utf8, renvoie utf8
static function clean2utf8($str)
{
	if( ! function_exists('mb_detect_encoding') )
	{
		formxSession::getInstance()->addErreur("Attention !! Le module PHP mbstring n'est pas installé : des problèmes d'encodage peuvent survenir.");
		return $str ;
	}

	if(  mb_detect_encoding($str.'fixbug','UTF-8, ISO-8859-1') != 'UTF-8' )
		return utf8_encode($str);
	return $str ;
}

//renvoie dans le format d'encodage attendu en sortie navigateur
//plus tard, mettre option pour encodage de sortie
static function encode4web($str)
{

	if( ! function_exists('mb_detect_encoding') )
	{
		formxSession::getInstance()->addErreur("Attention !! Le module PHP mbstring n'est pas installé : des problèmes d'encodage peuvent survenir.");
		return $str ;
	}
	
	if(  mb_detect_encoding($str.'fixbug','UTF-8, ISO-8859-1') != 'UTF-8' )
		return $str ;
	
	return utf8_decode($str) ;
}

//encode une chaine de caracteres UTF8 en  BDD ( crade et ne devrait pas exister mais prÃ©vient des horreurs via le traitement resulquery)
static function encode2bdd($str)
{
	//remplace paragraphe par yen
	return str_replace(chr(0xA7),chr(0xA5),$str);
}

//decode une chaine de caracteres UTF8 depuis sa recupe via resulquery ( crade et ne devrait pas exister mais prévient des horreurs via le traitement resulquery)
static function decodeFromBdd($str)
{
	//remplace yen par paragraphe
	$str = str_replace(chr(0xA5),chr(0xA7),$str);
	//replace mauvais 'à'  utf8 (aucune idée d'où ils viennent ) par les bons
	$str = str_replace(chr(195).chr(32),chr(195).chr(160),$str);


	//for($i = 0 ; $i < strlen($str) ; $i++ )
	//echo '<br />_'.$str[$i] .'_'.ord($str[$i]);
	//die ;
	
	return $str ;
}

/**
 * renvoie l'id atomique d'un formx
 * ex: enquetes/toto -> toto
 */
static public function strGetIdAtomiqueFx($str)
{
    //  zrezr/zerzer/zerzer/   zrz_e123rez
    if( preg_match('/^.*\/([^\/]+)$/', $str,$tabMatch) )
        $str = $tabMatch[1];
    if( preg_match('/^(.*)\.xml$/', $str,$tabMatch) )
        $str = $tabMatch[1];

    return $str ;
}

//-----------------------------------------------------------------------------------------------------------
// ACCES AUX VARIABLES GLOBALES
//-----------------------------------------------------------------------------------------------------------

static function globalsLoad(/*int*/ $ids) {
  
  return new formxGlobals($ids);

}








static function globalsDelVar(DOMDocument $dom ,$var,$isSave = false)
{
	formxTools::globalsSetVar( $dom , $var, '',$isSave) ;
	if ( $isSave ) formxTools::globalsSave($dom) ;
}




//---------------------------------------------
//Manipulation de formulaires
//---------------------------------------------
// teste si un formulaire est présent dans les données posts
// utile pour savoir si à l'exterieur d'une classe appelante,
// quelque chose est affiché à l'écran ou pas
//attention, buggé dans TU , à cause des rpost qui polluent la fiche patient ( probabilité qu'ils soient généré lors de l'appel formx pas négligeable)

static function manipIsFormPresent() {
	if ( isset($_POST['FormX_ext_goto_']) || isset($_POST['FoRmX_INSTANCE']) || isset($_POST['FoRmX_chooseNew']) )
	{
		return true ;
	}
	return false;
}


//---------------------------------------------
//Exports de données
//---------------------------------------------


static public function exportsFormxDispos($options='')
{
	$dos = opendir(formxSession::getInstance()->getFxLocalPath()) ;
	$liste = array();
	while ($fich = readdir($dos)) {
	if (ereg("^.*\.xml$",$fich)) {
		//on ouvre le fichier pour en trouver les caracteristiques principales
		$xml =  simplexml_load_file(FORMX_LOCATION.$fich);
		if (! $xml) {
			//eko("pb chargement de l'instance");
		} else {
			if($xml['hide']=='y') continue;
			$liste[(string) $xml['id']] = utf8_decode((string) $xml->Libelle) ;
		}
	}
	};
 return $liste;
}

static public function exportsGetTabIds($ids,$options='')
{
    if(is_array($options) && isset($options['cw']))
        $cw = ' AND '.$options['cw'] ;
    else
        $cw = '' ;
	return self::exportsGetTabCw(" ids ='$ids' $cw ",$options);
}

static public function exportsGetTabIdsIdform($ids,$idform,$options='')
{
    if(! $options) $options = array() ;
    $idform = self::strGetIdAtomiqueFx($idform);
	return self::exportsGetTabIds($ids,$options + array('cw' => " idformx = '$idform' "  ) );
}

static public function exportsGetTabIdform($idform,$options='')
{
    if(! $options) $options = array() ;
    $idform = self::strGetIdAtomiqueFx($idform);
    if(is_array($options) && isset($options['cw']))
        $cw = ' AND '.$options['cw'] ;
    else
        $cw = '' ;
	return self::exportsGetTabCw("  idformx = '$idform' $cw ",$options);
}

static public function exportsGetTabIdsIdformFilterValue($ids,$idform,$idItem,$val,$options='')
{
	if( ! $options ) $options = array() ;
	return self::exportsGetTabIdsIdform($ids,$idform,$options +  array('filterValues'  =>    array($idItem => $val )  ) );
}

static public function exportsGetTabIdformFilterValue($idform,$idItem,$val,$options='')
{
    if( ! $options ) $options = array() ;
	return self::exportsGetTabIdform($idform,$options +  array('filterValues'  =>    array($idItem => $val )  ) );
}


//exportsGetTabCw(" ids='123' AND idformx='avc'   ",array('filterValues'  =>    array('id_passage' => 15678)  ))
/**
 *
 * @param <type> $cw
 * @param <type> $options basic, basicOnly , noNominativeData , etat , order=recentFirst
 * @return <type>
 */
static public function exportsGetTabCw($cw,$options='')
{

	//eko($cw);
	//eko($options);

	if(! $options || ( is_array($options) && ( count($options) == 0 ) ) )
	{
		$options = array('noNominativeData' => true , 'etat' => array('I','E','F','H'));
	}

	if( ! isset($options['etat']))
	{
		$options = array_merge($options, array( 'etat' => array('I','E','F','H') ) );
	}

	
	$cw = "  ( $cw ) AND  status IN   ('".implode(    "','"   ,   $options['etat']   ). "') "  ;

	if( isset($options['order'] ) && $options['order'] == 'recentFirst' )
		$cw .= " ORDER BY id_instance DESC ";

	$requete = "SELECT id_instance ,idformx,ids , dt_creation, dt_modif, idformx, libelle, status, author FROM formx WHERE $cw ";



	$obRequete = clFoRmXSession::getInstance()->getObjRequete();
	$res = array();
    
	foreach ( $obRequete->exec_requete($requete,'tab') as $ligne )
	{
		//on veut les infos basiques du formulaire
		$tabBasic = array() ;
		if( ( isset($options['basic']) && $options['basic'] ) || ( isset($options['basicOnly']) && $options['basicOnly'] ) )
		{
			$tabBasic['idInstance'] = $ligne['id_instance'];
			$tabBasic['ids'] = $ligne['ids'];
			$tabBasic['dtCreation'] = $ligne['dt_creation'] ;
			$tabBasic['dtModif'] = $ligne['dt_modif'] ;
			$tabBasic['idFormx'] = $ligne['idformx'] ;
			$tabBasic['libelle'] = $ligne['libelle'] ;
			$tabBasic['status'] = $ligne['status'] ;
			$tabBasic['author'] = $ligne['author'] ;
		}

		if(  ( isset($options['basicOnly']) && $options['basicOnly'] ) )
		{
			$res[] = $tabBasic ;
			continue ;

		}

		try {
            
			$formx = new clFoRmX($ligne['ids'],'NO_POST_THREAT');
			$formx->loadInstance($ligne['id_instance']);
			$okForExtract = false ;

			if(isset($options['filterValues']))
			{
				foreach($options['filterValues'] as $idItem => $needValue)
				{
					if($formx->getFormVar($idItem) == $needValue )
					{
						$okForExtract = true ;
					}
				}
			}
			else
			{
				$okForExtract = true ;
			}
			if($okForExtract)
			{
				$resLigne = $tabBasic + $formx->getTabAllItemsValues($options) ;
				$res[] = $resLigne ;
			}
		} catch (Exception $e) {

		}
        
	}

	return $res ;
	
}




static public function exportsGetCsvCw($cw,$nomFic,$options)
{
	$data = clFoRmXtOoLs::exportsGetTabCw($cw,$options);
	return clFoRmXtOoLs::exportsGetCsvFromData($data,$nomFic);
}


/**
 *transforme un tableau de données en csv; renvoie l'url publique du fichier
 * @param <type> $dataTab
 * @param <type> $nomFic
 */

static public function exportsGetCsvFromData(&$dataTab,$nomFic = '',$options = array() )
{
	if( ! isset( $options['local_access'] ))
		$isLocalAccess = false ;
	else
		$isLocalAccess = true ;
	$tabIndic = array() ;
	if( ! $nomFic )
	{
		$nomFic = 'export_'.rand(1,999999).'.csv';
	}

	$urlLocalDepot = '';
	if( isset ($options['urlDepot']) )
	{
	    $urlLocalDepot = $options['urlDepot'] ;
	}
	else
	{
	    $urlLocalDepot = clFoRmXSession::getInstance()->getLocalUrlCache() ;
	}

	$hFic = fopen($urlLocalDepot.$nomFic,'w') ;
	//on parcourt une premiere fois pour avoir les indicateurs et créer le header
	$header = '' ;
	$tabIndexIgnore = array() ;
	foreach($dataTab as $ligne)
	{
		foreach($ligne as $idIndic => $val)
		{
			if( ! isset( $tabIndic[$idIndic] ))
			{
				if(  isset ( $options['cols'] ) &&  ! in_array($idIndic, $options['cols']) )
				{
					continue ;
				}
				$tabIndic[$idIndic] = $idIndic ;
				$header.= $idIndic. ';';
			}
		}
	}
	$header = rtrim($header,';');
	fwrite($hFic, $header);
	foreach($dataTab as $ligne)
	{
		$ligneCsv = "\n" ;
		foreach($tabIndic as $idIndic )
		{
			$value ='';

			if(  isset($ligne[$idIndic] ) )
			{
				$value = self::clean2utf8($ligne[$idIndic]) ;
				$value=str_replace(';', ',', $value);
				$value=str_replace("\n", ' ', $value);
				$value=str_replace("\r", ' ', $value);
			}
			$ligneCsv .= $value.';' ;
		}
		$ligneCsv = rtrim($ligneCsv,';');
		//eko($ligneCsv);
		fwrite($hFic, $ligneCsv);
	}
	fclose($hFic);

	//eko(file_get_contents(clFoRmXSession::getInstance()->getLocalUrlCache().$nomFic));

	if( ! $isLocalAccess )
		return clFoRmXSession::getInstance()->getWebUrlCache().$nomFic;
	else
		return $urlLocalDepot.$nomFic;
}





//-----------------------------------------------------------------------------------------------------------
//MANIPULATION DE DOM
//-----------------------------------------------------------------------------------------------------------

static function domGetValueFxItem(DomNode $item)
{
	return formxTools::domGetTagValue($item,'Val');
}

static function domGetTagValue($item,$tag)
{
	return formxTools::getTagValue( $item, $tag) ;
}

static function domGetRoot(DOMDocument $dom) {
	return $dom->documentElement ;
}

static function domSetTagValue(DOMNode $item, $tag, $value)
{
	return formxTools::setTagValue( $item, $tag, $value) ;
}

static function domControlNode(DOMNode $item)
{
	if( ! in_array('DOMNode',class_parents($item)) )
		throw new exception('1st argument is not a DOMNode');	
}




//va chercherïé quelle ligne on ïétait une pile plus haut
 static   function upCodeInfo()
    	{
	$infoDebug = debug_backtrace();
	return $infoDebug[1]["line"];
	}
	
static	function getAllFormsAny($cw=' 1 = 1 '){
		$req = new clResultQuery ;
  		$param['table']=TABLEFORMX;
  		$param['cw']="where ".$cw;
  		$res = $req -> Execute ( "Fichier", "FX_getGen", $param, "ResultQuery" ) ;
  		return $res;
	}
	
// fonction qui empeche erreurs notice poour variable non instanciée
static function rpost($vars)
    {
	   	foreach ($vars as $value) {
    	if (! isset($_POST[$value])) $_POST[$value] ='';
	   	}
    }
    
static function getPost($var) 
{
	if(isset($_POST[$var]))
		return($_POST[$var]);	
}

function setPost($var,$val) 
{
	$_POST[$var]=$val;
}

//transforme un format de date php en format de date pour le cal js
static function formatDatePhp2FormatCalJs($formatphp) {
	$in  = array( 'Y', 'm', 'd', 'H', 'M', 'S','i');
	$out = array('%Y','%m','%d','%H','%M','%S','%M');
	return str_replace($in,$out,$formatphp);
}

static function getDomState(clFoRmX $formx )  
{
	return formxTools::getTagValue($formx->XMLDOM->documentElement,'STATUS');
}

static function setDomState(clFoRmX $formx , $state)  
{
	global $xham;
	return formxTools::setTagValue($formx->XMLDOM->documentElement,'STATUS',$state);
}

static function isNotNullSxItem(SimpleXMLElement $item )  
{
	$val = (string) $item->Val ;
	return clFoRmXtOoLs::isNotNullVal($val);
}

static function isNotNullDomItem(DomNode $item )  
{
	$val = formxTools::getValueDomItem($item);
	return clFoRmXtOoLs::isNotNullVal($val);
}

static function getValueDomItem(DomNode $item)
{
	return formxTools::getTagValue($item,'Val');
}

static function domSearch($item,$bal)
{
	formxTools::domControlNode($item);
	return $item->getElementsByTagName($bal);
}



//renvoie le nodeValue du premier fils $tag rencontrïé
static function getTagValue(DOMNode $item, $tag)
{
	formxTools::controlNodeTag( $item, $tag);
	return $item->getElementsByTagName($tag)->item(0)->nodeValue ;
}
 
//a tester
static function hasRootAttribute( clFoRmX $formx ,$att) {
	return formxTools::hasAtttribute($formx->getRootDom(),$formx) ;
}

//a tester
static function hasAttribute(DOMNode $item,$att) {
	return $item->hasAttribute($att);
}


//teste si un fils existe
static function isTag(DOMNode $item,$tag)
{
	return is_object($item->getElementsByTagName($tag)->item(0));	
}

static function controlNodeTag(DOMNode $item, $tag)
{
	formxTools::domControlNode($item);
	$node = $item->getElementsByTagName($tag)->item(0) ;
	if( ! is_object($node) )
		throw new exception('tag '.$tag.' is not yet created');
}

//affecte le nodeValue du premier fils $tag rencontrïé
static function setTagValue(DOMNode $item, $tag, $value)
{
	formxTools::controlNodeTag( $item, $tag);
	//on regle le pb du caractere &
	$value = preg_replace ("#&(?!(amp|lt|gt);)#U","&amp;",$value);
	$item->getElementsByTagName($tag)->item(0)->nodeValue = formxTools::clean2utf8($value) ;
}

static function createTag(DOMNode $item, $tag,DOMDocument $domdoc)
{
	$new = $domdoc->createElement($tag);
	$item->appendChild($new);
	return $new ;
}


static function createTagValue(DOMNode $item, $tag, $value,DOMDocument $domdoc)
{
	if ( ! formxTools::isTag($item,$tag) )
	{
		formxTools::createTag($item, $tag,$domdoc);
	}
	
	formxTools::setTagValue($item , $tag, $value) ;
}



static function setTag(DOMNode $item,$tag)
{
	$newNode = $item->createElement($tag);
	$item->appendChild($newNode);	
}

		


static function isNotNullVal($val) 
{
	if ( ! $val  || in_array((string) $item->Val,formxSession::getInstance()->getNullValues() ) )
		return false ;
	return true ;
}


static function genListFormsXml($ids) {
	$xml= "<listeInfosFormulairesDispos>";
	foreach (array("F","E","I") as $etat) {
	$data =clFoRmXtOoLs::ListFromIds($ids,$etat);
		for ( $i = 0 ; isset ( $data['id_instance'][$i] ) ; $i++ ) {
			$newInstance = new clFoRmX($ids,'NO_POST_THREAT');
			$newInstance->loadInstance($data['id_instance'][$i]);
			$droit = $newInstance->getFormMainDroit() ;
			if ( ! $newInstance->getDroit ( $droit ,'r') )
				continue ;
			$xml.="<formulaire>";
			$xml.="<id_instance>".$data['id_instance'][$i]."</id_instance>";
			$xml.="<author>".$data['author'][$i]."</author>";
			$xml.="<id_formx>".$data['idformx'][$i]."</id_formx>";
			$xml.="<dt_creation>".$data['dt_creation'][$i]."</dt_creation>";
			$xml.="<dt_modif>".$data['dt_modif'][$i]."</dt_modif>";
			$xml.="<libelle>".$data['libelle'][$i]."</libelle>";
			$xml.="<statut>".$data['status'][$i]."</statut>";
			$xml.="<groupe>".$newInstance->groupeClassement."</groupe>";
			$xml.="</formulaire>";
		}
	}
	$xml.="</listeInfosFormulairesDispos>";
	return $xml;
}

/**
 * renvoie une mise en forme HTML depuis caracteres spéciaux Formx
 * @param string $chaine
 * @return string
 */
static function helper_str_mef($chaine)
{
	$conv = array('-*-' => '<br/>' , '-isNull-' => '' , '-**' => '<' , '**-' => '>' );
	$conv['/*and*/']='&';
	return strtr( $chaine , $conv) ;
}


static function helper_html_barcode($str,$type)
{
	$urlBarrer = formxSession::getInstance()->getWebUrl().'classes_ext/barcode/image.php';



	return "<img height=120 src='$urlBarrer?code=$str&style=196&type=C39&width=460&height=120&xres=3&font=5' alt='$str' />";
}


static function helper_formatDatatype($item,$val)
{
    if($val  === '') return $val ;

	if( ! $item->hasAttribute('datatype')) return $val ;
	switch($item->getAttribute('datatype'))
	{
		case 'int':
			$val = (int) $val ;
			//$val = preg_replace('/[^0-9]+/', '', $val) ;
			break ;
		case 'float':
			$val = preg_replace('/,/', '_', $val,1) ;
			$val = preg_replace('/\./', '_', $val,1) ;
			$val = preg_replace('/[^0-9_]+/', '', $val) ;
			$val = preg_replace('/_/', '.', $val) ;
			break ;
	}

	if($val  === '') return $val ;



	if(  $item->hasAttribute('minvalue'))
	{
		if ( $val < $item->getAttribute('minvalue') ) return '';
	}
	if(  $item->hasAttribute('maxvalue'))
	{
        if ( $val > $item->getAttribute('maxvalue') ) return '';
	}
	return $val;
}



 static  function genCase($ids,$bubulle) {
 	global $formxSession ;
 	if(! is_object($formxSession))
 		$formxSession = new formxSession() ;
	// Chargement du template ModeliXe.
    $mod = new ModeliXe ( "FX_blocActions.mxt" ) ;
    $mod -> SetModeliXe (  ) ;
	$nbActions = 0 ;
	//la en fait on ne genere pas un lien, qui serait la solution la plus simple, mais l'enjeu est de tout gerer (Post, variables de la classe...) lors de la crïéation de la classe. On ne touche pas aux variables de navigation. Donc on va crïéer un champ qui - pour l'utilisateur - ressemble comme deux gouttes d'eau ïé un lien mais qui en fait est un appel javascript remplissant un champ cachïé par une variable donnïée.
	foreach (array("F","E","I") as $etat) {
	$data =clFoRmXtOoLs::ListFromIds($ids,$etat);
		for ( $i = 0 ; isset ( $data['id_instance'][$i] ) ; $i++ ) {
			if (clFoRmXtOoLs::printLigneForm($i,$mod,$etat,true,$bubulle,$data))
				$nbActions ++;
		}
	}
	
	$mod -> Mxattribut("newact_code","document.FoRmXcase.FormX_ext_goto_.value = 'new';document.FoRmXcase.submit();");
	
	if($nbActions == 0 )	 {
		$mod -> MxBloc ( "actions.frem", "delete" ) ;
		$mod -> MxBloc ( "titre", "delete" ) ;
	} else {
		$mod -> MxBloc ( "padetitre", "delete" ) ;
	}
	$mod -> MxHidden ( "hidden", "navi=".$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2) )) ;
	return $mod -> MxWrite ( "1" ) ; 
}

  //Affiche la ligne que concerne le formulaire dans la case ci-dessus
static function printLigneForm($i,$mod,$status,$ro=false,$bubulle=false,& $data) {
	global $formxSession;
	$ids = $data['ids'][$i];
	$idinstance = $data['id_instance'][$i];
  	switch($status) {
  	case 'I':
  		$affStatus =  "<span  style='color:#FF0000'>Initialisé</span>" ;
  		break;
  	case 'E':
  		$affStatus = "<span  style='color:#AA8822'>En cours</span>" ;
  		break;
  	case 'F':
  		$affStatus = "<span  style='color:#00DD00'>Fini</span>" ;
  		break;	
  	}

  	$newInstance = new clFoRmX($ids,'NO_POST_THREAT');
	$newInstance->loadInstance($idinstance);
	
	//si une donnïée idApplication est prïésente (ce qui signifie qu'on est sur une base
  	//formx multi-applicative (ex: montana ), on n'affiche pas les initialises et les
  	//en cours pour une appli diffente de la courante
  	if( ! defined('FX_IDAPPLICATION')  )
 	 	define('FX_IDAPPLICATION',IDAPPLICATION);
	if($newInstance->idApplication) {
		if($newInstance->idApplication != FX_IDAPPLICATION  and $status != 'F' )
			return false ;	
	}
	
	$droit = $newInstance->getFormMainDroit() ;
	if ( ! $newInstance->getDroit ( $droit ,'r') ) {

		return false;
	}
 	$padaction='';
	$libelle = $newInstance->libelle;
	if(! $libelle)
		$libelle = $newInstance->getIdFormx() ;
	$mod -> MxText ( "actions.statut",$affStatus) ;
	$dateDerModif = new clDate($data['dt_modif'][$i]);
	$mod -> MxText ( "actions.dermodif",$dateDerModif->getSimpleDate());
	$mod -> MxText ( "actions.author","&nbsp;".$data['author'][$i]."&nbsp;");
	$mod -> MxText ( "actions.libelle", $libelle ) ;
	$mod -> Mxattribut("actions.codemouseover","montre_resume_formulaire(event,'resu_$idinstance')");
	$mod ->Mxattribut("actions.codemouseout","hide('resu_$idinstance')");
	if($bubulle)
		$bubulle->addBulle("resu_$idinstance",$newInstance->gen_resume());
	unset($newInstance);
	$mod -> Mxattribut("actions.code","document.FoRmXcase.FormX_ext_goto_.value = '".$idinstance."';document.FoRmXcase.submit();");

		if ( $formxSession->getDroit ( $droit,'d' ) ) {
		$mod->Mxattribut("actions.frem.code","document.FoRmXcase.FormX_ext_goto_.value = 'RM".$data['id_instance'][$i]."';document.FoRmXcase.submit();");
		} else { $mod -> MxBloc ( "actions.frem", "delete" ) ; }
			
		if ( $formxSession->getDroit ( $droit,'a' ) && $data['status'][$i] == 'F') {
		$mod->Mxattribut("actions.fed.code","document.FoRmXcase.FormX_ext_goto_.value = 'ED".$data['id_instance'][$i]."';document.FoRmXcase.submit();");
		} else { $mod -> MxBloc ( "actions.fed", "delete" ) ; }
	
	
	$mod -> MxBloc ( "actions", "loop" ) ;
	return true ;
  }

//messages (pour future localisation :P)
static function message($m) {
switch($m) {
	case 'infoNoValid1':
		return "L'item \"";
	case 'infoNoValid2':
		return "\" doit être rempli";		
	}
}	
//idem avec encodage utf8	
static function u8message($m){
	return utf8_encode(clFoRmXtOoLs::message($m));
}



//donne le format datetime en une date simple mais efficace 04/12/1745
static function date_simple($dte_naiss){
 if ( $dte_naiss == "0000-00-00 00:00:00" || $dte_naiss == 0 ) {
 	return "";
 	}
$age = new clDate ( $dte_naiss ) ;
return $age -> getSimpleDate ( );
}



 
 


//pour patch php 5.1.3
static function implode_r($tab) {
	foreach($tab as $key=>$value) {
	if(is_array($value))
			$tab[$key]=clFoRmXtOoLs::implode_r($value);
	}
 return implode('|',$tab);
}

//transforme les id de balises en noms de colonne
static function idbal2col ($s) {
	$trans = array();
	$trans[' ']="_";
	return strtr($s,$trans); 
}

static function anydate2datetime($date) {
	if(is_object($date))
		return $date->getDatetime();
	$objdate = new clDate($date);
		return $objdate->getDatetime();
}

//retourne le contenu d'un objet simple xml en mettant en forme HTML sa valeur selon ses 
//Attributs css
static function getStyledBal($objSxml) {
 	//si classe css appliquïée au libelle
   if ($objSxml["class"]) 
   	 return "<span class='".$objSxml["class"]."'>".$objSxml[0]."</span>" ;
   if ($objSxml["style"])
     return "<span style='".$objSxml["style"]."'>".$objSxml[0]."</span>";
   return $objSxml[0];
}



//parcours et genere l'ensemble des squelettes de formulaires pour generer des infos en xml
static function genChoiceFormsXml($prefix,$ids) {
 global $formxSession ;
 if(! is_object($formxSession))
 	$formxSession = new formxSession() ;
   $dos=opendir($formxSession->xmlLocation); // Met le pointeur de lecture sur le dossier courant.
   $tabAllForms = array ();
   while ($fich = readdir($dos)) // Boucle jusqu'a ce que le pointeur de lecture ïéchoue.
        {
	if (ereg("^.*\.xml$",$fich)) {
		//on ouvre le fichier pour en trouver les caracteristiques principales
		$xml =  simplexml_load_file($formxSession->xmlLocation.$fich);
		if (! $xml) {
			eko("pb chargement des datas");
		} else {
			if($xml['hide']=='y') continue;
			if ($xml['access'] ) { 
				$droit = $formxSession->getDroit(utf8_decode((string) $xml['access']),'w') ;
			} else $droit = $formxSession->getDroit($formxSession->droitGen,'w') ;
   			if(! $droit ) continue ;
   			$infos = array ("id" => $xml['id'] , 'titre' => (string) $xml->Libelle , 'objet' => (string) $xml->Objet);
			if ( (string) $xml->Groupe ) {
				$tabAllForms[(string) $xml->Groupe ][] = $infos ;
			} else {
				$tabAllForms[utf8_encode("Général")][] = $infos ;
			}
		}	
	   }
     }
    $res = "";
    foreach($tabAllForms as $groupe => $tabGroupe) {
    	$res.= "<groupe id=\"$groupe\">";
    	foreach($tabGroupe as $infoGroupe) {
    		$res.="<form id='".$infoGroupe['id']."'/>";
    		$res.="<titre>".$infoGroupe['id']."</titre>";
    		$res.="<objet>".$infoGroupe['objet']."</objet>";
    		$res.="</form>";
    	}
    	$res.= "</groupe>";	
    }
    return utf8_decode($res);  
}

//parcours et genere l'ensemble des squelettes de formulaires pour generer le selecteur
static function genMenuSelection($prefix,$ids) {
 global $formxSession ;
 if(! is_object($formxSession))
 	$formxSession = new formxSession() ;
 $mod = new ModeliXe ( "FX_selectNew.mxt" ) ;
 $mod -> SetModeliXe (  ) ;
   $dos=opendir(FORMX_LOCATION); // Met le pointeur de lecture sur le dossier courant.
   $tabAllForms = array ();
   while ($fich = readdir($dos)) // Boucle jusqu'a ce que le pointeur de lecture ï¿½choue.
        {
	if (ereg("^.*\.xml$",$fich)) {
		//on ouvre le fichier pour en trouver les caracteristiques principales
		$xml =  simplexml_load_file(FORMX_LOCATION.$fich);
		if (! $xml) {
			eko("pb chargement de l'instance");
		} else {
			if($xml['hide']=='y') continue;
			if ($xml['access'] ) { 
				$droit = $formxSession->getDroit(utf8_decode((string) $xml['access']),'w') ;
			} else $droit = $formxSession->getDroit(DROITGENFORMX,'w') ;
   			if(! $droit ) continue ;
   			$infos = array ("id" => $xml['id'] , 'titre' => (string) $xml->Libelle , 'objet' => (string) $xml->Objet);
			if ( (string) $xml->Groupe ) {
				$tabAllForms[(string) $xml->Groupe ][] = $infos ;
			} else {
				$tabAllForms[utf8_encode("Général")][] = $infos ;
			}
		}	
	   }
     }
     
    foreach($tabAllForms as $groupe => $tabGroupe) {
    	$mod->MxText('groupe.titregroupe',$groupe);
    	$mod->MxAttribut("groupe.id_head","head_".$groupe);
    	$mod->MxAttribut("groupe.id_body","body_".$groupe);
    	$mod->MxAttribut("groupe.codeOnClick","document.getElementById('body_$groupe').style.display='block';");
    	$mod->MxAttribut("groupe.codeOnClickClose","document.getElementById('body_$groupe').style.display='none';");
    	$mod->MxAttribut("groupe.codeOnClickCheckAll","checkAllIn('body_$groupe');");
    	foreach($tabGroupe as $infoGroupe) {
    		$mod->MxText('groupe.SQUELETTE.titre',$infoGroupe['titre']);
			$mod->MxText('groupe.SQUELETTE.id',$infoGroupe['id']);
			$mod->MxText('groupe.SQUELETTE.objet',$infoGroupe['objet']);
			$mod->MxCheckerField('groupe.SQUELETTE.check','checkbox',$prefix."chooseNew[]",$infoGroupe['id'],'',"class=\"casechoi\"");
			$mod->MxBloc('groupe.SQUELETTE','loop'); 
    	}
    $mod->MxBloc('groupe','loop');	
    }  

     
     
    $mod->MxFormField("selCancel","image",$prefix."selCancel","on","value='on' src=\"".FX_URLIMGANNMINI."\"");
  	$mod->MxFormField("selValid","image",$prefix."selValid","on","value='on' src=\"".FX_URLIMGVAL."\"");
	//fermeture de fenï¿½tre 
	if($formxSession->infos['mode'] != 'iframe' )
		$mod->MxFormField("windowClose","image",$prefix."close","on","value='on'  src=\"".FX_URLIMGCLO."\"");
	//navigation
	$mod -> MxHidden ( "hidden1", "navi=".$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2), $formxSession->getNavi(3))) ;
	//ids
	$mod -> MxHidden ( "hidden2", "ids=".$ids ) ;
	return $mod -> MxWrite ( "1" ) ; 
}


//genere un lien et ses javascitps pour l'affichage d'une iframe d'infos
static function getHyperLink($args) {
	$res = array();
	if(ereg("url:([^;]*);",$args,$res)) {
		$url = FX_URLDOCUMENT.$res[1];
		if(ereg("width:([^;]*);",$args,$res))
			$width = $res[1];
		else
			$width = 400 ;
		if(ereg("height:([^;]*);",$args,$res))
			$height = $res[1];
		else 
			$width = 400 ;
		return "&nbsp;<a href='about:blank' onmouseover=\"formx_infobulle_preload(this) ; 
			formx_infobulle_changeurl('$url',$width,$height);this.onclick=formx_infobulle_load;\" ><img alt='[?]' src='images/help.gif'/></a>";
	} elseif (ereg("text:([^;]*);",$args,$res)) {
		$text =  $res[1];
		return "&nbsp;<a href='about:blank' onmouseover=\"formx_infobulle_preload(this) ; 
			formx_infobulle_changetext('".addslashes($text)."');this.onclick=formx_infobulle_load;\" ><img alt='[?]' src='images/help.gif'/></a>";
	} else {
		return "";
	}

}

static function simpleRemoveInstance($idInstance) {
  $req = new clResultQuery ;
  $param['table']=formxSession::getInstance()->getTable() ;
  $param['id_instance']=$idInstance ;
  $res = $req -> Execute ( "Fichier", "FX_rmIns", $param, "ResultQuery" ) ;
  return true ;
}


//Les quatre fonction suivantes gerent la liste des formulaires
//ï¿½ remplir lorsque plusieurs ont ï¿½tï¿½ demandï¿½s lors de la
//Sï¿½lection

static function addFormToLoad($context,$idinstance,$priority=0) {
	$_SESSION['formx_pipe_'.$context][$idinstance] = $priority ;
}




static function delFormToLoad($context,$idinstance) {
	global $errs;
//	$errs->whereAmI();
	//eko("demande de suppresion pour $idinstance");
	if (! isset( $_SESSION['formx_pipe_'.$context]) )
		return ;
	foreach ( $_SESSION['formx_pipe_'.$context] as $key => $val ) {
			if($key == $idinstance)
				unset($_SESSION['formx_pipe_'.$context][$key]);
	}
}

static function isFormToLoad($context) {
//	eko($_SESSION['formx_pipe_'.$context]);
	if (! isset( $_SESSION['formx_pipe_'.$context]) )
		return false ;
	$id2return = false ;
	foreach ( $_SESSION['formx_pipe_'.$context] as $key => $val ) {
		if(! isset($derminval)) {
			$derminval = $val ;
			$id2return = $key ;
		} else if ( $val < $derminval ) {
			$derminval = $val ;
			$id2return = $key ;
		}
	}
	return $id2return;
}

static function cleanFormToLoad($context) {
if (isset($_SESSION['formx_pipe_'.$context]))
	unset ($_SESSION['formx_pipe_'.$context]);	
}
//Retourne un tableau concernant un ou des idformx avec les id de balises en colonnes.
//toutes par default, sinon que celles spï¿½cifiï¿½es dans le tableau value
//filtre prend un nom de fichier resulquery personalisï¿½
//date1 et date 2 optionnelles encadrent la selection des dates de cration et de modification


static function peerGetFromCw($cw)
{
	$requete = formxSession::getInstance()->getObjRequete();
	$tabIdInstances = $requete->getGen($cw,'tab',' ids , id_instance ') ;
	$tabRetour = array() ;
	foreach ( $tabIdInstances as $ligne )
	{
		$formx = new clFoRmX($ligne['ids'],'NO_POST_THREAT');
		$formx -> loadInstance($ligne['id_instance']);
		$tabRetour[] = $formx ;
	}
	return $tabRetour ;
}


static function peerGetFromIdIdu($id,$idu)
{
	return formxTools::peerGetFromCw(" idformx = '$id' AND  ids = '$idu'  ");
}




  /*renvoie un tableau avec les identifiants d'instance -> titre concernant un ids donnïé
   status : I: init / F : fini / E : en cours
   */
static  function ListFromIds($ids,$status='',$onlyselfappli='y') {
	//formxSession::getInstance()->addErreur("ListFromIds est une fonction obsolette et plus maintenue. utilser getExport");
	//TODO
	//réécrire sans appels resultQuery
	
	global $formxSession;
	if(! is_object($formxSession))
 		$formxSession = new formxSession() ;
	if(is_array($status)) {
		$status = implode("','",$status);
		$status = "('".$status."')";
		$param["cw"] = "WHERE ids = '".$ids."' and status IN $status and idformx != '' ORDER BY dt_modif DESC";
	} else {
		if($status =='')
			$param["cw"] = "WHERE ids = '".$ids."' and idformx != '' ORDER BY dt_modif DESC";
		else
  			$param["cw"] = "WHERE ids = '".$ids."' and status = '$status' and idformx != '' ORDER BY dt_modif DESC";
	}


    $param["table"] = $formxSession->tableInstances ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "FX_getGen", $param, "ResultQuery" ) ;
     if ( ! $res['INDIC_SVC'][2] ) {
     	return '';
     } else {
     	if (isset($res['id_application'])) {
     		 $nb = $res['INDIC_SVC'][2] ;
     		 $j = -1 ;
     		 $newres = array();
     		for( $i = 0 ; $i< $nb ; $i ++ ) {
     			if(! $res['id_application'][$i] || $res['id_application'][$i] == $formxSession->idApplication) {
     				$j ++ ;
     				foreach($res as $key => $value) {
     					if ( ! isset($newres[$key]))
     						$newres[$key] = array();
     					if(	$key != 'INDIC_SVC' ) {
     						$newres[$key][$j] = $res[$key][$i] ;
     					}
     				}
     			}
     		}
     		$newres['INDIC_SVC'] = $res['INDIC_SVC'];
     		$newres['INDIC_SVC'][2] = $j + 1 ;
     		return $newres;
     	}
     	return $res;
     }
}


//-------------------------
// Transformation de types
//-------------------------

static function transfo_typeliste_2_array($str)
{
	$tabMatches = array() ;
	if( preg_match('/^list:(.*)$/', $str,$tabMatches))
		$str = $tabMatches[1] ;
	return explode('|',$str);
}


static function transfo_str_2_bool($str)
{
	if( ! $str ) return false ;
	$tabNullValues = formxSession::getInstance()->getNullValues() ;
	$tabNullValues = array_merge($tabNullValues , array('non','NON','Non','no','No'));
	if ( in_array($str,$tabNullValues) )
		return false ;
		eko($str);
	eko($tabNullValues);
	return true ;
}






//--------------------------------
// Helpers et Templates
//--------------------------------
	public static function helpers_readTemplate($nom,$data = array() )
	{
		$session = formxSession::getInstance();
		$urlLocalTemplates = $session->getLocalUrlTemplates();


		//si template spécifique pour le site:
		if(file_exists($urlLocalTemplates.$nom.'.tpl.php'))
			$file = $urlLocalTemplates.$nom.'.tpl.php';
		else //Templates par defaut
			throw new Exception("Template $nom not found ");
		//instanciation des valeurs à passer à la template
		if(! is_array($data)) $data = array() ;
		foreach($data as $var => $val) $$var = $val ;
		ob_start(); // start buffer
		include ($file);
		$content = ob_get_contents(); // assign buffer contents to variable
		ob_end_clean(); // end buffer and remove buffer contents
		return $content;
	}


/*
 * LE COIN DES ANTIQUITES
 */



static function searchdom($item,$bal)
{
	formxSession::getInstance()->addErreur("searchdom est une fonction obsolete, utiliser domSearch");
	return formxTools::domSearch($item,$bal);
}




static function getinstances($idformx,$values='',$filtre="FX_getInstances",$date1="",$date2="") {
	formxSession::getInstance()->addErreur("getinstances est une fonction obsolette");
	if($date1) $date1 = clFoRmXtOoLs::anydate2datetime($date1);
	if($date2) $date2 = clFoRmXtOoLs::anydate2datetime($date2);
	if(! $filtre) $filtre="FX_getInstances";
	$param = array();
	if($date1 && $date2) {
		$param['cwdate'] = " dt_modif >= '$date1' and  dt_creation <= '$date2' ";
	} elseif ($date1) {
		$param['cwdate'] = " dt_modif >= '$date1' ";
	} elseif($date2) {
		$param['cwdate'] = " dt_modif <= '$date2' ";
	} else{
		$param['cwdate'] = " 1=1 ";
	}
  if($values)
  if(! is_array($values)) $values = array($values);
  if(is_array($idformx)) {
  		$param['listeidformx']="'".implode("','",$idformx)."'";
  } else { 
  		$param['listeidformx']="'".$idformx."'";
  }
  //print affTab ( $param ) ; return ;
  $req = new clResultQuery ;
  if( defined('FX_INSTANCES'))
  	$param['table']=FX_INSTANCES;
  else
  	$param['table']=TABLEFORMX;
  $res = $req -> Execute ( "Fichier", $filtre, $param, "ResultQuery" ) ;
  $nb = $res['INDIC_SVC'][2];
  //print affTab ( $res['INDIC_SVC'] ) ;
  if ( $nb == 0 ) return array();
  $ret = array();
  $ret['ids']=array();
  $ret['id_instance']=array();
  $ret['dt_creation']=array();
  $ret['dt_modif']=array();
  $ret['idformx']=array();
  $ret['libelle']=array();
  $ret['status']=array();
  $ret['author']=array();
  
  for($i=0;$i<$nb;$i++) {
  	$ret['ids'][$i]=$res['ids'][$i];
  	$ret['id_instance'][$i]=$res['id_instance'][$i];
  	$ret['dt_creation'][$i]=$res['dt_creation'][$i];
  	$ret['dt_modif'][$i]=$res['dt_modif'][$i];
  	$ret['idformx'][$i]=$res['idformx'][$i];
  	$ret['libelle'][$i]=$res['libelle'][$i];
  	$ret['status'][$i]=$res['status'][$i];
  	$ret['author'][$i]=$res['author'][$i];
 	$newInstance = new clFoRmX($res['ids'][$i],'NO_POST_THREAT');
  	$newInstance->loadInstance($res['id_instance'][$i]);
  	if(! $values ) { $values = $newInstance->getAllItems();
  		
  	}
	foreach($values as $val) {
		if(! isset($ret[$val])) $ret[$val] = array();
		$ret[$val][$i] = utf8_decode($newInstance->getFormVar($val));
		}
	unset($newInstance);
  	}
  	$ret['INDIC_SVC'][2]=$nb;
  	
	return $ret;
	
	
	
}



























}

