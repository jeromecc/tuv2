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

<mx:text id="confirmation" />

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
            <table summary="Informations utilisateur">
              <tr><td class="droite">UID : </td><td><b><mx:text id="uid" /></b></td></tr>
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
                <input name="Modifier" type="image" value="Modifier" src="images/modifier2.gif" style="border: 0px; background-color: transparent;" />
              </mx:bloc id="boutonmodifier">
              <mx:bloc id="boutonsupprimer">
                <input name="Supprimer" type="image" value="Supprimer" src="images/Supprimer.gif" style="border: 0px; background-color: transparent;" />
              </mx:bloc id="boutonsupprimer">
              </center>
          </td>
          <td>
            <center>Groupes disponibles :</center><br />
            <mx:select id="groupesdispos" /><br /><br />
            <center>
            <mx:bloc id="boutonajouter">
              <input name="Ajouter" type="image" value="Ajouter" src="images/ajouter2.gif" style="border: 0px; background-color: transparent;" />
            </mx:bloc id="boutonajouter">
            </center>
          </td>
          <td>
            <center>Groupes affectés :</center><br />
            <mx:select id="groupesaffect" /><br /><br />
            <center>
            <mx:bloc id="boutonenlever">
              <input name="Enlever" type="image" value="Enlever" src="images/Supprimer.gif" style="border: 0px; background-color: transparent;" />
            </mx:bloc id="boutonenlever">
            </center>
          </td>
        </tr>
      </table>
    </mx:bloc id="iduserselect">
    <mx:hidden id="hidden" />
    </form>
    <mx:bloc id="boutonagirETQ">
      <form method="post" action="index.php" target="_new">
        <input target="_new" name="agirEnTantQue" type="image" value="agirEnTantQue" src="images/agir.gif" style="border: 0px; background-color: transparent;" />
        <mx:hidden id="hidden" />
      </form>
    </mx:bloc id="boutonagirETQ">
  </center>
  <br />
</div>
