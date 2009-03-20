<div id="ajouterattendu">
  <div id="messagesviewtitre">
    <table class="w100">
      <tr id="handlep"><th><mx:text id="titre" /></th><th class="droite"><a mXattribut="href:lienClose"><mx:image id="imgClose"/><br /></th></tr>
    </table>
  </div>
  <form method="post" action="index.php">
    <table>
      <tr>
        <td class="droite">
          Sexe :
        </td>
        <td>
          <mx:select id="sexe" style="width: 345px;" />
        </td>
      </tr>
      <tr>
        <td class="droite">
          Prénom :
        </td>
        <td>
          <mx:formField id="prenom" style="width: 345px;" />
        </td>
      </tr>
      <tr>
        <td class="droite">
          Nom :
        </td>
        <td>
          <mx:formField id="nom" style="width: 345px;" />
        </td>
      </tr>
      <tr>
        <td class="droite">
          Age :
        </td>
        <td>
          <mx:formField id="age" cols="40" rows="4" style="width: 345px;"/>
        </td>
      </tr>
      <tr>
        <td class="droite">
          Médecin :
        </td>
        <td>
          <mx:select id="medecin" style="width: 345px;" />
        </td>
      </tr>
      <tr>
        <td class="droite">
          Adresseur :
        </td>
        <td>
          <mx:select id="adresseur" style="width: 345px;" />
        </td>
      </tr>
      <tr>
        <td class="droite">
	  Observations :
        </td>
        <td>
          <mx:formField id="observations" cols="40" rows="4" style="width: 345px;" />
        </td>
      </tr>
      <tr>
	<td colspan="2" align="center">
	  <mx:bloc id="ajouter">
	    <input name="ValiderAjouter"  type="image" src="images/ajouter2.gif"   value="ValiderAjouter" style="border: 0px; background-color: transparent;" />
          </mx:bloc id="ajouter">
	  <mx:bloc id="modifier">
	    <input name="ValiderModifier"  type="image" src="images/modifier2.gif"   value="ValiderModifier" style="border: 0px; background-color: transparent;" />
          </mx:bloc id="modifier">
	  <form method="post" action="index.php">
	  <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" style="border: 0px; background-color: transparent;" />
      </tr>
    </table>
    <mx:hidden id="hidden" />
  </form>
</div>
