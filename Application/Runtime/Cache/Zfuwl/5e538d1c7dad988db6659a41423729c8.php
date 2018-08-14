<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
            <th><a href="javascript:sort('add_time');">申请时间</a></th>
            <th><a href="javascript:sort('mid');">钱包</a></th>
            <th>收款信息</th>
            <th>备注</th>
            <th><a href="javascript:sort('statu');">审核结果</a></th>
            <th>审核员</th>
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
                <td><?php echo (date('Y-m-d H:i:s',$v["add_time"])); ?></td>
                <td>
                    会员账号：<?php echo ($userList[$v[uid]]); ?><br />
                    申请账户：<?php echo ($moneyCarryInfo[$v[mid]]); ?> => <?php echo ($v[add_money]); ?><br />
                    含手续费：<?php echo ((isset($v["fee"]) && ($v["fee"] !== ""))?($v["fee"]):'0'); ?>  % （<?php echo ($v["fee_money"]); ?>）<br />
                    实际支付：<?php echo ($v["out_money"]); ?><br />
                </td>
                <td>
                    银行：<?php echo ($bankInfo[$v[opening_id]]); ?><br />
                    户名：<?php echo ($v["bank_name"]); ?><br />
                    账号：<?php echo ($v["bank_account"]); ?>
                </td>
                <td><?php echo ($v["note"]); ?></td>
                <td><?php echo ($moneyCarryLogStatu[$v[statu]]); ?>
                    <?php if($v["statu"] == 3): ?><br/><?php echo (date('Y-m-d H:i:s',$v["refuse_time"])); ?><br /><span style="color:red;"> <?php echo ($v[refuse]); ?></span><?php endif; ?>
                    <?php if($v["affirm_time"] > 0): ?><span style="color:red;"> <br /><?php echo (date('Y-m-d H:i:s',$v["affirm_time"])); ?></span><?php endif; ?>
                    <?php if($v["pay_time"] > 0): ?><span style="color:red;"> <br /><?php echo (date('Y-m-d H:i:s',$v["pay_time"])); ?> 己付款</span><?php endif; ?>
                </td>
                <td><?php echo ($adminlist[$v[admin_id]]); ?></td>
                <td>
                     <?php switch($v["statu"]): case "1": ?><a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-normal affirmCarryAdd"><i class="layui-icon">&#xe65e;</i>确认并付款</a><br/>
                            <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-normal toCarryAdd"><i class="layui-icon">&#x1005;</i>确认审核待付款</a><br/>
                            <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-warm refuseCarryAdd"><i class="layui-icon">&#xe64f;</i>拒绝该笔提现</a><br/><?php break;?>
                        <?php case "2": ?><a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-normal affirmCarryAdd"><i class="layui-icon">&#xe65e;</i>确认并付款</a><br/><?php break; endswitch;?>
                    <a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-mini del"><i class="layui-icon">&#xe640;</i> 删除(<?php echo ($v["id"]); ?>)</a>
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
    $('.toCarryAdd').click(function () {
        var url = "<?php echo U('Carry/toCarryAdd');?>";
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
        layer.confirm('是否确认该笔提现?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
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
    $('.affirmCarryAdd').click(function () {
        var url = "<?php echo U('Carry/affirmCarryAdd');?>";
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
        layer.confirm('是否确认并己付款?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
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
    $('.refuseCarryAdd').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入你的拒绝原因',formType: 2}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('Carry/refuseCarryAdd');?>",
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
        var url = "<?php echo U('Carry/delCarryLog');?>";
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