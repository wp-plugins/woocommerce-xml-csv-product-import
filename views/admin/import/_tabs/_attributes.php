<div class="panel woocommerce_options_panel" id="woocommerce_attributes" style="display:none;">
	<div style="margin-left:-2%;">
		<div class="woo-add-on-free-edition-notice upgrade_template" style="margin-top:0;">
			<a href="http://www.wpallimport.com/upgrade-to-pro/?utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=woocommerce" target="_blank" class="upgrade_woo_link"><?php _e('Upgrade to the professional edition of WP All Import and the WooCommerce add-on to import to Variable, Affiliate, and Grouped products.', 'pmxi_plugin');?></a>
		</div>
	</div>	
	<div class="input">
		<table class="form-table custom-params" id="attributes_table" style="max-width:95%;">
			<thead>
				<tr>
					<td><?php _e('Name', 'pmxi_plugin'); ?></td>
					<td style="padding-bottom: 5px;">
						<?php _e('Values', 'pmxi_plugin'); ?>
						<a href="#help" class="wpallimport-help" title="<?php _e('Separate mutiple values with a |', 'pmxi_plugin') ?>" style="top:-1px;">?</a>
					</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($post['attribute_name'][0])):?>
					<?php foreach ($post['attribute_name'] as $i => $name): if ("" == $name) continue; ?>
						<tr class="form-field">
							<td style="width: 50%;">
								<input type="text" class="widefat" name="attribute_name[]"  value="<?php echo esc_attr($name) ?>" style="width:100%;"/>
							</td>
							<td style="width: 50%;">
								<input type="text" class="widefat" name="attribute_value[]" value="<?php echo str_replace("&amp;","&", htmlentities(htmlentities($post['attribute_value'][$i]))); ?>" style="width:100%;"/>						
								<span class="wpallimport-clear"></span>
								<p class="form-field wpallimport-radio-field" style="padding: 0 !important; position: relative; left: -100%; width: 200%;">
									<span class='in_variations'>													
										<input type="checkbox" name="in_variations[]" id="in_variations_<?php echo $i; ?>" <?php echo ($post['in_variations'][$i]) ? 'checked="checked"' : ''; ?> style="float: left;" value="1"/>
										<label for="in_variations_<?php echo $i; ?>"><?php _e('In Variations','pmxi_plugin');?></label>															
									</span>

									<span class='is_visible'>
										<input type="checkbox" name="is_visible[]" id="is_visible_<?php echo $i; ?>" <?php echo ($post['is_visible'][$i]) ? 'checked="checked"' : ''; ?> style="float: left;" value="1"/>
										<label for="is_visible_<?php echo $i; ?>"><?php _e('Is Visible','pmxi_plugin');?></label>																									
									</span>

									<span class='is_taxonomy'>
										<input type="checkbox" name="is_taxonomy[]" id="is_taxonomy_<?php echo $i; ?>" <?php echo ($post['is_taxonomy'][$i]) ? 'checked="checked"' : ''; ?> style="float: left;" value="1"/>
										<label for="is_taxonomy_<?php echo $i; ?>"><?php _e('Taxonomy','pmxi_plugin');?></label>													
									</span>

									<span class='is_create_taxonomy'>
										<input type="checkbox" name="create_taxonomy_in_not_exists[]" id="create_taxonomy_in_not_exists_<?php echo $i; ?>" <?php echo ($post['create_taxonomy_in_not_exists'][$i]) ? 'checked="checked"' : ''; ?> style="float: left;" value="1"/>
										<label for="create_taxonomy_in_not_exists_<?php echo $i; ?>"><?php _e('Auto-Create Terms','pmxi_plugin');?></label>													
									</span>												
								</p>
							</td>
							<td class="action remove"><a href="#remove" style="top:9px;"></a></td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
				<tr class="form-field">
					<td style="width: 50%;">
						<input type="text" name="attribute_name[]" value="" class="widefat" style="width:100%;"/>
					</td>
					<td style="width: 50%;">
						<input type="text" name="attribute_value[]" class="widefat" vaalue="" style="width:100%;"/>
						<span class="wpallimport-clear"></span>					
						<p class="form-field wpallimport-radio-field" style="padding: 0 !important; position: relative; left: -100%; width: 200%;">
							<span class='in_variations'>
								<input type="checkbox" name="in_variations[]" id="in_variations_0" checked="checked" style="float: left;" value="1"/>
								<label for="in_variations_0"><?php _e('In Variations','pmxi_plugin');?></label>											
							</span>
							<span class='is_visible'>
								<input type="checkbox" name="is_visible[]" id="is_visible_0" checked="checked" style="float: left;" value="1"/>
								<label for="is_visible_0"><?php _e('Is Visible','pmxi_plugin');?></label>
							</span>
							<span class='is_taxonomy'>
								<input type="checkbox" name="is_taxonomy[]" id="is_taxonomy_0" checked="checked" style="float: left;" value="1"/>
								<label for="is_taxonomy_0"><?php _e('Taxonomy','pmxi_plugin');?></label>
							</span>
							<span class='is_create_taxonomy'>
								<input type="checkbox" name="create_taxonomy_in_not_exists[]" id="create_taxonomy_in_not_exists_0" checked="checked" style="float: left;" value="1"/>
								<label for="create_taxonomy_in_not_exists_0"><?php _e('Auto-Create Terms','pmxi_plugin');?></label>
							</span>
						</p>
					</td>
					<td class="action remove"><a href="#remove" style="top: 9px;"></a></td>
				</tr>
				<?php endif;?>
				<tr class="form-field template">
					<td style="width: 50%;">
						<input type="text" name="attribute_name[]" value="" class="widefat" style="width:100%;"/>
					</td>
					<td style="width: 50%;">
						<input type="text" name="attribute_value[]" class="widefat" value="" style="width:100%;"/>
						<span class="wpallimport-clear"></span>
						<p class="form-field wpallimport-radio-field" style="padding: 0 !important; position: relative; left: -100%; width: 200%;">
							<span class='in_variations'>
								<input type="checkbox" name="in_variations[]" checked="checked" style="float: left;" value="1"/>
								<label for=""><?php _e('In Variations','pmxi_plugin');?></label>																	
							</span>
							<span class='is_visible'>
								<input type="checkbox" name="is_visible[]" checked="checked" style="float: left;" value="1"/>
								<label for=""><?php _e('Is Visible','pmxi_plugin');?></label>																	
							</span>
							<span class='is_taxonomy'>
								<input type="checkbox" name="is_taxonomy[]" checked="checked" style="float: left;" value="1"/>
								<label for=""><?php _e('Taxonomy','pmxi_plugin');?></label>																	
							</span>
							<span class='is_create_taxonomy'>
								<input type="checkbox" name="create_taxonomy_in_not_exists[]" checked="checked" style="float: left;" value="1"/>
								<label for=""><?php _e('Auto-Create Terms','pmxi_plugin');?></label>																	
							</span>										
						</p>
					</td>
					<td class="action remove"><a href="#remove" style="top: 9px;"></a></td>
				</tr>
				<tr>
					<td colspan="3"><a href="#add" title="<?php _e('add', 'pmxi_plugin')?>" class="action add-new-custom"><?php _e('Add more', 'pmxi_plugin') ?></a></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="options_group show_if_variable">
		<p class="form-field wpallimport-radio-field" style="padding-left: 10px !important;">
			<input type="hidden" name="link_all_variations" value="0" />
			<input type="checkbox" id="link_all_variations" name="link_all_variations" value="1" <?php echo $post['link_all_variations'] ? 'checked="checked"' : '' ?>/>
			<label style="width: 100px;" for="link_all_variations"><?php _e('Link all variations', 'pmxi_plugin') ?></label>
			<a href="#help" class="wpallimport-help" title="<?php _e('This option will create all possible variations for the presented attributes. Works just like the Link All Variations option inside WooCommerce.', 'pmxi_plugin') ?>" style="top:3px;">?</a>
		</p>
	</div>
</div><!-- End Product Panel -->