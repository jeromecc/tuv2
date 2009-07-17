<HTML>
  <HEAD>
    <title>Appel Viewer Dx Care</title>
	<meta content="VBScript" name="vs_defaultClientScript">
  </HEAD>
  <body MS_POSITIONING="GridLayout">
    <object id="pManager" classid="clsid:EB5EEED4-0F1E-11D5-A2AB-0080C8F94268" VIEWASTEXT  ></object>
    <script language="VBScript">
    <!--
	
	dim hres
	
    sub finViewVer() 
       //msgbox("finViewVer: IN")
       //msgbox("pManager   = <" + appel.hdlViewer.value + ">")
       pManager.closeObject(hdlViewer.value)
       //msgbox("finViewVer: OUT")
    end sub
	
    sub appelViewVer() 

       //msgbox("appelViewVer: IN")

       on error resume next
		
       //msgbox("pEvent   = <" + pEvent + ">")
       //msgbox("pManager   = <" + pManager + ">")
       //msgbox("parametres = <" + appel.appel_id.value + ">")

       on error resume next
       //hres = pManager.openObject(nothing, "NIP=0907000804;NDA=109770139;UH=1551;OBJECTTYPE=113;OBJECTID=;USERID=M.D;ACTIF=T;")
       hres = pManager.openObject(nothing, "NIP=<?php echo $_GET['ilp'] ; ?>;NDA=<?php echo $_GET['nsej'] ; ?>;UH=<?php echo $_GET['uf'] ; ?>;OBJECTTYPE=<?php echo $_GET['fct'] ?>;OBJECTID=;USERID=<?php echo $_GET['userid'] ; ?>;ACTIF=T;")
       if err then		
          msgbox("Erreur : " + cstr(err.number)  + ": "  + err.Description)
          err.Clear
       end if

       msgbox("Après openObject <" + hres + ">")
       hdlViewer.value = hres
       msgbox("Après openObject <" + hres + ">")

       msgbox("appelViewVer: OUT")
    end sub
    appelViewVer()
    //-->
    </script>
    <script language="VBScript" for="pEvent" event="onObjectClosed()">
      msgbox("onObjectClosed: IN")
      msgbox("onObjectClosed: OUT")
    </script>
    <script type="text/javascript">
      window.close ( ) ;
    </script>
  </body>
</HTML>
