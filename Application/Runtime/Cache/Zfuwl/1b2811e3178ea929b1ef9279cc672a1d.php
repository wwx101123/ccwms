<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <!--<th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>-->
            <th>中文名</th>
            <!-- <th>英文名</th> -->
            <th><a href="javascript:sort('statu');">状态</a></th>
            <th><a href="javascript:sort('sort');">排序</a></th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <!--<td><input type="checkbox" name="selected[]" value="<?php echo ($v['id']); ?>" lay-skin="primary"></td>-->
                <td><?php echo ($v['name_cn']); ?></td>
                <!-- <td><input type="text" name="name_en" value="<?php echo ($v['name_en']); ?>" onchange="updateSort2('region', 'id', '<?php echo ($v["id"]); ?>', 'name_en', this)"  style="height:30px;" class="layui-input" ></td> -->
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatu' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                <td><input type="text" name="sort" value="<?php echo ($v['sort']); ?>" onchange="updateSort2('region', 'id', '<?php echo ($v["id"]); ?>', 'sort', this)"  style="height:30px;" class="layui-input" ></td>
                <td>
                    <a href="<?php echo U('Website/regionEdit',array('id'=>$v['id']));?>" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑(<?php echo ($v['id']); ?>)</a>
                    <a href="<?php echo U('Website/regionIndex',array('parent_id'=>$v['id']));?>" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon">&#xe642;</i>查看(<?php echo ($v['id']); ?>)</a>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-sm del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v['id']); ?>)</a>
                </td>
            </tr><?php endforeach; endif; endif; ?>
</tbody>
</table>
<!--<button type="button" class="layui-btn layui-btn-danger del" style="float:left;margin:20px 0px;"><i class="layui-icon">&#xe640;</i>删除</button>-->
<?php echo ($page); ?>
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
            var url = "<?php echo U('Website/saveRegionStatu');?>";
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
        var url = "<?php echo U('Website/regionDel');?>";
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