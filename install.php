<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
print "<br />Installation...";

if( ! isset($_GET['release'])) header('Location :index.test.php');

set_time_limit(0);
ini_set('memory_limit', '512M');



function rmdir_recurse($path) {
	$result = true ;
    $path= rtrim($path, '/').'/';
    $handle = opendir($path);
    for (;false !== ($file = readdir($handle));)
        if($file != "." and $file != ".." ) {
            $fullpath= $path.$file;
            if( is_dir($fullpath) ) {
               $result = $result && rmdir_recurse($fullpath);
            } else {
                $result = $result && unlink($fullpath);
            }
    }
    closedir($handle);
    $result = $result && rmdir($path);
	return $result ;
}




// Appel du fichier de configuration.
include_once ( "config.php" ) ;

$version = $_GET['release'] ;
$version = str_replace('/', '', $version);

require(URLLOCAL.'classes_ext/Tar.php');

$ficArchive = URLLOCAL.'var/dist/'.PREFIXEARCHIVE.'.maj.'.$version.'.tgz';

$tmpInflatePath =  URLLOCAL.'temp/installdist/' ;


//si précédente décompression temporaire: on efface
if ( file_exists($tmpInflatePath))
if( ! rmdir_recurse($tmpInflatePath) )
{
	echo "Impossible de supprimer le répertoire $tmpInflatePath . Abandon." ;
	die ;
}

//on décompresse
mkdir($tmpInflatePath);

$archive = new Archive_Tar($ficArchive);

$res = $archive->extract($tmpInflatePath);
if( ! $res )
{
	echo "Probleme lors de la décompression" ;
	die ;
}

//On dresse la liste relative de chaque fichier

function listFiles($path, & $tabFics,$root='') {
	$result = true ;
    $path= rtrim($path, '/').'/';
    $handle = opendir($path);
    for (;false !== ($file = readdir($handle));)
        if($file != "." and $file != ".." ) {
            $fullpath= $path.$file;
            if( is_dir($fullpath) ) {
              listFiles($fullpath,$tabFics,$root.$file.'/');
            } else {
                $tabFics[]=$root.$file;
            }
    }
    closedir($handle);
	return true ;
}

//TODO:
//Sauvegarde du TU existant

$tabFics = array();
listFiles($tmpInflatePath,$tabFics);

//on regarde si la cible qui doit être installée est ok en écriture
//et si on arrive bien à créer les nouveaux dossiers
//et si les nouveaux fichiers sont dans des répertoires autorisés en écriture
$listeFicsKo = array() ;
$listeDossiersKo = array() ;
$listeDossiersKoDroits = array() ;

foreach($tabFics as $file)
{
	if( ! file_exists(dirname(URLLOCAL.$file)))
	{
		if( ! mkdir(dirname(URLLOCAL.$file),0777,true) )
		{
			$listeDossiersKo[] = dirname(URLLOCAL.$file) ;
		}
	}


	if( ! file_exists(URLLOCAL.$file) && ! is_writable(dirname (URLLOCAL.$file) ))
	 {
			$listeDossiersKoDroits[] = dirname(URLLOCAL.$file) ;
	 }
	else if(  file_exists(URLLOCAL.$file) && ! is_writable((URLLOCAL.$file)))
	{
		$listeFicsKo[] = URLLOCAL.$file ;
	}
}

if( count($listeFicsKo) > 0 ||  count($listeDossiersKoDroits) > 0 ||  count($listeDossiersKo) > 0 )
{
	foreach( $listeFicsKo as $file )
	{
		print "<br /><font color=\"red\">$file n'est pas autorisé en écriture pour le user ".$_ENV["APACHE_RUN_USER"]."</font>"	;
	}
	foreach( $listeDossiersKoDroits as $dossier )
	{
		print "<br /><font color=\"red\">Le dossier $dossier doit etre autorisé en écriture pour le user ".$_ENV["APACHE_RUN_USER"]."</font>"	;
	}
	foreach( $listeDossiersKo as $dossier )
	{
		print "<br /><font color=\"red\">Le dossier $dossier n'a pas pu être créé par le user ".$_ENV["APACHE_RUN_USER"]."</font>"	;
	}
	print "<br />La mise à jour  ne s'est pas effectuée."	;
	die ;
}

foreach($tabFics as $file)
{
	print "<br />Installing ". $tmpInflatePath.$file.'  on '.URLLOCAL.$file ;ob_flush();flush();
	if ( ! copy($tmpInflatePath.$file, URLLOCAL.$file))
		{
			//TODO:
			//erreur de copie -> reprendre la sauvegarde précédente
		}
}

print "<br />Votre installation de TU est à jour<a href='index.test.php'>Mettre à jour la base de données</a>" ;


?>