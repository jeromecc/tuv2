<?php 
// Titre  : Fichier index du terminal
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 05 Janvier 2005
 
// Description : 
// Le squelette du site est fabriquï¿½ ici et les objets "globaux" sont
// instanciï¿½s ï¿½ ce niveau.


// Appel du fichier de configuration.
include_once ( "config.php" ) ;

print "<h3>Procédure de vérification</h3>" ;
print "<hr>" ;
print "<h4>Création des fichiers de configuration MySQL</h4>" ;

$dom      = new DOMDocument ( '1.0', 'utf8' ) ;
$result   = $dom->createElement ( 'result', '' ) ;
$result  -> setAttribute ( 'num', '1' ) ;
$element  = $dom->createElement ( 'element', '' ) ;
$id       = $dom -> createElement ( 'id', 1 ) ;
$nom      = $dom -> createElement ( 'nom', 'Local' ) ;
$type     = $dom -> createElement ( 'type', 'MySQL' ) ;
$host     = $dom -> createElement ( 'host', MYSQL_HOST ) ;
$login    = $dom -> createElement ( 'login', MYSQL_USER ) ;
$password = $dom -> createElement ( 'password', MYSQL_PASS ) ;
$db       = $dom -> createElement ( 'db', BASEXHAM ) ;
$env      = $dom -> createElement ( 'env', 'cfg' ) ;
$element -> appendChild ( $id ) ;
$element -> appendChild ( $nom ) ;
$element -> appendChild ( $type ) ;
$element -> appendChild ( $host ) ;
$element -> appendChild ( $login ) ;
$element -> appendChild ( $password ) ;
$element -> appendChild ( $db ) ;
$element -> appendChild ( $env ) ;
$result  -> appendChild ( $element ) ;
$dom     -> appendChild ( $result ) ;
$FIC      = fopen ( "queries_gen/config_xham.cfg", "w" ) ;
print "Creation du fichier 'queries_gen/config_xham.cfg' => " ;
if ( fwrite ( $FIC, $dom->saveXML ( ) ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;
fclose ( $FIC ) ;

$dom      = new DOMDocument ( '1.0', 'utf8' ) ;
$result   = $dom->createElement ( 'result', '' ) ;
$result  -> setAttribute ( 'num', '1' ) ;
$element  = $dom->createElement ( 'element', '' ) ;
$id       = $dom -> createElement ( 'id', 1 ) ;
$nom      = $dom -> createElement ( 'nom', 'Local' ) ;
$type     = $dom -> createElement ( 'type', 'MySQL' ) ;
$host     = $dom -> createElement ( 'host', MYSQL_HOST ) ;
$login    = $dom -> createElement ( 'login', MYSQL_USER ) ;
$password = $dom -> createElement ( 'password', MYSQL_PASS ) ;
$db       = $dom -> createElement ( 'db', CCAM_BDD ) ;
$env      = $dom -> createElement ( 'env', 'cfg' ) ;
$element -> appendChild ( $id ) ;
$element -> appendChild ( $nom ) ;
$element -> appendChild ( $type ) ;
$element -> appendChild ( $host ) ;
$element -> appendChild ( $login ) ;
$element -> appendChild ( $password ) ;
$element -> appendChild ( $db ) ;
$element -> appendChild ( $env ) ;
$result  -> appendChild ( $element ) ;
$dom     -> appendChild ( $result ) ;
$FIC      = fopen ( "queries_gen/config_ccam.cfg", "w" ) ;
print "<br>Creation du fichier 'queries_gen/config_ccam.cfg' => " ;
if ( fwrite ( $FIC, $dom->saveXML ( ) ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;
fclose ( $FIC ) ;

$dom      = new DOMDocument ( '1.0', 'utf8' ) ;
$result   = $dom->createElement ( 'result', '' ) ;
$result  -> setAttribute ( 'num', '1' ) ;
$element  = $dom->createElement ( 'element', '' ) ;
$id       = $dom -> createElement ( 'id', 1 ) ;
$nom      = $dom -> createElement ( 'nom', 'Local' ) ;
$type     = $dom -> createElement ( 'type', 'MySQL' ) ;
$host     = $dom -> createElement ( 'host', MYSQL_HOST ) ;
$login    = $dom -> createElement ( 'login', MYSQL_USER ) ;
$password = $dom -> createElement ( 'password', MYSQL_PASS ) ;
$db       = $dom -> createElement ( 'db', BDD ) ;
$env      = $dom -> createElement ( 'env', 'cfg' ) ;
$element -> appendChild ( $id ) ;
$element -> appendChild ( $nom ) ;
$element -> appendChild ( $type ) ;
$element -> appendChild ( $host ) ;
$element -> appendChild ( $login ) ;
$element -> appendChild ( $password ) ;
$element -> appendChild ( $db ) ;
$element -> appendChild ( $env ) ;
$result  -> appendChild ( $element ) ;
$dom     -> appendChild ( $result ) ;
$FIC      = fopen ( "queries_gen/config_terminal.cfg", "w" ) ;
print "<br>Creation du fichier 'classes_gen/config_terminal.cfg' => " ;
if ( fwrite ( $FIC, $dom->saveXML ( ) ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;
fclose ( $FIC ) ;

$dom      = new DOMDocument ( '1.0', 'utf8' ) ;
$result   = $dom->createElement ( 'result', '' ) ;
$result  -> setAttribute ( 'num', '1' ) ;
$element  = $dom->createElement ( 'element', '' ) ;
$id       = $dom -> createElement ( 'id', 1 ) ;
$nom      = $dom -> createElement ( 'nom', 'Local' ) ;
$type     = $dom -> createElement ( 'type', 'MySQL' ) ;
$host     = $dom -> createElement ( 'host', MYSQL_HOST ) ;
$login    = $dom -> createElement ( 'login', MYSQL_USER ) ;
$password = $dom -> createElement ( 'password', MYSQL_PASS ) ;
$db       = $dom -> createElement ( 'db', (FX_BDD?FX_BDD:BDD) ) ;
$env      = $dom -> createElement ( 'env', 'cfg' ) ;
$element -> appendChild ( $id ) ;
$element -> appendChild ( $nom ) ;
$element -> appendChild ( $type ) ;
$element -> appendChild ( $host ) ;
$element -> appendChild ( $login ) ;
$element -> appendChild ( $password ) ;
$element -> appendChild ( $db ) ;
$element -> appendChild ( $env ) ;
$result  -> appendChild ( $element ) ;
$dom     -> appendChild ( $result ) ;
$FIC      = fopen ( "queries_gen/config_formx.cfg", "w" ) ;
print "<br>Creation du fichier 'classes_gen/config_formx.cfg' => " ;
if ( fwrite ( $FIC, $dom->saveXML ( ) ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;
fclose ( $FIC ) ;

print "<br><br><hr><h4>Connexions aux bases</h4>" ;

print "Connexion au serveur MySQL '".MYSQL_USER."@".MYSQL_HOST." (using password: ".(MYSQL_PASS?'YES':'NO').")' => " ;
$res = mysql_pconnect ( MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
if ( $res ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;

print "<br>Connexion ï¿½ la base '".BASEXHAM."' => " ;
if ( mysql_select_db ( BASEXHAM ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;

clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,BASEXHAM);

print "<br>Connexion ï¿½ la base '".BDD."' => " ;
if ( mysql_select_db ( BDD ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;

clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,BDD);

print "<br>Connexion ï¿½ la base '".CCAM_BDD."' => " ;
if ( mysql_select_db ( CCAM_BDD ) ) print "<font color=\"green\">OK</font>" ;
else print "<font color=\"red\">KO</font>" ;

clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,CCAM_BDD);





print "<br><br><hr><h4>Vérification des répertoires</h4>" ;



clUpdater::testEcritureDossier(URLCACHE);
clUpdater::testEcritureDossier(URLDOCS);
clUpdater::testEcritureDossier(URLLOCAL.'hprim/');
clUpdater::testEcritureDossier(URLLOCAL.'hprim/ok/');
clUpdater::testEcritureDossier(URLLOCAL.'hprim/xml/');
clUpdater::testEcritureDossier(URLLOCAL.'rpu/');
clUpdater::testEcritureDossier(URLLOCAL.'rpu/ok/');
clUpdater::testEcritureDossier(URLLOCAL.'rpu/logs/');
clUpdater::testEcritureDossier(URLLOCAL.'var/');
clUpdater::testEcritureDossier(URLLOCAL.'var/maj/');




clUpdater::applyPatchs();


?>
