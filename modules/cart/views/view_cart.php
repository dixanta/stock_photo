

<?php if ($this->go_cart->total_items()==0):?>
	<div class="alert alert-info">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo lang('empty_view_cart');?>
	</div>
<?php else: ?>
	
	<div class="page-header">
		<h2><?php echo lang('your_cart');?></h2>
	</div>
	<?php echo form_open('cart/update_cart', array('id'=>'update_cart_form'));?>
	
	<?php include('summary.php');?>
	
	
	<div class="row">
		<div class="span5">
			<label><?php echo lang('coupon_label');?></label>
			<input type="text" name="coupon_code" class="span3" style="margin:0px;">
			<input class="span2 btn" type="submit" value="<?php echo lang('apply_coupon');?>Apply Coupon"/>
			
			<?php if($gift_cards_enabled):?>
				<label style="margin-top:15px;"><?php echo lang('gift_card_label');?></label>
				<input type="text" name="gc_code" class="span3" style="margin:0px;">
				<input class="span2 btn"  type="submit" value="<?php echo lang('apply_gift_card');?>Apply gift card"/>
			<?php endif;?>
		</div>
		
		<div class="span7" style="text-align:right;">
				<input id="redirect_path" type="hidden" name="redirect" value=""/>
	
				<?php if(!$this->Customer_model->is_logged_in(false,false)): ?>
					<input class="btn" type="submit" onclick="$('#redirect_path').val('checkout/login');" value="<?php echo lang('login');?>Login"/>
					<input class="btn" type="submit" onclick="$('#redirect_path').val('checkout/register');" value="<?php echo lang('register_now');?>Register now"/>
				<?php endif; ?>
					<input class="btn" type="submit" value="<?php echo lang('form_update_cart');?>Update Cart"/>
					
			<?php if ($this->Customer_model->is_logged_in(false,false) || !$this->config->item('require_login')): ?>
				<input class="btn btn-large btn-primary" type="submit" onclick="$('#redirect_path').val('checkout');" value="<?php echo lang('form_checkout');?>Check out"/>
			<?php endif; ?>
			
		</div>
	</div>

</form>
<?php endif; ?>

