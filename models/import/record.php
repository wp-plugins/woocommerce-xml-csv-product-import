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
			$this->data['product_regular_price'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_regular_price'], $file)->parse($records); $tmp_files[] = $file;
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
			$this->data['product_sale_price'] = XmlImportParser::factory($xml, $import->xpath, $import->options['single_product_sale_price'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price'] = array_fill(0, $count, "");
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

	public function import($pid, $i, $import, $articleData, $xml, $is_cron = false){

		$logger = create_function('$m', 'echo "<div class=\\"progress-msg\\">$m</div>\\n"; if ( "" != strip_tags(pmxi_strip_tags_content($m))) { $_SESSION[\'pmxi_import\'][\'log\'] .= "<p>".strip_tags(pmxi_strip_tags_content($m))."</p>"; flush(); }');		

		global $woocommerce;

		extract($this->data);

		// Add any default post meta
		add_post_meta( $pid, 'total_sales', '0', true );

		// Get types
		$product_type 		= 'simple';
		$is_downloadable 	= $product_downloadable[$i];
		$is_virtual 		= $product_virtual[$i];
		$is_featured 		= $product_featured[$i];

		// Product type + Downloadable/Virtual
		wp_set_object_terms( $pid, $product_type, 'product_type' );
		update_post_meta( $pid, '_downloadable', ($is_downloadable == "yes") ? 'yes' : 'no' );
		update_post_meta( $pid, '_virtual', ($is_virtual == "yes") ? 'yes' : 'no' );						

		// Update post meta
		update_post_meta( $pid, '_regular_price', stripslashes( $product_regular_price[$i] ) );
		update_post_meta( $pid, '_sale_price', stripslashes( $product_sale_price[$i] ) );
		update_post_meta( $pid, '_tax_status', stripslashes( $product_tax_status[$i] ) );
		update_post_meta( $pid, '_tax_class', stripslashes( $product_tax_class[$i] ) );			
		update_post_meta( $pid, '_visibility', stripslashes( $product_visibility[$i] ) );			
		update_post_meta( $pid, '_purchase_note', stripslashes( $product_purchase_note[$i] ) );
		update_post_meta( $pid, '_featured', ($is_featured == "yes") ? 'yes' : 'no' );

		// Dimensions
		if ( $is_virtual == 'no' ) {
			update_post_meta( $pid, '_weight', stripslashes( $product_weight[$i] ) );
			update_post_meta( $pid, '_length', stripslashes( $product_length[$i] ) );
			update_post_meta( $pid, '_width', stripslashes( $product_width[$i] ) );
			update_post_meta( $pid, '_height', stripslashes( $product_height[$i] ) );
		} else {
			update_post_meta( $pid, '_weight', '' );
			update_post_meta( $pid, '_length', '' );
			update_post_meta( $pid, '_width', '' );
			update_post_meta( $pid, '_height', '' );
		}

		// Save shipping class
		$product_shipping_class = $product_shipping_class[$i] > 0 && $product_type != 'external' ? absint( $product_shipping_class[$i] ) : '';
		wp_set_object_terms( $pid, $product_shipping_class, 'product_shipping_class');

		// Unique SKU
		$sku				= get_post_meta($pid, '_sku', true);
		$new_sku 			= esc_html( trim( stripslashes( $product_sku[$i] ) ) );
		
		if ( $new_sku == '' ) {
			update_post_meta( $pid, '_sku', '' );
		} elseif ( $new_sku !== $sku ) {
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
					update_post_meta( $pid, '_sku', $new_sku );
				}
			} else {
				update_post_meta( $pid, '_sku', '' );
			}
		}

		// Save Attributes
		$attributes = array();
		
		if ( !empty($serialized_attributes) ) {							
			
			$attribute_position = 0;

			foreach ($serialized_attributes as $attr_name => $attr_data) {				
				
				$is_visible 	= intval( $attr_data['is_visible'][$i] );
				$is_variation 	= intval( $attr_data['in_variation'][$i] );
				$is_taxonomy 	= intval( $attr_data['in_taxonomy'][$i] );


				if ( $is_taxonomy ) {										

					if ( isset( $attr_data['value'][$i] ) ) {
				 		
				 		$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( '|', $attr_data['value'][$i] ) ) );

					 	// Remove empty items in the array
					 	$values = array_filter( $values );

					 	if ( ! taxonomy_exists( $woocommerce->attribute_taxonomy_name( $attr_name ) ) and intval($attr_data['is_create_taxonomy_terms'][$i])) {

					 		// Grab the submitted data							
							$attribute_name    = ( isset( $attr_name ) )   ? woocommerce_sanitize_taxonomy_name( stripslashes( (string) $attr_name ) ) : '';
							$attribute_label   = ucwords($attribute_name);
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
								'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year',
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

								$logger and call_user_func($logger, sprintf(__('<b>CREATED</b>: Taxonomy attribute “%s” have been successfully created.', 'pmxi_plugin'), sanitize_title( $attribute_name )));	
								
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
			 		$values = implode( ' | ', array_map( 'sanitize_text_field', explode( '|', $attr_data['value'][$i] ) ) );

			 		// Custom attribute - Add attribute to array and set the values
				 	$attributes[ sanitize_title( $attr_name ) ] = array(
				 		'name' 			=> sanitize_text_field( $attr_name ),
				 		'value' 		=> $values,
				 		'position' 		=> $attribute_position,
				 		'is_visible' 	=> $is_visible,
				 		'is_variation' 	=> $is_variation,
				 		'is_taxonomy' 	=> 0
				 	);

			 	}

			 	$attribute_position++;
			}							
		}						
		
		update_post_meta( $pid, '_product_attributes', $attributes );		

		$date_from = isset( $product_sale_price_dates_from[$i] ) ? $product_sale_price_dates_from[$i] : '';
		$date_to = isset( $product_sale_price_dates_to[$i] ) ? $product_sale_price_dates_to[$i] : '';

		// Dates
		if ( $date_from )
			update_post_meta( $pid, '_sale_price_dates_from', strtotime( $date_from ) );
		else
			update_post_meta( $pid, '_sale_price_dates_from', '' );

		if ( $date_to )
			update_post_meta( $pid, '_sale_price_dates_to', strtotime( $date_to ) );
		else
			update_post_meta( $pid, '_sale_price_dates_to', '' );

		if ( $date_to && ! $date_from )
			update_post_meta( $pid, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );

		// Update price if on sale
		if ( $product_sale_price[$i] != '' && $date_to == '' && $date_from == '' )
			update_post_meta( $pid, '_price', stripslashes( $product_sale_price[$i] ) );
		else
			update_post_meta( $pid, '_price', stripslashes( $product_regular_price[$i] ) );

		if ( $product_sale_price[$i] != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
			update_post_meta( $pid, '_price', stripslashes($product_sale_price[$i]) );

		if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $pid, '_price', stripslashes($product_regular_price[$i]) );
			update_post_meta( $pid, '_sale_price_dates_from', '');
			update_post_meta( $pid, '_sale_price_dates_to', '');
		}		

		// Sold Individuall
		if ( "yes" == $product_sold_individually[$i] ) {
			update_post_meta( $pid, '_sold_individually', 'yes' );
		} else {
			update_post_meta( $pid, '_sold_individually', '' );
		}
		
		// Stock Data
		if ( $product_manage_stock[$i] == 'yes' ) {

			if ( ! empty( $product_manage_stock[$i] ) ) {

				// Manage stock
				update_post_meta( $pid, '_stock', (int) $product_stock_qty[$i] );
				update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				update_post_meta( $pid, '_backorders', stripslashes( $product_allow_backorders[$i] ) );
				update_post_meta( $pid, '_manage_stock', 'yes' );

				// Check stock level
				if ( $product_type !== 'variable' && $product_allow_backorders[$i] == 'no' && (int) $product_stock_qty[$i] < 1 )
					update_post_meta( $pid, '_stock_status', 'outofstock' );

			} else {

				// Don't manage stock
				update_post_meta( $pid, '_stock', '' );
				update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				update_post_meta( $pid, '_backorders', stripslashes( $product_allow_backorders[$i] ) );
				update_post_meta( $pid, '_manage_stock', 'no' );

			}

		} else {

			update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );

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

			update_post_meta( $pid, '_upsell_ids', $upsells );
		} else {
			delete_post_meta( $pid, '_upsell_ids' );
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

			update_post_meta( $pid, '_crosssell_ids', $crosssells );
		} else {
			delete_post_meta( $pid, '_crosssell_ids' );
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
			if ( !empty( $product_file_paths[$i] ) ) {
				$_file_paths = array();
				
				$file_paths = explode( $import->options['product_files_delim'] , $product_file_paths[$i] );

				foreach ( $file_paths as $file_path ) {
					$file_path = trim( $file_path );
					$_file_paths[ md5( $file_path ) ] = $file_path;
				}				

				// grant permission to any newly added files on any existing orders for this product
				do_action( 'woocommerce_process_product_file_download_paths', $pid, 0, $_file_paths );

				update_post_meta( $pid, '_file_paths', $_file_paths );
			}
			if ( isset( $product_download_limit[$i] ) )
				update_post_meta( $pid, '_download_limit', esc_attr( $_download_limit ) );
			if ( isset( $product_download_expiry[$i] ) )
				update_post_meta( $pid, '_download_expiry', esc_attr( $_download_expiry ) );
		}

		// Product url
		if ( $product_type == 'external' ) {
			if ( isset( $product_url[$i] ) && $product_url[$i] )
				update_post_meta( $pid, '_product_url', esc_attr( $product_url[$i] ) );
			if ( isset( $product_button_text[$i] ) && $product_button_text[$i] )
				update_post_meta( $pid, '_button_text', esc_attr( $product_button_text[$i] ) );
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
	
	/**
	 * Find duplicates according to settings
	 */
	public function findDuplicates($articleData, $custom_duplicate_name = '', $custom_duplicate_value = '', $duplicate_indicator = 'title')
	{		
		if ('custom field' == $duplicate_indicator){
			$duplicate_ids = array();
			$args = array(
				'post_type' => $articleData['post_type'],
				'meta_query' => array(
					array(
						'key' => $custom_duplicate_name,
						'value' => $custom_duplicate_value,						
					)
				)
			);			
			$query = new WP_Query( $args );
			
			if ( $query->have_posts() ) $duplicate_ids[] = $query->post->ID;

			wp_reset_postdata();

			return $duplicate_ids;
		}
		else{
			$field = 'post_' . $duplicate_indicator; // post_title or post_content
			return $this->wpdb->get_col($this->wpdb->prepare("
				SELECT ID FROM " . $this->wpdb->posts . "
				WHERE
					post_type = %s
					AND ID != %s
					AND REPLACE(REPLACE(REPLACE($field, ' ', ''), '\\t', ''), '\\n', '') = %s
				",
				$articleData['post_type'],
				isset($articleData['ID']) ? $articleData['ID'] : 0,
				preg_replace('%[ \\t\\n]%', '', $articleData[$field])
			));
		}
	}
		
	
}
