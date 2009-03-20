<div id="ajouteritem">
  <br />
  Ajouter un item à la liste "<mx:text id="nomListe" />" :
  <br /><br />
  <form method="post" action="index.php">
    <mx:hidden id="hidden" />
    <table>
    <tr><td><mx:text id="nomItem" /></td><td><mx:formField id="nomItemF" /></td></tr>
    <tr><td><mx:text id="placer" /></td><td><mx:select id="placerF" /></td></tr>
    <mx:bloc id="formType">
      <tr><td><mx:text id="type" /></td><td><mx:select id="typeF" /></td></tr>
    </mx:bloc id="formType">
    </table>
    <br />
    <table class="boutons">
      <tr>
	<td>
	  <input name="Valider" type="image" value="Valider" src="images/Ok.gif" />
	</td>
	<td>
	  <input name="Annuler" type="image" value="Annuler" src="images/annuler2.gif" />
	</td>
      </tr>
    </table>
    <br />
  </form>
</div>