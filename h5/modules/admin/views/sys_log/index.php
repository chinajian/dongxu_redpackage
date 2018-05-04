<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
	<h5 class="nav pull-left">操作日志</h5>
</nav>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-condensed table-striped">
		<thead>
			<tr class="active">
				<th class="text-center width-50">ID</th>
				<th>操作人</th>
				<th>IP</th>
				<th>内容</th>
				<th>时间</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($sysLogList as $k => $v){?>
			<tr>
				<td class="text-center widtd-50"><?php echo $v['log_id'];?></td>
				<td><?php echo $v['mname'];?></td>
				<td><?php echo long2ip($v['login_ip']);?></td>
				<td><?php echo $v['content'];?></td>
				<td><?php echo $v['operate_time']?date('Y-m-d H:i:s', $v['operate_time']):'';?></td>
			</tr>
			<?php }?>
		</tbody>
		<tfoot class="pages">
			<tr>
				<td class="pagelist noselect text-right" colspan="5"></td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
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
			window.location.href = "<?php echo Url::to(['sys_log/index'])?>&page=" + currPage;
		},
		callback: function (currPage, size, count) {
			window.location.href = "<?php echo Url::to(['sys_log/index'])?>&page=" + currPage;
		}
	});
</script>