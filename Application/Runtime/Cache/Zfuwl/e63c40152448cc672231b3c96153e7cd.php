<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <!-- <th>语言</th> -->
        <th>分类名称</th>
        <th>关键词</th>
        <th>描述</th>
        <th>显示</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
    <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <!-- <td><?php echo ($languageType[$v[cn]]); ?></td> -->
                <td><?php echo ($v['title']); ?></td>
                <td><?php echo ($v['keywords']); ?></td>
                <td>
                    <a href="javaScript:void(0);" class="checkContent">点击查看</a>
                    <div style="display:none;">
                        <?php echo (htmlspecialchars_decode($v['desc'])); ?>
                    </div>
                </td>
                <td><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatu' value='<?php echo ($v["statu"]); ?>' data-value="<?php echo ($v['cat_id']); ?>" lay-text="开启|关闭" <?php if($v['statu'] == 1): ?>checked<?php endif; ?> type="checkbox"></td>
                 <td>
                    <div class="layui-input-inline">
                        <input type="text" name="sort" value="<?php echo ($v['sort']); ?>" onchange="updateSort2('article_cat', 'cat_id', '<?php echo ($v["cat_id"]); ?>', 'sort', this)" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" class="layui-input" >
                    </div>
                </td>
                <td>
                    <a href="<?php echo U('Article/editCat');?>?id=<?php echo ($v['cat_id']); ?>" class="layui-btn layui-btn-mini layui-btn-normal edit"><i class="layui-icon">&#xe642;</i>编辑(<?php echo ($v['cat_id']); ?>)</a>
                    <!--<a data="<?php echo ($v['cat_id']); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除(<?php echo ($v['cat_id']); ?>)</a>-->
                </td>
            </tr><?php endforeach; endif; endif; ?>
    </tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var laypage = layui.laypage, $ = layui.jquery,form=layui.form;
        form.render('checkbox');
        form.on('switch(switchStatu)', function(data){
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Article/saveCatStatu');?>";
            $.post(url, {val:val,id:id}, function(res) {
                if (res.status == 0) {
                    layer.msg(res.msg, {icon: 5});
                    return;
                } else {
                    layer.msg(res.msg, {icon: 6, time: 2000, shade:0.01}, function() {
                        location.reload();
                    });
                }
            });
        });
    });
    $('.checkContent').click(function(){
        var obj = $(this);
        top.layer.open({
            type: 1,
            title: false,
            area:['80%', '80%'],
            closeBtn: true,
            shade: 0.8,
            id: 'checkImg' //设定一个id，防止重复弹出
            ,content: $(obj).next().html()
        });
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function(){
        var url = "<?php echo U('Article/delCat');?>";
        var id = $(this).attr('data');
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