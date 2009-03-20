<?php

/*
 * exemple d'objet type
 */

class caExemple extends caObjetBase 
{
	public function save() 
	{
		caExemplePeer::save($this);	
	}
	
}