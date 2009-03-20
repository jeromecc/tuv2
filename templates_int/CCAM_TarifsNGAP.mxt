<center>
<h3>Lettres-clé NGAP</h3>

<form method="post" action="index.php">
<mx:hidden id="hidden"/>
<mx:hidden id="hidden2"/>

<mx:bloc id="informations">
	<div id="informations"><mx:text id="infos"/></div><p>
</mx:bloc id="informations">

<mx:bloc id="erreurs">
	<div id="erreurs"><mx:text id="errs"/></div><p>
</mx:bloc id="erreurs">

<mx:bloc id="modifier">
<mx:formField id="imgModifier"/>
</mx:bloc id="modifier">

<mx:bloc id="maj">
<mx:formField id="imgCalcul"/>
</mx:bloc id="maj"><p>

<table align="center"  border="1" style="border-collapse: collapse;">
<tr>
	<th>Lettres-clé<br>locales</th>
	<th>Correspondances<br>nationales</th>
	<th>Tarifs</th>
</tr>
<mx:bloc id="ligneLC">
<tr>
	<td align="center"><mx:text id="LC"/></td>
	<td align="center"><mx:text id="LCnat"/><mx:formField id="LCnat"/></td>
	<td align="right"><mx:text id="tarif"/><mx:formField id="tarif"/></td>
</tr>
</mx:bloc id="ligneLC">
</table><p>

<mx:bloc id="valider">
	<mx:formField id="imgValider"/>
	<mx:formField id="imgAnnuler"/>
</mx:bloc id="valider">
</form>
</center>


