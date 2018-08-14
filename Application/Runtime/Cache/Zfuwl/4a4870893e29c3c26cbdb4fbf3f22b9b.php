<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
        <th><a href="javascript:sort('id');" class="active">ID</a></th>
        <th>会员账号</th>
        <th><a href="javascript:sort('bonus_id');">奖金名称</a></th>
        <th><a href="javascript:sort('add_time');">发放时间</a></th>
        <th><a href="javascript:sort('sj');">结算方式</a></th>
        <th><a href="javascript:sort('money');">奖金金额</a></th>
        <th><a href="javascript:sort('come_uid');">来源于</a></th>
        <th>备注</th>
        <th><a href="javascript:sort('sj_time');">状态</a></th>
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
                <td><?php echo ($v["id"]); ?></td>
                <td><?php echo ($userList[$v[uid]]); ?></td>
                <td><?php echo ($bonusSingle[$v[bonus_id]]); ?></td>
                <td><?php echo (date('Y-m-d H:i',$v["add_time"])); ?></td>
                <td><?php echo bonusSj($v[sj]);?></td>
                <td><?php echo ($v["money"]); ?></td>
                <td><?php echo ($userList[$v[come_uid]]); ?></td>
                <td><?php echo ($v["note"]); ?></td>
                <td><?php if($v["sj_time"] > 1): echo (date('Y-m-d H:i',$v["sj_time"])); ?> <?php else: ?> <?php echo bonusLogStatu($v[statu]); endif; ?></td>
                <td>
                    <a href="<?php echo U('Bonus/editBonusLog',array('id'=>$v['id']));?>" class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe642;</i>编辑</a>
                    <?php if($v["statu"] != 9): ?><a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-radius layui-btn-mini bonusClear"><i class="layui-icon">&#xe6b2;</i>结算</a><?php endif; ?>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除</a>
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
        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
        $('.bonusClear').click(function(){
            var id = $(this).attr('data');
            layer.prompt({title:'请输入备注信息',formType: 2}, function(value, index, elem){
                $.ajax({
                    type:'post',
                    data:{id:id, name:value},
                    url:"<?php echo U('Bonus/bonusLogClear');?>",
                    success:function(data){
                        layer.close(index);
                        if (data.status == 0) {
                            layer.msg(data.msg, {icon: 5});
                        } else if (data.status == 1) {
                            layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                                var page = $('.pagination .active').find('a').data('p');
                                ajax_get_table('search-form2', page);
                            });
                        }
                    }
                })
            });
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function(){
        var url = "<?php echo U('Bonus/delBonusLog');?>";
        var id = $(this).attr('data');
        if(!id) {
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
        if(!id) {
            return false;
        }
        layer.confirm('确定删除吗?', {icon: 3,skin: 'layer-ext-moon',btn: ['确认', '取消']}, function() {
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