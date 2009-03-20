<div id="items">
  <br />
  <mx:text id="nomListe" /> :
  <br /><br />
  <form method="post" action="index.php">
    <mx:hidden id="hidden" />
    <table summary="Formulaire item complexe">
      <tr><td class="col1"><mx:text id="nomItem" /></td><td><mx:formField id="nomItemF" /></td></tr>
      <tr><td class="col1"><mx:text id="placer" /></td><td><mx:select id="placerF" /></td></tr>
      <tr><td class="col1"><mx:text id="code" /></td><td><mx:formField id="codeF" /></td></tr>
    </table>
    <br />
    <table class="boutons" summary="Actions">
      <tr>
	  <mx:bloc id="ajouter">
	    <td>
	      <input name="Ajouter"  type="image" src="images/ajouter2.gif"   value="Ajouter" style="border: 0px; background-color: transparent;" />
        </td>
	  </mx:bloc id="ajouter">
	  <mx:bloc id="modifier">
	    <td>
	      <input name="Modifier"  type="image" src="images/modifier2.gif"   value="Modifier" style="border: 0px; background-color: transparent;" />
	    </td>
	  </mx:bloc id="modifier">
      <td class="boutons">
	    <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" style="border: 0px; background-color: transparent;" />
      </td>
      <mx:bloc id="supprimer">
	    <td>
	      <input name="Supprimer"  type="image" src="images/Supprimer.gif"  value="Supprimer" style="border: 0px; background-color: transparent;" />
        </td>
	  </mx:bloc id="supprimer">
      </tr>
    </table>
  </form>
  <br />
</div>
