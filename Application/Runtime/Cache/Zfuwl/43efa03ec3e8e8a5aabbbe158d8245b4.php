<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th>税务名称</th>
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
                <td><?php echo ($v['name_cn']); if($v["money_id"] > 0): ?>&nbsp; <b><sapn style="color:red;">统计到 </span><?php echo ($moneylist[$v[money_id]]); ?> </b><?php endif; ?></td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatu' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['tax_id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td>
                    <a href="<?php echo U('Bonus/taxEdti',array('id'=>$v['tax_id']));?>" class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑(<?php echo ($v['tax_id']); ?>)</a>
                    <!--<a data="<?php echo ($v['tax_id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v['tax_id']); ?>)</a>-->
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
            var url = "<?php echo U('Bonus/saveStatuTax');?>";
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
        var url = "<?php echo U('Bonus/delTax');?>";
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