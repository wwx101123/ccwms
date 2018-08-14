<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
            <th><a href="javascript:sort('uid');">会员账号</a></th>
            <th><a href="javascript:sort('bank_id');">汇款银行</a></th>
            <th><a href="javascript:sort('mid');">钱包</a></th>
            <th><a href="javascript:sort('add_money');">充值金额</a></th>
            <th><a href="javascript:sort('add_time');">充值时间</a></th>
            <th>付款截图</th>
            <th>备注</th>
            <th>审核员</th>
            <th>操作 / <a href="javascript:sort('id');">ID</a></th>
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
                <td><?php echo ($bankInfo[$v[bank_id]]); ?></td>
                <td><?php echo ($moneyInfo[$v[mid]]); ?></td>
                <td>
                    充值金额：<?php echo ($v["add_money"]); ?><br/>
                    平台汇率：<?php echo ($v["money_per"]); ?><br/>
                    实际到账：<?php echo ($v["actual_money"]); ?><br/>
                    审核状态：<b style="color:red;"><?php echo ($addMoneyType[$v[type]]); ?></b>
                </td>
                <td>
                    充值时间：<?php echo (date('Y-m-d H:i:s',$v["add_time"])); ?><br/>
                    付款时间：<?php echo (date('Y-m-d H:i:s',$v["fk_time"])); ?><br/>
                    <?php if($v[affirm_time] > 0): ?>确认时间：<?php echo (date('Y-m-d H:i:s',$v["affirm_time"])); endif; ?>
                    <?php if($v[refuse_time] > 0): ?>拒绝时间：<?php echo (date('Y-m-d H:i:s',$v["refuse_time"])); ?> <br /><span style="color:red;"> <?php echo ($v[refuse]); ?></span><?php endif; ?>
                </td>
                <td>
                    <img src="<?php echo ((isset($v['img']) && ($v['img'] !== ""))?($v['img']):'/Public/images/not_adv.jpg'); ?>" style="height:30px;" class="checkContent" />
                    <div style="display:none;"><img src="<?php echo ((isset($v["img"]) && ($v["img"] !== ""))?($v["img"]):'/Public/images/not_adv.jpg'); ?>" class="checkContent" /></div>
                </td>
                <td><?php echo ($v[note]); ?></td>
                <td><?php echo ($adminlist[$v[admin_id]]); ?></td>
                <td>
                    <?php if($v[type] == 2): ?><a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-warm refuseMoneyAdd"><i class="layui-icon">&#xe609;</i>拒绝汇款</a><br/><?php endif; ?>
                    <?php if($v[type] == 2): ?><a data="<?php echo ($v['id']); ?>" class="layui-btn  layui-btn-normal affirmMoneyAdd"><i class="layui-icon">&#xe605;</i>确认充值</a><br/><?php endif; ?>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger del"><i class="layui-icon">&#xe640;</i>删除 (<?php echo ($v["id"]); ?>)</a><br/>
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
    $('.checkContent').click(function () {
        var obj = $(this);
        top.layer.open({
            type: 1,
            title: false,
            area: ['80%', '80%'],
            closeBtn: true,
            shade: 0.8,
            id: 'checkImg', 
            content: $(obj).next().html()
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.affirmMoneyAdd').click(function () {
        var url = "<?php echo U('Recharge/affirmUserMoneyAdd');?>";
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
        layer.confirm('是否确认己收到款吗?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
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
    $('.refuseMoneyAdd').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入你的拒绝原因',formType: 2}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('Recharge/refuseUserMoneyAdd');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                       layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                            var page = $('.pagination .active').find('a').data('p');
                            ajax_get_table('search-form2', page);
                        });
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
    $('.del').click(function () {
        var url = "<?php echo U('Recharge/delAddUserMoneyLog');?>";
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