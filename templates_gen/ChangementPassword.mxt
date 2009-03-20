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
    <h4>Changement de mot de passe</h4>
    <form method="post" action="index.php">
      <table summary="Mise en page">
        <tr>
          <td>
            <table summary="Formulaire password">
              <tr><td class="droite">UID : </td><td><b><mx:text id="uid" /></b></td></tr>
              <tr><td class="droite">Password actuel : </td><td><mx:formField id="pwd" /></td></tr>
              <tr><td class="droite">Nouveau password : </td><td><mx:formField id="pwd1" /></td></tr>
              <tr><td class="droite">Vérification : </td><td><mx:formField id="pwd2" /></td></tr>
            </table><br />
              <center>
              <mx:bloc id="boutonannuler">
                <input name="Annuler" type="image" value="Annuler" src="images/annuler2.gif" style="border: 0px; background-color: transparent;" >
              </mx:bloc id="boutonannuler">
              <mx:bloc id="boutonmodifier">
                <input name="Modifier" type="image" value="Modifier" src="images/modifier2.gif" style="border: 0px; background-color: transparent;" >
              </mx:bloc id="boutonmodifier">
              </center>
          </td>
        </tr>
      </table>
    <mx:hidden id="hidden" />
    </form>
  </center>
  <br />
</div>
