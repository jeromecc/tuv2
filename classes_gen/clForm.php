<?php

// Titre  : Classe Formulaires
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 09 Février 2005

// Description :
// Cette classe génère des bouts de formulaire :
// input text, input hidden, input image, input submit,
// select, textarea...

class clForm {

  // Retourne le code HTML d'une balise ouvrante d'un formulaire.
  function genForm ( $action='', $method='post', $name='' ) {
    if ( $name ) $nam = "name=\"$name\"" ;
    return "<form $nam method=\"$method\" action=\"$action\">\n" ;
  }

  // Retourne le code HTML d'une balise fermante d'un formulaire.
  function genEndForm ( ) { return "</form>" ; }

  // Retourne le code HTML d'un champs texte.
  function genText ( $nom='text', $valeur='', $size='', $maxlength='', $libre='' ) {
    if ( $size ) $siz = "size=\"$size\"" ; else $siz='';
    if ( $maxlength ) $maxlen = "maxlength=\"$maxlength\"" ; else $maxlen = '' ;
    
    return "<input type=\"text\" name=\"$nom\" value=\"$valeur\" $maxlen $siz $libre />" ;
  }

  // Retourne le code HTML d'un champs de type hidden.
  function genHidden ( $nom='hidden', $valeur='' ) {
    return "<input type=\"hidden\" name=\"$nom\" value=\"$valeur\" />\n" ;
  }

  // Retourne le code HTML d'un bouton submit.
  function genSubmit ( $nom='submit', $valeur='Valider', $libre='' ) {
    return "<input type=\"submit\" name=\"$nom\" value=\"$valeur\" $libre />\n" ;
  }

  // Retourne le code HTML d'un bouton submit de type image.
  function genImage ( $nom='image', $valeur='', $image='', $libre='' ) {
    return "<input type=\"image\" src=\"$image\" name=\"$nom\" value=\"$valeur\" $libre />\n" ;
  }

  // Retourne le code HTML d'un champs select (simple ou multiple).
  function genSelect ( $nom='select', $valeur='', $data='', $reload='', $size='', $multiple='', $libre='' ) {
    if ( $reload )   $rel  = "onChange=\"reload(this.form)\"" ;
    if ( $size )     $siz  = "size=\"$size\"" ;
    if ( $multiple ) $mult = "multiple=\"yes\"" ;
    $af = "<select name=\"$nom\" ".(isset($rel)?$rel:'')." ".(isset($siz)?$siz:'')." ".(isset($mult)?$mult:'')." >\n" ;
    if ( is_array ( $data ) ) {
      reset ( $data ) ;
      while ( list ( $key, $val ) = each ( $data ) ) {
	if ( $key == $valeur ) $sel = "selected" ; else $sel = "" ;
	$af .= "\t<option value=\"$key\" $sel>$val</option>\n" ;
      }
    }
    $af .= "</select>\n" ;
    return $af ;
  }

  // Retourne le code HTML d'un champs textarea.
  function genTextArea ( $nom='textarea', $valeur='', $cols='30', $rows='4' ) {
    $af .= "<textarea name=\"$nom\" cols=\"$cols\" rows=\"$rows\">" ;
    $af .= "$valeur" ;
    $af .= "</textarea>" ;
    return $af ;
  }

  // Retourne le code HTML d'une checkbox.
  function genCheckbox ( $nom, $valeur, $checked='', $reload='' ) {
    if ( $checked ) $ch  = "checked" ;
    if ( $reload  ) $rel = "onChange=\"reload(this.form)\"" ;
    return "<input type=\"checkbox\" name=\"$nom\" value=\"$valeur\" $ch $rel />" ;
  }

  // Retourne le code HTML d'un bouton radio.
  function genRadio ( $nom, $valeur, $checked='', $reload='' ) {
    if ( $checked ) $ch  = "checked" ;
    if ( $reload  ) $rel = "onChange=\"reload(this.form)\"" ;
    return "<input type=\"radio\" name=\"$nom\" value=\"$valeur\" $ch $rel />" ;
  }

  // Retourne le code HTML d'un champs de type "fichier".
  function genFile ( $nom='', $value='', $reload='' ) {
    if ( $reload  ) $rel = "onChange=\"reload(this.form)\"" ;
    return "<input type=\"file\" name=\"$nom\" value=\"$valeur\" $rel />" ;
  }

}

?>