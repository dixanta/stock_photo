
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	create_sortable();	
});
// Return a helper with preserved width of cells
var fixHelper = function(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
};
function create_sortable()
{
	$('#box_sortable').sortable({
		scroll: true,
		helper: fixHelper,
		axis: 'y',
		handle:'.handle',
		update: function(){
			save_sortable();
		}
	});	
	$('#box_sortable').sortable('enable');
}

function save_sortable()
{
	serial=$('#box_sortable').sortable('serialize');
			
	$.ajax({
		url:'<?php echo site_url('box/admin/box/organize');?>',
		type:'POST',
		data:serial
	});
}
function areyousure()
{
	return confirm('<?php echo lang('confirm_delete_box');?>');
}
//]]>
</script>



<a style="float:right;" class="btn" href="<?php echo site_url('box/admin/box/form'); ?>"><i class="icon-plus-sign"></i> <?php echo lang('add_new_box');?></a>

<strong style="float:left;"><?php echo lang('sort_box')?></strong>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo lang('sort');?></th>
			<th><?php echo lang('title');?></th>
			<th><?php echo lang('enable_on');?></th>
			<th><?php echo lang('disable_on');?></th>
			<th></th>
		</tr>
	</thead>
	<?php echo (count($box) < 1)?'<tr><td style="text-align:center;" colspan="5">'.lang('no_box').'</td></tr>':''?>

	<?php if($box):?>
	<tbody id="box_sortable">
	<?php
	foreach ($box as $box):

		//clear the dates out if they're all zeros
		if ($box->enable_on == '0000-00-00')
		{
			$enable_test	= false;
			$enable			= '';
		}
		else
		{
			$eo			 	= explode('-', $box->enable_on);
			$enable_test	= $eo[0].$eo[1].$eo[2];
			$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
		}

		if ($box->disable_on == '0000-00-00')
		{
			$disable_test	= false;
			$disable		= '';
		}
		else
		{
			$do			 	= explode('-', $box->disable_on);
			$disable_test	= $do[0].$do[1].$do[2];
			$disable		= $do[1].'-'.$do[2].'-'.$do[0];
		}


		$disabled_icon	= '';
		$curDate		= date('Ymd');

		if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
		{
			$disabled_icon	= '<span style="color:#ff0000;">&bull;</span> ';
		}
		?>
		<tr id="box-<?php echo $box->id;?>">
			<td class="handle"><a class="btn" style="cursor:move"><span class="icon-align-justify"></span></a></td>
			<td><?php echo $disabled_icon.$box->title;?></td>
			<td><?php echo $enable;?></td>
			<td><?php echo $disable;?></td>
			<td>
				<div class="btn-group" style="float:right">
					<a class="btn" href="<?php echo site_url('box/admin/box/form/'.$box->id); ?>"><i class="icon-pencil"></i> <?php echo lang('edit');?></a>
					<a class="btn btn-danger" href="<?php echo site_url('box/admin/box/delete/'.$box->id); ?>" onclick="return areyousure();"><i class="icon-trash icon-white"></i> <?php echo lang('delete');?></a>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<?php endif;?>
</table>
