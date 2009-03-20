<div id="droits">
  <center>
  <br />
  <mx:text id="bonus" />
  <b>Gestion des droits : attribution</b><br /><br />
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
  <form method="post" action="index.php">
    <mx:hidden id="hidden"/>
    <table summary="Choix">
      <tr><td>Groupe : </td><td><mx:select id="groupe" /></td></tr>
      <tr><td>Action : </td><td><mx:select id="action" /></td></tr>
      <tr><td><mx:text id="nomtype" /></td><td><mx:select id="type" /></td></tr>
    </table>
    <br/>
    <table summary="Attribution"><tr><th>Droits disponibles</th><th>Droits de ce groupe</th></tr>
    <tr><td><mx:select id="tous" /></td><td><mx:select id="droits" /></td></tr>
    <tr>
    <td align="center">
    <mx:bloc id="boutonsajouter">
      <input name="Ajouter" type="image" value="Ajouter" src="images/ajouter2.gif" style="border: 0px; background-color: transparent;" />
    </mx:bloc id="boutonsajouter">
    </td>
    <td align="center">
    <mx:bloc id="boutonsenlever">
      <input name="Enlever" type="image" value="Enlever" src="images/Supprimer.gif" style="border: 0px; background-color: transparent;" />
    </mx:bloc id="boutonsenlever">
    </td>
    </tr>
    </table>
  </form>
  <br/>
  <mx:bloc id="creation">
    <hr/>
    <h4>Création d'un nouveau droit</h4>
    <form method="post" action="index.php">
      Libelle : <mx:formField id="libelle" size="16" maxlength="64" /> Description : <mx:formField id="description" size="40" maxlength="255" />
      <mx:hidden id="hidden"/>
      <input type="image" name="CreerDroit" value="CreerDroit" src="images/valider.gif" style="border: 0px; background-color: transparent;" />
    </form>
    <br/><br/>
  </mx:bloc id="creation">
  <mx:bloc id="suppression">
    <hr/>
    <h4>Suppression d'un droit</h4>
    <form method="post" action="index.php">
      <mx:select id="libelles" />
      <mx:hidden id="hidden"/>
      <br />
      <input type="image" name="SupprimerDroit" value="SupprimerDroit" src="images/Supprimer.gif" style="border: 0px; background-color: transparent;" />
    </form>
    <br/><br/>
  </mx:bloc id="suppression">
  <br /><br />
  </center>
</div>
