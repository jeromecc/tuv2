<div id="fusionpatients">
  <form method="post" action="index.php">
    <h4>Fusions des patients</h4><br />
    <mx:bloc id="informations"><div id="informations"><mx:text id="infos" /></div></mx:bloc id="informations">
    <mx:bloc id="erreurs"><div id="erreurs"><mx:text id="erreurs" /></div></mx:bloc id="erreurs">
    <table>
      <tr><td>Patients manuels<br /><br /></td><td>Patients automatiques<br /><br /></td></tr>
      <tr><td></td><td class="gauche">
        <table class="gauche"><tr>
	<td>Filtre sur le nom : </td><td><mx:formField id="nom" /><input type="submit" name="Valider" value="Valider" /></td></tr>
	<td>Filtre sur le pr&eacute;nom : </td><td><mx:formField id="prenom" /><input type="submit" name="Valider" value="Valider" /></td></tr>
	<tr><td>Date d'admission : </td><td><mx:select id="date" class="petit" /></td></tr></table>
      </td></tr>
      <tr><td><mx:select id="manuels" /></td><td><mx:select id="automatiques" /></td></tr>
      <mx:hidden id="hidden" />
      <tr><td>
        <mx:bloc id="supprimer"><input name="Supprimer"  type="image" src="images/Supprimer.gif"   value="Supprimer" style="border: 0px; background-color: transparent;" /></mx:bloc id="supprimer">
      </td><td></td></tr>
      <tr><td colspan="2">
        <mx:bloc id="fusionner"><input name="Fusionner"  type="image" src="images/fusionner2.gif"   value="Fusionner" style="border: 0px; background-color: transparent;" /></mx:bloc id="fusionner">
      </td></tr>
    </table>
  </form>
  <br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
</div>
