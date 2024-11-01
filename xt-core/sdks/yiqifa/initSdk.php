<?php
/////////////SDK的入口/////////////////
include "utils/YiqifaUtils.php";
include "utils/YiqifaOpen.php";
// 检测根目录,并定义成常量
define('ROOT',str_replace('/initSdk.php','',str_replace('\\','/',__FILE__)));
function __autoload($classname) {
    require(ROOT . '/request/' . $classname . '.php');
	
}