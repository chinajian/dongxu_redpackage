<?php 
    use yii\helpers\Url;
?>
<nav class="navbar navbar-default child-nav">
	<h5 class="nav pull-left">概率设置</h5>
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
				<th>微信红包</th>
				<th>中奖数量/总数量</th>
				<th>概率</th>
			</tr>
		</thead>
		<tbody>
			<form class="form-horizontal" id="modForm">
				<input type="hidden" class="form-control input-sm" name="Ratio[sid]" value="<?php echo isset($get['sid'])?$get['sid']:'';?>">
				<?php foreach($ratioList as $k => $v){?>	
				<tr>
					<td class="text-center"><?php echo $v['id'];?></td>
					<td><?php echo $v['prize']['prize_name'];?></td>
					<td>
						<?php if($v['prize']['prize_img']){?>
							<div class='table-img'><img src='<?php echo explode(',', $v['prize']['prize_img'])[0];?>'></div>
						<?php }?>
					</td>
					<td>
						<?php if($v['prize']['is_red_packet']){?>
							<span class="glyphicon glyphicon-usd text-danger"></span><?php echo $v['prize']['red_packet_money'];?>
						<?php }?>
					</td>
					<td><?php echo $v['out_num'];?>/<?php echo $v['total_num'];?></td>
					<td>
						<div class="input-group" style="width: 300px;">
							<input type="text" class="form-control input-sm" name="Ratio[probability][]" value="<?php echo $v['probability'];?>">
							<span class="input-group-addon">/10000</span>
						</div>
					</td>
				</tr>
				<?php }?>
			</form>
		</tbody>
		<tfoot class="pages">
			<tr>
				<td class="pagelist noselect text-right" colspan="8">
					<button type="button" class="btn btn-primary btn-sm" id="save"><span class="glyphicon glyphicon-ok"></span> 提交</button>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	/*搜索*/
	$("#search").click(function(){
        window.location.href = '<?php echo Url::to(['probability/index'])?>&' + $('#searchForm').serialize();
    })

    /*提交*/
    $("#save").click(function(){
        jajax("<?php echo Url::to(['probability/index'])?>", $('#modForm').serialize());
    })

	/*设置概率*/
    $('[name="Ratio[probability][]"]').change(function(){
    	var num = 0;//所有概率的总和
    	var set_zero = false;//如果所有概率总和大于10000，那么后面的奖品概率都将为0
    	$('[name="Ratio[probability][]"]').each(function(){
    		if(!set_zero){
	    		if(parseInt($(this).val())>=10000 || (parseInt($(this).val())+num)>=10000){
	    			$(this).val(10000-num);
	    			set_zero = true;
	    		}else{
	    			$(this).val(parseInt($(this).val()));
	    		}
	    		num = num + parseInt($(this).val());
    		}else{
    			$(this).val(0);
    		}
    	})
    })

</script>