<?php
/*  引入  */
require_once "header.php";

/*  流程控制  */
$op          = isset($_REQUEST['op']) ? my_filter($_REQUEST['op'], "string") : '';
$goods_sn    = isset($_REQUEST['goods_sn']) ? my_filter($_REQUEST['goods_sn'], "int") : 0;
$goods_title = isset($_REQUEST['goods_title']) ? my_filter($_REQUEST['goods_title'], "string") : '';
$keyword     = isset($_REQUEST['keyword']) ? my_filter($_REQUEST['keyword'], "string") : '';
$tweets_sn = isset($_REQUEST['tweets_sn']) ? my_filter($_REQUEST['tweets_sn'], "int") : 0;
$co          = isset($_REQUEST['co']) ? my_filter($_REQUEST['co'], "string") : '';
$so          = isset($_REQUEST['so']) ? my_filter($_REQUEST['so'], "string") : '';
$te          = isset($_REQUEST['te']) ? my_filter($_REQUEST['te'], "string") : '';
$cp          = isset($_REQUEST['cp']) ? my_filter($_REQUEST['cp'], "string") : '';



switch ($op) {
    /*case 'add_to_collects':
        add_to_collects($goods_sn, $goods_title);
        header("location:index.php");
        exit;
        break;*/
    default:
        if ($goods_sn) {
            $op = 'goods_display';
            display_goods($goods_sn);
			list_tweets($goods_sn);
			list_posts($tweets['tweets_sn']);
			collects_users($goods_sn);
        }elseif($keyword){
			$op = 'goods_list';
			list_goods($keyword);
        }elseif($keyword){
			$op = 'goods_list';
			list_goods($keyword);
        }elseif($co){
			$op = 'list';
			list_goodss($co);
		}elseif($tweets_sn){
			$op='posts_list';
			list_posts($tweets_sn);
		}
		elseif($so){
			$op = 'listt';
			list_goodsss($so);
		}
		elseif($te){
			$op = 'te';
			hot_goods();
		}
		else{
			list_tweets($goods_sn);		
        }
        break;
}
/*  輸出  */
require_once "footer.php";

/*  本檔案使用函數  */

//熱門景點列表
function list_goodsss($so)
{
    global $mysqli, $smarty;
    $sql     = "SELECT * FROM `goods` where `sort`='{$so}' ORDER BY `goods_date` desc";
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
    while ($goods = $result->fetch_assoc()) {
        $all_goods[$i]        = $goods;
        $all_goods[$i]['pic'] = get_goods_pic($goods['goods_sn'], 'thumbs');
        $i++;
    }

    $smarty->assign('all_goods', $all_goods);
}

function list_goodss($co)
{
    global $mysqli, $smarty;
	//include_once "class/PageBar.php";
    $sql     = "SELECT * FROM `goods` where `County`='{$co}' ORDER BY `goods_date` desc";
	//$PageBar = getPageBar($sql, 12, 4);
    //$bar     = $PageBar['bar'];
    //$sql     = $PageBar['sql'];
    //$total   = $PageBar['total'];
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
    while ($goods = $result->fetch_assoc()) {
        $all_goods[$i]        = $goods;
        $all_goods[$i]['pic'] = get_goods_pic($goods['goods_sn'], 'thumbs');
        $i++;
    }
	
    $smarty->assign('all_goods', $all_goods);
	//$smarty->assign('total', $total);
    //$smarty->assign('bar', $bar);
}

function hot_goods()
{
    global $mysqli, $smarty;
    $sql     = "SELECT * FROM `goods` ORDER BY `goods_counter` desc LIMIT 9" ;
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
    while ($goods = $result->fetch_assoc()) {
        $all_goods[$i]        = $goods;
        $all_goods[$i]['pic'] = get_goods_pic($goods['goods_sn'], 'thumbs');
        $i++;
    }

    $smarty->assign('all_goods', $all_goods);
}
//景點列表
function list_goods($keyword = "")
{
    global $mysqli, $smarty;
    //include_once "class/PageBar.php";
    $where   = !empty($keyword) ? "where `goods_title` like '%{$keyword}%'" : "";
    $sql     = "SELECT * FROM `goods` $where ORDER BY `goods_date` desc";
    //$PageBar = getPageBar($sql, 12, 4);
    //$bar     = $PageBar['bar'];
    //$sql     = $PageBar['sql'];
    //$total   = $PageBar['total'];
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
    while ($goods = $result->fetch_assoc()) {
        $all_goods[$i]        = $goods;
        $all_goods[$i]['pic'] = get_goods_pic($goods['goods_sn'], 'thumbs');
        $i++;
    }

    $smarty->assign('all_goods', $all_goods);
    //$smarty->assign('total', $total);
    //$smarty->assign('bar', $bar);
}
//觀看某一景點
function display_goods($goods_sn = '')
{
    global $mysqli, $smarty;
    add_goods_counter($goods_sn);
    $sql          = "SELECT * FROM `goods` WHERE `goods_sn`='{$goods_sn}'";
    $result       = $mysqli->query($sql) or die($mysqli->connect_error);
    $goods        = $result->fetch_assoc();
    $goods['pic'] = get_goods_pic($goods_sn);
    $smarty->assign('goods', $goods);
}

//增加景點計數器
function add_goods_counter($goods_sn)
{
    global $mysqli;

    $sql = "UPDATE `goods` SET `goods_counter`=`goods_counter`+1 WHERE `goods_sn`='{$goods_sn}'";
    $mysqli->query($sql) or die($mysqli->connect_error);

}


//推文列表
function list_tweets($goods_sn='')
{
    global $mysqli, $smarty;
	$where   = !empty($goods_sn) ? "where `goods_sn`='{$goods_sn}'" : "";
    $sql     = "SELECT * FROM `tweets` $where ORDER BY `tweets_date` desc";
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
    while ($tweets = $result->fetch_assoc()) {
        $all_tweets[$i]        = $tweets;
		list_posts($tweets['tweets_sn']);
        $all_tweets[$i]['pic'] = get_tweets_pic($tweets['tweets_sn'], 'tweetsthumbs');
        $i++;
    }
    $smarty->assign('all_tweets', $all_tweets);
}

//增加推文計數器
function add_tweets_counter($tweets_sn)
{
    global $mysqli;

    $sql = "UPDATE `tweets` SET `tweets_counter`=`tweets_counter`+1 WHERE `tweets_sn`='{$tweets_sn}'";
    $mysqli->query($sql) or die($mysqli->connect_error);

}

function collects_users($goods_sn='')
{
    global $mysqli, $smarty;
    $sql     = "SELECT * FROM `collects` WHERE `goods_sn`='{$goods_sn}'" ;
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
	$clt="false";
    while ($collects = $result->fetch_assoc()) {
        $all_collects[$i]        = $collects;
		if($_SESSION['user_sn']==$all_collects[$i]['user_sn']){
			$clt="true";
		}
        $i++;
    }
    $smarty->assign('clt', $clt);
}
function list_posts($tweets_sn='')
{
    global $mysqli, $smarty;
	$where   = !empty($tweets_sn) ? "where `tweets_sn`='{$tweets_sn}'" : "";
    $sql     = "SELECT * FROM `posts` $where ORDER BY `posts_date` desc";
    $result  = $mysqli->query($sql) or die($mysqli->connect_error);

    $i = 0;
    while ($posts = $result->fetch_assoc()) {
        $all_posts[$i]   = $posts;
        $i++;
    }
    $smarty->assign('all_posts', $all_posts);
}