<?php

function getArticleCat($id) {

    static $arr = array();

    $articleCatModel = new \Zfuwl\Model\ArticleCatModel();

    $articleCat = $articleCatModel->findArticleCatById($id);

    if ($articleCat['parent_id'] > 0) {
        getArticleCat($articleCat['parent_id']);
    }
    $arr[$id] = $articleCat;
    return $arr;
}
