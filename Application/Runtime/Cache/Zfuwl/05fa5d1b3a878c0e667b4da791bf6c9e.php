<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
        <tr>
            <th><a href="javascript:sort('user_id');" class="active">ID</a></th>
            <th>会员号</th>
            <th>级别</th>
            <th>注册时间</th>
            <th>激活时间</th>
            <th>推荐人</th>
            <!--<th>报单人</th>-->
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><?php echo ($v['user_id']); ?></td>
                <td><?php echo ($v['account']); ?></td>
                <td>
                    <?php if($v["level"] > 0): ?><b style="color:red;">会员等级：<?php echo ($levelInfo[$v['level']]); ?></b><br/><?php endif; ?>
                    <!--<?php if($v["leader"] > 0): ?><b style="color:royalblue;">领导等级：<?php echo ($leaderInfo[$v['leader']]); ?></b><br/><?php endif; ?>-->
                    <!--<?php if($v["agent"] > 0): ?><b style="color:rosybrown;">报单等级：<?php echo ($agentInfo[$v['agent']]); ?></b><br/><?php endif; ?>-->
                    <!--<?php if($v["service"] > 0): ?><b style="color:#ff00ff;">服务等级：<?php echo ($serviceInfo[$v['service']]); ?></b><br/><?php endif; ?>-->
                </td>
                <td><?php echo (date('Y-m-d H:i:s', $v["reg_time"])); ?></td>
                <td><?php echo (date('Y-m-d H:i:s', $v["jh_time"])); ?></td>
                <td><?php echo ((isset($userlist[$v['tjr_id']]) && ($userlist[$v['tjr_id']] !== ""))?($userlist[$v['tjr_id']]):'无'); ?></td>
                <!--<td><?php echo ((isset($userlist[$v['bdr_id']]) && ($userlist[$v['bdr_id']] !== ""))?($userlist[$v['bdr_id']]):'无'); ?></td>-->
                <td>
                    <a href="<?php echo U('Level/editUserLevel',array('user_id'=>$v['user_id']));?>" class="layui-btn layui-btn-sm layui-btn-radius"><i class="layui-icon">&#xe62c;</i>会员级别调整</a>
                    <!--<a href="<?php echo U('Leader/editUserLeader',array('user_id'=>$v['user_id']));?>" class="layui-btn layui-btn-sm layui-btn-radius"><i class="layui-icon">&#xe62c;</i>领导级别调整</a>-->
                    <!--<a href="<?php echo U('Agent/editUserAgent',array('user_id'=>$v['user_id']));?>" class="layui-btn layui-btn-sm layui-btn-radius"><i class="layui-icon">&#xe62c;</i>报单级别调整</a>-->
                    <!--<a href="<?php echo U('Service/editUserService',array('user_id'=>$v['user_id']));?>" class="layui-btn layui-btn-sm layui-btn-radius"><i class="layui-icon">&#xe62c;</i>代理级别调整</a>-->
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
</script>