<style>
#miniMessagerie { background-color: #E4F7FD; border: 1px solid #330033; padding-left: 5px; padding-right: 5px;position: absolute; left: 920px; top: 8px; cursor:move;z-index:10000;}
#miniMessagerie .messages { color: green; }
#miniMessagerie .messagesPerso { color: blue; }
#miniMessagerie img { cursor: pointer; }

</style>
<div id="miniMessagerie">
  <a mXattribut="href:lienAffichage"><mx:image id="imgAffichage"/></a> 
  <span class="messages"><mx:text id="messages" /> </span>
  <span class="messagesPerso"><mx:text id="messagesPerso" /> </span>
  <mx:image id="imgFermer" onClick="javascript:document.getElementById('miniMessagerie').style.display='none';"/>
</div>
<div id="messagerie">

</div>

