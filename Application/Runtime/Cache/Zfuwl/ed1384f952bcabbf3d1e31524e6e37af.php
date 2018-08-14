<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
            <th><a href="javascript:sort('uid');">会员账号</a></th>
            <th><a href="javascript:sort('is_type');">变动类型</a></th>
            <th><a href="javascript:sort('zf_time');">变动时间</a></th>
            <th><a href="javascript:sort('mid');">钱包</a></th>
            <th><a href="javascript:sort('money');">变动金额</a></th>
            <th><a href="javascript:sort('total');">变动后总金额</a></th>
            <th><a href="javascript:sort('come_uid');">相关会员</a></th>
            <th>备注</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><input type="checkbox" name="selected[]" value="<?php echo ($v['id']); ?>" lay-skin="primary"></td>
                <td><?php echo ($userList[$v[uid]]); ?></td>
                <td><?php echo ($moneyLogType[$v[is_type]]); ?></td>
                <td><?php echo (date('Y-m-d H:i',$v["zf_time"])); ?></td>
                <td><?php echo ($moneyInfo[$v[mid]]); ?></td>
                <td><?php echo ($v["money"]); ?></td>
                <td><?php echo ($v["total"]); ?></td>
                <td><?php echo ($userList[$v[come_uid]]); ?></td>
                <td><?php echo ($v["note"]); ?></td>
                <td>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-mini layui-btn-normal confirmNote"><i class="layui-icon">&#xe628;</i>修改备注(<?php echo ($v["id"]); ?>)</a>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v["id"]); ?>)</a>
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<button type="button" class="layui-btn layui-btn-danger del" style="float:left;margin:20px 0px;"><i class="layui-icon">&#xe640;</i>删除</button>
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
    $('.del').click(function () {
        var url = "<?php echo U('Money/delMoneyLog');?>";
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
    $('.confirmNote').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入备注',formType: 2}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('Money/editMoneyLog');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
</script>