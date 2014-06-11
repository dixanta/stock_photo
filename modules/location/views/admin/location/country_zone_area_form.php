

<?php echo form_open('location/admin/location/zone_area_form/'.$zone_id.'/'.$id); ?>

	<label for="code"><?php echo lang('code');?></label>
	<?php
	$data	= array( 'name'=>'code', 'value'=>set_value('code', $code), 'class'=>'span12');
	echo form_input($data);
	?>
	
	<label for="tax"><?php echo lang('tax');?></label>
	<div class="input-append">
		<?php
		$data	= array('name'=>'tax', 'maxlength'=>'10', 'value'=>set_value('tax', $tax));
		echo form_input($data);
		?>
		<span class="add-on">%</span>
	</div>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><?php echo lang('form_save');?>Save</button>
		<a href="<?php echo site_url('location/admin/location/zone_areas/'.$zone_id.'/'.$id)?>" class="btn btn-danger">Cancel</a>
	</div>

</form>

<script type="text/javascript">
$('form').submit(function() {
	$('.btn').attr('disabled', true).addClass('disabled');
});
</script>


