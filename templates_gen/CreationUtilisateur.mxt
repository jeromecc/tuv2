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

<div id="gestionutilisateurs">
  <center>
    <h4>Gestion des utilisateurs</h4>
    <form method="post" action="index.php">
    Sélection de l'utilisateur : <mx:select id="iduser" />
    <br /><br />
    <mx:bloc id="iduserselect">
      <table summary="Mise en page">
        <tr>
          <td>
            <center>Informations :</center><br />
            <table summary="Formulaire utilisateur">
              <tr><td class="droite">UID : </td><td><mx:formField id="uid" /></td></tr>
              <tr><td class="droite">Nom : </td><td><mx:formField id="fnom" /></td></tr>
              <tr><td class="droite">Prénom : </td><td><mx:formField id="fprenom" /></td></tr>
              <tr><td class="droite">Mail : </td><td><mx:formField id="fmail" /></td></tr>
              <mx:bloc id="password">
                <tr><td class="droite">Password : </td><td><mx:formField id="pwd1" /></td></tr>
                <tr><td class="droite">Vérification : </td><td><mx:formField id="pwd2" /></td></tr>
              </mx:bloc id="password">
            </table><br />
              <center>
              <mx:bloc id="boutonannuler">
                <input name="Annuler" type="image" value="Annuler" src="images/annuler2.gif" style="border: 0px; background-color: transparent;" />
              </mx:bloc id="boutonannuler">
              <mx:bloc id="boutonmodifier">
                <input name="AjouterUtilisateur" type="image" value="AjouterUtilisateur" src="images/ajouter2.gif" style="border: 0px; background-color: transparent;" />
              </mx:bloc id="boutonmodifier">
              </center>
          </td>
        </tr>
      </table>
    </mx:bloc id="iduserselect">
    <mx:hidden id="hidden" />
    </form>
  </center>
  <br />
</div>
