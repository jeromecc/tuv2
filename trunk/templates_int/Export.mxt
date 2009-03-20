<div id="export">
  <h4>Module d'export</h4>
  <form method="post" action="index.php">
    Date d'admission comprise entre le <mx:select id="date1" /> et le <mx:select id="date2" /> exclu.<br />
   <mx:checker id="filtre1"/> Tous <mx:checker id="filtre2"/> Non-UHCD <mx:checker id="filtre3"/> UHCD<br />
    <mx:hidden id="hidden" />
    <input type="image" src="images/exporter.gif" name="Chercher" value="Chercher" alt="Exporter" style="border: 0px; background-color: transparent;" /><br />
  </form>
  <mx:bloc id="donnees">
    <br />
    Résultat : <b><mx:text id="nombre" /></b> <mx:text id="resultat" />.<br />
    Avec le clic droit de la souris, enregistrez ce <a mXattribut="href:lienExport" target="_new">lien</a> pour les rapatrier sur votre machine.
  </mx:bloc id="donnees">
  <br /><br />
</div>
