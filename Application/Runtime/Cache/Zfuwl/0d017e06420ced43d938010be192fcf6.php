<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
<title><?php echo ($config['webInfo_web_title']); ?>后台管理</title>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/plugins/layuiadmin/layui/css/layui.css" media="all">
<link rel="stylesheet" href="/Public/plugins/layuiadmin/style/admin.css" media="all">
<link rel="stylesheet" href="/Public/plugins/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" href="/Public/css/page.css">
<script type="text/javascript" src="/Public/js/jquery-1.8.3.min.js"></script>
</head>
<body>
    <div class="admin-main">
        <fieldset class="layui-elem-field">
            <div class="layui-field-box">
                <form class="layui-form layui-form-pane" action="">
                    <!--<div class="layui-inline layui-form-item" pane="">
                        <label class="layui-form-label">新会员账号</label>
                        <div class="layui-input-block">
                            <input type="radio" name="auto_account" value="1" title="自动生成" <?php if($config["auto_account"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="auto_account" value="2" title="手动填写" <?php if($config["auto_account"] == 2): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="auto_account" value="3" title="手机注册" <?php if($config["auto_account"] == 3): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">注册ID以</label>
                        <div class="layui-input-inline">
                            <input type="text" name="reg_id" value="<?php echo ((isset($config['reg_id']) && ($config['reg_id'] !== ""))?($config['reg_id']):'1'); ?>" placeholder="注册ID开始号" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">黙认为“ 1 ”</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">开始</b></div>
                    </div>
                    <div class="layui-inline layui-form-item" pane="">
                        <label class="layui-form-label">会员注册时</label>
                        <div class="layui-input-block">
                            <input type="radio" name="grap_phone_code" value="1" title="开启手机验证码" <?php if($config["grap_phone_code"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="grap_phone_code" value="2" title="关闭手机验证码" <?php if($config["grap_phone_code"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">账号格式</label>
                            <div class="layui-input-inline">
                                <input type="text" name="account_mai" value="<?php echo ((isset($config['account_mai']) && ($config['account_mai'] !== ""))?($config['account_mai']):'3'); ?>" placeholder="账号格式" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid"><b style="color:red;">位至 - </b></div>
                            <div class="layui-input-inline">
                                <input type="text" name="account_max" value="<?php echo ((isset($config['account_max']) && ($config['account_max'] !== ""))?($config['account_max']):'8'); ?>" placeholder="账号格式" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid"><b style="color:red;">位</b></div>
                        </div>
                    </div>
                    <div class="layui-inline layui-form-item" pane="">
                        <label class="layui-form-label">首位是字母</label>
                        <div class="layui-inline layui-input-block">
                            <input type="radio" name="isfirst" value="1" title="开启" <?php if($config["isfirst"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="isfirst" value="2" title="关闭" <?php if($config["isfirst"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">账号前缀为</label>
                        <div class="layui-input-inline">
                            <input type="text" name="account_start" value="<?php echo ((isset($config['account_start']) && ($config['account_start'] !== ""))?($config['account_start']):'zf'); ?>" placeholder="账号前缀为" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">如前缀 zf 账号 888 连起来是 ：zf888</b></div>
                    </div>
                   <!-- <div class="layui-inline layui-form-item" pane="">
                        <label class="layui-form-label">推荐人账号</label>
                        <div class="layui-inline layui-input-block">
                            <input type="radio" name="tjr_account" value="1" title="必须填写注册" <?php if($config["tjr_account"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="tjr_account" value="2" title="黙认公司账号" <?php if($config["tjr_account"] == 2): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="tjr_account" value="3" title="黙认特定账号" <?php if($config["tjr_account"] == 3): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">特定账号为</label>
                        <div class="layui-input-inline">
                            <input type="text" name="default_tjrAccount" value="<?php echo ((isset($config['default_tjrAccount']) && ($config['default_tjrAccount'] !== ""))?($config['default_tjrAccount']):'1'); ?>" placeholder="特定新会员注册的推荐人账号" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">特定新会员的推荐人账号</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">为黙认推荐人</b></div>
                    </div>
<!--
                    <div class="layui-form-item">
                        <label class="layui-form-label">登录密码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="default_pass" value="<?php echo ((isset($config['default_pass']) && ($config['default_pass'] !== ""))?($config['default_pass']):'888888'); ?>" placeholder="会员注册黙认的登录密码" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">黙认为：888888</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">会员注册黙认的登录密码</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">二级密码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="default_repass" value="<?php echo ((isset($config['default_repass']) && ($config['default_repass'] !== ""))?($config['default_repass']):'999999'); ?>" placeholder="会员注册黙认的二级密码" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">黙认为：999999</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">会员注册黙认的登录密码</b></div>
                    </div>
-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">同身份注册</label>
                        <div class="layui-input-inline">
                            <input type="text" name="card_num" value="<?php echo ((isset($config['card_num']) && ($config['card_num'] !== ""))?($config['card_num']):'0'); ?>" placeholder="同身份证注册会员账号数量" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">同一张身份证号；0 为不限制</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">个会员账号</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">同手机注册</label>
                        <div class="layui-input-inline">
                            <input type="text" name="phone_num" value="<?php echo ((isset($config['phone_num']) && ($config['phone_num'] !== ""))?($config['phone_num']):'0'); ?>" placeholder="同手机号注册会员账号数量" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">同一个手机号；0 为不限制</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">个会员账号</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">同邮箱注册</label>
                        <div class="layui-input-inline">
                            <input type="text" name="email_num" value="<?php echo ((isset($config['email_num']) && ($config['email_num'] !== ""))?($config['email_num']):'0'); ?>" placeholder="同邮箱号注册会员账号数量" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">同一个邮箱账号；0 为不限制</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">个会员账号</b></div>
                    </div>

                    <div class="layui-inline layui-form-item" pane="">
                        <label class="layui-form-label">注册成功后</label>
                        <div class="layui-input-block">
                            <input type="radio" name="is_acvite" value="1" title="黙认己经激活" <?php if($config["is_acvite"] == 1): ?>checked=""<?php endif; ?>>
                            <!--<input type="radio" name="is_acvite" value="2" title="黙认等待激活" <?php if($config["is_acvite"] == 2): ?>checked=""<?php endif; ?>>-->
                        </div>
                        <div class="layui-input-block">
                            <input type="radio" name="is_lock" value="1" title="黙认正常登录" <?php if($config["is_lock"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="is_lock" value="2" title="黙认禁止登录" <?php if($config["is_lock"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                        <div class="layui-input-block">
                            <input type="radio" name="is_tk" value="1" title="黙认正常提现" <?php if($config["is_tk"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="is_tk" value="2" title="黙认禁止提现" <?php if($config["is_tk"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>
<!--                    <div class="layui-form-item">
                        <label class="layui-form-label">报单金额</label>
                        <div class="layui-input-inline">
                            <input type="text" name="jh_money" value="<?php echo ((isset($config['jh_money']) && ($config['jh_money'] !== ""))?($config['jh_money']):'0'); ?>" placeholder="报单金额" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">激活赠送</label>
                        <div class="layui-input-inline">
                            <input type="text" name="jh_zs_nyl_num" value="<?php echo ((isset($config['jh_zs_nyl_num']) && ($config['jh_zs_nyl_num'] !== ""))?($config['jh_zs_nyl_num']):'0'); ?>" placeholder="激活赠送" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">诺一链</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">出局奖励</label>
                        <div class="layui-input-inline">
                            <input type="text" name="cj_jl_num" value="<?php echo ((isset($config['cj_jl_num']) && ($config['cj_jl_num'] !== ""))?($config['cj_jl_num']):'0'); ?>" placeholder="出局奖励" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">复投金额</label>
                        <div class="layui-input-inline">
                            <input type="text" name="ft_num" value="<?php echo ((isset($config['ft_num']) && ($config['ft_num'] !== ""))?($config['ft_num']):'0'); ?>" placeholder="复投金额" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo bonusInfo(1)['name_cn'];?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="bonus_1_money" value="<?php echo ((isset($config['bonus_1_money']) && ($config['bonus_1_money'] !== ""))?($config['bonus_1_money']):'0'); ?>" placeholder="<?php echo bonusInfo(1)['name_cn'];?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo bonusInfo(12)['name_cn'];?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="bonus_12_money" value="<?php echo ((isset($config['bonus_12_money']) && ($config['bonus_12_money'] !== ""))?($config['bonus_12_money']):'0'); ?>" placeholder="<?php echo bonusInfo(12)['name_cn'];?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>-->
                   <!-- <div class="layui-form-item">
                        <label class="layui-form-label">直推</label>
                        <div class="layui-input-inline">
                            <input type="text" name="my_xj_jd_tj" value="<?php echo ((isset($config['my_xj_jd_tj']) && ($config['my_xj_jd_tj'] !== ""))?($config['my_xj_jd_tj']):'0'); ?>" placeholder="直推多少人以上可自己往自己下面接点" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">人以上可自己往自己下面接点（0即为不启用）</div>
                    </div>-->
                    <!--<fieldset class="layui-elem-field layui-field-title">
                        <legend><button class="layui-btn layui-btn-radius layui-btn-primary">激活会员方案一</button></legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <label class="layui-form-label">激活会员用</label>
                        <div class="layui-input-inline">
                            <select name="ma_id" lay-search="">
                                <option value="0">&#45;&#45;请选择&#45;&#45;</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['ma_id']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="ma_per" value="<?php echo ((isset($config['ma_per']) && ($config['ma_per'] !== ""))?($config['ma_per']):'50'); ?>" placeholder="账号使用比例" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">%</b></div>
                    </div>
-->
                    <!--<<fieldset class="layui-elem-field layui-field-title">
                        <legend><button class="layui-btn layui-btn-radius layui-btn-primary">激活会员方案二</button></legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <label class="layui-form-label">激活会员用</label>
                        <div class="layui-input-inline">
                            <select name="mb_id" lay-search="">
                                <option value="0">&#45;&#45;请选择&#45;&#45;</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['mb_id']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="mb_per" value="<?php echo ((isset($config['mb_per']) && ($config['mb_per'] !== ""))?($config['mb_per']):'50'); ?>" placeholder="账号使用比例" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">%</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">激活会员用</label>
                        <div class="layui-input-inline">
                            <select name="mc_id" lay-search="">
                                <option value="0">&#45;&#45;请选择&#45;&#45;</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['mc_id']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="mc_per" value="<?php echo ((isset($config['mc_per']) && ($config['mc_per'] !== ""))?($config['mc_per']):'50'); ?>" placeholder="账号使用比例" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">%</b></div>
                    </div>-->
                    <!-- <div class="layui-form-item">
                        <label class="layui-form-label">激活会员用</label>
                        <div class="layui-input-inline">
                            <select name="md_id" lay-search="">
                                <option value="0">--请选择--</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['md_id']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="md_per" value="<?php echo ((isset($config['md_per']) && ($config['md_per'] !== ""))?($config['md_per']):'50'); ?>" placeholder="账号使用比例" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">%</b></div>
                    </div> -->
                    <!-- <fieldset class="layui-elem-field layui-field-title">
                        <legend><button class="layui-btn layui-btn-radius layui-btn-primary">激活会员方案三</button></legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <label class="layui-form-label">激活会员用</label>
                        <div class="layui-input-inline">
                            <select name="me_id" lay-search="">
                                <option value="0">--请选择--</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['me_id']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="me_per" value="<?php echo ((isset($config['me_per']) && ($config['me_per'] !== ""))?($config['me_per']):'50'); ?>" placeholder="固定数量" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">个</b></div>
                    </div>

                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">回填单会员</label>
                            <div class="layui-input-inline">
                                <input type="text" name="back_tk" value="<?php echo ((isset($config['back_tk']) && ($config['back_tk'] !== ""))?($config['back_tk']):'50'); ?>" placeholder="提现比例" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid"><b style="color:red;"> % 可以申请提现  </b></div>
                            <div class="layui-input-inline">
                                <input type="text" name="back_reg" value="<?php echo ((isset($config['back_reg']) && ($config['back_reg'] !== ""))?($config['back_reg']):'50'); ?>" placeholder="回填账户比列" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid"><b style="color:red;">回填至报单金额</b></div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">空单会员</label>
                            <div class="layui-input-inline">
                                <input type="text" name="empty_tk" value="<?php echo ((isset($config['empty_tk']) && ($config['empty_tk'] !== ""))?($config['empty_tk']):'50'); ?>" placeholder="提现比例" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid"><b style="color:red;"> % 可以申请提现  </b></div>
                            <div class="layui-input-inline">
                                <input type="text" name="empty_reg" value="<?php echo ((isset($config['empty_reg']) && ($config['empty_reg'] !== ""))?($config['empty_reg']):'50'); ?>" placeholder="回填账户比列" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid"><b style="color:red;">回填至报单金额</b></div>
                        </div>
                    </div> -->
                    <!--<div class="layui-form-item">
                        <label class="layui-form-label">金额达到</label>
                        <div class="layui-input-inline">
                            <input type="text" name="enter_network_goods_money" value="<?php echo (floatval($config['enter_network_goods_money'])); ?>" placeholder="消费金额达到多少进入网络图" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">进入公排网</div>
                        <label class="layui-form-label">赠送</label>
                        <div class="layui-input-inline">
                            <input type="text" name="enter_network_give_money" value="<?php echo (floatval($config['enter_network_give_money'])); ?>" placeholder="消费金额达到多少进入网络图" autocomplete="off" class="layui-input">
                        </div>
                    </div>-->
                   <!-- <div class="layui-form-item">
                        <label class="layui-form-label">激活<?php echo moneyList(3);?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="activate_money3_per" value="<?php echo (floatval($config['activate_money3_per'])); ?>" placeholder="激活使用<?php echo moneyList(3);?>比例" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">%</div>
                    </div>-->
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" id="submitBtn" lay-submit lay-filter="articleHandle">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>
    </div>
<script src="/Public/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
    base: '/Public/plugins/layuiadmin/' //静态资源所在路径
}).extend({
    index: 'lib/index' //主入口模块
}).use('index');
</script>
<script type="text/javascript" src="/Public/js/zfuwlAjax.js"></script>

<script>
    layui.use(['layer', 'form', 'upload', 'laydate'], function () {
        var form = layui.form, $ = layui.jquery;
        //监听提交
        form.on('submit(articleHandle)', function (data) {
            var ArticleInfo = data.field;
            var url = "<?php echo U('');?>";
            $.post(url, ArticleInfo, function (data) {
                if (data.status != 1) {
                    layer.msg(data.msg, {icon: 5});
                } else {
                    layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                        location.reload();
                    });
                }
            });
            return false;//阻止表单跳转
        });

    });
</script>
</body>
</html>