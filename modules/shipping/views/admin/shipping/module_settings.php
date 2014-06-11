

<div class="row">
	<div class="span12">
		<?php echo form_open('shipping/admin/shipping/settings/'. $module);?>
			<fieldset>
<?php
echo $form;
?>
				<div class="form-actions">
					<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
					<a href="<?php echo site_url('setting/admin/setting');?>" class="btn btn-danger">Cancel</a>
				</div>
			</fieldset>
		</form>
	</div>
</div>
