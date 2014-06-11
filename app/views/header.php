
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo (!empty($seo_title)) ? $seo_title .' - ' : ''; echo $this->config->item('company_name'); ?></title>


<?php if(isset($meta)):?>
	<?php echo $meta;?>
<?php else:?>
<meta name="Keywords" content="Shopping Cart, eCommerce, Code Igniter">
<meta name="Description" content="Go Cart is an open source shopping cart built on the Code Igniter framework">
<?php endif;?>

<?php echo theme_css('bootstrap.min.css', true);?>
<?php echo theme_css('bootstrap-responsive.min.css', true);?>
<?php echo theme_css('styles.css', true);?>

<?php echo theme_js('jquery.js', true);?>
<?php echo theme_js('bootstrap.min.js', true);?>
<?php echo theme_js('squard.js', true);?>
<?php echo theme_js('equal_heights.js', true);?>

<?php
//with this I can put header data in the header instead of in the body.
if(isset($additional_header_info))
{
	echo $additional_header_info;
}

?>
</head>

<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">

				<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
			
				<a class="brand" href="<?php echo site_url();?>"><?php echo $this->config->item('company_name');?></a>
				
				<div class="nav-collapse">
					<ul class="nav">
						<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('catalog');?> Catalog<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php foreach($this->categories as $cat_menu):?>
								<?php foreach($cat_menu as $key => $value):?>
								<li><a href="<?php echo site_url($value->slug);?>"><?php echo $value->name;?></a></li>
								<?php endforeach;?>
								<?php endforeach;?>
							</ul>
							
							<?php foreach($this->pages as $menu_page):?>
							<?php foreach($menu_page as $key => $values):?>
								<li>

								<?php if(empty($values->content)):?>

                                    <a href="<?php echo $values->url;?>" <?php if($values->new_window ==1){echo 'target="_blank"';} ?>><?php echo $values->menu_title;?></a>
								<?php else:?>
									<a href="<?php echo site_url($values->slug);?>"><?php echo $values->menu_title;?></a>
								<?php endif;?>
								</li>
							<?php endforeach;?>	
							<?php endforeach;?>
					</ul>
					
					<ul class="nav pull-right">
						
						<?php if($this->Customer_model->is_logged_in(false, false)):?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('account');?> Account<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo  site_url('secure/my_account');?>"><?php echo lang('my_account')?>My account</a></li>
									<li><a href="<?php echo  site_url('secure/my_downloads');?>"><?php echo lang('my_downloads')?>My downloads</a></li>
									<li class="divider"></li>
									<li><a href="<?php echo site_url('secure/logout');?>"><?php echo lang('logout');?>Logout</a></li>
								</ul>
							</li>
						<?php else: ?>
							<li><a href="<?php echo site_url('secure/login');?>"><?php echo lang('login');?>LOGIN</a></li>
						<?php endif; ?>
							<li>
								<a href="<?php echo site_url('cart/view_cart');?>">
								<?php
								if ($this->go_cart->total_items()==0)
								{
									echo lang('empty_cart'); 
								}
								else
								{
									if($this->go_cart->total_items() > 1)
									{
										echo sprintf (lang('multiple_items').'Multiple items', $this->go_cart->total_items());
									}
									else
									{
										echo sprintf (lang('single_item').'single item', $this->go_cart->total_items());
									}
								}
								?>
								</a>
							</li>
					</ul>
					
					<?php echo form_open('search', 'class="navbar-search pull-right"');?>
						<input type="text" name="term" class="search-query span2" placeholder="<?php echo lang('search');?>Search"/>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="span12">
        <ul class="breadcrumb">
            <a href=""><li class="active" style="width: 100px">Photos</li></a>
            <a href=""><li class="active" style="width: 100px">Vectors</li></a>
            <a href=""><li class="active" style="width: 100px">Icons</li></a>
            <a href=""><li class="active" style="width: 100px">Videos</li></a>
            <a href=""><li class="active" style="width: 100px">Fresh!</li></a>
            <a href=""><li class="active" style="width: 100px">Special</li></a>
            <li class="active" style="width: 100px"><form style="height: 20px"><input type="text" placeholder="search"></form></li>
        </ul>
    </div>
	<div class="container">
		<?php if(!empty($base_url) && is_array($base_url)):?>
			<div class="row">
				<div class="span12">
					<ul class="breadcrumb">
						<?php
						$url_path	= '';
						$count	 	= 1;
						foreach($base_url as $bc):
							$url_path .= '/'.$bc;
							if($count == count($base_url)):?>
								<li class="active"><?php echo $bc;?></li>
							<?php else:?>
								<li><a href="<?php echo site_url($url_path);?>"><?php echo $bc;?></a></li> <span class="divider">/</span>
							<?php endif;
							$count++;
						endforeach;?>
 					</ul>
				</div>
			</div>
		<?php endif;?>
		
		
		<?php if ($this->session->flashdata('message')):?>
			<div class="alert alert-info">
				<a class="close" data-dismiss="alert">×</a>
				<?php echo $this->session->flashdata('message');?>
			</div>
		<?php endif;?>
		
		<?php if ($this->session->flashdata('error')):?>
			<div class="alert alert-error">
				<a class="close" data-dismiss="alert">×</a>
				<?php echo $this->session->flashdata('error');?>
			</div>
		<?php endif;?>
		
		<?php if (!empty($error)):?>
			<div class="alert alert-error">
				<a class="close" data-dismiss="alert">×</a>
				<?php echo $error;?>
			</div>
		<?php endif;?>
		
		

<?php
/*
End header.php file
*/