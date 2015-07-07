<?php
function pmwi_prepare_price( $price, $disable_prepare_price, $prepare_price_to_woo_format ){

	if ( $disable_prepare_price ){

	    $price = preg_replace("/[^0-9\.,]/","", $price);

	}

	if ( $prepare_price_to_woo_format ){		

		$price = str_replace(",", ".", $price);

	 	$price = str_replace(",", ".", str_replace(".", "", preg_replace("%\.([0-9]){1,2}?$%", ",$0", $price)));	    
	    
	    $price = ("" != $price) ? number_format( (double) $price, 2, '.', '' ) : "";

	}

	return apply_filters('pmxi_price', $price);

}