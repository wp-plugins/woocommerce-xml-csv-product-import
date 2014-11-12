<?php
function pmwi_adjust_price( $price, $field, $options ){

	switch ($field) {

		case 'regular_price':
			
			if ( ! empty($options['single_product_regular_price_adjust']) ){

				switch ($options['single_product_regular_price_adjust_type']) {
					case '%':
						$price = ($price/100) * $options['single_product_regular_price_adjust'];
						break;

					case '$':

						$price += (double) $options['single_product_regular_price_adjust'];

						break;						
				}

			}

			break;

		case 'sale_price':
			
			if ( ! empty($options['single_product_sale_price_adjust']) ){

				switch ($options['single_product_sale_price_adjust_type']) {
					case '%':
						$price = ($price/100) * $options['single_product_sale_price_adjust'];
						break;

					case '$':

						$price += (double) $options['single_product_sale_price_adjust'];

						break;						
				}

			}

			break;
		
		/*default:
			
			return $price;*/

			break;
	}

	return ( (double) $price > 0) ? number_format( (double) $price, 2, '.', '' ) : 0;

}