<div id="ccam">
<center>
<br><b>Contrôle de la validité des actes (<mx:text id="versionCCAM"/>)</b><p>
<form method="post" action="index.php">
<mx:hidden id="hidden"/><mx:hidden id="hidden2"/>
 
<mx:bloc id="informations">
<div id="informations">
  <mx:text id="infos"/>
</div>
</mx:bloc id="informations">

<table>
  <tr>
    <mx:bloc id="action">
    <td><mx:checker id="action"/><mx:text id="libAction"/></td>
    </mx:bloc id="action">
  </tr>
</table>
<p>

<mx:bloc id="existeActes">
<table>
<tr><th><mx:text id="libelleTypeActes"/></th><th>Remplacer ou supprimer l'acte '<mx:text id="codeSelectionne"/>' 
    dans les différentes listes
  </th>
</tr>
<tr align="left">
  <td></td>
  <td>
    Actes commençant par : <mx:formField id="debCodeActe"/><br>
    <div id="infos">Les actes en vert sont déjà utilisés dans la liste restreinte</div>
  </td>
</tr>
<tr align="left">
  <td width="50%"><mx:select id="listeActesInvalides"/><br>
   <mx:text id="libelleActe"/><mx:text id="tarif"/><mx:text id="listePacks"/><mx:text id="listeDiags"/>
  </td>
	<td valign="top">
    <mx:select id="listeARemplacer"/><br>
    <mx:text id="libelleARemplacer"/><mx:text id="tarifNvx"/><p>
    <center><mx:formField id="imgValiderRemplacer"/></center>
  </td>
</tr>
</table>
</mx:bloc id="existeActes">
<mx:bloc id="nonExisteActes">
<b>Tous les actes de la liste restreinte sont valides par rapport à la <mx:text id="versionCCAM"/></b>
</mx:bloc id="nonExisteActes">
</center>
</form>
</div>
