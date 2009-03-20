<SCRIPT language="javascript">
   
   setTimeout("Scroller()", 500);
   
   function GetId(id)
{
return document.getElementById(id);
}
   var formx_infobulle_display=false;
   
  function formx_infobulle_move(e) {
  if(formx_infobulle_display) {  // Si la bulle est visible, on calcul en temps reel sa position ideale
    if (navigator.appName!="Microsoft Internet Explorer") { // Si on est pas sous IE
    GetId("formx_infobulle").style.left=e.pageX + 5+"px";
    GetId("formx_infobulle").style.top=e.pageY + 10+"px";
    }
    else { 
    if(document.documentElement.clientWidth>0) {
GetId("formx_infobulle").style.left=20+event.x+document.documentElement.scrollLeft+"px";
GetId("formx_infobulle").style.top=10+event.y+document.documentElement.scrollTop+"px";
    } else {
GetId("formx_infobulle").style.left=20+event.x+document.body.scrollLeft+"px";
GetId("formx_infobulle").style.top=10+event.y+document.body.scrollTop+"px";
         }
    }
  }
} 
   
   function formx_infobulle_montre(text) {
  if(formx_infobulle_display==false) {
  GetId("formx_infobulle").style.visibility="visible"; // Si il est caché (la verif n'est qu'une securité) on le rend visible.
  formx_infobulle_display=true;
  } else {
  }
}



   
   function formx_infobulle_cache() {
if(formx_infobulle_display==true) {
GetId("formx_infobulle").style.visibility="hidden"; // Si la bulle est visible on la cache
formx_infobulle_display=false;
}
}

function formx_infobulle_changeurl(url,width,height) {
 GetId("formx_infobulle").style.width = (width + 10) +'px' ;
 GetId("formx_infobulle").style.height = (height + 10)+'px' ;
 GetId("formx_infobulle_contenu").innerHTML ="<iframe src='"+url+"' scrolling='no' frameborder='0' width='"+width+"' height='"+height+"'></iframe>";
}

function formx_infobulle_changetext(text) {
 GetId("formx_infobulle_contenu").innerHTML =text;
  GetId("formx_infobulle").style.width = "auto" ;
 GetId("formx_infobulle").style.height = "auto" ;
}

function formx_infobulle_preload(e) {
formx_infobulle_move(e);
}


function formx_infobulle_load(e) {
formx_infobulle_move(e);
formx_infobulle_montre('');
formx_infobulle_move(e);
return false;
}
   
   
   
   
</SCRIPT>

<style type="text/css">
#formx_infobulle{
    position: absolute;   
    visibility : hidden;
    border: 1px solid Black;
    padding: 10px;
    font-family: Verdana, Arial;
    font-size: 10px;
    background-color: #FFFFCC;
    z-index:300;
}
</style>

<div id="formX">
<form method="post" name="formx_form" ENCTYPE="multipart/form-data">
<table class="tablegenerale">
  <tr align="left" id="formx_titre" >
    <td colspan="3"><mx:text id="titre" /></td>
    <td colspan="1" align="right">
      <mx:formField id="windowPrint"/></td>
    <td colspan="1">
     <mx:formField id="windowClose"/></td></tr>
  <tr >
   <td colspan="5"> 
   <mx:bloc id="objet">
    <b>Objet:</b> <mx:text id="libobj" /> 
    </mx:bloc id="objet">
   <mx:bloc id="explication">
    <br/><br/><mx:text id="explication"/><br/><br/>
    </mx:bloc id="explication">
    </td>
  </tr>
  <mx:bloc id="etape">
    <tr>
      <td colspan="1" class="gauche"></td>
      <td colspan="3" class="aa">
      <mx:text id="titre_etape"/> <i><mx:text id="navi_etape"/></i>
      </td>
      <td colspan="1" class="droite"></td>
    </tr>
     <mx:bloc id="item">
     <mx:bloc id="explication">
      <tr>
        <td colspan="1" class="gauche"></td>
         <td colspan="3" mxAttribut="class:style_gche" valign='top'>
          <b><mx:text id="lib"/></b><mx:text id="expl"/>
         </td>
       <td colspan="1" class="droite"></td>
      </tr>
      </mx:bloc id="explication">
       <tr class="aa_aa" id="<mx:text id="idligne" />"   <mx:text id="optionsligne" /> >
        <td colspan="1" class="gauche"></td>
         <td colspan="1" mxAttribut="class:style_gche" valign='top'>
	 <mx:bloc id="libelle">
         <mx:text id="libelles"/>
	 </mx:bloc id="libelle">
         </td>
        <td colspan="2"  mxAttribut="class:style_dte" >
         <mx:bloc id="LISTE">
          <mx:select id="select1"/></mx:bloc id="LISTE">
         <mx:bloc id="RO">
      	  <mx:text id="minitxt"/></mx:bloc id="RO">
 	  <mx:bloc id="CHECK">
				<table style="width:100%;background:transparent;border: 0px solid black;">
				<mx:bloc id="ligne">
				<tr>
					<mx:bloc id="col">
					<td>
					<mx:checker id="champ"/><mx:text id="champ_aff"/>
					</td>
					</mx:bloc id="col">
				</tr>				
				</mx:bloc id="ligne">
				</table>
 	  </mx:bloc id="CHECK">
 	  <mx:bloc id="RADIO">
				<table style="width:100%;background:transparent;border: 0px solid black;">
				<mx:bloc id="ligne">
				<tr>
					<mx:bloc id="col">
					<td>
					<mx:checker id="champ"/><mx:text id="champ_aff"/>
					</td>
					</mx:bloc id="col">
				</tr>				
				</mx:bloc id="ligne">
				</table>
 	  </mx:bloc id="RADIO">
	  <mx:bloc id="LISTEN">
	    <table>
	    <mx:bloc id="miniitem">
	      <tr><td>
              <mx:select id="select1"/>
	      </td></tr>
	      </mx:bloc id="miniitem">
	      </table>
	    </mx:bloc id="LISTEN">
	    <mx:bloc id="FILE">
	    	<mx:text id="indication"/><br/>
          	<INPUT type="file" mxattribut="name:name" />
          	<INPUT type="hidden" name="MAX_FILE_SIZE"  mxattribut="VALUE:value"/>
	    	</mx:bloc id="FILE">
	    <mx:bloc id="TAB">
	    <table class="tableausaisie">
	    <tr>
	    <mx:bloc id="titre">
	    <td style="text-align:center;"><mx:text id="libel"/></td>
	    </mx:bloc id="titre">
	    </tr>
	    <mx:bloc id="ligne">
	      <tr>
	      <td><mx:text id="lib"/></td>
	      <mx:bloc id="colonne">
	      <td style="text-align:center;">
	      <mx:bloc id="case_simple">
              <mx:formField id="lacase"/>
	      </mx:bloc id="case_simple">
	      
	      <mx:bloc id="case_select">
              <mx:select id="lacase"/>
	      </mx:bloc id="case_select">
	      
	      
	      </td>
	      </mx:bloc id="colonne">
	      </tr>
	      </mx:bloc id="ligne">
	      </table>
	    </mx:bloc id="TAB">
	    
	    
         <mx:bloc id="LISTEDYN">
         <mx:select id="select1"/>
	      </td><td colspan="1" ></td></tr>
	     	<tr>
	      	<td colspan="1" class="gauche"></td>
    		<td colspan="1" class="aa_gauche"></td>
	     	<td colspan="1" class="bb_gauche">
		<img  mxAttribut="src:img"/>
		Nouvelle valeur:</td>
		<td colspan="1" class="bb_cc">
		<mx:formField id="newEntry"/>
                </mx:bloc id="LISTEDYN">
       	<mx:bloc id="TXT"><mx:formField id="textsimple"/>
      </mx:bloc id="TXT">
      <mx:bloc id="LONGTXT">
      	   <mx:formField id="textlong"/>
      </mx:bloc id="LONGTXT">
      <mx:bloc id="SLIDER">
       		<div class="formx_slider_texte_gauche"><mx:text id="slider_labelg"/></div>
      		<div class="formx_slider_texte_droite"><mx:text id="slider_labeld"/></div>
      		<div class="formx_slider">
      		<div class="slider" class="formx_slider" mxattribut="id:idslider"  tabIndex="1">
				<input class="slider-input" mxattribut="id:idsliderinput" mxattribut="name:idslidername"/>
				<script type="text/javascript">
					var slider_<mx:text id="varsliderid"/> = new Slider(document.getElementById("<mx:text id="idslider2"/>"),document.getElementById("<mx:text id="idsliderinput2"/>"),"horizontal",<mx:text id="isreadonly"/>);
					slider_<mx:text id="varsliderid"/>.setMinimum(<mx:text id="minslider"/>);
					slider_<mx:text id="varsliderid"/>.setMaximum(<mx:text id="maxslider"/>);
					slider_<mx:text id="varsliderid"/>.setValue(<mx:text id="slidervalue"/>);
				</script>
			</div>
			</div>
      </mx:bloc id="SLIDER">
      <mx:bloc id="CAL">
      		<mx:formField id="textCal"/>
      		<img   mxAttribut="src:img"  mXattribut="id:idcal" onclick="document.getElementById('divcal_<mx:text id="id" />').style.display='block';" />
      	<div id="divcal_<mx:text id="id" />" style="display:none;width:230px;" >
      	<a href="#" onclick="document.getElementById('divcal_<mx:text id="id" />').style.display='none';return false;" >Fermer le calendrier</a>
      	</div>	
      	
      </mx:bloc id="CAL">
      </td>
    <td colspan="1" class="droite"></td>
    </tr>
    </mx:bloc id="item">
    
    <mx:bloc id="infoNoValid">
    <tr>
      <td colspan="3">
      </td>
      <td colspan="1">
      <mx:text id="infoNoValid"/>
      </td>
      <td colspan="1">
      </td>
    </tr> 
    </mx:bloc id="infoNoValid">
    
    <mx:bloc id="valid_bouttons">
    <tr>
      <td colspan="3">
      </td>
      <td colspan="1">
      <mx:formField id="etapePrev"/>
      <mx:formField id="etapeNext"/>
      <mx:formField id="etapeCancel"/>
      <mx:formField id="etapeValid"/>
      </td>
      <td colspan="1">
      </td>
    </tr>
    </mx:bloc id="valid_bouttons">
     <mx:bloc id="actions">
    <tr>
      <td colspan="1" class="gauche"></td>
      <td colspan="3" class="titre_actions">
      Actions dÃ©clenchÃ©es
      </td>
      <td colspan="1" class="droite"></td>
    </tr>
    <mx:bloc id="item">
    <tr>
    <td colspan="1" class="gauche"></td>
    <td colspan="3" class="fond_gris">
    <mx:text id="libelle"/>
    <td colspan="1" class="droite"></td>
    </tr>
    </mx:bloc id="item">
    <tr>
      <td colspan="6">
      </td>
     </tr>
     <tr></td></tr>
  </mx:bloc id="actions">
  </mx:bloc id="etape">
  <tr>
  <td ><td/>
  <td ><td/>
  <td style="text-align:right;"><mx:formField id="windowPrint"/><td/>
  <td>

    <mx:formField id="windowClose"/>
  </td>
  </tr>
  
</table>
<mx:hidden id="hidden1" />
<mx:hidden id="hidden2" />
<mx:hidden id="hidden_listen" />
</form>
<script type="text/javascript">
<mx:bloc id="JavaCAL"> 
 if (  typeof( dateChanged<mx:text id="javcalid"/>) != 'function') 
 dateChanged<mx:text id="javcalid"/> = function (calendar) {
 if (calendar.dateClicked) {
      var y = calendar.date.getFullYear();
      var m = addZero(1 + calendar.date.getMonth());     // integer, 0..11
      var d = addZero(calendar.date.getDate());
      var datestr = d + '/' + m + '/' + y
      //date2.print("%Y-%m-%d %H:%M");
      document.getElementById('<mx:text id="javcalid"/>').value = datestr
      document.getElementById('divcal_<mx:text id="javcalid"/>').style.display = 'none'
  }
}

 Calendar.setup(
    	{
    	flat         : 'divcal_<mx:text id="javcalid"/>', // ID of the parent element
    	flatCallback : dateChanged<mx:text id="javcalid"/>           // our callback function

    	}
  		);
 </mx:bloc id="JavaCAL">
 
 <mx:text id="js" />
 </script>
</div>
<div id="formx_infobulle" >
<div id="handler" style="position:absolute;width:100%;height:10px;top:2px;left:0px;border:0px solid black;">
<img style="position:absolute;right:10px;" src="images/close.gif" alt="fermer" onclick="formx_infobulle_cache();"/>
</div>
<div id="formx_infobulle_contenu" />
</div>
<script type="text/javascript">
groupbulle = drag.createSimpleGroup(boxHandle, document.getElementById('formx_infobulle'))
</script>