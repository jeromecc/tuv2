<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
	<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
	<title><?php echo $titre ?></title>

<link rel="stylesheet" type="text/css"  href="<?php echo $urlCss?>" />

</head>
<body onLoad="window.print();window.close();">
<img id="logo"  alt="logo" src="<?php echo $urlLogo ?>"  />

<div id="titre">
	<?php echo $titre ?>
</div>

<div id="formulaire">
	<?php echo $htmlFormulaire ?>
</div>


</body>
</html>