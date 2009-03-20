<mx:bloc id="list">
  <table>
    <tr><th>Messages</th><th class="droite"><a mXattribut="href:lienNewMessage"><mx:image id="imgNewMessage"/></a></th></tr>
    <mx:bloc id="messages">
      <tr class="droite"><td ><mx:text id="nbMessages" /></td><td ><a mXattribut="href:lienViewMessages"><mx:image id="imgViewMessages"/></td></tr>
    </mx:bloc id="messages">
  </table>
</mx:bloc id="list">

<mx:bloc id="view">
  <iframe style="border: 0px; display: block; position: absolute; 
                 left: 90px; top: 145px; width: 869px; " ></iframe>
  <div id="messagesview">
    <center>
    <div id="messagesviewtitre" style="cursor:move;">
      <table>
        <tr><th>Messages transmis</th><th class="alignD"><a mXattribut="href:lienCloseMessages"><mx:image id="imgCloseMessages"/><br /></th></tr>
      </table>
    </div>
    <table>
      <tr>
	   <th class="gauche">Type</th>
	   <th  class="gauche">Date</th>
	   <th>Contenu</th></tr>
      <mx:bloc id="messages">
        <tr>
          <td class="alignC"><mx:text id="type" /></td>
          <td class="alignC"><mx:text id="date" /></td>
          <td><mx:text id="contenu" /></td>
        </tr>
      </mx:bloc id="messages">
    </table>
    </center>
  </div>
</mx:bloc id="view">

<mx:bloc id="new">
  <iframe style="border: 0px; display: block; position: absolute; left: 190px; top: 120px; width: 436px; height:200px;"></iframe>
  <div id="messagenew">
    <table>
      <form method="post" action="index.php">
        <tr id="handlem" style="cursor:move;"><th colspan="2">Envoyer un message de signalement</th></tr>
        <tr>
          <th colspan="2">
            <mx:checker id="type1" onclick="reload(this.form)" style="border:0px; background-color: transparent;" />maltraitance
            <mx:checker id="type2" onclick="reload(this.form)" style="border:0px; background-color: transparent;" />conflit
            <mx:checker id="type3" onclick="reload(this.form)" style="border:0px; background-color: transparent;" />social
          </th>
        </tr>
        <tr><td class="gauche">De :</td>
		<td class="droite"> <mx:text id="nomApplication" /> &lt;<mx:text id="mailApplication" />&gt;</td></tr>
        <tr><td class="gauche">Pour :</td>
		<td class="droite"> <mx:text id="nomsDestinataires" /></td></tr>
	<tr><td class="gauche">Sujet :</td>
	<td class="droite"> <mx:text id="sujet" /></td></tr>
	<tr><td class="gauche">Message :</td>
	<td class="droite"> <i><mx:text id="message" /></i></td></tr>
	<tr valign="top"><td colspan="2" class="hover">Observations complémentaires : <mx:text id="observations" /></td>
	</tr>
        <tr>
	<td colspan="2" align="center">
	  <input name="Envoyer"  type="image" src="images/ajouter2.gif"   value="Envoyer" style="border: 0px; background-color: transparent;"/>
          <mx:hidden id="hidden1" />
      </form>
	  <form method="post" action="index.php">
	  <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" style="border: 0px; background-color: transparent;"/>
          <mx:hidden id="hidden2" />
      </form>
        </tr>
    </table>
  </div>
</mx:bloc id="new">
