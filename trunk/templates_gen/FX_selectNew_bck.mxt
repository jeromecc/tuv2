<div id="formXselect">
	<form method="post">
	<div id="newform_blocvalid">
	<mx:formField id="windowClose" style="position:absolute;right:400px;" />
	</div>
	Création d'un formulaire
 	<mx:bloc id="groupe">
 	<div class="newform_groupe" style="display:block;">
 		<div style="display:block;" class="newform_groupe_titre" mXattribut="id:id_head" mXattribut="onClick:codeOnClick">
			<mx:text id="titregroupe"  /><span class="newform_boutonOpen" style="margin:3px;padding:-3px:" > Ouvrir</span>
		</div>
		<div class="newform_groupe_body" mXattribut="id:id_body" >
			<table>
  			<mx:bloc id="SQUELETTE">
  			<tr>
  			<td class="new_form_col1" ><mx:text id="titre" /></td>
  			<td class="new_form_col2"><mx:text id="objet" /></td>
   			<td class="new_form_col3"><mx:checker id="check"/></td>
  			</tr>
    		</mx:bloc id="SQUELETTE">
    		<tr>
  			<td>&nbsp;</td>
  			<td><span class="newform_boutonClose" mXattribut="onClick:codeOnClickClose">Fermer le panneau</span>
  			<span class="newform_boutonClose" mXattribut="onClick:codeOnClickCheckAll">Cocher tout</span>
  			</td>
   			<td>&nbsp;</td>
   			</tr>
    		</table>
    	</div>
  	</div>
  	</mx:bloc id="groupe">
  	<br />
  	<div id="newform_lastligne">
  	<div id="newform_blocvalid">
  		<mx:formField id="selCancel" />
  		<mx:formField id="selValid" />
  	</div>
  	</div>
	<mx:hidden id="hidden1" />
	<mx:hidden id="hidden2" />
	</form>
</div> 



<script type="text/javascript">
function checkAllIn(idGroupe) {
ele = document.getElementById(idGroupe);
eles = ele.getElementsByTagName("input");
for(var i=0 ; i<eles.length ; i++) {
	if ( eles[i].type =="checkbox" )
		eles[i].checked = "checked" ;

}



}
</script>