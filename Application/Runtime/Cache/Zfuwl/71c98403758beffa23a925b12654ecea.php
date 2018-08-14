<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
        <th><a href="javascript:sort('zf_time');">日期</a></th>
        <th>会员账号</th>
        <?php if(is_array($bonusList)): $i = 0; $__LIST__ = $bonusList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vb): $mod = ($i % 2 );++$i;?><th><a href="javascript:sort('bonus_<?php echo ($vb["bonus_id"]); ?>');"><?php echo ($vb["name_cn"]); ?></a></th><?php endforeach; endif; else: echo "" ;endif; ?>
        <th><a href="javascript:sort('total');">应发</a></th>
        <?php if(is_array($taxList)): $i = 0; $__LIST__ = $taxList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vt): $mod = ($i % 2 );++$i;?><th><a href="javascript:sort('tax_<?php echo ($vt["tax_id"]); ?>');"><?php echo ($vt["name_cn"]); ?></a></th><?php endforeach; endif; else: echo "" ;endif; ?>
        <th><a href="javascript:sort('money');">实发</a></th>
        <?php if(is_array($moneylist)): $i = 0; $__LIST__ = $moneylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vm): $mod = ($i % 2 );++$i;?><th><a href="javascript:sort('out_<?php echo ($vm["money_id"]); ?>');"><?php echo ($vm["name_cn"]); ?></a></th><?php endforeach; endif; else: echo "" ;endif; ?>
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
                <td><?php echo (date('Y-m-d',$v["zf_time"])); ?></td>
                <td><?php echo ($userList[$v[uid]]); ?></td>
                <?php if(is_array($bonusList)): $i = 0; $__LIST__ = $bonusList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vba): $mod = ($i % 2 );++$i;?><td><?php echo $v['bonus_'.$vba['bonus_id']]; ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                <td><?php echo ($v["total"]); ?></td>
                <?php if(is_array($taxList)): $i = 0; $__LIST__ = $taxList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vta): $mod = ($i % 2 );++$i;?><td><?php echo $v['tax_'.$vta['tax_id']]; ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                <td><?php echo ($v["money"]); ?></td>
                <?php if(is_array($moneylist)): $i = 0; $__LIST__ = $moneylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vma): $mod = ($i % 2 );++$i;?><td><?php echo $v['out_'.$vma['money_id']]; ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                <td><a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v["id"]); ?>)</a></td>
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
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function(){
        var url = "<?php echo U('Money/delUserDay');?>";
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