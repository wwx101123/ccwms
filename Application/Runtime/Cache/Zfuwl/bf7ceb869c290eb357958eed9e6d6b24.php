<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th>管理员账号</th>
            <th><a href="javascript:sort('log_time');">日期</a></th>
            <th><a href="javascript:sort('log_ip');">IP地址</th>
            <th><a href="javascript:sort('equipment');">操作设备</a></th>
            <th><a href="javascript:sort('log_info');">操作说明</a></th>
            <th>地区</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><?php echo ($adminList[$v[admin_id]]); ?></td>
                <td><?php echo (date('Y-m-d H:i',$v["log_time"])); ?></td>
                <td><?php echo ($v["log_ip"]); ?></td>    
                <td><?php echo ($v["equipment"]); ?></td>
                <td><?php echo ($v["log_info"]); ?></td>
                <td><a data-ip="<?php echo ($v['log_ip']); ?>" class="layui-btn layui-btn-radius layui-btn-normal checkAddressByIp <?php echo ($v['log_ip']); ?>">查看</a></td>
                <td>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v["id"]); ?>)</a>
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var form = layui.form;
        form.render('checkbox');
        form.on('checkbox(allChoose)', function (data) {
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function (index, item) {
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.checkAddressByIp').on('click', function(){
        var obj = $(this);
        var ip = $(this).data('ip');
        var tipsIndex = layer.tips('加载中...', obj, {time:0,tips:[4]});
        // var layerIndex = layer.msg('获取中...', {icon:16, time:0, shade:0.1})
        $.post('<?php echo U("Admin/checkAddressByIp");?>', {ip:ip}, function(data){
            layer.close(tipsIndex);
            // layer.close(layerIndex);
            layer.tips(data.msg, obj, {time:0,tips:[4], id:ip, closeBtn:true});
            // layer.msg(data.msg, {time:0,closeBtn:true,id:ip});
        });
    });
    $('.del').click(function () {
        var url = "<?php echo U('Admin/dellogIndex');?>";
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