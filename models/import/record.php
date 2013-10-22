<?php

class PMWI_Import_Record extends PMWI_Model_Record {		

	/**
	 * Associative array of data which will be automatically available as variables when template is rendered
	 * @var array
	 */
	public $data = array();

	/**
	 * Initialize model instance
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'imports');
	}	
	
	/**
	 * Perform import operation
	 * @param string $xml XML string to import
	 * @param callback[optional] $logger Method where progress messages are submmitted
	 * @return PMWI_Import_Record
	 * @chainable
	 */
	public function process($import, $count, $xml, $logger = NULL, $chunk = false) {
		add_filter('user_has_cap', array($this, '_filter_has_cap_unfiltered_html')); kses_init(); // do not perform special filtering for imported content
		
		$this->data = array();

		$records = array();

		($chunk == 1 or (empty($import->large_import) or $import->large_import == 'No')) and $logger and call_user_func($logger, __('Composing product data...', 'pmxi_plugin'));

		// Composing product types
		if ($import->options['is_multiple_product_type'] != 'yes' and "" != $import->options['single_product_type']){
			$this->data['product_types'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_type'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_types'] = array_fill(0, $count, $import->options['multiple_product_type']);
		}

		// Composing product is Virtual									
		if ($import->options['is_product_virtual'] == 'xpath' and "" != $import->options['single_product_virtual']){
			$this->data['product_virtual'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_virtual'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_virtual'] = array_fill(0, $count, $import->options['is_product_virtual']);
		}

		// Composing product is Downloadable									
		if ($import->options['is_product_downloadable'] == 'xpath' and "" != $import->options['single_product_downloadable']){
			$this->data['product_downloadable'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_downloadable'] = array_fill(0, $count, $import->options['is_product_downloadable']);
		}

		// Composing product is Variable Enabled									
		if ($import->options['is_product_enabled'] == 'xpath' and "" != $import->options['single_product_enabled']){
			$this->data['product_enabled'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_enabled'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_enabled'] = array_fill(0, $count, $import->options['is_product_enabled']);
		}

		// Composing product is Featured									
		if ($import->options['is_product_featured'] == 'xpath' and "" != $import->options['single_product_featured']){
			$this->data['product_featured'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_featured'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_featured'] = array_fill(0, $count, $import->options['is_product_featured']);
		}

		// Composing product is Visibility									
		if ($import->options['is_product_visibility'] == 'xpath' and "" != $import->options['single_product_visibility']){
			$this->data['product_visibility'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_visibility'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_visibility'] = array_fill(0, $count, $import->options['is_product_visibility']);
		}

		if ("" != $import->options['single_product_sku']){
			$this->data['product_sku'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_sku'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sku'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_url']){
			$this->data['product_url'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_url'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_url'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_button_text']){
			$this->data['product_button_text'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_button_text'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_button_text'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_regular_price']){
			$this->data['product_regular_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_regular_price'], $file)->parse($records)); $tmp_files[] = $file;			
		}
		else{
			$count and $this->data['product_regular_price'] = array_fill(0, $count, "");
		}

		if ($import->options['is_regular_price_shedule'] and "" != $import->options['single_sale_price_dates_from']){
			$this->data['product_sale_price_dates_from'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price_dates_from'] = array_fill(0, $count, "");
		}

		if ($import->options['is_regular_price_shedule'] and "" != $import->options['single_sale_price_dates_to']){
			$this->data['product_sale_price_dates_to'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price_dates_to'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_sale_price']){
			$this->data['product_sale_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_sale_price'], $file)->parse($records)); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_whosale_price']){
			$this->data['product_whosale_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_whosale_price'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_files']){
			$this->data['product_files'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_files'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_files'] = array_fill(0, $count, "");
		}		

		if ("" != $import->options['single_product_download_limit']){
			$this->data['product_download_limit'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_download_limit'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_limit'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_download_expiry']){
			$this->data['product_download_expiry'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_expiry'] = array_fill(0, $count, "");
		}
		
		// Composing product Tax Status									
		if ($import->options['is_multiple_product_tax_status'] != 'yes' and "" != $import->options['single_product_tax_status']){
			$this->data['product_tax_status'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_tax_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_tax_status'] = array_fill(0, $count, $import->options['multiple_product_tax_status']);
		}

		// Composing product Tax Class									
		if ($import->options['is_multiple_product_tax_class'] != 'yes' and "" != $import->options['single_product_tax_class']){
			$this->data['product_tax_class'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_tax_class'] = array_fill(0, $count, $import->options['multiple_product_tax_class']);
		}

		// Composing product Manage stock?								
		if ($import->options['is_product_manage_stock'] == 'xpath' and "" != $import->options['single_product_manage_stock']){
			$this->data['product_manage_stock'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_manage_stock'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_manage_stock'] = array_fill(0, $count, $import->options['is_product_manage_stock']);
		}

		if ("" != $import->options['single_product_stock_qty']){
			$this->data['product_stock_qty'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_stock_qty'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_stock_qty'] = array_fill(0, $count, "");
		}					

		// Composing product Stock status							
		if ($import->options['product_stock_status'] == 'xpath' and "" != $import->options['single_product_stock_status']){
			$this->data['product_stock_status'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_stock_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_stock_status'] = array_fill(0, $count, $import->options['product_stock_status']);
		}

		// Composing product Allow Backorders?						
		if ($import->options['product_allow_backorders'] == 'xpath' and "" != $import->options['single_product_allow_backorders']){
			$this->data['product_allow_backorders'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_allow_backorders'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_allow_backorders'] = array_fill(0, $count, $import->options['product_allow_backorders']);
		}

		// Composing product Sold Individually?					
		if ($import->options['product_sold_individually'] == 'xpath' and "" != $import->options['single_product_sold_individually']){
			$this->data['product_sold_individually'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_sold_individually'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_sold_individually'] = array_fill(0, $count, $import->options['product_sold_individually']);
		}

		if ("" != $import->options['single_product_weight']){
			$this->data['product_weight'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_weight'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_weight'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_length']){
			$this->data['product_length'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_length'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_length'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_width']){
			$this->data['product_width'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_width'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_width'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_height']){
			$this->data['product_height'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_height'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_height'] = array_fill(0, $count, "");
		}

		// Composing product Shipping Class				
		if ($import->options['is_multiple_product_shipping_class'] != 'yes' and "" != $import->options['single_product_shipping_class']){
			$this->data['product_shipping_class'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_shipping_class'] = array_fill(0, $count, $import->options['multiple_product_shipping_class']);
		}

		if ("" != $import->options['single_product_up_sells']){
			$this->data['product_up_sells'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_up_sells'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_up_sells'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_cross_sells']){
			$this->data['product_cross_sells'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_cross_sells'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_cross_sells'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['grouping_product']){
			$this->data['product_grouping_parent'] = XmlImportParser::factory($xml, $import->xpath, $import->options['grouping_product'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_grouping_parent'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_purchase_note']){
			$this->data['product_purchase_note'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_purchase_note'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_purchase_note'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_menu_order']){
			$this->data['product_menu_order'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_menu_order'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_menu_order'] = array_fill(0, $count, "");
		}
		
		// Composing product Enable reviews		
		if ($import->options['is_product_enable_reviews'] == 'xpath' and "" != $import->options['single_product_enable_reviews']){
			$this->data['product_enable_reviews'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_enable_reviews'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_enable_reviews'] = array_fill(0, $count, $import->options['is_product_enable_reviews']);
		}		
		
		// Composing variations attributes					
		($chunk == 1 or (empty($import->large_import) or $import->large_import == 'No')) and $logger and call_user_func($logger, __('Composing variations attributes...', 'pmxi_plugin'));
		$attribute_keys = array(); 
		$attribute_values = array();	
		$attribute_in_variation = array(); 
		$attribute_is_visible = array();			
		$attribute_is_taxonomy = array();	
		$attribute_create_taxonomy_terms = array();		
							
		if (!empty($import->options['attribute_name'][0])){			
			foreach ($import->options['attribute_name'] as $j => $attribute_name) { if ($attribute_name == "") continue;								    											
				$attribute_keys[$j]   = XmlImportParser::factory($xml, $import->xpath, $attribute_name, $file)->parse($records); $tmp_files[] = $file;
				$attribute_values[$j] = XmlImportParser::factory($xml, $import->xpath, $import->options['attribute_value'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_in_variation[$j] = XmlImportParser::factory($xml, $import->xpath, $import->options['in_variations'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_is_visible[$j] = XmlImportParser::factory($xml, $import->xpath, $import->options['is_visible'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_is_taxonomy[$j] = XmlImportParser::factory($xml, $import->xpath, $import->options['is_taxonomy'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_create_taxonomy_terms[$j] = XmlImportParser::factory($xml, $import->xpath, $import->options['create_taxonomy_in_not_exists'][$j], $file)->parse($records); $tmp_files[] = $file;
			}			
		}					

		// serialized attributes for product variations
		$this->data['serialized_attributes'] = array();
		if (!empty($attribute_keys)){
			foreach ($attribute_keys as $j => $attribute_name) {											
				if (!in_array($attribute_name[0], array_keys($this->data['serialized_attributes']))){
					$this->data['serialized_attributes'][$attribute_name[0]] = array(
						'value' => $attribute_values[$j],
						'is_visible' => $attribute_is_visible[$j],
						'in_variation' => $attribute_in_variation[$j],
						'in_taxonomy' => $attribute_is_taxonomy[$j],
						'is_create_taxonomy_terms' => $attribute_create_taxonomy_terms[$j]
					);						
				}							
			}
		} 					

		remove_filter('user_has_cap', array($this, '_filter_has_cap_unfiltered_html')); kses_init(); // return any filtering rules back if they has been disabled for import procedure
		
		foreach ($tmp_files as $file) { // remove all temporary files created
			unlink($file);
		}

		return $this->data;
	}		

	public function filtering($var){
		return ("" == $var) ? false : true;
	}

	public function import($pid, $i, $import, $articleData, $xml, $is_cron = false){

		$logger = create_function('$m', 'echo "<div class=\\"progress-msg\\">$m</div>\\n"; if ( "" != strip_tags(pmxi_strip_tags_content($m))) { PMXI_Plugin::$session[\'pmxi_import\'][\'log\'] .= "<p>".strip_tags(pmxi_strip_tags_content($m))."</p>"; flush(); }');		

		global $woocommerce;		

		extract($this->data);

		// Add any default post meta
		add_post_meta( $pid, 'total_sales', '0', true );

		// Get types
		$product_type 		= empty( $product_types[$i] ) ? 'simple' : sanitize_title( stripslashes( $product_types[$i] ) );
		$is_downloadable 	= $product_downloadable[$i];
		$is_virtual 		= $product_virtual[$i];
		$is_featured 		= $product_featured[$i];

		if ( class_exists('woocommerce_wholesale_pricing') ):			
			if ($product_type == "simple") add_action( 'woocommerce_process_product_meta_simple', array( &$this, 'process_product_meta' ), 2, 1 );
			if ($product_type == "variable") add_action( 'woocommerce_save_product_variation', array( &$this, 'process_product_meta_variable' ), 1000, 1 );
		endif;

		$existing_meta_keys = array();
		foreach (get_post_meta($pid, '') as $cur_meta_key => $cur_meta_val) $existing_meta_keys[] = $cur_meta_key;
				
		// Product type + Downloadable/Virtual
		wp_set_object_terms( $pid, $product_type, 'product_type' );
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_downloadable')) update_post_meta( $pid, '_downloadable', ($is_downloadable == "yes") ? 'yes' : 'no' );
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_virtual')) update_post_meta( $pid, '_virtual', ($is_virtual == "yes") ? 'yes' : 'no' );						

		// Update post meta
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_regular_price')) update_post_meta( $pid, '_regular_price', stripslashes( $product_regular_price[$i] ) );
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price')) update_post_meta( $pid, '_sale_price', stripslashes( $product_sale_price[$i] ) );
		if ( class_exists('woocommerce_wholesale_pricing') and $this->keep_custom_fields($existing_meta_keys, $import->options, 'wholesale_price')) update_post_meta( $pid, 'pmxi_wholesale_price', stripslashes( $product_whosale_price[$i] ) );
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_tax_status')) update_post_meta( $pid, '_tax_status', stripslashes( $product_tax_status[$i] ) );
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_tax_class')) update_post_meta( $pid, '_tax_class', stripslashes( $product_tax_class[$i] ) );			
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_visibility')) update_post_meta( $pid, '_visibility', stripslashes( $product_visibility[$i] ) );			
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_purchase_note')) update_post_meta( $pid, '_purchase_note', stripslashes( $product_purchase_note[$i] ) );
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_featured')) update_post_meta( $pid, '_featured', ($is_featured == "yes") ? 'yes' : 'no' );

		// Dimensions
		if ( $is_virtual == 'no' ) {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_weight')) update_post_meta( $pid, '_weight', stripslashes( $product_weight[$i] ) );
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_length')) update_post_meta( $pid, '_length', stripslashes( $product_length[$i] ) );
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_width')) update_post_meta( $pid, '_width', stripslashes( $product_width[$i] ) );
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_height')) update_post_meta( $pid, '_height', stripslashes( $product_height[$i] ) );
		} else {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_weight')) update_post_meta( $pid, '_weight', '' );
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_length')) update_post_meta( $pid, '_length', '' );
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_width')) update_post_meta( $pid, '_width', '' );
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_height')) update_post_meta( $pid, '_height', '' );
		}

		// Enable reviews
		wp_update_post(array(
			'ID' => $pid,
			'comment_status' => ($product_enable_reviews[$i] == 'yes') ? 'open' : 'closed',
			'menu_order' => ($product_menu_order[$i] != '') ? $product_menu_order[$i] : 0
		));

		// Save shipping class
		$product_shipping_class = is_numeric($product_shipping_class[$i]) && $product_shipping_class[$i] > 0 && $product_type != 'external' ? absint( $product_shipping_class[$i] ) : $product_shipping_class[$i];
		wp_set_object_terms( $pid, $product_shipping_class, 'product_shipping_class');

		// Unique SKU
		$sku				= get_post_meta($pid, '_sku', true);
		$new_sku 			= esc_html( trim( stripslashes( $product_sku[$i] ) ) );
		
		if ( $new_sku == '' and $import->options['disable_auto_sku_generation'] ) {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sku')) 				
					update_post_meta( $pid, '_sku', '' );
		}
		elseif ( $new_sku == '' and ! $import->options['disable_auto_sku_generation'] ) {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sku')){				
				$unique_keys = XmlImportParser::factory($xml, $import->xpath, $import->options['unique_key'], $file)->parse($records); $tmp_files[] = $file;
				foreach ($tmp_files as $file) { // remove all temporary files created
					unlink($file);
				}
				$new_sku = substr(md5($unique_keys[$i]), 0, 12);
			}
		}
		if ( $new_sku != '' and $new_sku !== $sku ) {
			if ( ! empty( $new_sku ) ) {
				if (
					$this->wpdb->get_var( $this->wpdb->prepare("
						SELECT ".$this->wpdb->posts.".ID
					    FROM ".$this->wpdb->posts."
					    LEFT JOIN ".$this->wpdb->postmeta." ON (".$this->wpdb->posts.".ID = ".$this->wpdb->postmeta.".post_id)
					    WHERE ".$this->wpdb->posts.".post_type = 'product'
					    AND ".$this->wpdb->posts.".post_status = 'publish'
					    AND ".$this->wpdb->postmeta.".meta_key = '_sku' AND ".$this->wpdb->postmeta.".meta_value = '%s'
					 ", $new_sku ) )
					) {
					$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Product SKU must be unique.', 'pmxi_plugin')));
					
				} else {
					if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sku')) update_post_meta( $pid, '_sku', $new_sku );
				}
			} else {
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sku')) update_post_meta( $pid, '_sku', '' );
			}
		}

		// Save Attributes
		$attributes = array();
		
		if ( !empty($serialized_attributes) ) {							
			
			$attribute_position = 0;

			foreach ($serialized_attributes as $attr_name => $attr_data) {	$attr_name = strtolower($attr_name);
				
				$is_visible 	= intval( $attr_data['is_visible'][$i] );
				$is_variation 	= intval( $attr_data['in_variation'][$i] );
				$is_taxonomy 	= intval( $attr_data['in_taxonomy'][$i] );

				if ( $is_taxonomy ) {										

					if ( isset( $attr_data['value'][$i] ) ) {
				 		
				 		$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( '|', $attr_data['value'][$i] ) ) );

					 	// Remove empty items in the array
					 	$values = array_filter( $values, array($this, "filtering") );
					 	
					 	if ( ! taxonomy_exists( $woocommerce->attribute_taxonomy_name( $attr_name ) ) and intval($attr_data['is_create_taxonomy_terms'][$i])) {

					 		// Grab the submitted data
							$attribute_name    = ( isset( $attr_name ) )   ? woocommerce_sanitize_taxonomy_name( stripslashes( (string) $attr_name ) ) : '';
							$attribute_label   = ucwords( stripslashes( (string) $attr_name ));
							$attribute_type    = 'select';
							$attribute_orderby = 'menu_order';

							$reserved_terms = array(
								'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and',
								'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day',
								'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name',
								'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm',
								'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type',
								'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence',
								'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id',
								'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'w', 'withcomments', 'withoutcomments', 'year',
							);

							if ( in_array( $attribute_name, $reserved_terms ) ) {
								$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Slug “%s” is not allowed because it is a reserved term. Change it, please.', 'pmxi_plugin'), sanitize_title( $attribute_name )));
							}			
							else{
								$this->wpdb->insert(
									$this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
									array(
										'attribute_label'   => $attribute_label,
										'attribute_name'    => $attribute_name,
										'attribute_type'    => $attribute_type,
										'attribute_orderby' => $attribute_orderby,
									)
								);								

								$logger and call_user_func($logger, sprintf(__('<b>CREATED</b>: Taxonomy attribute “%s” have been successfully created.', 'pmxi_plugin'), $attribute_label));	
								
								// Register the taxonomy now so that the import works!
								$domain = $woocommerce->attribute_taxonomy_name( $attr_name );
								register_taxonomy( $domain,
							        apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array('product') ),
							        apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
							            'hierarchical' => true,
							            'show_ui' => false,
							            'query_var' => true,
							            'rewrite' => false,
							        ) )
							    );

								delete_transient( 'wc_attribute_taxonomies' );

								$attribute_taxonomies = $this->wpdb->get_results( "SELECT * FROM " . $this->wpdb->prefix . "woocommerce_attribute_taxonomies" );

								set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );

								apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );
							}

					 	}

					 	if ( ! empty($values) and taxonomy_exists( $woocommerce->attribute_taxonomy_name( $attr_name ) )){

					 		$attr_values = array();
					 		
					 		$terms = get_terms( $woocommerce->attribute_taxonomy_name( $attr_name ), array('hide_empty' => false));								
					 		
					 		if ( ! is_wp_error($terms) ){
					 		
						 		foreach ($values as $key => $value) {
						 			$term_founded = false;	
									if ( count($terms) > 0 ){	
									    foreach ( $terms as $term ) {									    										    	
									    	if ( strtolower($term->name) == trim(strtolower($value)) ) {
									    		$attr_values[] = $term->slug;									    		
									    		$term_founded = true;
									    		break;
									    	}
									    }
									}
								    if ( ! $term_founded and intval($attr_data['is_create_taxonomy_terms'][$i]) ){								    	
								    	$term = wp_insert_term(
											$value, // the term 
										  	$woocommerce->attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
										);	
										if ( ! is_wp_error($term) ){
											$term = get_term_by( 'id', $term['term_id'], $woocommerce->attribute_taxonomy_name( $attr_name ));
											$attr_values[] = $term->slug; 
										}					    		
											
								    }
						 		}
						 	}
						 	else {
						 		$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: %s.', 'pmxi_plugin'), $terms->get_error_message()));
						 	}

					 		$values = $attr_values;

					 	} 
					 	else {
					 		$values = array();
					 	}

				 	} 				 				 	
				 	
			 		// Update post terms
			 		if ( taxonomy_exists( $woocommerce->attribute_taxonomy_name( $attr_name ) ))			 			
			 			wp_set_object_terms( $pid, $values, $woocommerce->attribute_taxonomy_name( $attr_name ) );			 		
			 		
			 		if ( $values ) {
									 			
				 		// Add attribute to array, but don't set values
				 		$attributes[ $woocommerce->attribute_taxonomy_name( $attr_name ) ] = array(
					 		'name' 			=> $woocommerce->attribute_taxonomy_name( $attr_name ),
					 		'value' 		=> '',
					 		'position' 		=> $attribute_position,
					 		'is_visible' 	=> $is_visible,
					 		'is_variation' 	=> $is_variation,
					 		'is_taxonomy' 	=> 1,
					 		'is_create_taxonomy_terms' => (!empty($attr_data['is_create_taxonomy_terms'][$i])) ? 1 : 0
					 	);

				 	}

			 	} else {

			 		if ( taxonomy_exists( $woocommerce->attribute_taxonomy_name( $attr_name ) ))
			 			wp_set_object_terms( $pid, NULL, $woocommerce->attribute_taxonomy_name( $attr_name ) );

			 		// Text based, separate by pipe
			 		//$values = implode( ' | ', array_map( 'sanitize_text_field', explode( '|', $attr_data['value'][$i] ) ) );

			 		// Custom attribute - Add attribute to array and set the values
				 	$attributes[ sanitize_title( $attr_name ) ] = array(
				 		'name' 			=> sanitize_text_field( $attr_name ),
				 		'value' 		=> $attr_data['value'][$i],
				 		'position' 		=> $attribute_position,
				 		'is_visible' 	=> $is_visible,
				 		'is_variation' 	=> $is_variation,
				 		'is_taxonomy' 	=> 0
				 	);

			 	}

			 	$attribute_position++;
			}							
		}						
		
		if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_product_attributes')) update_post_meta( $pid, '_product_attributes', $attributes );

		// Sales and prices
		if ( ! in_array( $product_type, array( 'grouped' ) ) ) {

			$date_from = isset( $product_sale_price_dates_from[$i] ) ? $product_sale_price_dates_from[$i] : '';
			$date_to = isset( $product_sale_price_dates_to[$i] ) ? $product_sale_price_dates_to[$i] : '';

			// Dates
			if ( $date_from ){
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', strtotime( $date_from ) );
			}
			else{
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', '' );
			}

			if ( $date_to ){
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_to')) update_post_meta( $pid, '_sale_price_dates_to', strtotime( $date_to ) );
			}
			else{
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_to')) update_post_meta( $pid, '_sale_price_dates_to', '' );
			}

			if ( $date_to && ! $date_from ){
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );
			}

			// Update price if on sale
			if ( $product_sale_price[$i] != '' && $date_to == '' && $date_from == '' ){
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes( $product_sale_price[$i] ) );
			}
			else{
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes( $product_regular_price[$i] ) );
			}

			if ( $product_sale_price[$i] != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ){
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes($product_sale_price[$i]) );				
			}

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes($product_regular_price[$i]) );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', '');
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sale_price_dates_to')) update_post_meta( $pid, '_sale_price_dates_to', '');
			}
		}

		// Update parent if grouped so price sorting works and stays in sync with the cheapest child
		if ( $product_type == 'grouped' || ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0)) {

			$clear_parent_ids = array();													

			if ( $product_type == 'grouped' )
				$clear_parent_ids[] = $pid;		

			if ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0 )
				$clear_parent_ids[] = absint( $product_grouping_parent[$i] );					

			if ( $clear_parent_ids ) {
				foreach( $clear_parent_ids as $clear_id ) {

					$children_by_price = get_posts( array(
						'post_parent' 	=> $clear_id,
						'orderby' 		=> 'meta_value_num',
						'order'			=> 'asc',
						'meta_key'		=> '_price',
						'posts_per_page'=> 1,
						'post_type' 	=> 'product',
						'fields' 		=> 'ids'
					) );
					if ( $children_by_price ) {
						foreach ( $children_by_price as $child ) {
							$child_price = get_post_meta( $child, '_price', true );
							update_post_meta( $clear_id, '_price', $child_price );
						}
					}

					// Clear cache/transients
					$woocommerce->clear_product_transients( $clear_id );
				}
			}
		}

		// Sold Individuall
		if ( "yes" == $product_sold_individually[$i] ) {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sold_individually')) update_post_meta( $pid, '_sold_individually', 'yes' );
		} else {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_sold_individually')) update_post_meta( $pid, '_sold_individually', '' );
		}
		
		// Stock Data
		if ( $product_manage_stock[$i] == 'yes' ) {

			if ( $product_type == 'grouped' ) {

				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', '' );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'no' );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', 'no' );

			} elseif ( $product_type == 'external' ) {

				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', 'instock' );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', '' );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'no' );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', 'no' );

			} elseif ( ! empty( $product_manage_stock[$i] ) ) {

				// Manage stock
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', (int) $product_stock_qty[$i] );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', stripslashes( $product_allow_backorders[$i] ) );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'yes' );

				// Check stock level
				if ( $product_type !== 'variable' && $product_allow_backorders[$i] == 'no' && (int) $product_stock_qty[$i] < 1 ){
					if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', 'outofstock' );
				}

			} else {

				// Don't manage stock
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', '' );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', stripslashes( $product_allow_backorders[$i] ) );
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'no' );

			}

		} else {

			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );

		}

		// Upsells
		if ( !empty( $product_up_sells[$i] ) ) {
			$upsells = array();
			$ids = explode(',', $product_up_sells[$i]);
			foreach ( $ids as $id ){								
				$args = array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $id,						
						)
					)
				);			
				$query = new WP_Query( $args );
				
				if ( $query->have_posts() ) $upsells[] = $query->post->ID;

				wp_reset_postdata();
			}								

			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_upsell_ids')) update_post_meta( $pid, '_upsell_ids', $upsells );
		} else {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_upsell_ids')) delete_post_meta( $pid, '_upsell_ids' );
		}

		// Cross sells
		if ( !empty( $product_cross_sells[$i] ) ) {
			$crosssells = array();
			$ids = explode(',', $product_cross_sells[$i]);
			foreach ( $ids as $id ){
				$args = array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $id,						
						)
					)
				);			
				$query = new WP_Query( $args );
				
				if ( $query->have_posts() ) $crosssells[] = $query->post->ID;

				wp_reset_postdata();
			}								

			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_crosssell_ids')) update_post_meta( $pid, '_crosssell_ids', $crosssells );
		} else {
			if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_crosssell_ids')) delete_post_meta( $pid, '_crosssell_ids' );
		}

		// Downloadable options
		if ( $is_downloadable == 'yes' ) {

			$_download_limit = absint( $product_download_limit[$i] );
			if ( ! $_download_limit )
				$_download_limit = ''; // 0 or blank = unlimited

			$_download_expiry = absint( $product_download_expiry[$i] );
			if ( ! $_download_expiry )
				$_download_expiry = ''; // 0 or blank = unlimited
			
			// file paths will be stored in an array keyed off md5(file path)
			if ( !empty( $product_files[$i] ) ) {
				$_file_paths = array();
				
				$file_paths = explode( $import->options['product_files_delim'] , $product_files[$i] );

				foreach ( $file_paths as $file_path ) {
					$file_path = trim( $file_path );					
					$_file_paths[ md5( $file_path ) ] = $file_path;
				}				

				// grant permission to any newly added files on any existing orders for this product
				do_action( 'woocommerce_process_product_file_download_paths', $pid, 0, $_file_paths );

				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_file_paths')) update_post_meta( $pid, '_file_paths', $_file_paths );
			}
			if ( isset( $product_download_limit[$i] ) )
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_download_limit')) update_post_meta( $pid, '_download_limit', esc_attr( $_download_limit ) );
			if ( isset( $product_download_expiry[$i] ) )
				if ($this->keep_custom_fields($existing_meta_keys, $import->options, '_download_expiry')) update_post_meta( $pid, '_download_expiry', esc_attr( $_download_expiry ) );
		}						

		// Do action for product type
		do_action( 'woocommerce_process_product_meta_' . $product_type, $pid );		

		// Clear cache/transients
		$woocommerce->clear_product_transients( $pid );	

	}		

	public function _filter_has_cap_unfiltered_html($caps)
	{
		$caps['unfiltered_html'] = true;
		return $caps;
	}					

	function keep_custom_fields($existing_meta_keys, $options, $meta_key){

		$keep_custom_fields_specific = ( ! $options['keep_custom_fields'] and ! empty($options['keep_custom_fields_specific'])) ? array_map('trim', explode(',', $options['keep_custom_fields_specific'])) : array();
		$keep_custom_fields_except = ($options['keep_custom_fields'] and !empty($options['keep_custom_fields_except'])) ? array_map('trim', explode(',', $options['keep_custom_fields_except'])) : array();

		return (($options['keep_custom_fields'] and in_array($meta_key, $existing_meta_keys) and !in_array($meta_key, $keep_custom_fields_except)) or (in_array($meta_key, $existing_meta_keys) and ! $options['keep_custom_fields'] and in_array($meta_key, $keep_custom_fields_specific))) ? false : true;

	}

	// process simple product meta
	function process_product_meta( $post_id, $post = '' ) {

		$wholesale_price = get_post_meta($post_id, 'pmxi_wholesale_price', true);

		if ( '' !==  $wholesale_price )
			update_post_meta( $post_id, 'wholesale_price', stripslashes( $wholesale_price ) );
		else
			delete_post_meta( $post_id, 'pmxi_wholesale_price' );
	}

	// process variable product meta
	function process_product_meta_variable( $post_id ) {

		$product = get_post($post_id);

		if ( $product->post_type == 'product_variation' ) {		

			$wholesale_price = get_post_meta($post_id, 'pmxi_wholesale_price', true);

			if ( '' !==  $wholesale_price )
				update_post_meta( $post_id, 'wholesale_price', stripslashes( $wholesale_price ) );
			else
				delete_post_meta( $post_id, 'pmxi_wholesale_price' );	

			$post_parent = $product->post_parent;			

		}
		else $post_parent = $post_id;		
		
		$children = get_posts( array(
				    'post_parent' 	=> $post_parent,
				    'posts_per_page'=> -1,
				    'post_type' 	=> 'product_variation',
				    'fields' 		=> 'ids'
			    ) );

		$lowest_price = '';

		$highest_price = '';

		if ( $children ) {

			foreach ( $children as $child ) {

				$child_price = get_post_meta($child, 'pmxi_wholesale_price', true);

			
				if ( !$child_price ) continue;
	
				// Low price
				if ( !is_numeric( $lowest_price ) || $child_price < $lowest_price ) $lowest_price = $child_price;

				
				// High price
				if ( $child_price > $highest_price )
					$highest_price = $child_price;

			}


		}

		update_post_meta( $post_parent, 'wholesale_price', $lowest_price );
		update_post_meta( $post_parent, 'min_variation_wholesale_price', $lowest_price );
		update_post_meta( $post_parent, 'max_variation_wholesale_price', $highest_price );

		
	}
	
	function prepare_price( $price ){
		return preg_replace("/[^0-9\.,]/","",$price);
	}
}
