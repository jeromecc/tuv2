<div id="etape<?php echo $idEtape ?>" class="etape">
	<div class="titre_etape">
		<?php echo	$titre_etape ?>
		<?php if ( $navigation ) : ?>
			<div class="navigation"><?php echo $navigation  ?></div>
		<?php endif ?>
	</div>
	<div class="contenu_etape">
		<?php echo	$contenu ?>
	</div>
</div>