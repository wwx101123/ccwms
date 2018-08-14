<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th>银行名称</th>
            <th>分行支行</th>
            <th>开户姓名</th>
            <th>银行账号</th>
            <th>银行图片</th>
            <th>收款二维码</th>
            <th>会员汇款</th>
            <th>会员提现</th>
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
                <td><?php echo ($v["name_cn"]); ?><!--(<?php echo ($v["name_en"]); ?>)--></td>
                <td><?php echo ($v["address"]); ?></td>
                <td><?php echo ($v["username"]); ?></td>
                <td><?php echo ($v["account"]); ?></td>
                <td>
                    <img src="<?php echo ((isset($v['img']) && ($v['img'] !== ""))?($v['img']):'/Public/images/not_adv.jpg'); ?>" style="height:35px;" class="checkContent" />
                    <div style="display:none;">
                        <img src="<?php echo ((isset($v['img']) && ($v['img'] !== ""))?($v['img']):'/Public/images/not_adv.jpg'); ?>" class="checkContent" />
                    </div>
                </td>
                <td>
                    <img src="<?php echo ((isset($v['code']) && ($v['code'] !== ""))?($v['code']):'/Public/images/not_adv.jpg'); ?>" style="height:35px;" class="checkContent" />
                    <div style="display:none;">
                        <img src="<?php echo ((isset($v['code']) && ($v['code'] !== ""))?($v['code']):'/Public/images/not_adv.jpg'); ?>" class="checkContent" />
                    </div>
                </td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchIsC' value='<?php echo ($v["is_c"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['is_c'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchIsT' value='<?php echo ($v["is_t"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['is_t'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatus' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td>
                    <a href="<?php echo U('Bank/editBank',array('id'=>$v['id']));?>" class="layui-btn layui-btn-xs layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑</a>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-xs del"><i class="layui-icon">&#xe640;</i>删除</a>
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var $ = layui.jquery, form = layui.form;
        form.render('checkbox'); //刷新checkbox渲染
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
            var url = "<?php echo U('Bank/saveStatu');?>";
            $.post(url, {val: val, id: id}, function (res) {
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
        form.on('switch(switchIsC)', function (data) {
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Bank/saveIsC');?>";
            $.post(url, {val: val, id: id}, function (res) {
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
        form.on('switch(switchIsT)', function (data) {
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Bank/saveIsT');?>";
            $.post(url, {val: val, id: id}, function (res) {
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
    $('.checkContent').click(function () {
        var obj = $(this);
        top.layer.open({
            type: 1,
            title: false,
            area: ['50%', '50%'],
            closeBtn: true,
            shade: 0.8,
            id: 'checkImg' //设定一个id，防止重复弹出
            , content: $(obj).next().html()
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function () {
        var url = "<?php echo U('Bank/delBank');?>";
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