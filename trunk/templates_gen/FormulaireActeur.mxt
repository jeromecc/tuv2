<div id="formulaireActeur">
  <form method="post" action="index.php">
    <b><mx:text id="titre" /></b>
    <br />
    <br />
    <table summary="Formulaire acteur">
      <tr><td class="droite">Nom : </td><td><mx:formField id="nomActeur" /></td></tr>
      <tr><td class="droite">Mail : </td><td><mx:formField id="mailActeur" /></td></tr>
      <tr><td class="droite">Password : </td><td><mx:formField id="pwdActeur" /></td></tr>
    </table>
    <br />
    <mx:hidden id="hidden" />
    <mx:bloc id="annuler">
      <input type="image" src="images/annuler2.gif" name="AnnulerActeur" value="Annuler" />
    </mx:bloc id="annuler">
    <mx:bloc id="modifier">
      <input type="image" src="images/modifier2.gif" name="ModifierActeur" value="Modifier" />
    </mx:bloc id="modifier">
    <mx:bloc id="ajouter">
      <input type="image" src="images/ajouter2.gif" name="AjouterActeur" value="Créer" />
    </mx:bloc id="ajouter">
    <mx:bloc id="supprimer">
      <input type="image" src="images/Supprimer.gif" name="SupprimerActeur" value="Supprimer" />
    </mx:bloc id="supprimer">
  </form> 
  <mx:bloc id="agirETQ">
    <form method="post" action="index.php" target="_new">
      <mx:hidden id="hiddenIdActeur" />
      <input type="image" src="images/agir.gif" name="agirEnTantQue" value="agirEnTantQue" />
    </form> 
  </mx:bloc id="agirETQ">
  <br />
  <br />
</div>