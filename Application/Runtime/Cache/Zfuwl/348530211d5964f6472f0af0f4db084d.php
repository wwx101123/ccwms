<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th>等级呢称</th>
            <th><a href="javascript:sort('amount');">储存源码资产</a></th>
            <!--<th>排单期数</th>-->
            <!--<th>每期天数</th>-->
            <th><?php echo bonusInfo(1)['name_cn'];?></th>
            <th><?php echo bonusInfo(2)['name_cn'];?></th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><b style='color:<?php echo ($v["color"]); ?>;'><?php echo ($v['name_cn']); ?></b></td>
                <td><?php echo ($v['amount']); ?></td>
                <!--<td><?php echo ($v['periods']); ?></td>-->
                <!--<td><?php echo ($v['periods_time']); ?></td>-->
                <td>
                    <?php echo ($v['b_1']); ?>
                </td>
                <td><?php echo ($v['b_2']); ?></td>
                <td>
                    <a href="<?php echo U('Level/edit',array('id'=>$v['level_id']));?>" class="layui-btn layui-btn-mini layui-btn-normal edit">编辑</a>
                    <!--<a data="<?php echo ($v['level_id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v["level_id"]); ?>)</a>-->
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<!-- <?php echo ($page); ?> -->
<script>
    layui.use(['form'], function () {
        var laypage = layui.laypage, $ = layui.jquery,form=layui.form;
        form.render('checkbox'); //刷新checkbox渲染
        form.on('checkbox(allChoose)', function (data) {
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function (index, item) {
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
        form.on('switch(switchStatu)', function(data){
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Level/saveStatu');?>";
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
        form.on('switch(switchReg)', function(data){
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Level/saveReg');?>";
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
        var url = "<?php echo U('Level/del');?>";
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