<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
	<h5 class="nav pull-left">抽奖日志列表</h5>
</nav>
<div class="clearfix" style="margin: -10px 0 10px 0;">
	<form class="form-inline" id="searchForm">
		<div class="form-group">
			<label for="sid" class="control-label">选择场次：</label>
			<select class="form-control input-sm" name="sid" id="sid">
				<option value="0">全部场次</option>
				<?php foreach($seasonList as $k => $v){?>
					<option value="<?php echo $v['sid'];?>" <?php if(isset($get['sid']) and ($get['sid'] === $v['sid'])){?>selected<?php }?>><?php echo $v['season_name'];?>（<?php echo date('Y-m-d H:i:s', $v['luckydraw_begin_time']);?> ~ <?php echo date('Y-m-d H:i:s', $v['luckydraw_end_time']);?>）</option>
				<?php }?>
			</select>
		</div>
		<button type="button" class="btn btn-info btn-sm" id="search">搜索</button>
	</form>
</div>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-condensed table-striped">
		<thead>
			<tr class="active">
				<th class="text-center width-50">ID</th>
				<th>奖品名称</th>
				<th>奖品图片</th>
				<th>微信昵称</th>
				<th>奖品图片</th>
				<th>场次ID</th>
				<th>中奖时间</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($drawLogList as $k => $v){?>	
			<tr>
				<td class="text-center"><?php echo $v['id'];?></td>
				<td><?php echo $v['prize']['prize_name'];?></td>
				<td>
					<?php if($v['prize']['prize_img']){?>
						<div class='table-img'><img src='<?php echo explode(',', $v['prize']['prize_img'])[0];?>'></div>
					<?php }?>
				</td>
				<td><?php echo json_decode($v['user']['nickname']);?></td>
				<td>
					<?php if($v['user']['headimgurl']){?>
						<div class='table-img'><img src='<?php echo $v['user']['headimgurl'];?>'></div>
					<?php }?>
				</td>
				<td><?php echo $v['sid'];?></td>
				<td><?php echo date('Y-m-d H:i:s', $v['draw_time']);?></td>
			</tr>
			<?php }?>
		</tbody>
		<tfoot class="pages">
			<tr>
				<td class="pagelist noselect text-right" colspan="9"></td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	/*搜索*/
	$("#search").click(function(){
        window.location.href = '<?php echo Url::to(['draw/draw-log'])?>&' + $('#searchForm').serialize();
    })


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
			window.location.href = "<?php echo Url::to(['draw/draw-log'])?>&page=" + currPage + '&sid=<?php echo isset($get['sid'])?$get['sid']:'';?>';
		},
		callback: function (currPage, size, count) {
			window.location.href = "<?php echo Url::to(['draw/draw-log'])?>&page=" + currPage + '&sid=<?php echo isset($get['sid'])?$get['sid']:'';?>';
		}
	});
</script>
