<?php
/*montanaClient.php
 * Created on 6 mars 07
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Version 0.1A
 */
 
 class montanaClient {
 private $login;
 private $ids;	
 function montanaClient($login,$ids='') {
 	$this->ids = $ids;
 	$this->login = $login ;
 	$this->attr = "";
 }


 function genUrlAuth() {
 	$sec = round(time(),-2);
 	$pass= md5(KEYMONTANA.$sec.$this->login);
 	return URLMONTANA."index.php?navi=auth|authv1|".$this->login."|$pass";
 }

 function genCodeHtmlAuth($printEtat = true ) {
 	$stylePart = "";
 	if(! $printEtat)
 		$stylePart = "display:none;";
 	$af  = $this->getJsCode($this->genUrlAuth() ) ;
 	$af .= "<div id='authmontana' style='$stylePart' ><span style='color:#FF8A00;'>Authentification sur Montana en cours...</span></div>";
 	$af .= "<script type='text/javascript'>setTimeout('authOnMontana()',10);</script>";
 	return $af;
 }

//spécifie éventuellement les attributs qui vont être utilisés pour appliquer un style
//au lien, ou un javascript particulier
//ex: setLinkHtmlAttributes("style='color:#87abd6;'")
function setLinkHtmlAttributes($attr) {
$this->attr = $attr ;
}

function getLinkListChoices($intBal="Créer un formulaire") {
	return "<a href='".URLMONTANA."index.php?navi=ws|genCaseChoiceHTML|".$this->ids."' ".$this->attr." target='iframeMontana' onclick=\"document.getElementById('divMontana').style.display='block';document.getElementById('iframeMontana').src='about:blank';\" >$intBal</a>";
}

function getLinkListForms($intBal="Lister formulaire",$options="") {
	return "<a $options href='".URLMONTANA."index.php?navi=ws|listFormsHTML|".$this->ids."' ".$this->attr." target='iframeMontana' onclick=\"document.getElementById('divMontana').style.display='block'document.getElementById('iframeMontana').src='about:blank';\" >$intBal</a>";
}

function getLinkEditInstance($idInstance,$intBal="",$options="") {
 if( ! $intBal ) 
 	$intBal = "Edition du formulaire n° $idInstance" ;
  return "<a $options href='".$this->getUrlEditInstance($idInstance)."' ".$this->attr." target='iframeMontana' onclick=\"document.getElementById('divMontana').style.display='block';document.getElementById('iframeMontana').src='about:blank';\" >$intBal</a>";
}
 	
function getUrlEditInstance($idInstance) 	{
	return 	URLMONTANA."index.php?navi=ws|editInstanceHtml|".$this->ids."|".$idInstance ;
}
 	
function getLinkNewForm($idForm,$intBal="",$options="") {
if( ! $intBal ) 
 	$intBal = "Création de nouveau formulaire de type $idForm" ;
 return "<a $options href='".URLMONTANA."index.php?navi=ws|newFormHtml|".$this->ids."|".$idForm."' ".$this->attr." target='iframeMontana' onclick=\"document.getElementById('divMontana').style.display='block';document.getElementById('iframeMontana').src='about:blank';\" >$intBal</a>";
}

function getLinkDelInstance($idInstance,$intBal="",$options="") {
 if( ! $intBal ) 
 	$intBal = "Edition du formulaire n° $idInstance" ;
  return "<a $options href='".$this->getUrlDelInstance($idInstance)."' ".$this->attr." target='iframeMontana' onclick=\"document.getElementById('divMontana').style.display='block';document.getElementById('iframeMontana').src='about:blank';\" >$intBal</a>";
}

function getUrlDelInstance($idInstance) {
	return 	URLMONTANA."index.php?navi=ws|delInstanceHtml|".$this->ids."|".$idInstance ;
}


function getLinkAskDelInstance($idInstance,$intBal="",$options="") {
 if( ! $intBal ) 
 	$intBal = "Edition du formulaire n° $idInstance" ;
  return "<a $options href='".$this->getUrlAskDelInstance($idInstance)."' ".$this->attr." target='iframeMontana' onclick=\"document.getElementById('divMontana').style.display='block';document.getElementById('iframeMontana').src='about:blank';\" >$intBal</a>";
}

function getUrlAskDelInstance($idInstance) {
	return 	URLMONTANA."index.php?navi=ws|askDelInstanceHtml|".$this->ids."|".$idInstance ;
}

function  getUrlPrintInstance($idInstance) {
	return 	URLMONTANA."index.php?navi=ws|printInstanceHtml|".$this->ids."|".$idInstance ;
}

function getUrlReopenInstance($idInstance) {
	return 	URLMONTANA."index.php?navi=ws|reopenInstanceHtml|".$this->ids."|".$idInstance ;
}



function getCodeMainDiv($titre="Gestion de Formulaire via Montana") {
	return '
	<div id="divMontana" style="border: 1px solid black;display:none;width:601px;height:623px;">
	<div id="divMontanaHeader"  style="display:block;cursor:move;width:601px;height:22px;background:#CCCCCC;border-bottom: 1px solid black;">
		<div id="divMontanaTitre" style="font-style:bold;width:580px;">'.$titre.'</div>
		<div id="divMontanaClose" style="display:block;position:relative;width:21px;cursor:pointer;top:-12px;left:580px;">
		<img onclick="document.getElementById(\'divMontana\').style.display=\'none\';if(\'function\' == typeof(montanaCloseFrame)) { montanaCloseFrame(); }" alt="Fermer" src="'.URLMONTANA.'images/closepiti.gif" /> 
		</div>
	</div>
	<iframe id="iframeMontana" name="iframeMontana" src="about:blank" style="background:white;overflow:auto;width:600px;height:600px; border:none;" >
	</iframe>
	</div>';	
}
 
 
 function getJsCode($url) {
 	return '
<script type="text/javascript">
function authOnMontana() {
if(window.XMLHttpRequest) // Firefox 
xhr_object = new XMLHttpRequest(); 
else if(window.ActiveXObject) // Internet Explorer 
xhr_object = new ActiveXObject("Microsoft.XMLHTTP"); 
else { // XMLHttpRequest non supporté par le navigateur 
alert("Votre navigateur n\'est pas assez récent pour cette fonctionalité."); 
return; 
}
xhr_object.open("POST", "'.$url.'", true)	;
xhr_object.onreadystatechange = function() { 
if(xhr_object.readyState == 4) 
if(xhr_object.responseText == "ok") {
document.getElementById("authmontana").innerHTML="<span style=\'color:green;\'>Réussite authentification sur Montana<\/span>";
setTimeout("virerMessMontana()",1000);
} else {
alert("probleme d\'authentification: "+xhr_object.responseText);
document.getElementById("authmontana").innerHTML="<span style=\'color:red;\'>Echec Authentification sur Montana<\/span>";
}
}
xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
var data = "filtre=none";
xhr_object.send(data);
}
function virerMessMontana() {
document.getElementById("authmontana").innerHTML="";
}
</script>';	
}
 
 	
 	
}
 
 
?>
