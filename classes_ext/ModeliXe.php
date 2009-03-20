<?php
/*
Licence et conditions d'utilisations-----------------------------------------------------------------------------

-English---------------------------------------------------------------------------------------------------------
Copyright (C) 2001-2004 Thierry ANDRE

                    - ORIGINAL AUTHOR
                            - ANDRE thierry
                    - CURRENT MAINTAINER
                            - TRICHARD Rémy
                    - ADDING
                            - VILDAY Laurent.
                            - MOULRON Diogène.
                            - DELVILLE Romain.
                            - BOUCHERY Frederic.
                            - PERRICHOT Florian.
                            - RODIER Phillipe.
                            - HOUZE Sébastien.
                            - DECLEE Frédéric.
                            - HORDEAUX Sébastien.
                            - LELARGE Guillaume.
                            - GAUTHIER Jérémy.
                            - CASANOVA Matthieu.
                            - KELLER Christophe.
							- MARK HUMPHREYS Aidan.
							- KELLUM Patrick.
							- DE CORNUAUD Sébastien.
							- PIEL Régis.
							- LE LOARER Loïc.
                            - TRICHARD Rémy

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

Copyright (C) 2001-2004 Thierry ANDRE

                    - Auteur original
                            - ANDRE thierry
                    - Mainteneur actuel du projet
                            - TRICHARD Rémy
                    - Ajouts
                            - VILDAY Laurent.
                            - MOULRON Diogène.
                            - DELVILLE Romain.
                            - BOUCHERY Frederic.
                            - PERRICHOT Florian.
                            - RODIER Phillipe.
                            - HOUZE Sébastien.
                            - DECLEE Frédéric.
                            - HORDEAUX Sébastien.
                            - LELARGE Guillaume.
                            - GAUTHIER Jérémy.
                            - CASANOVA Matthieu.
                            - KELLER Christophe.
							- MARK HUMPHREYS Aidan.
							- KELLUM Patrick.
							- DE CORNUAUD Sébastien.
							- PIEL Régis.
							- LE LOARER Loïc.
                            - TRICHARD Rémy

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

Pour tout renseignements mailez à modelixe@free.fr ou tungsten180@hotmail.com
--------------------------------------------------------------------------------------------------------------------
*/

//Initialisation de l'include_path
$os = ((stristr(getenv('SERVER_SOFTWARE'), 'win') || stristr(getenv('SERVER_SOFTWARE'), 'microsoft'))? ';' : ':');
$path = ((defined('MX_GENERAL_PATH'))? MX_GENERAL_PATH : '').$os.((defined('MX_ERROR_PATH'))? MX_ERROR_PATH : '').$os.ini_get('include_path');
ini_set('include_path', $path);

//Inclusion du fichier de configuration et de Error Manager
require_once('Mxconf.php');
require_once('ErrorManager.php');

//Désactivation de magic_quotes_runtime de php.ini
if (get_magic_quotes_runtime()) set_magic_quotes_runtime(0);

class ModeliXe extends ErrorManager{

    public $template = '';
    public $absolutePath = '';
    public $relativePath = '';
    public $sessionParameter = '';
    public $mXParameterFile = '';
    public $mXTemplatePath = '';
    public $mXCachePath = '';
    public $mXUrlKey = '';

    public $outputSystem = '/>';
    public $flagSystem = 'xml';
    public $adressSystem = 'relative';
    public $mXVersion = '1.2';

    public $mXCacheDelay = 0;
    public $debut = 0;
    public $fin = 0;
    public $ExecutionTime = 0;

    public $mXcompress = false;
    public $mXsetting = false;
    public $mXmodRewrite = false;
    public $performanceTracer = false;
    public $mXoutput = false;

    public $mXsignature = true;
    public $isTemplateFile = true;

    public $templateContent = array();
    public $sheetBuilding = array();
    public $deleted = array();
    public $replacement = array();
    public $loop = array();
    public $IsALoop = array();
    public $xPattern = array();
    public $formField = array();
    public $checker = array();
    public $attribut = array();
    public $attributKey = array();
    public $htmlAtt = array();
    public $select = array();
    public $hidden = array();
    public $image = array();
    public $text = array();
    public $father = array();
    public $son = array();
    public $plugInMethods = array();

    public $flagArray = array(0 => 'hidden', 1 => 'select', 2 => 'image', 3 => 'text', 4 => 'checker', 5 => 'formField');
    public $attributArray = array(0 => 'attribut');

    //MX Generator----------------------------------------------------------------------------------------------------

    //Constructeur de ModeliXe
    function ModeliXe ($template, $sessionParameter = '', $templateFileParameter = '', $cacheDelay = -1){

        $this -> ErrorManager();

        $time = explode(' ',microtime());
        $this -> debut = $time[1] + $time[0];

        //Gestion des paramètres par défaut
        //Definition du systeme de compression
        if (defined('MX_COMPRESS')) $this -> SetMxCompress(MX_COMPRESS);

        //Activation du mode rewrite
        if (defined('MX_REWRITEURL')) $this -> SetMxModRewrite(MX_REWRITEURL);

        //Activation de la signature
        if (defined('MX_SIGNATURE')) $this -> SetMxSignature(MX_SIGNATURE);

        //Définition du répertoire de template
	if ( is_file ( "templates_int/".$template ) ) $this -> SetMxTemplatePath("templates_int");
	else $this -> SetMxTemplatePath("templates_gen");
//        if (defined('MX_TEMPLATE_PATH')) $this -> SetMxTemplatePath(MX_TEMPLATE_PATH);

        //Définition du fichier de paramétrage
        if (defined('MX_DEFAULT_PARAMETER') && ! $templateFileParameter)
           $this -> SetMxFileParameter(MX_DEFAULT_PARAMETER);
        elseif ($templateFileParameter != '')
           $this -> SetMxFileParameter($templateFileParameter);

        //Définition du type de balisage
        if (defined('MX_FLAGS_TYPE')) $this -> SetMxFlagsType(MX_FLAGS_TYPE);

        //Définition du type de balisage en sortie
        if (defined('MX_OUTPUT_TYPE')) $this -> SetMxOutputType(MX_OUTPUT_TYPE);

        //Définition du répertoire de cache
        if (defined('MX_CACHE_PATH')) $this -> SetMxCachePath(MX_CACHE_PATH);
        if (defined('MX_CACHE_DELAY')) $this -> SetMxCacheDelay(MX_CACHE_DELAY);
        if ($cacheDelay >= 0 && $cacheDelay != '') $this -> SetMxCacheDelay($cacheDelay);

        //Activation du traceur de performance
        if (defined('MX_PERFORMANCE_TRACER') && MX_PERFORMANCE_TRACER == 'on') $this -> performanceTracer = true;

        //Gestion des paramètres de sessions
        if ($sessionParameter) $this -> sessionParameter = $sessionParameter;

        //Instanciation de la ressource templates
        if (@is_file($this -> mXTemplatePath.$template)) $this -> template = $template;
        elseif (isset($template)) {
            $this -> template = $template;
            $this -> isTemplateFile = false;
            }
        else $this -> ErrorTracker (5, 'No template file defined.', 'ModeliXe', __FILE__, __LINE__);

        //Affectation du path d'origine
        if ($this -> ErrorChecker()) {
            $this -> absolutePath = substr(basename($this -> template), 0, strpos(basename($this -> template), '.'));
            $this -> relativePath = $this -> absolutePath;
            }
        }

    //Setting ModeliXe -------------------------------------------------------------------------------------------

    //Méthode d'instanciation du template
    function SetModelixe($out = ''){
        if ($this -> mXsetting)  $this -> ErrorTracker(4, 'You can\'t re-use this method after instanciate ModeliXe once time.', 'SetModelixe', __FILE__, __LINE__);
        if ($out) $this -> mXoutput = true;
        
        //Test du cache et insertion éventuelle
        if ($this -> mXCacheDelay > 0){
            $this -> mXUrlKey = $this -> GetMD5UrlKey();
            
            if ($this -> MxCheckCache()) $this -> MxGetCache();
            }

        //Initialisation de la classe
        $this -> GetMxFile();
        if ($this -> ErrorChecker()) $this -> MxParsing($this -> templateContent[$this -> absolutePath]);

        $this -> mXsetting = true;
        }

    //Instanciation de la compression
    function SetMxCompress($arg = ''){
        if ($arg != 'on') $this -> mXcompress = false;
        else $this -> mXcompress = true;

        return $this -> mXcompress;
        }

    //Instanciation du mode rewrite
    function SetMxModRewrite($arg = ''){
        if ($arg != 'on') $this -> mXmodRewrite = false;
        else $this -> mXmodRewrite = true;

        return $this -> mXmodRewrite;
        }

    //Instanciation de la signature
    function SetMxSignature($arg = ''){
        if ($arg != 'on') $this -> mXsignature = false;
        else $this -> mXsignature = true;

        return $this -> mXsignature;
        }

    //Instanciation du template path
    function SetMxTemplatePath($arg = ''){
    	global $relocate;
        if ($this -> mXsetting) $this -> ErrorTracker(1, 'You can\'t use this method after instanciate ModeliXe with setModeliXe method, it will be without effects.', 'SetMxTemplatePath', __FILE__, __LINE__);
        else {
            if ($arg[strlen($arg) - 1] != '/' && $arg) $arg .= '/';
            if($relocate && ! is_dir($arg))
            	$arg=$relocate.$arg;
            if (! is_dir($arg)) $this -> ErrorTracker(5, 'The MX_TEMPLATE_PATH (<b>'.$arg.'</b>) is not a directory.', 'SetMxTemplatePath', __FILE__, __LINE__);
            else $this -> mXTemplatePath = $arg;
            }

        return $this -> mXTemplatePath;
        }

    //Instanciation du fichier de paramètre
    function SetMxFileParameter($arg = ''){
        if ($arg != '' && ! is_file($arg)) $this -> ErrorTracker(1, 'The parameter\'s file path (<b>'.$arg.'</b>) does not exist.', 'SetMxFileParameter', __FILE__, __LINE__);
        else $this -> mXParameterFile = $arg;

        return $this -> mXParameterFile;
        }

    //Instanciation du balisage du template
    function SetMxFlagsType($arg){
        if ($this -> mXsetting) $this -> ErrorTracker(1, 'You can\'t use this method after instanciate ModeliXe with setModeliXe method, it will be without effects.', 'SetMxFlagsType', __FILE__, __LINE__);
        else {
            switch (strtolower($arg)){
                case 'classical':
                    $this -> flagSystem = 'classical';
                    break;
                case 'pear':
                    $this -> flagSystem = 'classical';
                    break;
                case 'xml':
                    $this -> flagSystem = 'xml';
                    break;
                default:
                    $this -> ErrorTracker(2, 'This type of flag system ('.$arg.') is unrecognized.', 'SetMxFlagsType', __FILE__, __LINE__);
                }
           }

        return $this -> flagSystem;
        }

    //Instanciation du balisage de sortie
    function SetMxOutputType($arg){
        if ($this -> mXsetting) $this -> ErrorTracker(1, 'You can\'t use this method after instanciate ModeliXe with setModeliXe method, it will be without effects.', 'SetMxOutputType', __FILE__, __LINE__);
        else {
            switch (strtolower($arg)){
                case 'xhtml':
                    $this -> outputSystem = '/>';
                    break;
                case 'html':
                    $this -> outputSystem = '>';
                    break;
                default:
                    $this -> ErrorTracker(2, 'This type of output flag system ('.$arg.') is unrecognized.', 'SetMxOutputType', __FILE__, __LINE__);
                }
            }
        return $arg;
        }

    //Instanciation du répertoire de cache
    function SetMxCachePath($arg){
    	global $relocate;
        if ($this -> mXsetting) $this -> ErrorTracker(1, 'You can\'t use this method after instanciate ModeliXe with setModeliXe method, it will be without effects.', 'SetMxCachePath', __FILE__, __LINE__);
        else {
            if ($arg[strlen($arg) - 1] != '/') $arg .= '/';
            if($relocate && ! is_dir($arg))
            	$arg=$relocate.$arg;
            if (! is_dir($arg) && $arg != '') $this -> ErrorTracker(5, 'The MxCachePath (<b>'.$arg.'</b>) is not a directory.', 'SetMxCachePath', __FILE__, __LINE__);
            elseif ($arg) $this -> mXCachePath = $arg;
            }

        return $this -> mXCachePath;
        }

    //Instanciation du délai de cache
    function SetMxCacheDelay($arg){
        if ($this -> mXsetting) $this -> ErrorTracker(1, 'You can\'t use this method after instanciate ModeliXe with setModeliXe method, it will be without effects.', 'SetMxCachePath', __FILE__, __LINE__);
        elseif ($arg >= 0) $this -> mXCacheDelay = (integer)$arg;

        return $this -> mXCacheDelay;
        }

    //Instanciation des paramètres de session
    function SetMxSession($arg){
        $this -> sessionParameter = $arg;
        }

    //Setting tools -----------------------------------------------------------------------------------------------

    //Recherche du fichier de template
    function GetMxFile($source = ''){
        if (! $source) {
	  /*
	  if ( is_file ( "template_int/".$this -> template ) ) $this -> mXTemplatePath = "templates_int/" ;
	  else $this -> mXTemplatePath = "templates_ext/" ;
	  */
	  $source = $this -> mXTemplatePath.$this -> template;
	}

        if (! $read = @fopen($source, 'rb')) $this -> ErrorTracker (3, 'Can\'t open this template file (<b>'.$source.'</b>) in read, see for change the read modalities.', 'GetMxFile', __FILE__, __LINE__);
        else {

            if (! $result = @fread($read, filesize($source)))  $this -> ErrorTracker (3, 'Can\'t read the template file (<b>'.$source.'</b>), see for file format and integrity.', 'GetMxFile', __FILE__, __LINE__);
            fclose($read);
            }

        /* Correction d'un bug : le bloc inclu était interprété comme un
	commentaire HTML par certains navigateurs (testé avec Firefox 0.9) */
	if (empty($result)) $result = '[no parsing, template file not found or invalid]';
        if ($this -> mXsignature && $source != $this -> mXTemplatePath.$this -> template) $result = "\n<!-- ModeliXe ".$this -> mXVersion.' [StartOf'.$dyn.'Inclusion : '.$source."] -->\n\n".$result."\n\n<!-- ModeliXe ".$this -> mXVersion.' [EndOf'.$dyn.'Inclusion : '.$source."] -->\n";

        //Affectation du path d'origine, et du content du template
        if ($source == $this -> mXTemplatePath.$this -> template) $this -> templateContent[$this -> absolutePath] = $result;
		else return $result;
        }

    //Lecture du fichier de configuration et parsing
    function GetParameterParsing ($template){
        $ligne = '';
        $signal = '';

        if (! $read = @fopen($this -> mXParameterFile, 'r')) $this -> ErrorTracker(4, 'The mXParameterFile (<b>'.$this -> mXParameterFile.'</b>) can\'t be open, the first parsing can\'t be do.', 'GetParameterParsing', __FILE__, __LINE__);
        for ($multi = false; !feof($read) && $this -> ErrorChecker(); $ligne = trim(@fgets($read, 1200))){
            if (strlen($ligne)) {
                if ($ligne[0] == '#' && $ligne[1] != '#'){

                    //Changement d'état pour les paramètres
                    switch(strtolower($ligne)){
                        case '#flag'     :
                               $signal = 'flag';
                               break;
                        case '#attribut' :
                               $signal = 'attribut';
                               break;
                        default :
                               $this -> ErrorTracker(3, '<b>'.$ligne.' </b> is not a valid section parameter type', 'GetParameterParsing', __FILE__, __LINE__);
                               break;
                           }
                    }
                else {
                    if($ligne[0] == '#') $ligne = substr($ligne,1);
                    if (! $multi){
                        $keyC = chop(substr($ligne, 0, strpos($ligne, '=') - 1));

                        //Gestion du multiligne, début d'une valeur sur plusieurs lignes
                        if (($content = ltrim(substr($ligne, strpos($ligne, '=') + 1))) && substr($content, 0, 3) == '"""') {
                            $multi = true;
                            $content = substr($content, 3);
                            }
                        }

                    //Gestion du multiligne, fin d'une valeur sur plusieurs lignes
                    else {
                        if (substr($ligne, strlen($ligne) - 3) == '"""') {
                            $multi = false;
                            $content .= ' '.substr($ligne, 0, strpos($ligne, '"""'));
                            }
                        else $content .= ' '.$ligne;
                        }

                    //Si nous ne sommes pas dans une valeur sur plusieurs lignes (valeur compléte)
                    if (! $multi){
                        switch ($this -> flagSystem){
                                case 'xml':
                                    $flagRegexp = '<mx:preformating id="'.$keyC.'"/>';
                                    $attRegexp = 'mXpreformating="'.$keyC.'"';
                                    break;
                                case 'classical':
                                    $flagRegexp = '{preformating id="'.$keyC.'"}';
                                    $attRegexp = '{preformatingAtt id="'.$keyC.'"}';
                                    break;
                            }

                        if ($signal == 'flag') $template = str_replace($flagRegexp, $content, $template);
                        if ($signal == 'attribut') $template = str_replace($attRegexp, $content, $template);
                        }
                       }
                   }
            }
        if ($read) @fclose($read);

        return $template;
        }

    //MX Builder-----------------------------------------------------------------------------------------
    function MxBloc($index, $mod, $value = ''){
        $mod = substr(strtolower($mod), 0, 4);

        if ($this -> adressSystem == 'relative') {
            if ($index) $index = $this -> relativePath.'.'.$index;
            else $index = $this -> relativePath;
            }
        else $index = $this -> absolutePath.'.'.$index;

        $fat = $this -> father[$index];
        if (! $fat && $index != $this -> absolutePath) $this -> ErrorTracker(2, 'The current path (<b>'.$index.'</b>) does not exist, or was deleting, him or his father, before.', 'MxBloc', __FILE__, __LINE__);

        switch ($mod){
            //Looping
            case 'loop':
                $this -> MxLoopBuilder($index);
                break;
            //Deleting
            case 'dele':
                $this -> sheetBuilding[$index] = '   ';
                $this -> loop[$index] = '';
                $this -> deleted[$index] = true;
                break;
            //Concatenating
            case 'appe':
                if (@is_file($value)) $value = $this -> GetMxFile($value);
                $this -> templateContent[$index] .= $value;
                $this -> MxParsing($value, $index, $this -> father[$index]);
                break;
            //Replacing
            case 'repl':
                if (@is_file($value)) $value = $this -> GetMxFile($value);
                $this -> sheetBuilding[$index] = $value;
                $this -> replacement[$index] = true;
                break;
            //Modify template references of this bloc
            case 'modi':
                $this -> sheetBuilding[$index] = '';
                $this -> loop[$index] = '';
                if (@is_file($value)) $value = $this -> GetMxFile($value);
                $this -> templateContent[$index] = $value;
                $this -> MxParsing($value, $index, $this -> father[$index]);
                break;
            //Reset, destroy all references
            case 'rese':
                $this -> sheetBuilding[$index] = '';
                $this -> loop[$index] = '';
                $this -> templateContent[$index] = '';
                $ind = substr($index, strrpos($index, '.') + 1);
                $this -> templateContent[$fat] = str_replace('<mx:inclusion id="'.$ind.'"/>', '', $this -> templateContent[$fat]);
                $this -> deleted[$index] = true;
                $this -> xPattern['inclusion'][$index] = '';
                break;
            }
        }

    function MxFormField($index, $type, $name, $value, $attribut = ''){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;
        $end = $this -> outputSystem;

        switch (strtolower($type)){
            case 'text':
                $replace = '<input type="text" name="'.$name.'" value="'.$value.'" '.$attribut.' '.$this -> htmlAtt[$index]."/".$end;
                break;
            case 'password':
                $replace = '<input type="password" name="'.$name.'" value="'.$value.'" '.$attribut.' '.$this -> htmlAtt[$index]."/".$end;
                break;
            case 'textarea':
                $replace = '<textarea name="'.$name.'" '.$attribut.' '.$this -> htmlAtt[$index].' >'.$value.'</textarea>';
                break;
            case 'file':
                $replace = '<input type="file" name="'.$name.'" value="'.$value.'" '.$attribut.' '.$this -> htmlAtt[$index].$end;
                break;
            case 'submit':
                $replace = '<input type="submit" name="'.$name.'" value="'.$value.'" '.$attribut.' '.$this -> htmlAtt[$index]."/".$end;
                break;
            case 'reset':
                $replace = '<input type="reset" name="'.$name.'" value="'.$value.'" '.$attribut.' '.$this -> htmlAtt[$index].$end;
                break;
            case 'button':
                $replace = '<input type="button" name="'.$name.'" value="'.$value.'" '.$attribut.' '.$this -> htmlAtt[$index].$end;
                break;
            case 'image':
	      $replace = '<input type="image" name="'.$name.'" '.$attribut.' '.$this -> htmlAtt[$index]."/".$end;
                break;
            default:
                $this -> ErrorTracker(3, 'This type (<b>'.$type.'</b>) is unknown for this formField manager.', 'MxFormField', __FILE__, __LINE__);
            }

        $this -> formField[$index] = $replace;
        }

    function MxImage($index, $imag, $title = '', $attribut = '', $size = false){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;
        $end = $this -> outputSystem;

        if (($ima = '<img src="'.$imag.'"') && ! $size) {
	    if ( file_exists ( $imag ) )
              $size = @getimagesize($imag);
            $ima .= ' '.$size[3];
            }

        if ($title == 'no') $ima .= ' ';
        elseif ($title) $ima .= ' alt="'.$title.'" ';
        else $ima .= ' alt="no title - source : '.basename($imag).'" ';

        if ($attribut) $ima .= $attribut;
	if ( isset ( $this -> htmlAtt[$index] ) )
	  $ima .= ' '.$this -> htmlAtt[$index].$end;
	else  $ima .= ' '.$end;
	  

        $this -> image[$index] = $ima;
        }

    function MxText($index, $att){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        $this -> text[$index] = $att;
        }

    function MxAttribut($index, $att){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;
        $marqueur = '';
        
        //Gestion des mailto et des javascripts dans les href
        if (strtolower($this -> attributKey[$index]) == 'mailto' || strtolower($this -> attributKey[$index]) == 'javascript') $marqueur = ' href="';

        //Gestion multi-attributs
        if (! ((isset($this -> attribut[$index]))? chop($this -> attribut[$index]): false) ) {
            if ($marqueur) $this -> attribut[$index] = $marqueur.$this -> attributKey[$index].':'.$att.'"';
            else $this -> attribut[$index] = $this -> attributKey[$index].'="'.$att.'"';
            }
        else {
            if (empty($this -> attribut[$this -> attribut[$index]])) {
                if ($marqueur) $this -> attribut[$this -> attribut[$index]] = ' '.$marqueur.$this -> attributKey[$index].':'.$att.'"';
                else $this -> attribut[$this -> attribut[$index]] = ' '.$this -> attributKey[$index].'="'.$att.'"';
                }
            else {
                if ($marqueur) $this -> attribut[$this -> attribut[$index]] .= $marqueur.$this -> attributKey[$index].':'.$att.'"';
                else $this -> attribut[$this -> attribut[$index]] .= ' '.$this -> attributKey[$index].'="'.$att.'"';
                }
            }
        }

    function MxSelect($index, $name, $value, $arrayArg, $defaut = '', $multiple = '', $javascript = '') {
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;
        $sel = '';

        if ($multiple && $multiple > 0) {
            $attribut = 'size="'.$multiple.'" multiple="multiple" ';
            $post = '[]';
            }
        else {
            $attribut = '';
            $post = '';
            }

        //Build of a select tag from an array
        if (is_array($arrayArg)){
            $sel = "\n".'<select name="'.$name.$post.'" ';
            if ($attribut) $sel .= $attribut.' ';
            if ($javascript) $sel .= $javascript;
            $sel .= ' '.$this -> htmlAtt[$index].' '.">\n";

            if (isset($defaut) && $defaut) $sel .= "\t".'<option value="#">'.$defaut.'</option>'."\n";

            $debut = 0;
            $fin = count($arrayArg);

            reset($arrayArg);
            while (list($cle, $Avalue) = each($arrayArg)){
                $test = 0;

                //Build of multiple choice select from a value array
                if (is_array($value) && $multiple > 0){
                    reset($value);
                    while (list($Vcle, $Vvalue) = each($value)){

                        /* correction d'un bug
			les variables devaient être comparées en tant que string */
			if ((string)$cle === (string)$Vvalue && $Vvalue != '') {
                            $sel .= "\t".'<option value="'.$cle.'" selected="selected">'.$Avalue.'</option>'."\n";
                            $test = 1;
                            break;
                            }
                        }
                    if ($test == 0) $sel .= "\t".'<option value="'.$cle.'">'.$Avalue.'</option>'."\n";
                    }

                //Simple select
                else {
                    /* correction d'un bug
		    les variables devaient être comparées en tant que string */
		    if ($value != '' && (string)$cle === (string)$value) $sel .= "\t".'<option value="'.$cle.'" selected="selected">'.$Avalue.'</option>'."\n";
                    else $sel .= "\t".'<option value="'.$cle.'">'.$Avalue.'</option>'."\n";
                    }
                }
            }
        else {
            $this -> ErrorTracker(2, 'This function need an Array in fourth argument to build the select <b>'.$index.'</b>.', 'MxSelect', __FILE__, __LINE__);
            $sel = '<select name="'.$name.'">'."\n\t".'<option value="null">No record found</option>'."\n";
            }

        $sel .= '</select>';

        $this -> select[$index] = $sel;
        }

    function MxUrl($index, $urlArg, $param = '', $noSid = false, $attribut = '') {
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        $ok = false;
        
        //Ajout des paramètres de sessions en cas de cache ou non
        if ($this -> sessionParameter && ! $noSid) {
            if ($this -> mXCacheDelay > 0) $urlArg .= '?<mx:session />';
            else $urlArg .= '?'.$this -> sessionParameter;

            $ok = true;
            }
        
        //Construction du lien
        if (is_string($param) && $param) {
            $param = explode('&',$param);
            for($i = 0; $i < count($param) && $param[$i]; $i++){
                $cle = explode('=', $param[$i]);
                if (! $this -> mXmodRewrite) $urlArg .= ($i == 0 && !$ok) ? '?'.urlencode($cle[0]).'='.urlencode($cle[1]) : '&'.urlencode($cle[0]).'='.urlencode($cle[1]);
                else $urlArg .= '/'.urlencode($cle[0]).'/'.urlencode($cle[1]);
                }
            }
        elseif (is_array($param)){
            reset($param);
            if ($this->mXmodRewrite) {
                while (list($cle, $valeur) = each($param)) {
                    $urlArg .= '/'.urlencode($cle).'/'.urlencode($valeur);
                    }
                }
            else {
                while (list($cle, $valeur) = each($param)) {
                     if (!$ok) {
                        $urlArg .= '?'.urlencode($cle).'='.urlencode($valeur);
                        $ok = true;
                        }
                    else $urlArg .= '&'.urlencode($cle).'='.urlencode($valeur);
                    }
                }
            }
        elseif ($param) $this -> ErrorTracker(3, 'The third argument must be a queryString or an array.', 'MxUrl', __FILE__, __LINE__);
        
	/* correction d'un bug mineur de rendu : espace placé avant l'attribut href
	concerne les 2 blocs ci-dessous */
        //Ajout d'éventuels attributs supplémentaires en dynamique
        $lien = ($attribut)? 'href="'.$urlArg.'" '.$attribut : 'href="'.$urlArg.'"';
        
        //Gestion multi-attributs
        if (! ((isset($this -> attribut[$index]))? chop($this -> attribut[$index]): false)) $this -> attribut[$index] = 'href="'.$urlArg.'"';
        else {
            if (empty($this -> attribut[$this -> attribut[$index]])) $this -> attribut[$this -> attribut[$index]] = 'href="'.$urlArg.'"';
            else $this -> attribut[$this -> attribut[$index]] .= 'href="'.$urlArg.'"';
            }
        }

    function MxHidden ($index, $param){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;
        $end = $this -> outputSystem;

        $hidden = '';

        if ($this -> mXCacheDelay == 0 && $this -> sessionParameter) $param .= '&'.$this -> sessionParameter;

        if (is_string($param)) $param = explode('&',$param);
        else $this -> ErrorTracker(3,'The second argument must be a queryString.',  'MxHidden', __FILE__, __LINE__);

        if (! empty($param)){
            for($i = 0; $i < count($param); $i++){
                if ($param[$i]) {
                    $cle = explode('=', $param[$i]);
		    /* correction de 2 bugs dans la balise hidden */
		    /*$hidden .= '<input type="hidden" name="'.$cle[0].'" value="'.$cle[1].'" '.$end."\n";*/
                    $hidden .= '<input type="hidden" name="'.htmlspecialchars(urldecode($cle[0])).'" value="'.htmlspecialchars(urldecode($cle[1])).'" '.$this -> htmlAtt[$index]."/".$end."\n";
                    }
                }
            }

        if ($this -> mXCacheDelay > 0) $hidden .= '<mx:hiddenSession />';

        $this -> hidden[$index] = $hidden;
        }

    function MxCheckerField($index, $type, $name, $value, $checked = false, $attribut = ''){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;
        $end = $this -> outputSystem;

        $type = strtolower($type);
        if ($type != "checkbox" && $type != "radio") $this -> ErrorTracker(2, 'This type (<b>'.$type.'</b>) is unknown for this CheckerField manager.', 'MxCheckerField', __FILE__, __LINE__);

        $replace = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'"';
        if ( $checked ) { $replace .= ' checked="checked"';
        //	print "oui";	
        } else {
        	
        	//print "non";	
        }
        
        
        if ($attribut) $replace .= ' '.$attribut;
        $replace .= ' '.$this -> htmlAtt[$index].'/'.$end;


        $this -> checker[$index] = $replace;
        }

    //MX Extender----------------------------------------------------------------------------------------
    function AddMxPlugIn($name, $type, $fonction){

        if (! $name) $this -> ErrorTracker(2, 'You must give a name to identify the plug-in.', 'AddMxPlugIn',__FILE__, __LINE__);
        if (! $type) $this -> ErrorTracker(2, 'The type of the plug-in is necessary to instanciate it.', 'AddMxPlugIn', __FILE__, __LINE__);
        if (! $fonction || ! function_exists($fonction)) $this -> ErrorManager(3, 'The method addMxPlugin need the name of a function in third argument.', 'AddMxPlugIn',__FILE__, __LINE__);

        if ($this -> ErrorChecker()){
            switch ($type){
                case 'flag':
                    for ($i = 0; $i < count($this -> flagArray); $i++){
                        if ($name == $this -> flagArray[$i]) $this -> ErrorTracker(2, 'This plug-in (<b>'.$name.'</b>) has got the same pattern as the native pattern of a ModeliXe flag.', 'AddMxPlugIn', __FILE__, __LINE__);
                        }
                    $this -> flagArray[count($this -> flagArray)] = $name;
                    break;
                case 'attribut':
                    for ($i = 0; $i < count($this -> attributArray); $i++){
                        if ($name == $this -> attributArray[$i]) $this -> ErrorTracker(2, 'This plug-in (<b>'.$name.'</b>) has got the same pattern as the native pattern of a ModeliXe attribut.', 'AddMxPlugIn', __FILE__, __LINE__);
                        }
                    $this -> attributArray[count($this -> attributArray)] = $name;
                    break;
                default :
                    $this -> ErrorTracker(2, 'This type of plug-in (<b>'.$type.'</b>) is unrecognized.', 'AddMxPlugIn', __FILE__, __LINE__);
                }
            }

        $this -> $name = array();
        $this -> plugInMethods[$name] = $fonction;
        }

    function SetMxPlugIn($name, $index, $arguments){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        if (isset($arguments) && ! is_array($arguments)) $this -> ErrorTracker(2, 'You must give an array of arguments for the plug-in function.', 'SetMxPlugIn', __FILE__, __LINE__);
        else {
            $tab = &$this -> $name;
            $tab[$index] = call_user_func($this -> plugInMethods[$name], $arguments);
            }
        }

    //MX tools -------------------------------------------------------------------------------------------------------------------

    //Vérifie l'existence d'un bloc
    function IsMxBloc($index){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        $fat = $this -> father[$index];
        if (! $fat && $index != $this -> absolutePath) return false;
        else return true;
        }

    //Vérifie l'existence d'une balise
    function IsMxFlag($index, $type){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        $tab = $this -> $type;
        if (! isset($tab[$index])) return false;
        else return $tab[$index];
        }

    //Vérifie l'existence d'un attribut
    function IsMxAttribut($index){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        if (! isset($this -> attributKey[$index])) return false;
        else {
            if (preg_match('/;/', $this -> attribut[$index])) return $this -> attribut[$this -> attribut[$index]];
            else return $this -> attribut[$index];
            }
        }

    //Retourne tout le contenu d'un bloc
    function GetMxBloc($index){
        if ($this -> adressSystem == 'relative') $index = $this -> relativePath.'.'.$index;

        if ($this -> sheetBuilding[$index]) return $this -> sheetBuilding[$index];
        else  return $this -> templateContent[$index];
        }

    //Construction d'une queryString
    function GetQueryString($keyString, $null = 1){
        $queryString = array();

        if (is_array($keyString)){
            reset($keyString);

            while (list($Akey, $value) = each($keyString)){
                if (is_array($value)) {
                    while (list($k, $v) = each($value)) {
                        array_push($queryString, urlencode($Akey.'['.$k.']').'='.urlencode($v));
                        }
                    }
                elseif ($null || strlen($value)) array_push($queryString, urlencode($Akey).'='.urlencode($value));
                }
            return implode('&',$queryString);
            }
        else $this -> ErrorTracker(3, 'The argument for this function must be an associative array.', 'GetQueryString', __FILE__, __LINE__);
        }

    //Adressage simplifié
    function WithMxPath($path = '', $origine = ''){

        if (! $origine) $origine = $this -> adressSystem;
        else {
            switch($origine){
                case 'relative':
                    break;
                case 'absolute':
                    break;
                default:
                    $origine = 'relative';
                    break;
                }
            }

        //Si on ne précise pas de path on retourne au path origine
        if (empty($path)){
            $this -> relativePath = $this -> absolutePath;

            if ($origine == 'absolute') $this -> adressSystem = 'absolute';
            elseif ($origine == 'relative') $this -> adressSystem = 'relative';
            }

        //Sinon, en absolu on se situe dans ce path, en relatif on se situe par rapport au path relatif
        if ($path) {
            if ($origine == 'relative') {

                //On redescend dans la hiérarchie jusqu'au path mentionné
                if (($test = explode('../', $path)) && count($test) > 1) {
                    $path = substr($path, strrpos($path, '/') + 1);
                    $this -> relativePath = substr($this -> relativePath, 0, strlen($this -> relativePath) - strlen(strstr($this -> relativePath, $path)) - 1);
                    if (! $this -> relativePath) $this -> ErrorTracker(3, 'This path (<b>'.$path.'</b>) does not exist, ModeliXe can\'t build relativePath.', 'WithMxPath', __FILE__, __LINE__);
                    }

                $this -> relativePath .= '.'.$path;

                $this -> adressSystem = 'relative';
                }
            elseif ($origine == 'absolute') {
                $this -> relativePath = $path;
                $this -> adressSystem = 'absolute';
                }
            }
        }

    //Informations de licence
    function AboutModeliXe($out = ''){
        $texte = "\nLicence et conditions d'utilisations :\n";
        $texte .= 'ModeliXe '.$this -> mXVersion."\nModeliXe est distribué sous licence LGPL, merci de laisser cet en-tête, gage et garantie de cette licence.\n";
        $texte .= "ModeliXe est un moteur de template destiné à être utilisé par des applications écrites en PHP.\n";
        $texte .= " \n";
        $texte .= "Copyright(c) 2001-2004 - ANDRE Thierry (aka Théo)\n";
        $texte .= " \n";
	$texte .= "Mainteneur actuel : TRICHARD Rémy (alias Tungsten)\n";
        $texte .= "Pour tout renseignements mailez à modelixe@free.fr ou tungsten180@hotmail.com\n";

        if ($out) return $texte;
        else print('<pre>'.$texte.'</pre>');
        }

    //Numéro de version
    function GetMxVersion(){
        return $this -> mXVersion;
        }

    //Rafraichissement
    function MxRefresh($query = ''){
        $this -> MxClearCache('this', $query);
        }

    function MxRefreshAll() {
        $this -> MxClearCache();
        }
        
    //Mesure de performances
    function GetExecutionTime(){
            $time = explode(' ',microtime());
            $fin = $time[1] + $time[0];
            $this -> ExecutionTime = intval(10000 * ((double)$fin - (double)$this -> debut)) / 10000;

            return($this -> ExecutionTime);
            }
	    
    //MX Parsing Engine------------------------------------------------------------------------------------------------------------

    function MxParsing($doc = '', $path = '', $father = ''){
        $countPath = Array();

        //Initialisation
        if (! $path) {
            $original = true;
            $path = $this -> absolutePath;
            }
        else $original = false;

        $this -> father[$path] = $father;
        $this -> IsALoop[$path] = false;

        //Parsing des balises de bloc, extraction des sous blocs
        $ok = true;

        switch ($this -> flagSystem){
                case 'xml':
                    $blocRegexp = '/<mx:bloc(?:[ ]+ref="([^"]+)")?[ ]+id="([^"]+)"[ ]*>/S';
                    break;
                case 'classical':
                    $blocRegexp = '/{start(?:[ ]+ref="([^"]+)")?[ ]+id="([^"]+)"[ ]*}/S';
                    break;
                }

        if (preg_match_all($blocRegexp, $doc, $inclusion)){

            for($i = 0; $ok; $i++){

                //Extraction des différentes informations extraites par la regex
                $id = $inclusion[2][0];
                $ref = $inclusion[1][0];
                $pattern = $inclusion[0][0];

                //Calcul des limites du bloc traité
                switch ($this -> flagSystem){
                    case 'xml':
                        $regexp = '</mx:bloc id="'.$id.'">';
                        break;
                    case 'classical':
                        $regexp = '{end id="'.$id.'"}';
                        break;
                    }

                $startOfIntrons = strpos($doc, $pattern) + strlen($pattern);
                $endOfIntrons = strpos($doc, $regexp);
                $length = $endOfIntrons - $startOfIntrons;

                if (! $endOfIntrons) $this -> ErrorTracker(4, 'The end of the "<b>'.$id.'</b>" bloc is not found, this bloc can\'t be generate. Verify that the end of bloc\'s flag exists and has a good form, like this pattern <b>'.htmlentities($regexp).'</b>.', 'MxParsing', __FILE__, __LINE__);

                //On teste si le bloc en cours posséde une référence vers un autre template
                if (! $ref) $this -> templateContent[$path.'.'.$id] = substr($doc, $startOfIntrons, $length);
                else {
                    if ($this -> mXTemplatePath) $ref = $this -> mXTemplatePath.$ref;
                    $this -> templateContent[$path.'.'.$id] = $this -> GetMxFile($ref);
                    }

                //Création du pattern du bloc traité
                $this -> xPattern['inclusion'][$path.'.'.$id] = '<mx:inclusion id="'.$id.'"/>';
                $this -> deleted[$path.'.'.$id] = false;
                $this -> replacement[$path.'.'.$id] = false;

                //Extraction du contenu du bloc pour reconstruire le bloc en cours
                $doc = substr($doc, 0, $startOfIntrons - strlen($pattern)).'<mx:inclusion id="'.$id.'"/>'.substr($doc, $endOfIntrons + strlen($regexp));
                $this -> templateContent[$path] = $doc;

                //Construction de la référence à ce bloc pour la récursivité
                $countPath[$i] = $path.'.'.$id;

                //Incrémentation du nbre de fils pour le bloc en cours
                if (! empty($this -> son[$path][0])) $compt = $this -> son[$path][0];
                else {
                    $compt = 0;
                    $this -> son[$path][0] = 0;
                    }

                //Construction de la référence au fils du bloc parsé pour le bloc en cours
                $this -> son[$path][++ $compt] = $path.'.'.$id;
                $this -> son[$path][0] ++;

                //Test de fin de boucle
                $ok = preg_match_all($blocRegexp, $doc, $inclusion);
                }
            }

        //Parsing des balises ModeliXe
        reset($this -> flagArray);
        while (list($Akey, $value) = each($this -> flagArray)){

            switch ($this -> flagSystem){
                case 'xml':
                    $regexp = '/<mx:'.$value.'(?:[ ]+(?:ref|info)="(?:[^"]+)")?[ ]+id="([^"]+)"(([^>])*(?=\/>))\/>/S';
                    break;
                case 'classical':
                    $regexp = '/{'.$value.'(?:[ ]+(?:ref|info)="(?:[^"]+)")?[ ]+id="([^"]+)"[ ]*(?i:htmlAtt\[([^\]]*)\])?}/S';
                    break;
                }

            if (preg_match_all($regexp, $doc, $flag)){
                 for ($i = 0; ; $i++){
                    if (empty ($flag[0][$i])) break;

                    //Construction du pattern et des valeurs par défaut de ces balises
                    $this -> xPattern[$value][$path.'.'.$flag[1][$i]] = $flag[0][$i];

                    //Modification Guillaume Lelarge compatibilité PHP3
/*<PHP3>
                    $ref = $this -> $value;
                    $ref[$path.'.'.$flag[1][$i]] = '   ';
                    $this -> $value = $ref;
                    $this -> htmlAtt[$path.'.'.$flag[1][$i]] = $flag[2][$i];
</PHP3>*/

                    $ref = &$this -> $value;
                    $ref[$path.'.'.$flag[1][$i]] = '   ';
                    $this -> htmlAtt[$path.'.'.$flag[1][$i]] = $flag[2][$i];
                    }
                }
            }

        //Parsing des attributs de ModeliXe
        switch ($this -> flagSystem){
                case 'xml':
                    $regexp = '/mXattribut="([^"]{3,})"/Si';
                    $separateur = ':';
                    break;
                case 'classical':
                    $regexp = '/{attribut ([^\}]+)}/Si';
                    $separateur = '=';
                    break;
                }

        if (preg_match_all($regexp, $doc, $flag)){
            for ($i = 0, $k = 0; ; $i++){

                if (empty($flag[0][$i])) break;

                $pattern = $flag[0][$i];
                $motif = $flag[1][$i];
                $k = 0;

                //Gestion de plusieurs couples de clé-valeurs dans les attributs
                $tabVal = explode(';', $motif);
                for ($j = 0; $j < count($tabVal); $j++) {

                    $tabCle = explode($separateur, trim($tabVal[$j]));
                    $patternKey[++ $k] = trim($tabCle[0]);
                    $indexValue[$k] = trim($tabCle[1]);

                    //Gestion multi-attributs
                    if (count($tabVal) > 1) {
                        $this -> attribut[$path.'.'.$indexValue[$k]] = $path.'.'.$indexValue[1].';';
                        if ($k == 1) $this -> xPattern['attribut'][$path.'.'.$indexValue[1].';'] = $pattern;
                        }
                    else {
                        $this -> attribut[$path.'.'.$indexValue[$k]] = '  ';
                        $this -> xPattern['attribut'][$path.'.'.$indexValue[$k]] = $pattern;
                        }

                    if ($patternKey[$k] != 'url') $this -> attributKey[$path.'.'.$indexValue[$k]] = $patternKey[$k];
                    }
                }
            }


        for ($i = 0; $i < count($countPath); $i++) $this -> MxParsing($this -> templateContent[$countPath[$i]], $countPath[$i], $path);
        }

    //MX Compression System ------------------------------------------------------------------------------------------------------------------

    //Vérifie si la compression est possible et son type
    function MxCheckCompress($file){
        if((! $this -> mXcompress) || (! extension_loaded("zlib")) || (headers_sent()) || (strlen($file) / 1000 < 8)) return false;
        global $HTTP_SERVER_VARS;

        if (strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'x-gzip'))  return "x-gzip";
        if (strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip'))  return "gzip";

        return false;
        }

    //Compression des donnees destinées au navigateur
    function MxSetCompress($filecontent){
        if ($encoding = $this -> MxCheckCompress($filecontent)){

            header('Content-Encoding: '.$encoding);
            $gzfilecontent =  "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $size = strlen($filecontent);
            $crc32 = crc32($filecontent);
            $gzfilecontent .= gzcompress($filecontent, 9);
            $gzfilecontent = substr($gzfilecontent, 0, strlen($gzfilecontent) - 4);
            $gzfilecontent .= pack('V',$crc32);
            $gzfilecontent .= pack('V',$size);

            return $gzfilecontent;
            }
        else return $filecontent;
        }


    //MX Cache system ------------------------------------------------------------------------------------------------------------------

    //Retourne une clé unique pour les arguments en POST et GET différents des paramètres de session
    function GetMD5UrlKey($query = ''){
        global $HTTP_POST_VARS, $HTTP_GET_VARS;

        if (! $query) $get = $HTTP_GET_VARS;
        else $get = $query;
        if (! $query) $post = $HTTP_POST_VARS;
        else $post = $query;
        
        //Intégration du nom du fichier php appelant
        $uri = getenv('REQUEST_URI');
        $uri = substr($uri, 0, strpos($uri, '?'));
        
        //Suppression des paramètres de session en GET
        $chaine = '';
        $this -> DeleteSessionKey($chaine, $get);

        //Suppression des paramètres de session en POST
        $this -> DeleteSessionKey($chaine, $post);

        return (md5($chaine.$uri));
        }

    function DeleteSessionKey(&$chaine, $httpvar){
        $param = explode('&', $this -> sessionParameter);

        //Tri des tableaux pour les avoir dans le même ordre
        asort($httpvar);
        asort($param);

        //pr est un marqueur pour éviter de parcourir toutes les valeurs des param de session si ceux-ci ont déja été tous supprimés
        $pr = 0;

        //suppression des paramètres de session(get ou post)
        for (reset($httpvar); $cle = key($httpvar); next($httpvar)){
            $ok = false;
            $compt = count($param);
            for ($i = 0; $i < $compt && ($pr != $compt); $i++){
                if (($cleU = explode('=', $param[$i])) && $cleU[0] == $cle) {
                    $ok = true;
                    $pr ++;
                    break;
                    }
                }
            if (! $ok) $chaine .= $cle.'='.$httpvar[$cle];
            }
        }

    //Vidage du cache
    function MxClearCache($fich = '', $query = ''){

        if (! $open = @opendir($this -> mXCachePath)) $this -> ErrorTracker(3, 'Can\'t open cache directory (<b>'.$this -> mXCachePath.'</b>) to clear old files.', 'MxClearCache', __FILE__, __LINE__);
        else {
            while ($fichier = @readdir($open)){

                if ($fichier != '.' && $fichier != '..'){
                    if (! $fich){
                        if (($currentTime = filemtime($this -> mXCachePath.$fichier)) && time() - $currentTime > $this -> mXCacheDelay) {
                            if (! @unlink($this -> mXCachePath.$fichier)) $this -> ErrorTracker(3, 'Can\'t unlink this file "<b>'.$fichier.'</b>" in cache directory.', 'MxClearCache', __FILE__, __LINE__);
                            }
                        }
                    else {
                        //Supprime spécifiquement le fichier du template en cours
                        $ana = explode('~', $fichier);
                        if ($ana[1] == $this -> template) {

                            //Gestion des suppressions spécifiques à une queryString
                            if ($query && $this -> GetMD5UrlKey($query) == $ana[0]){
                                if (! @unlink($this -> mXCachePath.$fichier)) $this -> ErrorTracker(3, 'Can\'t unlink this file "<b>'.$fichier.'</b>" in cache directory.', 'MxClearCache', __FILE__, __LINE__);
                                }
                            elseif (! @unlink($this -> mXCachePath.$fichier)) $this -> ErrorTracker(3, 'Can\'t unlink this file "<b>'.$fichier.'</b>" in cache directory.', 'MxClearCache', __FILE__, __LINE__);
                            }
                        }
                    }
                }

            @closedir($open);
            }
        }

    //Initialisation du cache
    function MxSetCache($filecontent) {

        $this -> MxClearCache('this');
	/* ajout du support des sous-répertoires */
	$this -> template = str_replace("/","%2F", $this -> template);
	/* correction d'un bug dans la construction du chemin du fichier cache */
        if (! $cache = fopen($this -> mXCachePath.$this -> mXUrlKey.'~'.$this -> template, 'w')) $this -> ErrorTracker(4, 'Can\'t open in writing the cache file on "<b>'.$this -> mXCachePath.$this -> template.'</b>" path.', 'MxSetCache', __FILE__, __LINE__);

        //Sauvegarde du contenu
        if ($this -> ErrorChecker()) {
            if (! $write = fputs($cache, $filecontent)) $this -> ErrorTracker(5, 'Can\'t wite the cache file on "<b>'.$this -> mXCachePath.$this -> mXUrlKey.'~'.$this -> template.'</b>" path.', 'MxSetCache', __FILE__, __LINE__);
            @fclose($cache);
            }
        }

    //Retourne le fichier de cache
    function MxGetCache() {
        /* ajout du support des sous-répertoires */
	$this -> template = str_replace("/","%2F", $this -> template);
	$cache_file = $this -> mXCachePath.$this -> mXUrlKey.'~'.$this -> template;

        if (! $open = @fopen($cache_file, 'rb')) $this -> ErrorTracker(5, 'Can\'t open the cache file on "<b>'.$cache_file.'</b>" path.', 'MxGetCache');
        if (! $read = @fread($open, filesize($cache_file))) $this -> ErrorTracker(5, 'Can\'t read the cache file on "<b>'.$cache_file.'</b>" path.', 'MxGetCache', __FILE__, __LINE__);

        @fclose($open);

        //Parsing des paramètres de sessions
        $read = $this -> MxSessionParameterParsing($read);

        //Si on cherche à mesurer les performances de ModeliXe
        if ($this -> performanceTracer) {
            $read = str_replace('<mx:performanceTracer />', $this -> GetExecutionTime().' [cache]', $read);
            }

        //Si il y a une gestion de la compression, envoie des en-têtes correspondantes
        $this -> ErrorChecker();
        if ($this -> mXoutput) return $read;
        else print($this -> MxSetCompress($read));

        die();
        }

    //Teste si le fichier de cache existe et son échéance
    function MxCheckCache() {
        /* ajout du support des sous-répertoires */
	$this -> template = str_replace("/","%2F", $this -> template);
	$cache_file = $this -> mXCachePath.$this -> mXUrlKey.'~'.$this -> template;

        if (@is_file($cache_file)){
            if (($currentTime = filemtime($cache_file)) && (((time() - $currentTime) < $this -> mXCacheDelay && filemtime($this -> mXTemplatePath.$this -> template) < $currentTime))) return true;
            }
        else return false;
        }

    //MX Template Fusion Engine --------------------------------------------------------------------------------------------------
    function MxSessionParameterParsing($content) {
        $hidden = '';

        $param = $this -> sessionParameter;

        if ($param){
            $content = str_replace('<mx:session />', $param, $content);

            $param = explode('&', $this -> sessionParameter);
            for($i = 0; $i < count($param); $i++){
                if ($param[$i]) {
                    $cle = explode('=', $param[$i]);
                    $hidden .= '<input type="hidden" name="'.$cle[0].'" value="'.$cle[1].'" />'."\n";
                    }
                }

            $content = str_replace('<mx:hiddenSession />', $hidden, $content);
            }

        return $content;
        }

    //Remplace le contenu des templates passés en arguments
    function MxReplace($path){

        if (! empty($this -> sheetBuilding[$path])) $cible = $this -> sheetBuilding[$path];
        else $cible = $this -> templateContent[$path];

        //Remplacement de l'ensemble des attributs ModeliXe par les valeurs qui ont été instanciées ou leurs valeurs par défaut
        reset($this -> attributArray);
        while (list($cle, $Fkey) = each($this -> attributArray)){
            $Farray = &$this -> $Fkey;

            if (is_array($Farray)){
                reset($Farray);

                while (list($Pkey, $value) = each($Farray)){

                    if ($path == substr($Pkey, 0, strrpos($Pkey, '.'))) {
                        if (isset($this -> xPattern[$Fkey][$Pkey])){
                            $pattern = $this -> xPattern[$Fkey][$Pkey];
                            $cible = str_replace($pattern, $value, $cible);
                            unset($Farray[$Pkey]);
                            }
                        }
                    }
                }
            }

        //Remplacement de l'ensemble des balises ModeliXe par les valeurs qui ont été instanciées ou leurs valeurs par défaut
        reset($this -> flagArray);
        while (list($cle, $Fkey) = each($this -> flagArray)){
            $Farray = &$this -> $Fkey;

            if (is_array($Farray)){
                reset($Farray);

                while (list($Pkey, $value) = each($Farray)){
                    if ($path == substr($Pkey, 0, strrpos($Pkey, '.'))) {
                        if (isset($this -> xPattern[$Fkey][$Pkey])){
                            $pattern = $this -> xPattern[$Fkey][$Pkey];
                            $cible = str_replace($pattern, $value, $cible);
                            unset($Farray[$Pkey]);
                            }
                        }
                    }
                }
            }
         return $cible;
         }

    //Construit les blocs et associe les blocs fils aux blocs parents
    function MxBlocBuilder($path = ''){
        $ordre = array();
        $hierarchie = 1;

        if (! $path) $path = $this -> absolutePath;
        $chemin = $path;

        //Classement de tout les fils de path du plus proche au plus lointain
        $base = count(explode('.', $path));
        $k = 1;
        $l = 1;
        $j = 1;

        for (; ;){

            //Si il existe un fils on le prend
            if (! empty($this -> son[$chemin][$j])) $fils = $this -> son[$chemin][$j];
            else $fils = '';

            //Si il existe on considère le dernier enregistrement trouvé précédant celui-ci
            if (! empty($ordre[$hierarchie])) $ancien = $ordre[$hierarchie][count($ordre[$hierarchie])];
            else $ancien = false;

            if ($fils == $ancien) break;

            //Si il n'y a plus de fils, on passe au noeud suivant
            if (empty($fils)) {
                $j = 1;

                if (! empty($ordre[$k][$l])) {
                    $chemin = $ordre[$k][$l];
                    $l ++;
                    }
                else {
                    $l = 1;
                    $k ++;

                    if (! empty($ordre[$k][$l])) $chemin = $ordre[$k][$l ++];
                    else break;
                    }
                }
            else {
                $j ++;

                //Si le fils n'a pas été détruit on le considére
                if ($this -> templateContent[$fils]) {

                    //hiérarchie compte le nombre de blocs à partir du bloc de base
                    $hierarchie = count(explode('.', $fils)) - $base;

                    if (empty($ordre[$hierarchie])) $ordre[$hierarchie] = array();
                    $ordre[$hierarchie][count($ordre[$hierarchie]) + 1] = $fils;
                    }
                }
            }

        //Insertion des fils les plus lointains dans les fils les plus proches jusqu'au path
        for ($i = count($ordre); $i > 0; $i --){

            for ($j = 1; $j <= count($ordre[$i]); $j++){

                $fils = $ordre[$i][$j];
                $pattern = $this -> xPattern['inclusion'][$fils];
                $pere = $this -> father[$ordre[$i][$j]];

                //Insertion du bloc fils dans le père
                if ($pere == $path && $this -> IsALoop[$path]) {

                    if ($this -> IsALoop[$fils]) {

                        if ($this -> deleted[$fils]) {
                            $rem = ' ';
                            $this -> deleted[$fils] = false;
                            }
                        else $rem = $this -> loop[$fils];

                        $this -> loop[$pere] = str_replace($pattern, $rem, $this -> loop[$pere]);
                        $this -> loop[$fils] = '';
                        }
                    else {

                        if ($this -> deleted[$fils]) {
                            $rem = ' ';
                            $this -> deleted[$fils] = false;
                            }
                        else $rem = $this -> MxReplace($fils);

                        $this -> loop[$pere] = str_replace($pattern, $rem, $this -> loop[$pere]);
						$this -> sheetBuilding[$fils] = '';
                        }
                    }
                else {

                    if (! empty($this -> sheetBuilding[$pere])) $source = $this -> sheetBuilding[$pere];
                    else $source = $this -> templateContent[$pere];

                    if ($this -> IsALoop[$fils]) {

                        if ($this -> deleted[$fils]) {
                            $rem = ' ';
                            $this -> deleted[$fils] = false;
                            }
                        else $rem = $this -> loop[$fils];

                        $this -> sheetBuilding[$pere] = str_replace($pattern, $rem, $source);
                        $this -> loop[$fils] = '';
                        }
                    else {

                        if ($this -> deleted[$fils]) {
                            $rem = ' ';
                            $this -> deleted[$fils] = false;
                            }
                        else $rem = $this -> MxReplace($fils);

                        $this -> sheetBuilding[$pere] = str_replace($pattern, $rem, $source);
                        $this -> sheetBuilding[$fils] = '';
						}
                    }
                
				}
            }
        }

     //Associe les boucles
     function MxLoopBuilder($path = ''){
        if (! $path) $path = $this -> absolutePath;

        $father = $this -> father[$path];
        $pattern = $this -> xPattern['inclusion'][$path];

        //On saute les blocs détruits
        if ($pattern){
            $this -> IsALoop[$path] = true;
            if (empty($this -> loop[$path])) $this -> loop[$path] = '';

            //Gestion des blocs remplacés temporairement
            if ($this -> replacement[$path]) {
                $this -> loop[$path] .= $this -> MxReplace($path);
                $this -> replacement[$path] = false;
                $this -> sheetBuilding[$path] = '';
                }

            //Gestion des boucles classiques
            else {
                $this -> sheetBuilding[$path] = '';
                if (empty($this -> loop[$path])) $this -> loop[$path] = '';
                $this -> loop[$path] .= $this -> MxReplace($path);
                }
            }

        //Insertion des fils de $path dans $path
        $this -> MxBlocBuilder($path);
        }

    //Mx Output -------------------------------------------------------------------------------------------------------------------

    //Sortie du fichier HTML généré
    function MxWrite ($out = ''){
        /* correction pour activer $retour = $page -> MxWrite('out'); */
	if ($out) $this -> mXoutput = true;
	
	if (! $this -> mXsetting) $this -> ErrorTracker(5, 'You d\'ont intialize ModeliXe with setModeliXe method, there is no data to write.', 'MxWrite', __FILE__, __LINE__);

        //Assemblage de l'ensemble des blocs fils
        $this -> MxBlocBuilder();

        if ($this -> mXsignature) $entete = '<!--[ModeliXe '.$this -> mXVersion.'] -- '.(($this -> isTemplateFile)? '[TemplateFile : '.$this -> mXTemplatePath.$this -> template.']' : '[Template : '.$this -> template.']').' -- [date '.date('j/m/Y H:i:s')."]-->\n";
        else $entete = '';

        if ($this -> ErrorChecker()) {
            $filecontent = (($entete)? str_replace('<head>', '<head>'."\n".$entete,$filecontent = $this -> MxReplace($this -> absolutePath)) : $filecontent = $this -> MxReplace($this -> absolutePath));

            //Remplacement des balises de paramètres
            if ($this -> mXParameterFile) $filecontent = $this -> GetParameterParsing($filecontent);

            //Mise en cache de la page générée sans les paramètres de sessions
            if ($this -> mXCacheDelay > 0) {
                $this -> MxSetCache($filecontent);

                //Parsing des paramètres de sessions
                $filecontent = $this -> MxSessionParameterParsing($filecontent);
                }

            //Si on cherche à mesurer les performances de ModeliXe
            if ($this -> performanceTracer) {
                $filecontent = str_replace('<mx:performanceTracer />', $this -> GetExecutionTime(), $filecontent);
                }

	    // rajout personnel : version de ModeliXe
	    $filecontent = str_replace('<mx:version />', $this -> mXVersion, $filecontent);

            if ($this -> mXoutput) return $filecontent;
            else print($this -> MxSetCompress($filecontent));
            }
        }
    }
?>
