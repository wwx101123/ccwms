<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/Public/css/datalist.css" type="text/css" />
        
    </head>
    <body>
        <div class="admin-main"  style="min-width:800px;">
            <table width="60%" align="center">
                <tbody>
                    <tr>
                <?php
 $md5_key = md5("select * from `__PREFIX__level` where statu = 1"); $sql_result_v = S("sql_".$md5_key); if(empty($sql_result_v)){ $Model = new \Think\Model(); $result_name = $sql_result_v = $Model->query("select * from `__PREFIX__level` where statu = 1"); S("sql_".$md5_key,$sql_result_v,ZFUWL_CACHE_TIME); } foreach($sql_result_v as $k=>$v): ?><td align="center" valign="middle" bgcolor="<?php echo ($v["color"]); ?>" width="60"><font color="#FFFFFF"><?php echo ($v["name_cn"]); ?></font></td><?php endforeach; ?>
                </tr>
                </tbody>
            </table>
            <table align="center">
                <tr>
                    <td>
                        <table width="100%" border="0">
                            <tbody>
                                <tr>
                            <form method="get" name="form1" id="form1">
                                <td height="30" align="center"><span id="Label1">会员账号：</span>
                                    <input name="account" type="text" class="xinshuru" id="userid" style="width:100px;border:1px solid #ddd;">
                                    <input type="submit" name="btnSearch" value="搜索" id="btnSearch" class="button_text" />
                                </td>
                            </form>
                </tr>
                </tbody>
            </table>
            <!-- 拓扑图开始 -->
        </td>
    </tr>
    <div align="center">
        <?php if($info['account'] != $zuihou['account']): ?><a href="?account=<?php echo ($info['tjrAccount']); ?>" class="button_text">上一层</a>
            <a href="?account=<?php echo ($zuihou['account']); ?>" class="button_text">置顶</a><?php endif; ?>
    </div>
    <br>
    <?php if($info): ?><tr>
            <td>
                <table align="center" cellpadding="0" cellspacing="0" width="80%">
                    <tbody>
                        <tr>
                            <td valign="top" align="center">
                                <table border="0" cellpadding="0" cellspacing="1" bgcolor="#517DBF" align="center"
                                       width="120px">
                                    <tbody>
                                        <tr>
                                            <td align="center" bgcolor="#FFFFFF">
                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                    <tbody>
                                                        <tr>
                                                            <td height="15" align="center" bgcolor="<?php echo ($level[color]); ?>"><?php echo ($info["account"]); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td height="15" align="center" bgcolor="<?php echo ($level[color]); ?>">
                                                                <font color="ffffff"><?php echo ($info["account"]); ?></font>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="15" align="center" bgcolor="<?php echo ($level[color]); ?>"><?php echo ($level["name_cn"]); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td height="15" align="center" bgcolor="<?php echo ($level[color]); ?>"><?php echo (date("Y-m-d H:i:s",$info["reg_time"])); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" bgcolor="#66c2cd">
                                                                <table width="100%" cellspacing="1" cellpadding="0" border="0" bgcolor="#E7F2FB">
                                                                    <tbody bgcolor="#c9e8ec" align='center'>
                                                                        <tr>
                                                                            <td height="15" width="30%">总</td>
                                                                            <td align="center" height="15"><?php echo count(nexttd($info['user_id']));?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td height="15" width="30%">直推</td>
                                                                            <td align="center" height="15"><?php echo (js_team($info['user_id'],"tjr_id")); ?></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" background="/Public/images/tab_19.gif">
                                                    <?php if($info["activate"] != 1): ?><span style="color:red;">未激活</span>
                                                        <?php else: ?>
                                                        <?php echo (date("Y-m-d H:i:s",$info["jh_time"])); endif; ?>
                                            </td>
                                        </tr>
                                        <tr></tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
        </table>
        <?php if(count($xjUser) > 0): ?><table border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td align="center"><img style="WIDTH: 1px; HEIGHT: 20px" alt="" src="/Public/images/line.gif" border="0"></td>
                    </tr>
                </tbody>
            </table>
            <?php echo tuopu_tjr_tree($info['user_id'],3,1); endif; ?>
        </td>
        </tr><?php endif; ?>
</table>
</div>
</body>
</html>