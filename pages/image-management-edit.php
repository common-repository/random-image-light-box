<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
if(!is_numeric($did)) { 
	die('<p>Are you sure you want to do this?</p>'); 
}

$result = rilb_cls_dbquery::rilb_count($did);
if ($result != '1') {
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'random-image-light-box'); ?></strong></p></div><?php
}
else {
	$ri_errors = array();
	$ri_success = '';
	$ri_error_found = false;
	
	$data = array();
	$data = rilb_cls_dbquery::rilb_select_byid($did);
	
	$form = array(
		'ri_id' => $data['ri_id'],
		'ri_image' => $data['ri_image'],
		'ri_link' => $data['ri_link'],
		'ri_title' => $data['ri_title'],
		'ri_width' => $data['ri_width'],
		'ri_status' => $data['ri_status'],
		'ri_group' => $data['ri_group']
	);
}

if (isset($_POST['ri_form_submit']) && $_POST['ri_form_submit'] == 'yes') {
	check_admin_referer('ri_form_edit');
	
	$form['ri_image'] = isset($_POST['ri_image']) ? esc_url_raw($_POST['ri_image']) : '';
	if ($form['ri_image'] == '') {
		$ri_errors[] = __('Please enter the image path.', 'random-image-light-box');
		$ri_error_found = true;
	}
	$form['ri_title'] = isset($_POST['ri_title']) ? sanitize_text_field($_POST['ri_title']) : '';
	$form['ri_link'] = isset($_POST['ri_image_link']) ? esc_url_raw($_POST['ri_image_link']) : '';
	$form['ri_group'] = isset($_POST['ri_group']) ? sanitize_text_field($_POST['ri_group']) : '';
	if ($form['ri_group'] == '') {
		$form['ri_group'] = isset($_POST['ri_group_txt']) ? sanitize_text_field($_POST['ri_group_txt']) : '';
	}
	if ($form['ri_group'] == '') {
		$ri_errors[] = __('Please enter the image group.', 'random-image-light-box');
		$ri_error_found = true;
	}
	$form['ri_width'] = isset($_POST['ri_width']) ? intval($_POST['ri_width']) : '0';
	$form['ri_status'] = isset($_POST['ri_status']) ? sanitize_text_field($_POST['ri_status']) : '';
	$form['ri_id'] = isset($_POST['ri_id']) ? sanitize_text_field($_POST['ri_id']) : '';

	if ($ri_error_found == FALSE)
	{	
		$status = rilb_cls_dbquery::rilb_action_ins($form, "update");
		if($status == 'update') {
			$ri_success = __('Image details was successfully updated.', 'random-image-light-box');
		}
		else {
			$ri_errors[] = __('Oops, something went wrong. try again.', 'random-image-light-box');
			$ri_error_found = true;
		}
	}
}

if ($ri_error_found == true && isset($ri_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $ri_errors[0]; ?></strong></p></div><?php
}

if ($ri_error_found == false && strlen($ri_success) > 0) {
	?><div class="updated fade"><p><strong><?php echo $ri_success; ?>
	<a href="<?php echo RILBP_ADMIN_URL; ?>"><?php _e('Click here', 'random-image-light-box'); ?></a> <?php _e('to view the details', 'random-image-light-box'); ?>
	</strong></p></div><?php
}

?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            // Let's assign the url value to the input field
            $('#ri_image').val(img_imageurl);
			//$('#ri_title').val(img_imagetitle);
        });
    });
	$('#upload-btn1').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image1 = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image1);
            var img_imageurl1 = uploaded_image1.toJSON().url;
            // Let's assign the url value to the input field
            $('#ri_image_link').val(img_imageurl1);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery');
wp_enqueue_media();
?>
<div class="form-wrap">
	<h1 class="wp-heading-inline"><?php _e('Update image', 'random-image-light-box'); ?></h1><br /><br />
	<form name="ri_form" method="post" action="#" onsubmit="return _ri_submit()"  >
      
	  <label for="tag-image"><strong><?php _e('Image path (URL)', 'random-image-light-box'); ?></strong></label>
      <input name="ri_image" type="text" id="ri_image" value="<?php echo $data['ri_image']; ?>" size="60" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the image located on the internet. Thumbnail image.', 'random-image-light-box'); ?> <br />
	  <a href="<?php echo $data['ri_image']; ?>" target="_blank"><img src="<?php echo $data['ri_image']; ?>" width="40"  /></a></p>
	  
	  <label for="tag-link"><strong><?php _e('Image title', 'random-image-light-box'); ?></strong></label>
      <input name="ri_title" type="text" id="ri_title" value="<?php echo $form['ri_title']; ?>" size="60" />
      <p><?php _e('Enter title for your image.', 'random-image-light-box'); ?></p>
	  
	  <label for="tag-width"><strong><?php _e('Thumbnail width', 'cool-fade-popup'); ?></strong></label>
	  <input name="ri_width" type="text" id="ri_width" value="<?php echo $form['ri_width']; ?>" maxlength="3" />
	  <p><?php _e('Please enter the thumbnail width (Optional).', 'cool-fade-popup'); ?></p>
	  
	  <label for="tag-image"><strong><?php _e('Image path (Optional)', 'random-image-light-box'); ?></strong></label>
      <input name="ri_image_link" type="text" id="ri_image_link" value="<?php echo $data['ri_link']; ?>" size="60" />
	  <input type="button" name="upload-btn1" id="upload-btn1" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the image located on the internet. Big image.', 'random-image-light-box'); ?> <br />
	  <?php if ($data['ri_link'] <> '') { ?>
	  <a href="<?php echo $data['ri_link']; ?>" target="_blank"><img src="<?php echo $data['ri_link']; ?>" width="60"  /></a>
	  <?php } ?>
	  </p>
      <label for="tag-select-gallery-group"><strong><?php _e('Image group', 'random-image-light-box'); ?></strong></label>
		<select name="ri_group" id="ri_group">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$selected = "";
			$groups = array();
			$groups = rilb_cls_dbquery::rilb_group();
			
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					if(strtoupper($form['ri_group']) == strtoupper($group["ri_group"])) { 
						$selected = "selected"; 
					}
					?>
					<option value="<?php echo stripslashes($group["ri_group"]); ?>" <?php echo $selected; ?>>
						<?php echo stripslashes($group["ri_group"]); ?>
					</option>
					<?php
					$selected = "";
				}
			}
			?>
		</select>
		(or) 
	   	<input name="ri_group_txt" type="text" id="ri_group_txt" value="" maxlength="10" onkeyup="return _ri_numericandtext(document.ri_form.ri_group_txt)" />
      <p><?php _e('This is to group the images. Select your slideshow group.', 'random-image-light-box'); ?></p>
	  
      <label for="tag-display-status"><strong><?php _e('Display', 'random-image-light-box'); ?></strong></label>
      <select name="ri_status" id="ri_status">
        <option value='Yes' <?php if($form['ri_status'] == 'Yes') { echo 'selected' ; } ?>>Yes</option>
        <option value='No' <?php if($form['ri_status'] == 'No') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Do you want the image to show in the frontend?', 'random-image-light-box'); ?></p>
	  
      <input name="ri_id" id="ri_id" type="hidden" value="<?php echo $form['ri_id']; ?>">
      <input type="hidden" name="ri_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'random-image-light-box'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_ri_redirect()" value="<?php _e('Cancel', 'random-image-light-box'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_ri_help()" value="<?php _e('Help', 'random-image-light-box'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('ri_form_edit'); ?>
    </form>
</div>
</div>