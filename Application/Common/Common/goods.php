<?php

/**
 * 商品相册缩略图
 * @param string $sub_img 图片地址
 * @param int $goods_id 商品id
 * @param int $width 图片宽度
 * @param int $height 图片高度
 * @return string
 */
function getSubImages($subImgUrl, $key, $goodsId, $width, $height) {
    //判断缩略图是否存在
    $path = "Public/upload/goods/thumb/$goodsId/";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
        chmod($path, 0777, true);
    }
    $goods_thumb_name = "goods_sub_thumb_{$goodsId}_{$key}_{$width}_{$height}";
    //这个缩略图 已经生成过这个比例的图片就直接返回了
    if (file_exists($path . $goods_thumb_name . '.jpg'))
        return '/' . $path . $goods_thumb_name . '.jpg';
    if (file_exists($path . $goods_thumb_name . '.jpeg'))
        return '/' . $path . $goods_thumb_name . '.jpeg';
    if (file_exists($path . $goods_thumb_name . '.gif'))
        return '/' . $path . $goods_thumb_name . '.gif';
    if (file_exists($path . $goods_thumb_name . '.png'))
        return '/' . $path . $goods_thumb_name . '.png';

    $original_img = '.' . $subImgUrl; //相对路径
    if (!file_exists($original_img))
        return '';

    $image = new \Think\Image();
    $image->open($original_img);

    $goods_thumb_name = $goods_thumb_name . '.' . $image->type();
    // 生成缩略图
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
        chmod($path, 0777, true);
    }
    $image->thumb($width, $height, 2)->save($path . $goods_thumb_name, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
    return '/' . $path . $goods_thumb_name;
}

function getConfigType($name = []) {
    $_config_type = [
        'attr_input_type' => [
            '1' => '手工录入',
            '2' => '单选',
            '3' => '下拉',
            '4' => '多选'
        ],
        'is_index' => [
            '1' => '是',
            '2' => '否',
        ]
    ];
    if (empty($name))
        return $_config_type;
    if (count($name) == 1) {
        return $_config_type[$name[0]];
    } else if (count($name) == 2) {
        return $_config_type[$name[0]][$name[1]];
    }
    echo '';
}

/**
 * 多个数组的笛卡尔积
 *
 * @param unknown_type $data
 */
function combineDika() {
    $data = func_get_args();
    $data = current($data);
    $cnt = count($data);
    $result = array();
    $arr1 = array_shift($data);
    foreach ($arr1 as $key => $item) {
        $result[] = array($item);
    }

    foreach ($data as $key => $item) {
        $result = combineArray($result, $item);
    }
    return $result;
}

/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
 */
function combineArray($arr1, $arr2) {
    $result = array();
    foreach ($arr1 as $item1) {
        foreach ($arr2 as $item2) {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}

/**
 * 获取商品分类
 * @param int $catId 分类id
 * @param array $catArr 分类
 * @return array $catArr 分类
 */
function navigateCat($catId, $catArr = array())
{

    $catInfo = M('goods_cate')->where(array('cat_id' => $catId))->field('name,parent_id')->find();
    if ($catInfo) {
        $catArr[$catId] = $catInfo['name'];

        if ($catInfo['parent_id'] > 0) {
            return navigateCat($catInfo['parent_id'], $catArr);
        }
    }
    return $catArr;
}
