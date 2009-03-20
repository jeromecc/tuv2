<?php

// Titre  : Classe PostIt
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 26 Septembre 2005

// Description : 
// Cette classe affiche des petites popup déplaçables et fermables.

// ADD (2006-05-17 / Damien Borel) : Génération simplifiée d'info-bulles 
// avec overlib via la fonction genInfoBulle ( $contenu )


class clPostIt {

  function __construct ( ) {
    $this->actuel = 1 ;
    $this->move = 0 ;
    $this->liste = '' ;
  }

  function addPostIt ( $titre='Titre', $msg='Message', $type='message', $lock='', $size='250px', $posx='400px', $posy='300px' ) {
    switch ( $type ) {
    case 'alerte' :
      $couleur="alertePI" ;
      break ;
    case 'erreur' :
      $couleur="erreurPI" ;
      break ;
    case 'reussite' :
      $couleur="reussitePI" ;
      break ;
    default :
      $couleur="messagePI" ;
      break ;
    }
    
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "PostIt.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Remplissage des champs.
    $class = "class=\"".$couleur."\"" ;
    $mod -> MxText ( "div", '<div id="id'.$this->actuel.'" style="'."font-size: 85%; -moz-border-radius: 6px; color: black; width: $size;top:$posy; left:$posx; position:absolute; z-index:100;".'" '.$class.'>' ) ;
    $mod -> MxText ( "trhandle", "<tr id=\"handleid".$this->actuel."\"  style=\"border: 5px solid black; cursor: move;\">" ) ;
    if ( ! $lock )
    $mod -> MxText ( "masquer", '<img alt="Fermer" src="images/close.png" title="masquer" style="cursor:pointer;" onClick="javascript:cache(\'id'.$this->actuel.'\');" />' ) ;
    //    $mod -> MxText ( "id", 'id'.$this->actuel ) ;

    $mod -> MxText ( "titre", $titre ) ;
    $mod -> MxText ( "contenu", $msg ) ;
   
    // On retourne le code HTML généré.
    $this->liste .= $mod -> MxWrite ( "1" ) ;
    $this->actuel++ ;
    
  }


  function addMove ( $fenetre, $handle='' ) {
    $this->fenetre[$this->move] = $fenetre ;
    $this->handle[$this->move] = ($handle?$handle:$fenetre) ;
    $this->move++ ;
    
  }

  // Génération d'un post-it'
  function genInfoBulle ( $contenu ) {
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


  function genJS ( ) {
    //eko ( $this->handle ) ;
    //eko ( $this->fenetre ) ;
    $js = "<script type=\"text/javascript\">
          window.onload = function() {
            var group
            var coordinates = ToolMan.coordinates()
            var drag = ToolMan.drag()
            " ;

    for ( $i = 0 ; $i < $this->move ; $i++ ) {
      $js .= "var boxHandle = document.getElementById(\"".$this->fenetre[$i]."\")
            if ( boxHandle ) {
	      group = drag.createSimpleGroup(boxHandle, document.getElementById(\"".$this->handle[$i]."\"))
            }
            " ;
    }
    for ( $i = 1 ; $i < $this->actuel ; $i++ ) {
      $js .= "var boxHandle = document.getElementById(\"id".$i."\")
            if ( boxHandle ) {
	      group = drag.createSimpleGroup(boxHandle, document.getElementById(\"handleid".$i."\"))
            }
            " ;
    }
    $js .= "
	    var boxHandle = document.getElementById(\"boxHandle\")
	    if ( boxHandle ) {
              group = drag.createSimpleGroup(boxHandle, document.getElementById(\"handle\"))
	    }
          }
  </script>" ;
    return $js ;
    
  }

  function getAffichage ( ) {
    return $this->liste ;
  }

}

?>
