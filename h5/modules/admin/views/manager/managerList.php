<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
	<h5 class="nav pull-left">管理员列表</h5>
	<div class="nav pull-right">
		<a href="<?php echo yii\helpers\Url::to(['manager/add-manager'])?>" type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-plus"></span> 添加管理员</a>
	</div>
</nav>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-condensed table-striped">
		<thead>
			<tr class="active">
				<th class="text-center width-50">ID</th>
				<th>用户名</th>
				<th>手机</th>
				<th>真实姓名</th>
				<th>创建者</th>
				<th>最后登录IP</th>
				<th>最后登录时间</th>
				<th>登录次数</th>
				<th class="text-center width-50">状态</th>
				<th>创建时间</th>
				<th class="text-center width-150">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($managerList as $k => $v){?>	
			<tr>
				<td class="text-center"><?php echo $v['user_id'];?></td>
				<td><?php echo $v['username'];?></td>
				<td><?php echo $v['phone'];?></td>
				<td><?php echo $v['realname'];?></td>
				<td><?php echo $v['creater'];?></td>
				<td><?php echo long2ip($v['lastloginip']);?></td>
				<td><?php echo $v['lastlogin_time']?date('Y-m-d H:i:s', $v['lastlogin_time']):'';?></td>
				<td><?php echo $v['logintimes'];?></td>
				<td class="text-center"><span class='glyphicon glyphicon-<?php echo $v['state']==1?'ok text-success':'remove text-danger';?>'></span></td>
				<td><?php echo $v['add_time']>0?date('Y-m-d H:i:s', $v['add_time']):'';?></td>
				<td class="text-center" data-id="<?php echo $v['user_id'];?>">
					<button type="button" class="btn btn-danger btn-xs del"><span class="glyphicon glyphicon-remove"></span> 删除</button>
					<a href="<?php echo Url::to(['manager/mod-manager', 'id' => $v['user_id']])?>" type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-pencil"></span> 修改</a>
				</td>
			</tr>
			<?php }?>
		</tbody>
		<tfoot class="pages">
			<tr>
				<td class="pagelist noselect text-right" colspan="11"></td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	/*删除管理员*/
	confirmation($('.del'), function(){
		var self = $(".popover").prev();
		self.confirmation('hide');
		var id = self.parent().data("id");
		if(id){
			var data = {
				'id': id
			}
			jajax('<?php echo Url::to(['manager/del-manager'])?>', data);
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
			window.location.href = "<?php echo Url::to(['manager/manager-list'])?>?page=" + currPage;
		},
		callback: function (currPage, size, count) {
			// jajax("<?php echo Url::to(['manager/manager-list'])?>?page=" + currPage);
			window.location.href = "<?php echo Url::to(['manager/manager-list'])?>?page=" + currPage;
		}
	});
</script>