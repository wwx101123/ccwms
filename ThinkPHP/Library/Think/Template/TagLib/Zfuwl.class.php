<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think\Template\TagLib;

use Think\Template\TagLib;

/**
 * 自定义标签
 */
class Zfuwl extends TagLib
{

    protected $tags = array(
        'adv' => array('attr' => 'limit,order,where,item', 'close' => 1),
        'zfuwl' => array('attr' => 'sql,key,item,result_name', 'close' => 1, 'level' => 3), // 万能标签
    );

    /**
     * 广告标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function _adv($tag, $content)
    {
        $where = $tag['where'];
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1';
        $item = !empty($tag['item']) ? $tag['item'] : 'item'; // 返回的变量item
        $key = !empty($tag['key']) ? $tag['key'] : 'key'; // 返回的变量key
        $limitStart = !empty($tag['limit_start']) ? $tag['limit_start'] : '0'; // 返回的变量key

        $str = '<?php ';
        $str .= '$where =' . $where . ';';
        $str .= '$adSite = M("ad_site")->where("site_where = \'".$where."\' and statu = 1")->cache(true,ZFUWL_CACHE_TIME)->find();';
        $str .= '$adSite && $result = D("ad")->where("site_id=".$adSite["id"]." and statu = 1 and start_time < ' . strtotime(date('Y-m-d H:00:00')) . ' and end_time > ' . strtotime(date('Y-m-d H:00:00')) . ' ")->order("sort desc")->cache(true,ZFUWL_CACHE_TIME)->limit("' . $limitStart.','.$limit . '")->select();';
        $str .= '
                foreach($result as $' . $key . '=>$' . $item . '):
                    $' . $item . '[\'ad_width\'] = $adSite[\'site_width\'];
                    $' . $item . '[\'ad_height\'] = $adSite[\'site_height\'];
            ?>';
        $str .= $this->tpl->parse($content);
        $str .= '<?php endforeach; ?>';
        return $str;
    }

    /**
     * sql 语句万能标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function _zfuwl($tag, $content)
    {
        $sql = $tag['sql']; // sql 语句
        $sql = str_replace(' eq ', ' = ', $sql); // 等于
        $sql = str_replace(' neq  ', ' != ', $sql); // 不等于
        $sql = str_replace(' gt ', ' > ', $sql); // 大于
        $sql = str_replace(' egt ', ' >= ', $sql); // 大于等于
        $sql = str_replace(' lt ', ' < ', $sql); // 小于
        $sql = str_replace(' elt ', ' <= ', $sql); // 小于等于

        $key = !empty($tag['key']) ? $tag['key'] : 'key'; // 返回的变量key
        $item = !empty($tag['item']) ? $tag['item'] : 'item'; // 返回的变量item
        $result_name = !empty($tag['result_name']) ? $tag['result_name'] : 'result_name'; // 返回的变量key
        $name = 'sql_result_' . $item; // 数据库结果集返回命名
        $parseStr = '<?php

                        $md5_key = md5("' . $sql . '");
                        $' . $name . ' = S("sql_".$md5_key);
                        if(empty($' . $name . ')){
                            $Model = new \Think\Model();
                            $' . $result_name . ' = $' . $name . ' = $Model->query("' . $sql . '");
                            S("sql_".$md5_key,$' . $name . ',ZFUWL_CACHE_TIME);
                        }
                             ';
        $parseStr .= ' foreach($' . $name . ' as $' . $key . '=>$' . $item . '): ?>';
        $parseStr .= $this->tpl->parse($content) . $tag['level'];
        $parseStr .= '<?php endforeach; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

}
