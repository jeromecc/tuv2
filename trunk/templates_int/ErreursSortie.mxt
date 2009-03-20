<iframe style="border: 0px; display: block; position: absolute; 
                 left: 180px; top: 175px; width: 542px; height: 150px;" ></iframe>
<div id="erreurssortie">
  <center>
  <div id="erreurssortietitre">
    <table>
      <tr><th>Impossible de faire sortir ce patient</th><th class="droite"><a mXattribut="href:lienCloseErreurs"><mx:image id="imgCloseErreurs"/></th></tr>
    </table>
  </div>
  <form method="post" action="index.php">
    <table>
      <tr><th>Nom</th><th>Description</th></tr>
      <mx:bloc id="erreur">
        <tr><td><mx:text id="nom" /></td><td><mx:text id="description" /></td></tr>
      </mx:bloc id="erreur">
    </table>
    <table>
      <tr>
          <mx:bloc id="forcer">
            <td>
            <span style="vertical-align:top;">Date de sortie : <mx:text id="date" /></span>
            </td>
            <td>
            <input name="Valider" type="image" src="images/forcer.gif" value="Forcer" style="border: 0px; background-color: transparent;" />
            </td>
            <mx:hidden id="hiddenForcer" />
          </mx:bloc id="forcer">
  </form>
  <form method="post" action="index.php">
  		  <td>
          <input name="Annuler" type="image" src="images/annuler2.gif" value="Annuler" style="border: 0px; background-color: transparent;" />
          </td>
          <mx:hidden id="hidden" />
  </form>
      </tr>
    </table>
  </center>
</div>
