<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? intval(sanitize_text_field($_GET['did'])) : '0';
if(!is_numeric($did)) { 
	die('<p>Are you sure you want to do this?</p>'); 
}

$result = mislider_cls_dbquery::mislider_count($did);
if ($result != '1') {
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'moving-image-slider'); ?></strong></p></div><?php
}
else {
	
	$mislider_errors = array();
	$mislider_success = '';
	$mislider_error_found = false;

	$data = array();
	$data = mislider_cls_dbquery::mislider_select_byid($did);
	
	$form = array(	
		'mislider_id' => $data['mislider_id'],
		'mislider_title' => $data['mislider_title'],
		'mislider_desc' => $data['mislider_desc'],
		'mislider_img' => $data['mislider_img'],
		'mislider_slide' => $data['mislider_slide'],
		'mislider_group' => $data['mislider_group'],
		'mislider_status' => $data['mislider_status']
	);
}

if (isset($_POST['mislider_form_submit']) && sanitize_text_field($_POST['mislider_form_submit']) == 'yes') {
	check_admin_referer('mislider_form_edit');
	
	$form['mislider_img'] = isset($_POST['mislider_img']) ? esc_url_raw($_POST['mislider_img']) : '';
	if ($form['mislider_img'] == '') {
		$mislider_errors[] = __('Please enter image path.', 'moving-image-slider');
		$mislider_error_found = true;
	}

	$form['mislider_title'] = isset($_POST['mislider_title']) ? sanitize_text_field($_POST['mislider_title']) : '';
	if ($form['mislider_title'] == '') {
		$mislider_errors[] = __('Please enter image title.', 'moving-image-slider');
		$mislider_error_found = true;
	}
	
	$form['mislider_desc'] = isset($_POST['mislider_desc']) ? sanitize_text_field($_POST['mislider_desc']) : '';
	$form['mislider_slide'] = isset($_POST['mislider_slide']) ? sanitize_text_field($_POST['mislider_slide']) : '';
	
	$form['mislider_group'] = isset($_POST['mislider_group']) ? sanitize_text_field($_POST['mislider_group']) : '';
	if ($form['mislider_group'] == '') {
		$form['mislider_group'] = isset($_POST['mislider_group_txt']) ? sanitize_text_field($_POST['mislider_group_txt']) : '';
	}
	if ($form['mislider_group'] == '') {
		$mislider_errors[] = __('Please enter the image group.', 'moving-image-slider');
		$mislider_error_found = true;
	}

	$form['mislider_status'] = isset($_POST['mislider_status']) ? sanitize_text_field($_POST['mislider_status']) : '';	
	$form['mislider_id'] = isset($_POST['mislider_id']) ? sanitize_text_field($_POST['mislider_id']) : '';

	if ($mislider_error_found == FALSE)
	{	
		$status = mislider_cls_dbquery::mislider_action_ins($form, "update");
		if($status == 'update') {
			$mislider_success = __('Image details was successfully updated.', 'moving-image-slider');
		}
		else {
			$mislider_errors[] = __('Oops, something went wrong. try again.', 'moving-image-slider');
			$mislider_error_found = true;
		}
	}
}

if ($mislider_error_found == true && isset($mislider_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $mislider_errors[0]; ?></strong></p></div><?php
}

if ($mislider_error_found == false && strlen($mislider_success) > 0) {
	?><div class="updated fade"><p><strong><?php echo $mislider_success; ?>
	<a href="<?php echo MISLIDERS_ADMIN_URL; ?>"><?php _e('Click here', 'moving-image-slider'); ?></a> <?php _e('to view the details', 'moving-image-slider'); ?>
	</strong></p></div><?php
}

?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            $('#mislider_img').val(img_imageurl);
			$('#mislider_title').val(img_imagetitle);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery');
wp_enqueue_media();
?>
<div class="form-wrap">
	<h1 class="wp-heading-inline"><?php _e('Update image details', 'moving-image-slider'); ?></h1><br /><br />
	<form name="mislider_form" method="post" action="#" onsubmit="return _mislider_submit()"  >
      
	  <label for="tag-image"><strong><?php _e('Image', 'moving-image-slider'); ?></strong></label>
      <input name="mislider_img" type="text" id="mislider_img" value="<?php echo $form['mislider_img']; ?>" size="60" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
      <p><?php _e('Upload (or enter image path) image for the moving slider.', 'moving-image-slider'); ?> </p>
	  <p><img src="<?php echo $form['mislider_img']; ?>" width="100"  /></p>
	   
	  <label for="tag-title"><strong><?php _e('Image title', 'moving-image-slider'); ?></strong></label>
      <input name="mislider_title" type="text" id="mislider_title" value="<?php echo stripslashes(esc_html($form['mislider_title'])); ?>" size="60" maxlength="50" />
      <p><?php _e('Please enter image title. only for admin reference.', 'moving-image-slider'); ?></p>
	  
	  <label for="tag-description"><strong><?php _e('Image description', 'moving-image-slider'); ?></strong></label>
      <input name="mislider_desc" type="text" id="mislider_desc" value="<?php echo stripslashes(esc_html($form['mislider_desc'])); ?>" size="60" maxlength="500" />
      <p><?php _e('Please enter image description. This will be shown in the slider.', 'moving-image-slider'); ?> </p>
	  
	  <label for="tag-slide"><strong><?php _e('Slide', 'moving-image-slider'); ?></strong></label>
      <select name="mislider_slide" id="mislider_slide">
        <option value='Random' <?php if($form['mislider_slide'] == 'Random') { echo 'selected' ; } ?>>Random</option>
		<option value='Zoom In' <?php if($form['mislider_slide'] == 'Zoom In') { echo 'selected' ; } ?>>Zoom In</option>
		<option value='Zoom Out' <?php if($form['mislider_slide'] == 'Zoom Out') { echo 'selected' ; } ?>>Zoom Out</option>
		<option value='Pan Up' <?php if($form['mislider_slide'] == 'Pan Up') { echo 'selected' ; } ?>>Pan Up</option>
        <option value='Pan Left' <?php if($form['mislider_slide'] == 'Pan Left') { echo 'selected' ; } ?>>Pan Left</option>
      </select>
      <p><?php _e('Please enter image slide.', 'moving-image-slider'); ?></p>

      <label for="tag-select-gallery-group"><strong><?php _e('Image group', 'moving-image-slider'); ?></strong></label>
		<select name="mislider_group" id="mislider_group">
			<option value=''><?php _e('Select', 'moving-image-slider'); ?></option>
			<?php
			$selected = "";
			$groups = array();
			$groups = mislider_cls_dbquery::mislider_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					if(strtoupper($form['mislider_group']) == strtoupper($group["mislider_group"])) { 
						$selected = "selected"; 
					}
					?>
					<option value="<?php echo stripslashes($group["mislider_group"]); ?>" <?php echo $selected; ?>>
						<?php echo stripslashes($group["mislider_group"]); ?>
					</option>
					<?php
					$selected = "";
				}
			}
			?>
		</select>
		(or) 
	   	<input name="mislider_group_txt" type="text" id="mislider_group_txt" value="" maxlength="10" onkeyup="return _mislider_numericandtext(document.mislider_form.mislider_group_txt)" />
      <p><?php _e('This is to group the images. Select your group.', 'moving-image-slider'); ?></p>

	  
      <label for="tag-display-status"><strong><?php _e('Display', 'moving-image-slider'); ?></strong></label>
      <select name="mislider_status" id="mislider_status">
        <option value='Yes' <?php if($form['mislider_status'] == 'Yes') { echo 'selected' ; } ?>>Yes</option>
        <option value='No' <?php if($form['mislider_status'] == 'No') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Do you want the image to show in the frontend?', 'moving-image-slider'); ?></p>
	  
      <input name="mislider_id" id="mislider_id" type="hidden" value="<?php echo $form['mislider_id']; ?>">
      <input type="hidden" name="mislider_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'moving-image-slider'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_mislider_redirect()" value="<?php _e('Cancel', 'moving-image-slider'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_mislider_help()" value="<?php _e('Help', 'moving-image-slider'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('mislider_form_edit'); ?>
    </form>
</div>
</div>