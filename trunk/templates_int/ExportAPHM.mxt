<div id="export">
  <h4>Module d'export</h4>
  <form method="post" action="index.php">
    Date d'admission comprise entre le <mx:select id="date1" /> et le <mx:select id="date2" /> exclu.<br />
    <mx:hidden id="hidden" />
     OU<br />
    IdPassage : <input type="text" name="idpassage"/>
    <br />
    <input type="image" src="images/exporter.gif" name="Chercher" value="Chercher" alt="Exporter" style="border: 0px; background-color: transparent;" /><br />
  </form>
  <mx:bloc id="donnees">
    <br />
    Résultat : <b><mx:text id="nombre" /></b> <mx:text id="resultat" />.<br />
    <span style="font-weight:bold;">CCAM:</span>Avec le clic droit de la souris, enregistrez ce <a mXattribut="href:lienExport" target="_new">lien</a> pour les rapatrier sur votre machine.
   <br /><span style="font-weight:bold;">NGAP:</span>Avec le clic droit de la souris, enregistrez ce <a mXattribut="href:lienExport2" target="_new">lien</a> pour les rapatrier sur votre machine.
  </mx:bloc id="donnees">
  <br /><br />
</div>
