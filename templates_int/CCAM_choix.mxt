<h2><mx:text id="titre" /></h2>
<form  method="post" mxAttribut="action:action">
<fieldset>
Du <input type="text" id="date1" name="date1" readonly='y' /><img id="bdate1" src="images/calendrier.gif"  style="position:relative;top:5px;"/>
Au <input type="text" id="date2" name="date2" readonly='y' /><img id="bdate2" src="images/calendrier.gif"  style="position:relative;top:5px;"/>
<input type="submit" name="ok" value="ok" />
<input type="hidden" name="postexecscript" value="y" />
</fieldset>
</form>
<script type="text/javascript">
 Calendar.setup(
    {
     inputField  : "date1",         // ID of the input field 23-06-2005 13:24
     ifFormat    : "%d/%m/%Y",    // the date format
     button      : "bdate1"  ,     // ID of the button
     firstDay	:	1
    }
 );

 Calendar.setup(
    {
     inputField  : "date2",         // ID of the input field 23-06-2005 13:24
     ifFormat    : "%d/%m/%Y",    // the date format
     button      : "bdate2"  ,     // ID of the button
     firstDay	:	1
    }
 );
</script>
