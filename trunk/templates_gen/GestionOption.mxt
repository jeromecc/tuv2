<div id="gestionoption">
  <h4><mx:text id="titre" /></h4>
  <mx:bloc id="erreurs">
    <div id="erreurs">
      <mx:text id="errs" />
    </div>
  </mx:bloc id="erreurs">
  <form method="post" action="index.php">
    <table summary="Formulaire option">
      <tr>
        <td class="droite">Libellé :</td>
        <td><mx:formField id="libelle" size="40" maxlength="64" style="width: 300px;" /></td>
      </tr>
      <tr>
        <td class="droite">Description :</td>
        <td><mx:formField id="description" size="40" maxlength="255" style="width: 300px;" /></td>
      </tr>
      <tr>
        <td class="droite">Administrateur :</td>
        <td style="text-align: left;"><mx:checker id="administrateur" /></td>
      </tr>
      <tr>
        <td class="droite">Catégorie :</td>
        <td><mx:select id="categories" style="width: 300px;" /></td>
      </tr>
      <mx:bloc id="nouvelle">
        <tr>
          <td class="droite">Ou nouvelle :</td>
          <td><mx:formField id="nouvelle" size="40" maxlength="64" style="width: 300px;" /></td>
        </tr>
      </mx:bloc id="nouvelle">
      <tr>
        <td class="droite">Type :</td>
        <td><mx:select id="types" style="width: 300px;" /></td>
      </tr>
      <mx:bloc id="choix">
        <tr>
          <td class="droite">Choix :</td>
          <td><mx:formField id="tchoix" size="40" maxlength="64" style="width: 300px;" /></td>
        </tr>
      </mx:bloc id="choix">
    </table>
    <br />
    <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" title="Annuler" style="border: 0px; background-color: transparent;" />
    <mx:bloc id="modifier">
      <input name="Modifier"  type="image" src="images/modifier2.gif" value="Modifier" title="Modifier" style="border: 0px; background-color: transparent;" />
    </mx:bloc id="modifier"> 
    <mx:bloc id="ajouter">
      <input name="Ajouter"  type="image" src="images/ajouter2.gif" value="Ajouter" title="Ajouter" style="border: 0px; background-color: transparent;" />
    </mx:bloc id="ajouter">
    <mx:hidden id="hidden" />
  </form>
</div>
