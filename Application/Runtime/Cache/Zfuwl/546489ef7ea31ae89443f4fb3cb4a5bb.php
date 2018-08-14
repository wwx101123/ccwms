<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th>奖项名称</th>
            <th>计算方式</th>
            <th>结算方式</th>
            <th>分配方式</th>
            <th>是否扣税</th>
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
                <td><?php echo ($v['name_cn']); ?></td>
                <td><?php echo ($bonusPerList[$v[type]]); ?></td>
                <td><?php echo ($bonusSjList[$v[sj]]); ?></td>
                <td style='color:red;'>
                    <?php if($v["mp_1"] > 0): echo ($v["mp_1"]); ?>%进<?php echo ($moneylist[$v[m_1]]); endif; ?>
                    <?php if($v["mp_2"] > 0): echo ($v["mp_2"]); ?>%进<?php echo ($blockInfo[$v[m_2]]); endif; ?>
                    <?php if($v["mp_3"] > 0): echo ($v["mp_3"]); ?>%进<?php echo ($moneylist[$v[m_3]]); endif; ?>
                    <?php if($v["mp_4"] > 0): echo ($v["mp_4"]); ?>%进<?php echo ($moneylist[$v[m_4]]); endif; ?>
                </td>
                <td style='color:red;'>
                    <?php if($v["tp_1"] > 0): echo ($bonusTaxlist[$v[t_1]]); echo ($v["tp_1"]); ?>%<?php endif; ?>
                    <?php if($v["tp_2"] > 0): echo ($bonusTaxlist[$v[t_2]]); echo ($v["tp_2"]); ?>%<?php endif; ?>
                    <?php if($v["tp_3"] > 0): echo ($bonusTaxlist[$v[t_3]]); echo ($v["tp_3"]); ?>%<?php endif; ?>
                </td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatu' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['bonus_id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td><a href="<?php echo U('Bonus/edit',array('id'=>$v['bonus_id']));?>" class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑(<?php echo ($v['bonus_id']); ?>)</a></td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var laypage = layui.laypage, $ = layui.jquery,form=layui.form;
        form.render('checkbox');
        form.on('switch(switchStatu)', function(data){
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Bonus/saveStatu');?>";
            $.post(url, {val:val,id:id}, function(res) {
                if (res.status == 0) {
                    layer.msg(res.msg, {icon: 5});
                    return;
                } else {
                    layer.msg(res.msg, {icon: 6, time: 2000, shade:0.01}, function() {
                        location.reload();
                    });
                }
            });
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function () {
        var url = "<?php echo U('Bonus/delbonus');?>";
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
                    layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                    });
                }
            });
        });
    });
</script>