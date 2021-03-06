<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <th><a href="javascript:sort('');">回本用户</a></th>
        <th><a href="javascript:sort('');">银行信息</a></th>
        <th><a href="javascript:sort('');">回本金额</a></th>
        <th><a href="javascript:sort('');">扣除分享积分</a></th>
        <th><a href="javascript:sort('');">状态</a></th>
        <!--<th><a href="javascript:sort('');">简介</a></th>-->
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><?php echo userInfo($v['b_uid'])['account'];?></td>
                <td>
                    <?php echo ($v['bank_name']); ?><br>
                    <?php echo ($bankInfo[$v['opening_id']]); ?><br>
                    <?php echo ($v['bank_account']); ?><br>
                    <?php echo ($v['bank_address']); ?>
                </td>
                <td><?php echo ($v['money']); ?></td>
                <td><?php echo ($v['deduct_fen']); ?></td>
                <td><?php echo blockrecoveryStatus($v['is_type']);?></td>
                <td>
                    <?php if($v['is_type'] == 1): ?><a data="<?php echo ($v['id']); ?>" class="layui-btn layui-btn-mini layui-btn-normal confirmNote"><i class="layui-icon">&#xe628;</i>确认回本(<?php echo ($v["id"]); ?>)</a>
                    <?php else: ?>
                        <a class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe628;</i>已处理(<?php echo ($v["id"]); ?>)</a><?php endif; ?>
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
        // layer.prompt({title:'请输入备注',formType: 2}, function(value, index, elem){
        // layer.confirm('确定回本吗?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
        //     $.ajax({
        //         type:'post',
        //         data:{id:id},
        //         url:"<?php echo U('Block/recoveryisType');?>",
        //         success:function(data){
        //             layer.close(index);
        //             if (data.status == 0) {
        //                 layer.msg(data.msg, {icon: 5});
        //             } else if (data.status == 1) {
        //                 var page = $('.pagination .active').find('a').data('p');
        //                 ajax_get_table('search-form2', page);
        //                 layer.msg(data.msg, {icon: 6, time: 2000});
        //             }
        //         }
        //     })
        // });
        layer.confirm('确认回本吗?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
            $.post("<?php echo U('Block/recoveryisType');?>", {id: id}, function (data) {
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

    $('.checkContent').click(function () {
        var obj = $(this);
        console.log(obj);
        top.layer.open({
            type: 1,
            title: false,
            area: ['90%', '90%'],
            closeBtn: true,
            shade: 0.8,
            id: 'checkImg' //设定一个id，防止重复弹出
            , content: $(obj).next().html()
        });
    });
</script>