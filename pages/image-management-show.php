<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if (isset($_POST['frm_ri_display']) && $_POST['frm_ri_display'] == 'yes') {
	$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
	if(!is_numeric($did)) { 
		die('<p>Are you sure you want to do this?</p>'); 
	}
	
	$ri_success = '';
	$ri_success_msg = false;
	$result = rilb_cls_dbquery::rilb_count($did);
	
	if ($result != '1') {
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'random-image-light-box'); ?></strong></p></div><?php
	}
	else {
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '') {
			check_admin_referer('ri_form_show');
			rilb_cls_dbquery::rilb_delete($did);
			$ri_success_msg = true;
			$ri_success = __('Selected record was successfully deleted.', 'random-image-light-box');
		}
	}
	
	if ($ri_success_msg == true) {
		?><div class="updated fade"><p><strong><?php echo $ri_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
    <h2><?php _e('Random image light box', 'random-image-light-box'); ?>
	<a class="add-new-h2" href="<?php echo RILBP_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'random-image-light-box'); ?></a></h2><br />
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = rilb_cls_dbquery::rilb_select_bygroup("");
	?>
	<form name="frm_ri_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Title', 'random-image-light-box'); ?></th>
			<th scope="col"><?php _e('Image', 'random-image-light-box'); ?></th>
            <th scope="col"><?php _e('Group', 'random-image-light-box'); ?></th>
            <th scope="col"><?php _e('Status', 'random-image-light-box'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
		  	<th scope="col"><?php _e('Title', 'random-image-light-box'); ?></th>
			<th scope="col"><?php _e('Image', 'random-image-light-box'); ?></th>
            <th scope="col"><?php _e('Group', 'random-image-light-box'); ?></th>
            <th scope="col"><?php _e('Status', 'random-image-light-box'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 ) {
			foreach ($myData as $data) {
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td><?php echo $data['ri_title']; ?>
						<div class="row-actions">
							<span class="edit"><a title="Edit" href="<?php echo RILBP_ADMIN_URL; ?>&ac=edit&amp;did=<?php echo $data['ri_id']; ?>"><?php _e('Edit', 'random-image-light-box'); ?></a> | </span>
							<span class="trash"><a onClick="javascript:_ri_delete('<?php echo $data['ri_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'random-image-light-box'); ?></a></span> 
						</div>
					</td>
					<td>
						<a href="<?php echo $data['ri_image']; ?>" target="_blank">
							<img src="<?php echo $data['ri_image']; ?>" width="40"  />
						</a>
						<?php if($data['ri_link'] <> '') { ?>
						<a href="<?php echo $data['ri_link']; ?>" target="_blank"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/link-icon.gif"  /></a>
						<?php } ?>
					</td>
					<td><?php echo $data['ri_group']; ?></td>
					<td><?php echo rilb_cls_dbquery::rilb_common_text($data['ri_status']); ?></td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else {
			?><tr><td colspan="5" align="center"><?php _e('No records available', 'random-image-light-box'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('ri_form_show'); ?>
		<input type="hidden" name="frm_ri_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo RILBP_ADMIN_URL; ?>&amp;ac=add">
	  <input class="button button-primary" type="button" value="<?php _e('Add New', 'random-image-light-box'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2020/10/11/wordpress-plugin-random-image-light-box/">
	  <input class="button button-primary" type="button" value="<?php _e('Short Code', 'random-image-light-box'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2020/10/11/wordpress-plugin-random-image-light-box/">
	  <input class="button button-primary" type="button" value="<?php _e('Help', 'random-image-light-box'); ?>" /></a>
	  </div>
	</div>
</div>