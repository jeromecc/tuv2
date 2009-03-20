<div id="gestiondesgroupes">
  <center>
  <h4>Attribution des groupes à l'application</h4>
  <form method="post" action="index.php">
    <table summary="Affectation des groupes">
      <tr>
        <td style="text-align: center;">Non affecté(s)<br /><br /><mx:select id="nonAffectes" /><br /><br /></td>
        <td style="text-align: center;">Affecté(s)<br /><br /><mx:select id="Affectes" /><br /><br /></td>
      </tr>
      <tr>
        <td style="text-align: center;">
          <mx:bloc id="ajouter">
            <input type="image" src="images/ajouter2.gif" name="Affecter" style="border: 0px; background-color: transparent;" />
          </mx:bloc id="ajouter">
        </td>
        <td style="text-align: center;">
          <mx:bloc id="enlever">
            <input type="image" src="images/Supprimer.gif" name="Enlever" style="border: 0px; background-color: transparent;" />
          </mx:bloc id="enlever">
        </td>
      </tr>
    </table>
    <mx:hidden id="hidden" />
  </form>
  <hr/>
  <h4>Gestion des groupes</h4>
  <mx:bloc id="informations">
    <div id="informations">
      <mx:text id="infos" />
    </div>
  </mx:bloc id="informations">
  <mx:bloc id="erreurs">
    <div id="erreurs">
      <mx:text id="erreurs" />
    </div>
  </mx:bloc id="erreurs">
  <form method="post" action="index.php">
    <mx:text id="gestiongroupe" />
    <mx:select id="listeGroupes" />
    <br /><br />
    <mx:bloc id="nouveau">
      <h4>Ajouter un nouveau groupe</h4>
      Nom du groupe : <input type="text" name="newgroupe" />
      <input name="Ajouter"  type="image" src="images/valider.gif"   value="Ajouter" style="border: 0px; background-color: transparent;" />
    </mx:bloc id="nouveau">
    <mx:hidden id="hidden" />
  </form>
  <br /><br />
  <h4><i>L'appartenance à un groupe est déduite de l'uid, de la
  fonction et des équipes de l'utilisateur connecté.</i></h4>
  </center>
</div>