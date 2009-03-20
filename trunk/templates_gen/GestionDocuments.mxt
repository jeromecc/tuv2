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
<div id="listedocuments">
  <center>
  <h4>Gestion des documents</h4>
  <mx:bloc id="categorie">
    <table summary="Liste des documents">
      <tr><th colspan="3"><mx:text id="titre" /></th></tr>
      <mx:bloc id="document">
        <mx:text id="ligne" />
          <td class="col1"><mx:text id="nomDocument" /></td>
          <td class="col2">Dernière modification le <mx:text id="dateModification" /></td>
          <td class="col3">
            <a mXattribut="href:modDoc"><mx:image id="imgMod"/></a>
            <a mXattribut="href:voirDoc" target="_new"><mx:image id="imgVoir"/></a>
          </td>
        </tr>
      </mx:bloc id="document">
    </table>
    <br />
  </mx:bloc id="categorie">
  </center>
</div>