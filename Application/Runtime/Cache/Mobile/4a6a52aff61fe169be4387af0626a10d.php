<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

    <head>
        <title>个人中心</title>
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
                <div class="noticeBox df">
                    <span class="span_1"><i class="fa fa-volume-up i"></i></span>
                    <div class="dowebok fx1" style="line-height: 35px;font-size:0.9em;">
                        <a class="likeA" data-url="<?php echo U('Notice/detail', array('id' => $notice_top['id']));?>"><?php echo ($notice_top['title']); ?></a>
                    </div>
                </div>
                <!--数据汇总-->
                <ul class="collectBox clearfix">
                    <li class="collectLi">
                        <div class="collectTb collectTb_1"><i class="fa fa-retweet"></i></div>
                        <div class="collectBt">实时兑换</div>
                        <div class="collectBt"><?php echo blockList(1,2);?></div>
                    </li>
                    <li class="collectLi">
                        <div class="collectTb collectTb_2"><i class="fa fa-graduation-cap"></i></div>
                        <div class="collectBt">会员等级</div>
                        <div class="collectBt"><?php echo ($levelInfo[$user['level']]); ?></div>
                    </li>
                    <li class="collectLi">
                        <div class="collectTb collectTb_3"><i class="fa fa-credit-card"></i></div>
                        <div class="collectBt">总投资钱包</div>
                        <div class="collectBt"><?php echo ((isset($user['invest_money']) && ($user['invest_money'] !== ""))?($user['invest_money']):'0.00'); ?></div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Block/transAdd');?>">
                        <div class="collectTb collectTb_4"><i class="fa fa-database"></i></div>
                        <div class="collectBt">存储YML</div>
                        <div class="collectBt"><?php echo usersBlock($user['user_id'], 1, 5);?></div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Block/flowAround');?>">
                        <div class="collectTb collectTb_5"><i class="fa fa-stack-overflow"></i></div>
                        <div class="collectBt">流动YML</div>
                        <div class="collectBt"><?php echo userBlock($user['user_id'], 1, 1);?></div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Block/blockSellIndex');?>">
                        <div class="collectTb collectTb_6"><i class="fa fa-tags"></i></div>
                        <div class="collectBt">卖出YML</div>
                        <div class="collectBt">0.00</div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Block/fenmoneyLog');?>">
                        <div class="collectTb collectTb_6"><i class="fa fa-tags"></i></div>
                        <div class="collectBt">买入YML</div>
                        <div class="collectBt">0.00</div>
                    </li>
                    <li class="collectLi">
                        <div class="collectTb collectTb_7"><i class="fa fa-cubes"></i></div>
                        <div class="collectBt">释放积分</div>
                        <div class="collectBt"><?php echo ((isset($releaseMoney) && ($releaseMoney !== ""))?($releaseMoney):"0.00"); ?></div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Change/getIntegral');?>">
                        <div class="collectTb collectTb_8"><i class="fa fa-map-o"></i></div>
                        <div class="collectBt">领取积分</div>
                        <div class="collectBt"><?php echo ((isset($receiveMoney) && ($receiveMoney !== ""))?($receiveMoney):"0.00"); ?></div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Block/transfrom');?>">
                        <div class="collectTb collectTb_5"><i class="fa fa-stack-overflow"></i></div>
                        <div class="collectBt">分享积分</div>
                        <div class="collectBt"><?php echo usersMoney($user['user_id'],1, 1);?></div>
                    </li>
                    <li class="collectLi likeA" data-url="<?php echo U('Bonus/logIndex');?>">
                        <div class="collectTb collectTb_10"><i class="fa fa-bookmark-o"></i></div>
                        <div class="collectBt">展示积分</div>
                        <div class="collectBt"><?php echo ((isset($moneyAll) && ($moneyAll !== ""))?($moneyAll):"0.00"); ?></div>
                    </li>
                  	<?php if($user['invest_money'] > 0 && $config['securityInfo_returnben'] == 1): ?><li class="collectLi likeA" data-url="<?php echo U('Block/returnben');?>">
                            <div class="collectTb collectTb_5"><i class="fa fa-stack-overflow"></i></div>
                            <div class="collectBt">一键回本</div>
                            <div class="collectBt"><?php echo ((isset($user['invest_money']) && ($user['invest_money'] !== ""))?($user['invest_money']):'0.00'); ?></div>
                        </li>
                    <?php elseif($recovery['is_type' ] == 1): ?>
                        <li class="collectLi">
                            <div class="collectTb collectTb_5"><i class="fa fa-stack-overflow"></i></div>
                            <div class="collectBt">回本处理中</div>
                        </li><?php endif; ?>
                  	
                </ul>
                <!--走势-->
                <div class="communalBox">
                    <div class="communalTl">
                       <span class="span_1">源码链走势</span>
                    </div>
                    <div class="communalTx">
                        <div class="communalTxbt">算力图表</div>
                        <div class="chartBox zgui-PE-img"><div id="main" style="width:100%;height:350px;borcontainerder:1px solid #dddddd;margin:10px auto;"></div></div>
                    </div>
                </div>
                <!--公告-->
                <div class="communalBox">
                    <div class="communalTl">
                        <span class="span_1">公告</span>
                    </div>
                    <div class="communalTx">
                        <ul class="journalismUl">
                            <?php if(is_array($notice)): foreach($notice as $key=>$v): ?><li class="journalismLi likeA" data-url="<?php echo U('Notice/detail', array('id' => $v['id'], 'cat_id' => 1));?>">
                                    <div class="caption">
                                        <i class="fa fa-circle i"></i>
                                        <?php echo ($v['title']); ?>
                                    </div>
                               <!--     <div class="journalismTime">【<?php echo date('Y-m-d' ,$v['add_time']);?>】</div> -->
                                </li><?php endforeach; endif; ?>
                        </ul>
                    </div>
                </div>
                <!--新闻-->
                <div class="communalBox">
                    <div class="communalTl">
                        <span class="span_1">新闻</span>
                    </div>
                    <div class="communalTx">
                        <ul class="journalismUl">
                            <?php if(is_array($news)): foreach($news as $key=>$v): ?><li class="journalismLi likeA" data-url="<?php echo U('Notice/detail', array('id' => $v['id'], 'cat_id' => 2));?>">
                                    <div class="caption">
                                        <i class="fa fa-circle i_1"></i>
                                        <?php echo ($v['title']); ?>
                                    </div>
                                 <!--       <div class="journalismTime">【<?php echo date('Y-m-d' ,$v['add_time']);?>】</div> -->
                                </li><?php endforeach; endif; ?>
                        </ul>
                    </div>
                </div>
                <!--邀请好友-->
                <div class="communalBox">
                    <div class="communalTl">
                        <span class="span_1">邀请好友</span>
                    </div>
                    <div class="communalTx">
                        <div class="inviteBox">
                            <div id="qrcode"></div>
                            <div class="inviteTx"><span>邀请链接 ： </span><span><?php echo ($tgurl); ?></span></div>
                            <button type="button" class="gonggButtonlj" id="copyTgUrl" data-clipboard-text="<?php echo ($tgurl); ?>">复制链接</button>
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
    <script src="/Template/Mobile/default/Static/js/clipboard.min.js"></script>
    <script src="/Public/js/qrcode.js"></script>
    <script src="/Template/Mobile/default/Static/js/xiaoxi.js" type="text/javascript" charset="utf-8"></script>
    <script src="/Public/js/echarts.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
            var myChart = echarts.init(document.getElementById('main'));
            option = {
                tooltip : {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#6a7985'
                        }
                    }
                },

                legend: {
                    data:['价格走势图']
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap : false,
                        data : <?php echo ($timeArr); ?>
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'¥',
                        type:'line',
                        stack: '总量',
                        label: {
                            normal: {
                                show: true,
                                position: 'top'
                            }
                        },
                        areaStyle: {normal: {}},
                        data:<?php echo ($priceArr); ?>
                    }
                ]
            };
            myChart.setOption(option);
		</script>
    <script type="text/javascript" charset="utf-8">

        mui('body').on('tap', '.development', function () {
            mui.confirm('功能暂未开放','温馨提示', ['确定']);
        });

        $(function() {
            $('.dowebok').liMarquee({
                drag: false
            });
        });

        mui('body').on('tap', ".serviceAniu", function () {
            if($('.serviceText').css('display') == 'none'){
                $(this).next('.serviceText').show();
                $('.zhixiJiant').css('display','inline-block');
                $('.regularJiant').css('display','none');
            }else {
                $(this).next('.serviceText').hide();
                $('.regularJiant').css('display','inline-block');
                $('.zhixiJiant').css('display','none');
            }
        });

    	//    复制链接
        window.onload = function(){
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                width : 150,//设置宽高
                height : 150
            });
            qrcode.makeCode("<?php echo ($tgurl); ?>");
        };
        var clipboard = new Clipboard('#copyTgUrl');
        clipboard.on('success', function (e) {
            e.clearSelection();
            mui.toast('复制成功', {duration: 'long', type: 'div'});
        });
        clipboard.on('error', function (e) {
            mui.toast('复制失败', {duration: 'long', type: 'div'});
        });

    </script>
</body>

</html>