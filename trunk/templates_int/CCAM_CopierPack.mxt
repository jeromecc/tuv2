<div id="ccam">
  <center>
  <br><b><mx:text id="titreFormulaire"/></b><p>
  <form method="post" action="index.php">
    <mx:hidden id="hidden"/>
   
	<mx:bloc id="informations">
	<div id="informations">
	  <mx:text id="infos" />
	</div>
	</mx:bloc id="informations">
	<mx:bloc id="erreurs">
	<div id="erreurs">
	  <mx:text id="errs" />
	</div>
	</mx:bloc id="erreurs">
	
	<table>
	<tr><td align="center" colspan="2" valign="top">
	 	<table>
		<tr>
			<td align=right><mx:text id="titreSelection0"/></td>
			<td><mx:select id="idListeSelection0"/></td>
		</tr>
		<tr>
			<td align=right><mx:text id="titreSelection1"/></td>
			<td><mx:select id="idListeSelection1"/></td>
		</tr>
		</table>
	</tr>

	<tr><th><mx:text id="titreDispo"/></th><th><mx:text id="titreAffecte"/></th></tr>
	<tr><td valign="top"><mx:text id="listeGauche"/></td>
		<td><mx:select id="listeDroite"/></td>
	</tr>
	<tr>
	    <td></td>
		<td align="center">
			<mx:bloc id="validerCopier">
	        <input name="OK" type="image" value="OK" src="images/Ok.gif">
	        </mx:bloc id="validerCopier">
		</td>
    </tr>
    </table>
  </form>
  </center>
</div>
