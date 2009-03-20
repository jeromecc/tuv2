<div id="gestionDesActeurs">
  <center>
    <br />
    <h4>Gestion des acteurs</h4>
    <br />
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
    <mx:text id="formulaireActeur" />
    <form method="post" action="index.php">
      <mx:select id="listeActeurs" />
      <mx:hidden id="hidden" />
    </form>
    <br /><br /><br />
    <hr />
    <br />
    <h4>Attribution des groupes</h4>
    <br />
    <form method="post" action="index.php">
      Choix du groupe : <mx:select id="listeGroupes" />
      <br /><br />
      <table>
        <tr>
          <td>Non-groupé(s)<br /><br /><mx:select id="nonGroupes" /><br /><br /></td>
          <td>Groupé(s)<br /><br /><mx:select id="Groupes" /><br /><br /></td>
        </tr>
        <tr>
          <td>
            <mx:bloc id="ajouter">
              <input type="image" src="images/ajouter2.gif" name="Ajouter" />
            </mx:bloc id="ajouter">
          </td>
          <td>
            <mx:bloc id="enlever">
              <input type="image" src="images/Supprimer.gif" name="Enlever" />
            </mx:bloc id="enlever">
          </td>
        </tr>
      </table>
      <mx:hidden id="hidden" />
    </form>
  </center>
</div>