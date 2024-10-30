<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if (isset($_POST['frm_mislider_display']) && sanitize_text_field($_POST['frm_mislider_display']) == 'yes') {
	$did = isset($_GET['did']) ? intval(sanitize_text_field($_GET['did'])) : '0';
	if(!is_numeric($did)) { 
		die('<p>Are you sure you want to do this?</p>'); 
	}
	
	$mislider_success = '';
	$mislider_success_msg = false;
	$result = mislider_cls_dbquery::mislider_count($did);
	
	if ($result != '1') {
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'moving-image-slider'); ?></strong></p></div><?php
	}
	else {
		if (isset($_GET['ac']) && sanitize_text_field($_GET['ac']) == 'del' && isset($_GET['did']) && intval($_GET['did']) != '') {
			check_admin_referer('mislider_form_show');
			mislider_cls_dbquery::mislider_delete($did);
			$mislider_success_msg = true;
			$mislider_success = __('Selected record was successfully deleted.', 'moving-image-slider');
		}
	}
	
	if ($mislider_success_msg == true) {
		?><div class="updated fade"><p><strong><?php echo $mislider_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
    <h2><?php _e('Moving image slider', 'moving-image-slider'); ?>
	<a class="add-new-h2" href="<?php echo MISLIDERS_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'moving-image-slider'); ?></a></h2><br />
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = mislider_cls_dbquery::mislider_select_bygroup("");
	?>
	<form name="frm_mislider_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Image', 'moving-image-slider'); ?></th>
			<th scope="col"><?php _e('Title', 'moving-image-slider'); ?></th>
			<th scope="col"><?php _e('Style', 'moving-image-slider'); ?></th>
            <th scope="col"><?php _e('Group', 'moving-image-slider'); ?></th>
            <th scope="col"><?php _e('Status', 'moving-image-slider'); ?></th>
			<th scope="col"><?php _e('Code', 'moving-image-slider'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Image', 'moving-image-slider'); ?></th>
			<th scope="col"><?php _e('Title', 'moving-image-slider'); ?></th>
			<th scope="col"><?php _e('Style', 'moving-image-slider'); ?></th>
            <th scope="col"><?php _e('Group', 'moving-image-slider'); ?></th>
            <th scope="col"><?php _e('Status', 'moving-image-slider'); ?></th>
			<th scope="col"><?php _e('Code', 'moving-image-slider'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 ) {
			foreach ($myData as $data) {
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td>
						<img src="<?php echo $data['mislider_img']; ?>" width="100"  />
						<a href="<?php echo $data['mislider_img']; ?>" target="_blank"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/link-icon.gif"  /></a>
					</td>
					<td>
						<?php echo stripslashes($data['mislider_title']); ?>
						<div class="row-actions">
							<span class="edit"><a title="Edit" href="<?php echo MISLIDERS_ADMIN_URL; ?>&ac=edit&amp;did=<?php echo $data['mislider_id']; ?>"><?php _e('Edit', 'moving-image-slider'); ?></a> | </span>
							<span class="trash"><a onClick="javascript:_mislider_delete('<?php echo $data['mislider_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'moving-image-slider'); ?></a></span> 
						</div>
					</td>
					<td><?php echo $data['mislider_slide']; ?></td>
					<td><?php echo $data['mislider_group']; ?></td>
					<td><?php echo mislider_cls_dbquery::mislider_common_text($data['mislider_status']); ?></td>
					<td>[moving-image-slider group="<?php echo $data['mislider_group']; ?>"]<br />
					[moving-image-slider id="<?php echo $data['mislider_id']; ?>"]</td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else {
			?><tr><td colspan="5" align="center"><?php _e('No records available', 'moving-image-slider'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('mislider_form_show'); ?>
		<input type="hidden" name="frm_mislider_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo MISLIDERS_ADMIN_URL; ?>&amp;ac=add">
	  <input class="button button-primary" type="button" value="<?php _e('Add New', 'moving-image-slider'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2021/05/26/moving-image-slider-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Short Code', 'moving-image-slider'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2021/05/26/moving-image-slider-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Help', 'moving-image-slider'); ?>" /></a>
	  </div>
	</div>
</div>