<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
  <!--  <div class="backstageTop">
        <div class="layui-col-xs2 topZIjiF">
            <div class="topZIji"><div class="topZIjiLeft topZIjiLeft_1"><i class="layui-icon">&#xe65e;</i></div>
                <a href="javascript:void(0);" class="topZIjiRight"><p class="p_1"><?php echo ((isset($userTotal) && ($userTotal !== ""))?($userTotal):'0.00'); ?></p><p class="p_2">总金额</p> </a><div style="clear: both;"></div>
            </div>
        </div>

        <div class="layui-col-xs2 topZIjiF">
            <div class="topZIji">
                <div class="topZIjiLeft topZIjiLeft_2"><i class="layui-icon">&#xe61f;</i></div>
                <a href="javascript:void(0);" class="topZIjiRight"> <p class="p_1"><?php echo ($newUsers); ?></p><p class="p_2">今日新增</p></a><div style="clear: both;"></div>
            </div>
        </div>

        <div class="layui-col-xs2 topZIjiF">
            <div class="topZIji">
                <div class="topZIjiLeft topZIjiLeft_6"><i class="layui-icon">&#xe6b2;</i></div>
                <a href="javascript:void(0);" class="topZIjiRight"><p class="p_1"><?php echo ((isset($emptyTotal) && ($emptyTotal !== ""))?($emptyTotal):'0.00'); ?></p><p class="p_2">冻结金额</p> </a><div style="clear: both;"></div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>-->
    <thead>
        <tr>
            <th>会员账号</th>
            <th><a href="javascript:sort('mid');">钱包</a></th>
            <th><a href="javascript:sort('money');">账户余额</a></th>
            <th><a href="javascript:sort('frozen');">冻结金额</a></th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <td><?php echo ($userList[$v[uid]]); ?></td>
                <td><?php echo ($moneyInfo[$v[mid]]); ?></td>
                <td><?php echo ($v[money]); ?></td>
                <td><?php echo ($v[frozen]); ?></td>
                <td>
                    <a href="<?php echo U('Money/userMoneyEdit',array('id'=>$v['id']));?>" class="layui-btn layui-btn-mini layui-btn-normal"><i class="layui-icon">&#xe65e;</i> 管理</a>
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