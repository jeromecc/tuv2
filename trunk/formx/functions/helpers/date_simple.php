<?php


function date_simple($str) {
	$objDate = new clDate($str);
	return $objDate->getSimpleDate();
}