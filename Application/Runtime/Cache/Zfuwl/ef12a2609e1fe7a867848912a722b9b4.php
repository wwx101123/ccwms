<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <th><a href="javascript:sort('user_id');" class="active">ID</a></th>
        <th>会员号</th>
        <th>级别</th>
        <th>冻结时间</th>
        <th>冻结原因</th>
        <th>推荐人</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($userList) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($userList)): foreach($userList as $k=>$v): ?><tr>
                <td><?php echo ($v['user_id']); ?></td>
                <td><?php echo ($v['account']); ?></td>
                <td><?php echo ($levels[$v['level']]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s', $v["lock_time"])); ?></td>
                <td><?php echo ($v["lock_info"]); ?></td>
                <td><?php echo ((isset($tjrList[$v['tjr_id']]) && ($tjrList[$v['tjr_id']] !== ""))?($tjrList[$v['tjr_id']]):'无'); ?></td>
            </tr><?php endforeach; endif; endif; ?>
    </tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var form = layui.form();
        form.render('checkbox'); //刷新checkbox渲染
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
</script>