<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th>钱包名称</th>
            <!--<th>会员等级</th>-->
            <th>提现参数</th>
            <th>提现规则</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><?php echo ($moneyCarryInfo[$v[mid]]); ?></td>
                <!--<td><?php echo ($levelInfo[$v[level_id]]); if($v["level_id"] <= 0): ?><b style="color:red;">不限级别</b><?php endif; ?></td>-->
                <td><!--<?php echo ($v["low"]); ?> 起 <?php echo ($v["bei"]); ?> 的倍数-->
                    <!--<?php if($v["out"] > 0): ?>,<b style="color:red;">单笔最高 <?php echo ($v["out"]); ?></b><?php endif; ?>-->
                    <?php if($v["day_total"] > 0): ?>单日最高 <?php echo ($v["day_total"]); endif; ?>
                    <?php if($v["fee"] > 0): ?>,手续费 <?php echo ($v["fee"]); ?> %<?php endif; ?>
                    <?php if($v["total_fee"] > 0): ?>,单笔最高手续费 <?php echo ($v["total_fee"]); endif; ?>
                </td>
                <td>
                     <?php if($v["is_tk"] == 1): ?>每<?php echo ($moneyCarryTk[$v[is_tk]]); ?>：<?php echo ($v[add_time]); ?> H  - <?php echo ($v[out_time]); ?> H<?php endif; ?>
                     <?php if($v["is_tk"] == 2): ?>每<?php echo ($moneyCarryTk[$v[is_tk]]); ?>星期：<?php echo ($v["week_time"]); ?> 的 <?php echo ($v[add_time]); ?> H  - <?php echo ($v[out_time]); ?> H<?php endif; ?>
                    <?php if($v["is_tk"] == 3): ?>每<?php echo ($moneyCarryTk[$v[is_tk]]); ?>：<?php echo ($v["month_time"]); ?>  号的 <?php echo ($v[add_time]); ?> H  - <?php echo ($v[out_time]); ?> H<?php endif; ?>
                </td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatu' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td>
                    <a href="<?php echo U('Carry/edit',array('id'=>$v['id']));?>" class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑(<?php echo ($v['id']); ?>)</a>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v['id']); ?>)</a>
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var laypage = layui.laypage, $ = layui.jquery,form=layui.form;
        form.render('checkbox'); //刷新checkbox渲染
        form.on('switch(switchStatu)', function(data){
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Carry/saveStatu');?>";
            $.post(url, {val:val,id:id}, function(res) {
                if (res.status == 0) {
                    layer.msg(res.msg, {icon: 5});
                    return;
                } else {
                    var page = $('.pagination .active').find('a').data('p');
                    ajax_get_table('search-form2', page);
                    layer.msg(res.msg, {icon: 6, time: 2000});
                }
            });
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function () {
        var url = "<?php echo U('Carry/del');?>";
        var id = $(this).attr('data');
        if (!id) {
            var obj = $("input[name*='selected']");
            if (obj.is(":checked")) {
                var check_val = [];
                for (var k in obj) {
                    if (obj[k].checked)
                        check_val.push(obj[k].value);
                }
                id = check_val;
            }
        }
        if (!id) {
            return false;
        }
        layer.confirm('确定删除吗?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
            $.post(url, {id: id}, function (data) {
                if (data.status == 0) {
                    layer.msg(data.msg, {icon: 5});
                } else if (data.status == 1) {
                    var page = $('.pagination .active').find('a').data('p');
                    ajax_get_table('search-form2', page);
                    layer.msg(data.msg, {icon: 6, time: 2000});
                }
            });
        });
    });
</script>