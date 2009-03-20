<div id="gestionmenu">
  <h4><mx:text id="titre" /></h4>
  <mx:bloc id="erreurs">
    <div id="erreurs">
      <mx:text id="errs" />
    </div>
  </mx:bloc id="erreurs">
  <form method="post" action="index.php">
    <table summary="Formulaire menu">
      <tr>
        <td class="droite">Libellé :</td>
        <td><mx:formField id="libelle" size="40" maxlength="64" style="width: 300px;" /></td>
      </tr>
      <tr>
        <td class="droite">Type :</td>
        <td><mx:select id="types" style="width: 300px;" /></td>
      </tr>
      <tr>
        <td class="droite">Clé :</td>
        <td><mx:formField id="cle" size="40" maxlength="64" style="width: 300px;" /></td>
      </tr
      <tr>
        <td class="droite">Option :</td>
        <td><mx:select id="options" style="width: 300px;" /></td>
      </tr>
      <mx:bloc id="valeur">
        <tr>
          <td class="droite">Valeur :</td>
          <td><mx:select id="svaleurs" /><mx:formField id="tvaleurs" size="40" maxlength="64" style="width: 300px;" /></td>
        </tr>
      </mx:bloc id="valeur">
      <tr>
        <td class="droite">Droit :</td>
        <td><mx:select id="droits" style="width: 300px;" /></td>
      </tr>
      <tr>
        <td class="droite">Etat :</td>
        <td><mx:select id="etats" style="width: 300px;" /></td>
      </tr>
      <tr>
        <td class="droite">Classe :</td>
        <td><mx:select id="classes" style="width: 300px;" /></td>
      </tr>
      <mx:bloc id="arguments">
        <tr>
          <td class="droite">Arguments :</td>
          <td><mx:formField id="arguments" size="40" maxlength="128" style="width: 300px;" /></td>
        </tr>
      </mx:bloc id="arguments">
      <mx:bloc id="code">
        <tr>
          <td class="droite">Code :</td>
          <td><textarea cols=40 rows=5 name="code" style="width: 300px;" ><mx:text id="code" /></textarea></td>
        </tr>
      </mx:bloc id="code">
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