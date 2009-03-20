<div id="saisieComplementActe">
<center><h3><mx:text id="titreFormulaire"/></h3></center>

<form method="post" action="index.php">
<mx:hidden id="hidden"/>
<table align="center">
	<tr>
	 	<td><mx:text id="complementActeLibQte"/></td>
		<td><mx:formField id="complementActeQte" style="border: 0px; background-color: transparent;" /></td>
	</tr>
	<!--- <tr>
	 	<td  align="right" valign="top"><mx:text id="complementActeLibPeriod"/></td>
		<td><mx:select id="complementActePeriod"/></td>
	</tr> --->
	<tr>
	 	<td align="center">
		<mx:formField id="imgOK"/> 
		</td>
	    <td align="center"><mx:formField id="imgAnnul" style="border: 0px; background-color: transparent;" /></td>
	</tr>
</table>
</form>
</div>
