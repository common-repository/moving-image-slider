<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$mislider_errors = array();
$mislider_success = '';
$mislider_error_found = false;

$form = array(
	'mislider_title' => '',
	'mislider_desc' => '',
	'mislider_img' => '',
	'mislider_slide' => '',
	'mislider_group' => '',
	'mislider_status' => ''
);

if (isset($_POST['mislider_form_submit']) && sanitize_text_field($_POST['mislider_form_submit']) == 'yes') {
	check_admin_referer('mislider_form_add');
	
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
	
	if ($mislider_error_found == false)
	{
		$status = mislider_cls_dbquery::mislider_action_ins($form, "insert");
		if($status == 'inserted') {
			$mislider_success = __('New image details was successfully added.', 'moving-image-slider');
		}
		else {
			$mislider_errors[] = __('Oops, something went wrong. try again.', 'moving-image-slider');
			$mislider_error_found = true;
		}
		
		$form = array(
			'mislider_title' => '',
			'mislider_desc' => '',
			'mislider_img' => '',
			'mislider_slide' => '',
			'mislider_group' => '',
			'mislider_status' => ''
		);
	}
}

if ($mislider_error_found == true && isset($mislider_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $mislider_errors[0]; ?></strong></p></div><?php
}
if ($mislider_error_found == FALSE && strlen($mislider_success) > 0) {
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
	<h1 class="wp-heading-inline"><?php _e('Add image details', 'moving-image-slider'); ?></h1><br /><br />
	<form name="mislider_form" method="post" action="#" onsubmit="return _mislider_submit()" >      
	    
	  <label for="tag-image"><strong><?php _e('Image', 'moving-image-slider'); ?></strong></label>
      <input name="mislider_img" type="text" id="mislider_img" value="" size="60" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
      <p><?php _e('Upload (or enter image path) image for the moving slider.', 'moving-image-slider'); ?> </p>
	   
	  <label for="tag-title"><strong><?php _e('Image title', 'moving-image-slider'); ?></strong></label>
      <input name="mislider_title" type="text" id="mislider_title" value="" size="60" maxlength="50" />
      <p><?php _e('Please enter image title. only for admin reference.', 'moving-image-slider'); ?></p>
	  
	  <label for="tag-description"><strong><?php _e('Image description', 'moving-image-slider'); ?></strong></label>
      <input name="mislider_desc" type="text" id="mislider_desc" value="" size="60" maxlength="500" />
      <p><?php _e('Please enter image description. This will be shown in the slider.', 'moving-image-slider'); ?> </p>
	  
	  <label for="tag-slide"><strong><?php _e('Slide', 'moving-image-slider'); ?></strong></label>
      <select name="mislider_slide" id="mislider_slide">
        <option value='Random'>Random</option>
		<option value='Zoom In'>Zoom In</option>
		<option value='Zoom Out'>Zoom Out</option>
		<option value='Pan Up'>Pan Up</option>
        <option value='Pan Left'>Pan Left</option>
      </select>
      <p><?php _e('Please enter image slide.', 'moving-image-slider'); ?></p>
	  
      <label for="tag-select-gallery-group"><strong><?php _e('Image group', 'moving-image-slider'); ?></strong></label>
		<select name="mislider_group" id="mislider_group">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$groups = array();
			$groups = mislider_cls_dbquery::mislider_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo stripslashes($group["mislider_group"]); ?>">
						<?php echo stripslashes($group["mislider_group"]); ?>
					</option>
					<?php
				}
			}
			?>
		</select>
		(or) 
	   	<input name="mislider_group_txt" type="text" id="mislider_group_txt" value="" maxlength="10" onkeyup="return _mislider_numericandtext(document.mislider_form.mislider_group_txt)" />
      <p><?php _e('This is to group the images. Select your group.', 'moving-image-slider'); ?></p>
	  
      <label for="tag-display-status"><strong><?php _e('Display', 'moving-image-slider'); ?></strong></label>
      <select name="mislider_status" id="mislider_status">
        <option value='Yes'>Yes</option>
        <option value='No'>No</option>
      </select>
      <p><?php _e('Do you want the image to show in the frontend?', 'moving-image-slider'); ?></p>
	  
      <input name="mislider_id" id="mislider_id" type="hidden" value="">
      <input type="hidden" name="mislider_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'moving-image-slider'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_mislider_redirect()" value="<?php _e('Cancel', 'moving-image-slider'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_mislider_help()" value="<?php _e('Help', 'moving-image-slider'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('mislider_form_add'); ?>
    </form>
</div>
</div>