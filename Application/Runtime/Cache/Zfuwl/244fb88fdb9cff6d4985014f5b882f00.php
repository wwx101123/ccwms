<?php if (!defined('THINK_PATH')) exit();?><table class="layui-table layui-form">
    <thead>
    <tr>
        <th style="width: 5px;"><input type="checkbox" lay-filter="allChoose"lay-skin="primary" /></th>
        <th><a href="javascript:sort('user_id');" class="active">ID</a></th>
        <th>基本信息</th>
        <th>报单信息</th>
        <th>联系地址</th>
        <th>帐号状态</th>
        <th>安全设置</th>
        <th>基本操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($list) == 0): ?><tr align="center">
            <td colspan="20">暂无数据</td>
        </tr>
        <?php else: ?>
        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr>
                <?php $userData = dataInfo($v['data_id']); ?>
                <td><input type="checkbox" name="selected[]" value="<?php echo ($v['user_id']); ?>" lay-skin="primary"></td>
                <td><?php echo ($v['user_id']); ?> <br />
                </td>
                <td>
                    账号：<?php echo ($v['account']); ?><br/>
                    呢称：<?php echo ((isset($v['nickname']) && ($v['nickname'] !== ""))?($v['nickname']):'暂无填写'); ?><br/>
                    姓名：<?php echo ((isset($userData["username"]) && ($userData["username"] !== ""))?($userData["username"]):'暂无填写'); ?><br/>
                    手机：<?php echo ((isset($userData['mobile']) && ($userData['mobile'] !== ""))?($userData['mobile']):'暂无手机号'); ?>
                        <?php if($userData["is_mobile"] == 1): ?><a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($userData['id']); ?>" data-title="是否取消手机号认证?"  data-url="<?php echo U('User/saveIsMobile');?>">
                                <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">己认证</button>
                            </a>
                        <?php else: ?>
                            <a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($userData['id']); ?>" data-title="是否确认手机码认证?"  data-url="<?php echo U('User/saveIsMobile');?>">
                                <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-disabled">未认证</button>
                            </a><?php endif; ?>
                    <br/>
                    邮箱：<?php echo ((isset($userData['email']) && ($userData['email'] !== ""))?($userData['email']):'暂无邮箱号'); ?>
                        <?php if($userData["is_email"] == 1): ?><a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($userData['id']); ?>" data-title="是否取消邮箱号认证?"  data-url="<?php echo U('User/saveIsEmail');?>">
                                <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">己认证</button>
                            </a>
                        <?php else: ?>
                            <a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($userData['id']); ?>" data-title="是否确认邮箱账号认证?"  data-url="<?php echo U('User/saveIsEmail');?>">
                                <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-disabled">未认证</button>
                            </a><?php endif; ?>
                    <br/>
                    <?php if($v["level"] > 0): ?><b style="color:red;">会员等级：<?php echo ($levelInfo[$v['level']]); ?></b><br/><?php endif; ?>
                  	<?php if($v["leader"] > 0): ?><b style="color:royalblue;">领导等级：<?php echo ($leaderInfo[$v['leader']]); ?></b><br/><?php endif; ?>
                    <?php if($v["service"] > 0): ?><b style="color:#ff00ff;">服务等级：<?php echo ($serviceInfo[$v['service']]); ?></b><br/><?php endif; ?>
                </td>
                <td>
                    引荐：<?php echo ((isset($tjrList[$v['tjr_id']]) && ($tjrList[$v['tjr_id']] !== ""))?($tjrList[$v['tjr_id']]):'主账号引荐'); ?> <a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs layui-btn layui-btn-radius etitTjr"><i class="layui-icon">&#xe642;</i> 修改</a><br/>
                  	投资金额：<?php echo ((isset($v['invest_money']) && ($v['invest_money'] !== ""))?($v['invest_money']):'0.00'); ?> <a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs layui-btn layui-btn-radius investMoney"><i class="layui-icon">&#xe642;</i> 修改</a><br/>
                    <!--报单：<?php echo ((isset($tjrList[$v['bdr_id']]) && ($tjrList[$v['bdr_id']] !== ""))?($tjrList[$v['bdr_id']]):'主账号报单'); ?> <a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs layui-btn layui-btn-radius etitBdr"><i class="layui-icon">&#xe642;</i> 修改</a><br/>-->
                    注册时间：<?php echo (date('Y-m-d H:i:s', $v["reg_time"])); ?><br/>
                    激活时间：<?php if($v["jh_time"] > 0): echo (date('Y-m-d H:i:s', $v["jh_time"])); else: ?>待激活<?php endif; ?><br/>
                    身份证号：<?php echo ((isset($userData["number"]) && ($userData["number"] !== ""))?($userData["number"]):'暂无填写'); ?>
                        <?php if($userData["is_number"] == 1): ?><a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($userData['id']); ?>" data-title="是否取消身份证号码认证?"  data-url="<?php echo U('User/saveIsNumber');?>">
                                <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">己认证</button>
                            </a>
                        <?php else: ?>
                            <a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($userData['id']); ?>" data-title="是否确认身份证号码认证?"  data-url="<?php echo U('User/saveIsNumber');?>">
                                <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-disabled">未认证</button>
                            </a><?php endif; ?>
                </td>

                <td>
                    省：<?php echo ((isset($regionInfo[$userData['province']]) && ($regionInfo[$userData['province']] !== ""))?($regionInfo[$userData['province']]):'无'); ?><br/>
                    市：<?php echo ((isset($regionInfo[$userData['city']]) && ($regionInfo[$userData['city']] !== ""))?($regionInfo[$userData['city']]):'无'); ?><br/>
                    县：<?php echo ((isset($regionInfo[$userData['district']]) && ($regionInfo[$userData['district']] !== ""))?($regionInfo[$userData['district']]):'无'); ?><br/>
                    乡：<?php echo ((isset($regionInfo[$userData['twon']]) && ($regionInfo[$userData['twon']] !== ""))?($regionInfo[$userData['twon']]):'无'); ?><br/>
                    <?php echo ($userData['address']); ?>
                </td>
                <td>
                    <?php if($v["frozen"] == 1): ?><button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">允许登录</button>
                    <?php else: ?>
                        <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-disabled">禁止登录</button><?php endif; ?><br/>
                    <?php if($v["activate"] == 1): ?><button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">己经激活</button>
                    <?php else: ?>
                        <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-disabled">暂未激活</button><?php endif; ?><br/>
                    <?php if($v["user"] == 1): ?><button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">正常报单</button><?php endif; ?>
                    <?php if($v["user"] == 2): ?><button class="layui-btn layui-btn-xs layui-btn-warm layui-btn-normal">空单账号</button><?php endif; ?>
                    <?php if($v["user"] == 3): ?><button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal">回填账号</button><?php endif; ?>
<!--                    <br/>
                    <?php if($v["power"] == 1): ?><a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($v['user_id']); ?>" data-title="是否取消无需按会员等级考核直接收款?"  data-url="<?php echo U('User/savePower');?>">
                            <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-normal"><i class="layui-icon">&#xe642;</i>无需考核</button>
                        </a>
                    <?php else: ?>
                        <a href="javaScript:void(0);" class="saveStatu" data-id="<?php echo ($v['user_id']); ?>" data-title="是否确认开启无需按会员等级考核直接收款?"  data-url="<?php echo U('User/savePower');?>">
                            <button class="layui-btn layui-btn-xs layui-btn-radius layui-btn-disabled"><i class="layui-icon">&#xe642;</i>考核收款</button>
                        </a><?php endif; ?>-->
                </td>
                <td>
                    <?php if($v['frozen'] == 1): ?><a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs layui-btn-warm addFrozen"><i class="layui-icon">&#xe609;</i>冻结当前会员</a><br/>
                    <?php else: ?>
                        <a href="<?php echo U('User/releaseFrozen',array('id'=>$v['user_id']));?>"  class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon">&#xe609;</i>释放当前会员</a><br/><?php endif; ?>
                    <a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs editPassword"><i class="layui-icon">&#xe614;</i>修改登录密码</a><br/>
                    <a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs editSecpwd"><i class="layui-icon">&#xe620;</i>修改二级密码</a><br/>
                    <!-- <a href="<?php echo U('User/editSecurity',array('id'=>$v['user_id']));?>" class="layui-btn layui-btn-xs"><i class="layui-icon">&#xe6b2;</i>修改密保信息</a><br/> -->
                </td>
                <td>
                    <a href="<?php echo U('User/editBank',array('id'=>$v['user_id']));?>" class="layui-btn layui-btn-xs layui-btn-normal"><i class="layui-icon">&#xe659;</i>修改收款信息</a><br/>
                    <a href="<?php echo U('Zfuwl/User/userLogin',array('id'=>$v['user_id']));?>" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal login"><i class="layui-icon">&#xe605;</i>登录会员中心</a><br/>
                    <a href="<?php echo U('User/editData',array('id'=>$v['user_id']));?>" class="layui-btn layui-btn-xs layui-btn-normal"><i class="layui-icon">&#xe642;</i>修改基本资料</a><br/>
                    <a data="<?php echo ($v["user_id"]); ?>" class="layui-btn layui-btn-xs layui-btn-danger del"><i class="layui-icon">&#xe640;</i>删除当前会员</a><br/>
                </td>
            </tr><?php endforeach; endif; endif; ?>
    </tbody>
</table>
<?php echo ($page); ?>
<script>
    layui.use(['form'], function () {
        var form = layui.form;
        form.render('checkbox'); //刷新checkbox渲染
        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
        $('.saveStatu').on('click',function(){
            var id = $(this).data('id');
            var url = $(this).data('url');
            var title = $(this).data('title');
            layer.confirm(title, {icon: 3,skin: 'layer-ext-moon',btn: ['确认', '取消']}, function() {
                $.post(url, {id:id}, function (data) {
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
    });
    $(".pagination a").click(function () {
        var page = $(this).data('p');
        ajax_get_table('search-form2', page);
    });
    $('.del').click(function(){
        var url = "<?php echo U('User/delUsers');?>";
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
        layer.confirm('确定删除吗?', {icon: 3,skin: 'layer-ext-moon',btn: ['确认', '取消']}, function() {
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
    $('.editPassword').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入新的登录密码'}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/editPassword');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        layer.closeAll();
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
    $('.editSecpwd').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入新的二级密码'}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/editSecpwd');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        layer.closeAll();
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
    $('.addFrozen').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入你的冻结原因',formType: 2}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/addFrozen');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
    $('.etitXinyu').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入信誉级别'}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/editXinyu');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
    $('.etitTjr').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入新的推荐人账号'}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/editUserTjr');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
    $('.etitBdr').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入新的报单人账号'}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/editUserBdr');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
  	$('.investMoney').click(function(){
        var id = $(this).attr('data');
        layer.prompt({title:'请输入新投资金额总数'}, function(value, index, elem){
            $.ajax({
                type:'post',
                data:{id:id, name:value},
                url:"<?php echo U('User/editInvestMoney');?>",
                success:function(data){
                    layer.close(index);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                    } else if (data.status == 1) {
                        var page = $('.pagination .active').find('a').data('p');
                        ajax_get_table('search-form2', page);
                        layer.msg(data.msg, {icon: 6, time: 2000});
                    }
                }
            })
        });
    });
</script>