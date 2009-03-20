<div id="gestiondesmenus">
  <center>
    <h4>Gestion des menus</h4>
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
    <mx:text id="action" />
    <table summary="Mise en page">
      <mx:bloc id="menu">
        <tr>
          <td><a mXattribut="href:lien"><mx:image id="img"/></a></td>
          <td><mx:text id="nom" /></td>
          <td><a mXattribut="href:lienMonter"><mx:image id="imgMonter"/></a></td>
          <td><a mXattribut="href:lienDescendre"><mx:image id="imgDescendre"/></a></td>
          <td><a mXattribut="href:lienModifier"><mx:image id="imgModifier"/></a></td>
          <td><a mXattribut="href:lienSupprimer"><mx:image id="imgSupprimer"/></a></td>
        </tr>
        <mx:bloc id="item">
          <tr>
            <td></td>
            <td><mx:text id="nom" /></td><td colspan="4"></td>
            <td><a mXattribut="href:lienMonter"><mx:image id="imgMonter"/></a></td>
            <td><a mXattribut="href:lienDescendre"><mx:image id="imgDescendre"/></a></td>
            <td><a mXattribut="href:lienModifier"><mx:image id="imgModifier"/></a></td>
            <td><a mXattribut="href:lienSupprimer"><mx:image id="imgSupprimer"/></a></td>
          </tr>
        </mx:bloc id="item">  
      </mx:bloc id="menu">
    </table>
    <br />
    <a mXattribut="href:lienAjouter"><mx:image id="imgAjouter"/></a>
    </mx:bloc id="maj">
  </center>
</div>
