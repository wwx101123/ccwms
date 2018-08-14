<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <title><?php echo ($_GET['cat_id']==1?'公告':'新闻'); ?></title>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<title></title>
<link rel="stylesheet" href="/Public/plugins/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/mui.min.css" />
<link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/style.css" />
<link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/mui.showLoading.css" />
<script src="/Template/Mobile/default/Static/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/xiaoxi.css" />
</head>
<body>
    <div class="publicBox">
        <!--左侧导航-->
        <div class="leftNavigationBox">
    <div class="CloseButton"></div>
    <div class="leftNavigation">
        <div class="navigationBox">
            <div class="navigBox"><img src="<?php echo ((isset($config['webInfo_web_logo_img']) && ($config['webInfo_web_logo_img'] !== ""))?($config['webInfo_web_logo_img']):'/Public/images/not_head.jpg'); ?>" alt="" /></div>
            <div class="headlineBox">源码链用户中心</div>
            <ul class="navigationUl">
                <li class="navigationLi ">
                    <div class="navigTop df">
                        <div class="fx1">
                            <span class="span_1"><i class="fa fa-user i"></i></span>
                            <span class="span_2">用户管理</span>
                        </div>
                        <div class="pointTo">
                            <i class="fa fa-angle-right zhiyou"></i>
                            <i class="fa fa-angle-down zhixia"></i>
                        </div>
                    </div>
                    <div class="navigTxHei">
                        <div class="likeA" data-url="<?php echo U('User/userIndex');?>">用户主页</div>
                        <div class="likeA" data-url="<?php echo U('User/lookData');?>">用户资料</div>
                        <!--<div class="likeA" data-url="<?php echo U('User/userIndex');?>">用户银行卡</div>-->
                    </div>
                </li>
                <li class="navigationLi ">
                    <div class="navigTop df">
                        <div class="fx1">
                            <span class="span_1"><i class="fa fa-users i"></i></span>
                            <span class="span_2">团队管理</span>
                        </div>
                        <div class="pointTo">
                            <i class="fa fa-angle-right zhiyou"></i>
                            <i class="fa fa-angle-down zhixia"></i>
                        </div>
                    </div>
                    <div class="navigTxHei">
                        <div class="likeA" data-url="<?php echo U('Team/ztIndex');?>">团队列表</div>
                    </div>
                </li>
                <li class="navigationLi ">
                    <div class="navigTop df">
                        <div class="fx1">
                            <span class="span_1"><i class="fa fa-pie-chart i"></i></span>
                            <span class="span_2">交易管理</span>
                        </div>
                        <div class="pointTo">
                            <i class="fa fa-angle-right zhiyou"></i>
                            <i class="fa fa-angle-down zhixia"></i>
                        </div>
                    </div>
                    <div class="navigTxHei">
                        <!--<div class="likeA" data-url="<?php echo U('User/userIndex');?>">交易变现</div>-->
                        <div class="likeA" data-url="<?php echo U('Change/getIntegral');?>">领取积分</div>
                        <div class="likeA" data-url="<?php echo U('Change/storeRollout');?>">存储YML转出</div>
                        <div class="likeA" data-url="<?php echo U('Change/pointsRoll');?>">分享积分转出</div>
                        <div class="likeA" data-url="<?php echo U('Block/transAdd');?>">流动YML转存储YML</div>
                        <div class="likeA" data-url="<?php echo U('Block/flowAround');?>">流动YML转流动YML</div>
                    </div>
                </li>
                <li class="navigationLi ">
                    <div class="navigTop df">
                        <div class="fx1">
                            <span class="span_1"><i class="fa fa-calendar-check-o i"></i></span>
                            <span class="span_2">日志管理</span>
                        </div>
                        <div class="pointTo">
                            <i class="fa fa-angle-right zhiyou"></i>
                            <i class="fa fa-angle-down zhixia"></i>
                        </div>
                    </div>
                    <div class="navigTxHei">
                        <div class="likeA" data-url="<?php echo U('Order/storeoutgoingLog');?>">存储YML转出日志</div>
                        <div class="likeA" data-url="<?php echo U('Block/stream');?>">流动YML转出日志</div>
                        <div class="likeA" data-url="<?php echo U('Order/flowOutLog');?>">流动YML转账日志</div>
                        <div class="likeA" data-url="<?php echo U('Order/scoreCollectionLog');?>">领取积分日志</div>
                        <div class="likeA" data-url="<?php echo U('Order/integralLog');?>">积分日志</div>
                        <div class="likeA" data-url="<?php echo U('Money/moneyLog');?>">钱包日志</div>
                        <div class="likeA" data-url="<?php echo U('Block/fenmoneyLog');?>">交易日志</div>
                    </div>
                </li>
                <li class="navigationLi ">
                    <div class="navigTop df">
                        <div class="fx1">
                            <span class="span_1"><i class="fa fa-shield i"></i></span>
                            <span class="span_2">安全管理</span>
                        </div>
                        <div class="pointTo">
                            <i class="fa fa-angle-right zhiyou"></i>
                            <i class="fa fa-angle-down zhixia"></i>
                        </div>
                    </div>
                    <div class="navigTxHei">
                        <div class="likeA" data-url="<?php echo U('User/editData');?>">设置资料</div>
                        <div class="likeA" data-url="<?php echo U('User/editPassword');?>">设置登录密码</div>
                        <div class="likeA" data-url="<?php echo U('User/editSecpwd');?>">设置交易密码</div>
                      	<div class="likeA" data-url="<?php echo U('Validate/validateBank');?>">设置银行卡</div>
                        <div class="logout guanbicbl">退出登录</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>

    // //退出
    // mui('body').on('tap', ".logout", function () {
    //     var btnArray = ['确认', '取消'];
    //     mui.confirm('是否确认退出？', '温馨提示', btnArray, function (e) {
    //         if (e.index == 0) {
    //             mui.openWindow({
    //                 id: 'logout',
    //                 url: "<?php echo U('User/logout');?>"
    //             });
    //         }
    //     });
    // });
</script>
        <div class="publicBoxzi">
            <!--公共头部-->
            <div class="publicHead">
    <span class="menuLinkA PopupNavigation"><i class="fa fa-reorder"></i></span>
    <div class="informationBox">
        <div class="zxlogo">
            <img src="<?php echo ((isset($userData['head']) && ($userData['head'] !== ""))?($userData['head']):'/Public/images/not_head.jpg'); ?>" class="uploadImgHead" alt="" />
            <input type='file' name='img' style='display:none' class='imgup' />
        </div>
        <div class="numberId"><?php echo ($user['account']); ?></div>
        <div class="status"><?php echo ($leaderInfo[$user['leader']]); ?></div>
    </div>
    <div class="headNavBox df">
        <div class="fx1 likeA" data-url="<?php echo U('User/userIndex');?>">
            <i class="fa fa-home i"></i>
            <span class="span_1">首页</span>
        </div>
        <span  class="fx1 PopupNavigation">
            <i class="fa fa-tasks i"></i>
            <span class="span_1">菜单</span>
        </span>
    </div>
</div>
            <!--公共盒子-->
            <div class="publicHeiz">

                <!--公告-->
                <div class="communalBox">
                    <div class="communalTl">
                        <span class="span_1"><?php echo ($_GET['cat_id']==1?'公告':'新闻'); ?></span>
                    </div>
                    <div class="communalTx">
                        <div class="detailBox">
                            <div class="detailTl">
                                <?php echo ($info['title']); ?>
                            </div>
                            <div class="detailTx">
                                <?php echo (htmlspecialchars_decode($info['content'])); ?>
                            </div>
                         <!--   <div class="detailTime">
                                <?php echo date('Y-m-d H:i:s', $info['add_time']);?>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="/Template/Mobile/default/Static/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/Template/Mobile/default/Static/js/mui.showLoading.js" type="text/javascript" charset="utf-8"></script>
<script src="/Template/Mobile/default/Static/js/style.js" type="text/javascript" charset="utf-8"></script>
<script>
    var mask=mui.createMask();
    var imgObj = [];
    mui('body').on('change', '.imgup', function(){
        var formData = new FormData();
        var img = $(this).val();
        formData.append("img", $(this)[0].files[0]);
        formData.append("field", 'img');
        mui.showLoading("上传中");
        $.ajax({
            url: "<?php echo U('Zfuwl/Api/imageUp');?>",
            type: "post",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            async:false,
            success: function(res) {
                mask.close();//关闭遮罩层
                mui.hideLoading();
                if(res.code != 0) {
                    mui.alert(res.msg);
                } else {
                    $.post("<?php echo U('User/saveUserHeadImg');?>",{src:res.data.src}, function(data){
                        if(data.status == 1) {
                            mui.toast('上传成功',{ duration:'2000', type:'div' });
                            $(imgObj).attr('src', res.data.src);
                        } else {
                            mui.toast(data.msg,{ duration:'2000', type:'div' });
                        }
                    });
                }
            },
            error: function(e) {
                alert("网络错误，请重试！！");
            }
        });
    });

    mui('body').on('tap', '.uploadImgHead', function(){
        $('.imgup').click();
        imgObj = $(this);
    });
</script>
</body>

</html>