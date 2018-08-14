<?php
namespace Think;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------

class LyPage {

    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    //
    public $isShort =   0;
    public $canshu1 =   'id';
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
//    protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    protected $config  =    array(
        'header' => '<span class="rows">共 %totalRow% 条记录</span>',
        /*
        'prev'   => '<<',
        'next'   => '>>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        */
        'prev' => '上一页',
        'next' => '下一页',
        'first' => '首页',
        'last' => '尾页',
        'theme'=>'  %header% %nowPage%/%totalPage% 页  %first%  %upPage%  %linkPage%  %downPage%  %end%'
//        'theme' => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    );
    // 默认分页变量名
    protected $varPage;
    //短路由开头
    protected $asgin;
    //短路由分隔符
    protected $split;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows   每页显示记录数
     * @param array $isShort    启用短路由
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$isShort=0, $canshu1, $parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        $this->asgin        =   C('URL_ASGIN') ? C('URL_ASGIN') : 'c' ;
        $this->split        =   C('URL_SPLIT') ? C('URL_SPLIT') : '_' ;
        $this->isShort      =   C('IS_SHORT')?C('IS_SHORT'):0;
        $this->canshu1      =   $canshu1;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        if(!empty($url))    $this->url  =   $url;

        if($isShort){
           $this->isShort=1;
        }
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{

            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';

            $parameter_1 = $parameter[$this->canshu1] ? $parameter[$this->canshu1] : 0 ;

                $url   = $this->isShort ? U('/*%'.$parameter_1.'%'.$parameter['p']) : U('',$parameter);

            $url=str_replace('%', $this->split, str_replace('*',$this->asgin, $url));
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
//            $upPage     =   "<a href='".str_replace('__PAGE__',$upRow,$url)."'>".$this->config['prev']."</a>";
            $upPage = '<li id="example1_previous" class="paginate_button previous"><a class="prev" target="_self" href="' . str_replace('__PAGE__',$upRow,$url) . '">' . $this->config['prev'] . '</a></li>';
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
//            $downPage   =   "<a href='".str_replace('__PAGE__',$downRow,$url)."'>".$this->config['next']."</a>";
            $downPage   =   '<li id="example1_next" class="paginate_button next"><a target="_self" class="next" href="' . str_replace('__PAGE__',$downRow,$url) . '">' . $this->config['next'] . '</a></li>';
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $this->nowPage-$this->rollPage;
//            $prePage    =   "<a href='".str_replace('__PAGE__',$preRow,$url)."' >上".$this->rollPage."页</a>";
//            $prePage = '<li id="example1_previous" class="paginate_button previous"><a class="prev" target="_self" href="' . str_replace('__PAGE__',$preRow,$url) . '">上' . $this->rollPage . '页</a></li>';
//            $theFirst   =   "<a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a>";
            $theFirst = '<li id="example1_previous" class="paginate_button previous"><a class="first" target="_self" href="' . str_replace('__PAGE__',1,$url) . '">' . $this->config['first'] . '</a></li>';
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $this->nowPage+$this->rollPage;
            $theEndRow  =   $this->totalPages;
//            $nextPage   =   "<a href='".str_replace('__PAGE__',$nextRow,$url)."' >下".$this->rollPage."页</a>";
            $nextPage   =   '<li id="example1_next" class="paginate_button next"><a target="_self" class="next" href="' . str_replace('__PAGE__',$nextRow,$url) . '">下' . $this->rollPage . '页1</a></li>';
//            $theEnd     =   "<a href='".str_replace('__PAGE__',$theEndRow,$url)."' >".$this->config['last']."</a>";
            $theEnd = '<li id="example1_previous" class="paginate_button previous"><a class="end" target="_self" href="' . str_replace('__PAGE__',$theEndRow,$url) . '">' . $this->config['last'] . '</a></li>';
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page       =   ($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
//                    $linkPage .= "<a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a>";
                    $linkPage .= '<li class="paginate_button"><a class="num" target="_self" href="' . str_replace('__PAGE__',$page,$url) . '">' . $page . '</a></li>';
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
//                    $linkPage .= "<span class='current'>".$page."</span>";
                    $linkPage .= '<li class="paginate_button active"><a target="_self" tabindex="0" data-dt-idx="1" aria-controls="example1" href="#">' . $page . '</a></li>';
                }
            }
        }
//        $pageStr     =   str_replace(
//            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
//            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
//        return $pageStr;
        $pageStr = str_replace(
            array('%header%', '%nowPage%', '%upPage%', '%downPage%', '%first%', '%linkPage%', '%end%', '%totalRow%', '%totalPage%'),
            array($this->config['header'], $this->nowPage, $upPage, $downPage, $theFirst, $linkPage, $theEnd, $this->totalRows, $this->totalPages),
            $this->config['theme']);
        return "<div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>{$pageStr}</ul></div>";
    }

}
