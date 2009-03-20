<?php
// Titre  : Classe clPetitBox
// Auteur : François Derock
// Date   : 21 Avril 2008


class clPetitBox {
  
  // Attributs de la classe.
  
  // Contient l'affichage généré par la classe.
  private $af ;
  private $erreurs ;
  
  // Constructeur
  /****************************************************************************/
  function __construct ( ) {
  /****************************************************************************/

    global $options;
    global $session;
    
    if ( isset($_REQUEST["elephant"]) and $_REQUEST["elephant"]=="ok" ) {
      $list         = new ListMaker ( "template/sendMailPetitBox.html" ) ;
      $pagination   = 1000;
  
      $list -> addUserVar ( 'titre'       , $options->getOption('PetitBox_MailTitre').$options->getOption('NomEtablissement') );
      $list -> addUserVar ( 'soustitre'   , "Type de demande : ".$_REQUEST["nature"]                       );
      $list -> addUserVar ( 'titremessage', $_REQUEST["titre"]                                             );
      $list -> addUserVar ( 'contenu'     , $_REQUEST["contenu"]                                           );
      $list -> addUserVar ( 'info'        , $_REQUEST["expediteur"]                                        );
      $list -> addUserVar ( 'autre'       , "le ".date("d/m/Y", time())."<br>".
      "par ".$_SERVER["REMOTE_ADDR"]."<br>avec ".$_SERVER["HTTP_USER_AGENT"]."<br>de ".$_SERVER["HTTP_REFERER"]
      );
      $list -> addUserVar ( 'session'     , 
    "Nom :".$session->getNom       (          )."<br>". 
    "Prenom :".$session->getPrenom    (          )."<br>".
    "User :".$session->getUser      (          )."<br>". 
    "Mail :".$session->getMail      (          )."<br>". 
    "Tel :".$session->getTel       ( $ind='0' )."<br>". 
    "Tels :".$session->getTels      (          )."<br>". 
    "Service :".$session->getService   ( $ind='0' )."<br>". 
    "Services :".$session->getServices  (          )."<br>".
    "Fonction :".$session->getFonction  ( $ind='0' )."<br>". 
    "Fonctions :".$session->getFonctions (          )."<br>". 
    "Groupes :".$session->getGroupes	 (		      )."<br>".
    "Orgs :".$session->getOrgs      (          )."<br>". 
    "IP :".$session->getIP        (          )."<br>". 
    "Uid :".$session->getUid       (          )."<br>".
    "Password :"."--------------------------------"."<br>".
    "UF :".$session->getUF        (          )."<br>".
    "Grp :".$session->getGrp       (          )."<br>".
    "Attribute :".$session->getAttribute ( $var     )."<br>". 
    "Type :".$session->getType      (          )."<br>"
    );
      $list -> addUserVar ( 'hopital'     , $options->getOption('NomEtablissement')                         );
      
      $objet        = $options->getOption('PetitBox_MailSujet').$options->getOption('NomEtablissement');
      $message      = "'".$list -> getList ( $pagination )."'<br>";
      $headers      = "MIME-Version: 1.0\n";
      $headers     .= "Content-type: text/html; charset=iso-8859-1\n";
      
      mail( $options->getOption('PetitBox_MailAdress'), $objet, $message, $headers );
        
      }
      
    $mod =  new ModeliXe("petitbox.html");
    $mod -> SetModeliXe();
    $mod -> MxText      ( "lien"        , URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1),$session->getNavi(2))) ;
    $mod -> MxText      ( "titreFenetre", $options->getOption('PetitBox_TitleBox')                                                    ) ;
    $mod -> MxImage     ( "imgFermer"   , URLIMGFER, "Fermer"                                                                         ) ;
    $mod -> MxFormField ( "titre"       , "text", "titre", ""                                                                         ) ;
    $mod -> MxFormField ( "expediteur"  , "text", "expediteur", ""                                                                    ) ;
    $mod -> MxFormField ( "contenu"     , "textarea","contenu","","rows=\"3\" cols=\"50\"	wrap=\"virtual\""                           ) ;
    
    $Nature = $options->getOption('PetitBox_MailNature');
    $tabNature = explode (",",$Nature);
    
    for ( $i = 0 ; isset($tabNature[$i]) ; $i++ ) {
      $tabNature[$i] = str_replace ( "\"" , "" , $tabNature[$i] );
      $tab[$tabNature[$i]] = $tabNature[$i]; 
      }
    $j=$i;
      
    $mod -> MxSelect    ( "nature"      , "nature",'',$tab,'','',"size=".$j." multiple=\"no\"");
    $mod -> MxHidden    ( "hidden1"     , "navi=".$session->genNaviFull ( )                                           ) ;
    $mod -> MxHidden    ( "hidden5"     , "elephant=ok"                                                               ) ;

    $this->af = $mod -> MxWrite ( "1" ) ;
    
    }
  
  // Renvoie l'affichage généré par la classe.
  /****************************************************************************/
  function getAffichage ( ) {
  /****************************************************************************/
    
    return $this->af ;
  
  }
  
}

?>


