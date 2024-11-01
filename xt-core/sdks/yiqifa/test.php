<?php

header("Content-type: text/html; charset=GBK");

include("initSdk.php");
//实例化YiqifaOpen类

$c = new YiqifaOpen;

$c->consumerKey = "1331544511075406";

$c->consumerSecret = "1d4abbec4489880adaf9154d948d9be8";

$c->format="json";


//实例化具体API对应的Request类
//$req = new  WebsiteListGetRequest;$req->setFields("web_id,web_name,web_catid,logo_url,web_o_url,commission,total");$req->setWtype(1);$req->setCatid("2");
$req = new  ProductCategoryGetRequest;$req->setFields("catid,cname,parent_id,alias,is_parent,modified_time");$req->setParent_id(101000000);
//$req = new  BrandListGetRequest;$req->setFields("brand_id,brand_name,brand_catid,logopic_url,brand_o_url,total");$req->setCatid("1004");
//$req = new  CommentsGetRequest;$req->setFields("com_id,pid,com_title,content,star,good,bad,com_type,com_url,time,user,user_pic,user_url,modified_time");$req->setPdt_id(42666370, 45505907, 45505913);$req->setWtype("good,normal,less");$req->setWebid(2618);
//$req = new  YiqifaAdListGetRequest;$req->setFields("ad_id,ad_name,ad_catid,ad_cname,logo_url,ad_o_url,adver_name,adver_id,charge_type,audit_mode,ad_type,begin_date,end_date,create_time,modified_time,commission,introduction,confirm_time,total");$req->setCharge_type("cps");$req->setAd_catid("13");$req->setAudit_mode("");$req->setAd_type("web");
//$req = new ProductSearchGetRequest;$req->setFields("pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total");$req->setKeyword('笔记本');$req->setPage_no(1);$req->setPage_size(2);$req->setWebid("");$req->setCatid("");$req->setPrice_range("");$req->setOrderby(1);

//$req = new TuanProductListGetRequest;$req->setFields("tuan_pid,title,web_id,pdt_o_url,pic_url,ori_price,cur_price,begin_time,end_time,bought,tuan_catid,city_id,city_name,discount,modified_time,total");$req->setWeb_id("");$req->setCatid("");$req->setCity_id("110000");$req->setPage_no(2);$req->setPage_size(40);$req->setPrice_range("1,1000");

//执行API请求并打印结果


$resp = $c->execute($req);

//echo "result:";

print_r(mb_convert_encoding( $resp, 'gbk','utf-8'));

//echo "<br>";


?>