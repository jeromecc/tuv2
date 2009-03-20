<div id="ccam">
  <center>
  <br><b><mx:text id="titreFormulaire"/></b><p>
  <form method="post" action="index.php">
    <mx:hidden id="hidden"/>
    
	<table>
    <tr>
		<mx:bloc id="action">
		<td><mx:checker id="action"/><mx:text id="libAction"/></td>
	    </mx:bloc id="action">
	</tr>
    </table>
    <p>
    
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
	
	<mx:text id="form1Acte"/>
	
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
			<td>
				<mx:bloc id="btnAjouterSelection1">
			    <input name="ajouterSelection1" type="image" value="ajouterSelection1" 
					src="images/ajouter2.gif">
			    </mx:bloc id="btnAjouterSelection1">
				
				<mx:bloc id="btnModifierSelection1">
			    <input name="modifierSelection1" type="image" value="modifierSelection1" 
					src="images/modifier2.gif">
			    </mx:bloc id="btnModifierSelection1">
				
				<mx:bloc id="btnSupprimerSelection1">
			    <input name="supprimerSelection1" type="image" value="supprimerSelection1" 
					src="images/Supprimer.gif">
			    </mx:bloc id="btnSupprimerSelection1">
			</td>
		</tr>
		</table>
	</tr>
	<!--- <tr><td align="center" colspan="2" valign="top">
	 		<mx:text id="titreSelection1"/><mx:select id="idListeSelection1"/>
			
			
			</td>
		</tr> --->
	<tr><th><mx:text id="titreDispo"/></th><th><mx:text id="titreAffecte"/></th></tr>
   	<tr><td><mx:text id="titreSelection2"/><mx:select id="idListeSelection2"/></td></tr>
	<tr><td align="right"><mx:select id="listeGauche"/></td>
		<td><mx:select id="listeDroite"/></td>
	</tr>
    <tr><td valign="top"><mx:text id="commentaireGauche"/></td>
      <td valign="top"><mx:text id="commentaireDroite"/></td>
    </tr>
	<tr>
	    <td align="center">
		<mx:bloc id="flDroite">
	    <input name="aDroite" type="image" value="aDroite" src="images/ADroite.gif">
	    </mx:bloc id="flDroite">
	    </td>
	    <td align="center">
	    <mx:bloc id="flGauche">
	    <input name="aGauche" type="image" value="aGauche" src="images/AGauche.gif">
	    </mx:bloc id="flGauche">
	    
		<mx:bloc id="btnAjouter">
	    <input name="ajouterActe" type="image" value="ajouterActe" src="images/ajouter2.gif">
	    </mx:bloc id="btnAjouter">
		
		<mx:bloc id="btnModifier">
	    <input name="modifierActe" type="image" value="modifierActe" src="images/modifier2.gif">
	    </mx:bloc id="btnModifier">

		<mx:bloc id="btnSupprimer">
	    <input name="supprimerActe" type="image" value="supprimerActe" src="images/Supprimer.gif">
	    </mx:bloc id="btnSupprimer">
		</td>
    </tr>
    </table>
  </form>
  </center>
</div>
