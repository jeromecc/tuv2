<div id="modifieritem">
  <br />
  Modification de l'item "<mx:text id="oldNomItem" />" de la liste "<mx:text id="nomListe" />" :
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
    <table>
      <tr>
	<td class="boutons">
          <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" />
        </td>
	<td>
	  <input name="Modifier"  type="image" src="images/modifier2.gif"   value="Modifier" />
	</td>
        <mx:bloc id="supprimer">
	  <td>
	    <input name="Supprimer"  type="image" src="images/Supprimer.gif" value="Supprimer" />
          </td>
	</mx:bloc id="supprimer">
      </tr>
    </table>
    <br />
    <mx:select id="select" />
  </form>
</div>