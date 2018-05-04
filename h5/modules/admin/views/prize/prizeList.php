<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
	<h5 class="nav pull-left">奖品列表</h5>
	<div class="nav pull-right">
		<a href="<?php echo yii\helpers\Url::to(['prize/add-prize'])?>" type="button" class="btn btn-info btn-xs" style='margin-top: 9px;'><span class="glyphicon glyphicon-plus"></span> 添加奖品</a>
	</div>
</nav>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-condensed table-striped">
		<thead>
			<tr class="active">
				<th class="text-center width-50">ID</th>
				<th>奖品名称</th>
				<th>奖品图片</th>
				<th>微信红包</th>
				<th class="text-center width-150">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($prizeList as $k => $v){?>	
			<tr>
				<td class="text-center"><?php echo $v['pid'];?></td>
				<td><?php echo $v['prize_name'];?></td>
				<td>
					<?php if($v['prize_img']){?>
						<div class='table-img'><img src='<?php echo explode(',', $v['prize_img'])[0];?>'></div>
					<?php }?>
				</td>
				<td>
					<?php if($v['is_red_packet']){?>
						<span class="glyphicon glyphicon-usd text-danger"></span><?php echo $v['red_packet_money'];?>
					<?php }?>
				</td>
				<td class="text-center" data-id="<?php echo $v['pid'];?>">
					<button type="button" class="btn btn-danger btn-xs del"><span class="glyphicon glyphicon-remove"></span> 删除</button>
					<a href="<?php echo Url::to(['prize/mod-prize', 'id' => $v['pid']])?>" type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-pencil"></span> 修改</a>
				</td>
			</tr>
			<?php }?>
		</tbody>
		<tfoot class="pages">
			<tr>
				<td class="pagelist noselect text-right" colspan="6"></td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	/*删除奖品*/
	confirmation($('.del'), function(){
		var self = $(".popover").prev();
		self.confirmation('hide');
		var id = self.parent().data("id");
		if(id){
			var data = {
				'id': id
			}
			jajax('<?php echo Url::to(['prize/del-prize'])?>', data);
		}
	});

	/*分页*/
	var page = new Paging();
	page.init({
		target: $('.pagelist'),
		pagesize: <?php echo $pageInfo['pageSize']?$pageInfo['pageSize']:1;?>,
		count: <?php echo $pageInfo['count']?>,
		// toolbar: true,
		hash: true,
		current: <?php echo $pageInfo['currPage']?>,
		pageSizeList: [5, 10, 15, 20 ,50],
		changePagesize: function(currPage){
			window.location.href = "<?php echo Url::to(['prize/prize-list'])?>?page=" + currPage;
		},
		callback: function (currPage, size, count) {
			// jajax("<?php echo Url::to(['prize/prize-list'])?>?page=" + currPage);
			window.location.href = "<?php echo Url::to(['prize/prize-list'])?>?page=" + currPage;
		}
	});
</script>