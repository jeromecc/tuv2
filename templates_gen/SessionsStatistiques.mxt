<div id="sessionsstats">
  <center>
    <h4>Statistiques sur les différentes parties de l'application depuis le <mx:text id="datestats" /></h4>
    <form method="post" action="index.php">
      Afficher les graphiques <mx:checker id="graph" /><br />
      Afficher les statistiques <mx:select id="choix" /> :<br /><i><mx:text id="informations" /></i><br />
      <mx:hidden id="hidden" />
    </form>
    <mx:text id="table" />
  </center>
</div>
<br />
<hr />
<mx:bloc id="graphs">
<center>
  <h4>Nombre de clics et requêtes par heure</h4>
  <mx:text id="clicsHeure" />
  <h4>Temps moyen par heure</h4>
  <mx:text id="tempsHeure" />
  <h4>Clics et requêtes par jour</h4>
  <mx:text id="clicsJour" />
</center>
</mx:bloc id="graphs">