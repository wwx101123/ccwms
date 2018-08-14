<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th><a href="javascript:sort('money_id');">转出名称</a></th>
            <th><a href="javascript:sort('type_id');">转入钱包</a></th>
            <th>参数配置</th>
            <th>转换比例</th>
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
                <td><?php echo ($moneyInfo[$v[money_id]]); ?></td>
                <td><?php echo ($moneyInfo[$v[type_id]]); ?></td>
                <td><?php echo ($v["low"]); ?> 起 <?php echo ($v["bei"]); ?> 的倍数<?php if($v["out"] > 0): ?>,<b style="color:red;">单笔最高 <?php echo ($v["out"]); ?></b><?php endif; if($v["fee"] > 0): ?>,手续费 <?php echo ($v["fee"]); ?> %<?php endif; ?></td>
                <td>1 <?php echo ($moneylist[$v[money_id]]); ?>  => <?php echo ($v["per"]); ?> <?php echo ($moneylist[$v[type_id]]); ?></td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatus' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td>
                    <a href="<?php echo U('Transform/edit',array('id'=>$v['id']));?>" class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑(<?php echo ($v["id"]); ?>)</a>
                    <!--<a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v["id"]); ?>)</a>-->
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var $ = layui.jquery, form = layui.form;
        form.render('checkbox'); 
        form.on('checkbox(allChoose)', function (data) {
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function (index, item) {
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
        form.on('switch(switchStatus)', function (data) {
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Transform/saveStatu');?>";
            $.post(url, {val: val, id: id}, function (res) {
                if (res.status == 0) {
                    layer.msg(res.msg, {icon: 5});
                    return;
                } else {
                    layer.msg(res.msg, {icon: 6, time: 2000, shade: 0.01}, function () {
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
        var url = "<?php echo U('Transform/del');?>";
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