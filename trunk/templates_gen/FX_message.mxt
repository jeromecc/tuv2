<SCRIPT language=javascript>
   function Scroller2() {
	window.scrollBy(0,-10000);
   }
   setTimeout("Scroller2()", 600);
</SCRIPT>

<div id="ajoutermini" class="formx_message" >
  <form method="post" action="index.php">
  <div id="messagesviewtitre" class="formx_message_titre">
    <mx:text id="titre" /><input name="annuler_popup"  type="image" src="images/fermer.gif" value="annuler_popup" />
  </div>
     <table>
      <tr>
        <td class="droite">
      <pre>
      <mx:text id="message" />
      </pre>
        </td>
      </tr>
      
      <tr>
	<td colspan="1" align="right">
	    <input name="annuler_popup"  type="image" src="images/annuler2.gif"   value="annuler_popup" />
	    <input name="valider_popup"  type="image" src="images/Ok.gif"   value="valider_popup" />
       
	  </td>
      </tr>
    </table>
    <mx:hidden id="hidden" />
    <mx:hidden id="hidden2" />
    
    <mx:hidden id="vars_prec" />
        
  </form>
</div>
