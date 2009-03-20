<style>

#casemaincourante { 
        background: #FFFF99;
        width:100%;
        height:100px;
        border-collapse: collapse;
        border: 1px solid #006699;
}

#casemaincourante textarea.maincourante { 
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 10px;
	border: none;
	background-color : #FFFF99 ;
	height: 80px;
	width:220px;
	text-align:left;
	scrollbar-3dlight-color: #FFFF99;
	scrollbar-arrow-color: #FFFF99;
    scrollbar-darkshadow-color: #FFFF99;
    scrollbar-face-color: #FFFF99;
    scrollbar-highlight-color: #FFFF99;
    scrollbar-shadow-color: #FFFF99;
    scrollbar-track-color: #FFFF99;
    scrollbar-track-color: #FFFF99;
}

#casemaincourante input.maincourante { 
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 10px;
	color:  #11BF85;
	border: none;
	background-color : #FFFF99 ;
	text-align:left;
}	

#casemaincourante_handler {
        cursor:move;
        cursor:hand;
}

</style>

<script language='javascript'>
document.onkeyup = saveOnEsc;
function saveOnEsc(e) {
var key = (window.event) ? event.keyCode : e.keyCode;
//alert ( key);
if (key == 118) {
	/*document.maincourante.maincourantetexte.style.setProperty('background-color','#000000',null);
	alert('poeut');*/
	document.maincourante.submit();
	}
}

</script>

<div  id="casemaincourante" >
<form name="maincourante" method="post"  action="index.php">
<div id="casemaincourante_handler" ><b>Bloc-notes</b> <input type='text' readonly name='comm' class='maincourante'/></div>
<table cellpadding="0" cellspacing="0" style="border:0px solid #EEEE88; background-color: #EEEE88; font-size: 90%; ">
<tr onclick="document.maincourante.maincourantetexte.style.background='#EEEEEE';document.maincourante.comm.value='Enregistrer=[F7]'"><td><mx:formField id="contenu" /></td></tr></table>
<mx:hidden id="hidden1" />
</form>
</div>
