<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
        <th><a href="javascript:sort('user_id');" class="active">ID</a></th>
        <th>基本信息</th>
        <th>推荐人</th>
        <!--<th>报单人</th>-->
        <th>注册时间</th>
        <th>省份</th>
        <th>市区</th>
        <th>县区</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><input type="checkbox" name="selected[]" value="<?php echo ($v['user_id']); ?>" lay-skin="primary"></td>
                <td><?php echo ($v['user_id']); ?></td>
                <td>
                    账号：<?php echo ($v['account']); ?><br/>
                    级别：<?php echo ($levelInfo[$v['level']]); ?>
                </td>
                <td><?php echo ((isset($tjrList[$v['tjr_id']]) && ($tjrList[$v['tjr_id']] !== ""))?($tjrList[$v['tjr_id']]):'主账号引荐'); ?></td>
                <!--<td><?php echo ((isset($tjrList[$v['bdr_id']]) && ($tjrList[$v['bdr_id']] !== ""))?($tjrList[$v['bdr_id']]):'主账号报单'); ?></td>-->
                <td><?php echo (date('Y-m-d H:i:s', $v["reg_time"])); ?> </td>
                <td><?php echo ((isset($regionInfo[$userData['province']]) && ($regionInfo[$userData['province']] !== ""))?($regionInfo[$userData['province']]):'未填'); ?></td>
                <td><?php echo ((isset($regionInfo[$userData['city']]) && ($regionInfo[$userData['city']] !== ""))?($regionInfo[$userData['city']]):'未填'); ?></td>
                <td><?php echo ((isset($regionInfo[$userData['district']]) && ($regionInfo[$userData['district']] !== ""))?($regionInfo[$userData['district']]):'未填'); ?> </td>
            </tr><?php endforeach; endif; endif; ?>
    </tbody>
</table>
<?php echo ($page); ?>
<script>
    var jh_type;
    layui.use(['form'], function () {
        var form = layui.form;
        form.render('checkbox'); //刷新checkbox渲染
        form.render();
        form.on('radio(filter)', function(data){
             jh_type = data.value;
        });
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