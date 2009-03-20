<div id="droits">
  <center>
  <br />
  <b>Gestion des droits : réglages</b><br /><br />
  <form method="post" action="index.php">
    <mx:hidden id="hidden"/>
    <table summary="Choix">
      <tr><td>Groupe : </td><td><mx:select id="groupe" /></td></tr>
      <tr><td>Action : </td><td><mx:select id="action" /></td></tr>
      <tr><td><mx:text id="nomtype" /></td><td><mx:select id="type" /></td></tr>
    </table>
    <br/>
    <table summary="Réglages des droits">
      <mx:text id="titres" />
      <mx:bloc id="listedroits">	
        <tr>
	  <td><mx:text id="libelle"/></td>
	  <td><mx:text id="description"/></td>
	  <td><mx:checker id="R"/></td>
	  <td><mx:checker id="W"/></td>
	  <td><mx:checker id="M"/></td>
	  <td><mx:checker id="D"/></td>
	  <td><mx:checker id="A"/></td>
	</tr>
      </mx:bloc id="listedroits">
    </table>
    <br/>
    <table summary="Actions">
    <tr>
      <mx:bloc id="boutons">
        <mx:text id="boutonsvalidateur"/>
        <td><input name="MajDroits" type="image" value="Valider" src="images/Ok.gif" style="border: 0px; background-color: transparent;" /></td>
        </form>
        <form method="post" action="index.php">
        <mx:hidden id="hidden2"/>
        <td><input name="Annuler" type="image" value="Annuler" src="images/annuler2.gif" style="border: 0px; background-color: transparent;" /></td>
      </mx:bloc id="boutons">
      </form>
    </tr>
    </table>
  </center>
</div>
