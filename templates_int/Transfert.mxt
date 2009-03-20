<mx:bloc id="transfert">
<tr>
<td>
<div id="transfertpatient">
  <form method="post" action="index.php">
    <table>  
      <tr>
        <th style="width: 320px;" colspan="1">
          Informations sur le transfert
        </th>
	<mx:bloc id="bloctrans">
        <td style="width: 320px;"><table class="sansstyle"><tr><td style="text-align: left;"><b>Motif</b></td><td><mx:select id="listeMotifs" /><mx:text id="Motif" /></td></tr></table></td>
        <td style="width: 320px;"><table class="sansstyle"><tr><td style="text-align: left;"><b>Transport</b></td><td><mx:select id="listeMoyens" /><mx:text id="Moyen" /></td></tr></table></td>
        </mx:bloc id="bloctrans">
      </tr>
    </table>
    <mx:hidden id="hidden" />
  </form>
</div>
</td>
</tr>
</mx:bloc id="transfert">
