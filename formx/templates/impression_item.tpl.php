<div id="item<?php echo $idItem ?>" class="item" >




	<div class="<?php echo ($print_lib_class ?$print_lib_class :"libelle") ?>"   <?php  echo ($print_lib_style ? 'style="'. $print_lib_style.'"' : '' )  ?> >
		<?php  echo $libelle  ?>
	</div>

	<div class="explication" ></div>

	<div class="<?php echo ($print_val_class ?$print_val_class :"valeur") ?>"   <?php  echo ($print_val_style ? 'style="'. $print_val_style.'"' : '' )  ?> >
		<?php  echo $valeur  ?>
	</div>
</div>




