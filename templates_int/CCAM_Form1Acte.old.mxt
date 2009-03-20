<div id="form1Acte">
<mx:hidden id="hidden2"/>
<table>
	<caption><b><mx:text id="titreEnCours"/></b></caption>
	<tr>
	 	<td align="right"><mx:text id="titreCodeActe"/></td>
		<td><mx:text id="codeActe"/></td>
	</tr>
	<tr>
	 	<td  align="right" valign="top"><mx:text id="titreLibActe"/></td>
		<td><mx:formField id="libActe"/>
			<mx:text id="libVisuActe"/>
		</td>
	</tr>
	<tr>
	 	<td  align="right" valign="top"><mx:text id="titreQte"/></td>
		<td><mx:formField id="qte"/></td>
	</tr>
	<tr>
	 	<td  align="right" valign="top"><mx:text id="titrePeriodicite"/></td>
		<td><mx:select id="periodicite"/></td>
	</tr>
	<tr>
	 	<td colspan="2" align="center"><br><mx:text id="confirmSuppr"/></td>
	</tr>
	<mx:bloc id="NGAP">
	<tr>
	 	<td colspan="2" align="center"><mx:text id="formNGAP"/></td>
	</tr>
	</mx:bloc id="NGAP">
	<tr>
	 	<td colspan=2"" align="center">
		<mx:bloc id="validerActe">
	    <input name="OK" type="image" value="OK" src="images/Ok.gif">
	    </mx:bloc id="validerActe">
		
		<mx:bloc id="annulerActe">
	    <input name="annuler" type="image" value="annuler" src="images/annuler2.gif">
	    </mx:bloc id="annulerActe">
		</td>
	</tr>
</table>
</div>
