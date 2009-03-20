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
	
	<table>
	<tr><th><mx:text id="titreDispo"/></th>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<th><mx:text id="titreAffecte"/></th>
	</tr>
   	<tr align="center"><td><mx:select id="listeGauche"/></td>
		<td><mx:image id="plus"/></td>
		<td><mx:text id="formNGAP"/></td>
		<td>
			<mx:bloc id="egal">
		    <input name="egal" type="image" value="egal" src="images/Egal.gif">
		    </mx:bloc id="egal">
	    </td>
		<td><mx:select id="listeDroite"/></td>
	</tr>
    <tr>
	    <td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	    <td align="center">
		    <mx:bloc id="flGauche">
		    <input name="aGauche" type="image" value="aGauche" src="images/AGauche.gif">
		    </mx:bloc id="flGauche"> 
		</td>
    </tr>
    </table>
  </form>
  </center>
</div>
