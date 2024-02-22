<?php
/*  引入  */
require_once "header.php";
if (!$isUser) {
    header("location:index.php");
    exit;
}

/*  流程控制  */
$op       = isset($_REQUEST['op']) ? my_filter($_REQUEST['op'], "string") : '';
$goods_sn = isset($_REQUEST['goods_sn']) ? my_filter($_REQUEST['goods_sn'], "int") : 0;
$tweets_sn = isset($_REQUEST['tweets_sn']) ? my_filter($_REQUEST['tweets_sn'], "int") : 0;
$user_sn = isset($_REQUEST['user_sn']) ? my_filter($_REQUEST['user_sn'], "string") : '';
$user_id = isset($_REQUEST['user_id']) ? my_filter($_REQUEST['user_id'], "string") : '';
$goods_title = isset($_REQUEST['goods_title']) ? my_filter($_REQUEST['goods_title'], "string") : '';
switch ($op) {
    case 'tweets_form':
		display_user($user_sn);
        goods_form($goods_sn);
        tweets_form($tweets_sn);
        break;

    case 'insert_tweets':
        $tweets_sn = insert_tweets();
        header("location:index.php?tweets_sn={$tweets_sn}");
        exit;
        break;
    case 'add_to_collects':
        $collects = add_to_collects($goods_sn,$goods_title,$user_sn);
        header("location:user.php?op=collects_list&user_sn={$user_sn}");
        exit;
        break;
	case 'delete_collects':
		delete_collects($goods_sn ,$user_sn);
		header("location:index.php?goods_sn={$goods_sn}");
		exit;
		break;
	case 'insert_posts':
		insert_posts($tweets_sn,$user_id,$posts_content);
		header("location:index.php?goods_sn={$goods_sn}");
		exit;
		break;
		
}

/*  輸出  */
require_once "footer.php";
/*  本檔案使用函數  */
//商品編輯表單
function tweets_form($tweets_sn)
{
    global $mysqli, $smarty;
    if (empty($tweets_sn)) {
        $sql    = "EXPLAIN `tweets`";
        $result = $mysqli->query($sql) or die($mysqli->connect_error);
        while (list($field_name) = $result->fetch_row()) {
            $tweets[$field_name] = '';
        }
    } else {
        $sql          = "SELECT * FROM `tweets` WHERE `tweets_sn`='{$tweets_sn}'";
        $result       = $mysqli->query($sql) or die($mysqli->connect_error);
        $tweets        = $result->fetch_assoc();
        $tweets['pic'] = get_tweets_pic($tweets_sn, 'tweetsthumbs');
    }
    $smarty->assign('tweets', $tweets);
}
//儲存推文
function insert_tweets()
{
    global $mysqli;
    foreach ($_POST as $var_name => $var_val) {
        $$var_name = $mysqli->real_escape_string($var_val);
    }
    $tweets_date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `tweets` (`tweets_title`,`goods_sn`, `tweets_content`, `user_id`, `tweets_counter`, `tweets_date`) VALUES ('{$tweets_title}','{$goods_sn}', '{$tweets_content}', '{$user_id}', '0', '{$tweets_date}')";
    $mysqli->query($sql) or die($mysqli->connect_error);
    $tweets_sn = $mysqli->insert_id;
    save_tweets_pic($tweets_sn);
    return $tweets_sn;
}
//儲存圖片
function save_tweets_pic($tweets_sn = "")
{
    include_once "class/upload/class.upload.php";
    $pic = new Upload($_FILES['tweets_pic'], 'zh_TW');
    if ($pic->uploaded) {
        //大圖
        $pic->file_new_name_body = $tweets_sn;
        $pic->file_overwrite     = true;
        $pic->image_resize       = true;
        $pic->image_x            = 600;
        $pic->image_y            = 400;
        $pic->image_convert      = 'png';
        $pic->image_ratio_crop   = true;
        $pic->Process('uploads/tweets/');
        if (!$pic->processed) {
            return 'error : ' . $pic->error;
        }
        //縮圖
        $pic->file_new_name_body = $tweets_sn;
        $pic->file_overwrite     = true;
        $pic->image_resize       = true;
        $pic->image_x            = 300;
        $pic->image_y            = 200;
        $pic->image_convert      = 'png';
        $pic->image_ratio_crop   = true;
        $pic->Process('uploads/tweetsthumbs/');
        if ($pic->processed) {
            $pic->Clean();
        } else {
            return 'error : ' . $pic->error;
        }
    }
}
//建立目錄
function mk_dir($dir = "")
{
    //若無目錄名稱秀出警告訊息
    if (empty($dir)) {
        die("無目錄名稱");
    }

    //若目錄不存在的話建立目錄
    if (!is_dir($dir)) {
        umask(000);
        //若建立失敗秀出警告訊息
        if (!mkdir($dir, 0777)) {
            die("無法建立目錄");
        }

    }
}

function update_tweets($tweets_sn)
{
    global $mysqli;
    foreach ($_POST as $var_name => $var_val) {
        $$var_name = $mysqli->real_escape_string($var_val);
    }

    $tweets_date = date("Y-m-d H:i:s");

    $sql = "UPDATE `tweets` SET
    `tweets_title`='{$tweets_title}',
    `tweets_content`='{$tweets_content}',
    `user_sn`='{$user_sn}',
    `tweets_date`='{$tweets_date}'
    WHERE `tweets_sn`='{$tweets_sn}'";
    $mysqli->query($sql) or die($mysqli->connect_error);
    save_tweets_pic($tweets_sn);
}

function goods_form($goods_sn)
{
    global $mysqli, $smarty;
    if (empty($goods_sn)) {
        $sql    = "EXPLAIN `goods`";
        $result = $mysqli->query($sql) or die($mysqli->connect_error);
        while (list($field_name) = $result->fetch_row()) {
            $goods[$field_name] = '';
        }
    } else {
        $sql          = "SELECT * FROM `goods` WHERE `goods_sn`='{$goods_sn}'";
        $result       = $mysqli->query($sql) or die($mysqli->connect_error);
        $goods        = $result->fetch_assoc();
        $goods['pic'] = get_goods_pic($goods_sn, 'thumbs');
    }
    $smarty->assign('goods', $goods);
}
function display_user($user_sn)
{
    global $mysqli, $smarty, $isAdmin, $isUser;
    if (empty($user_sn)) {
        $user_sn = $_SESSION['user_sn'];
    }

    if ($isUser) {
        $user_sn = $isAdmin ? $user_sn : $_SESSION['user_sn'];
    } else {
        die('非會員，請勿亂搞。');
    }
    $sql    = "SELECT * FROM `users` WHERE `user_sn`='{$user_sn}'";
    $result = $mysqli->query($sql) or die($mysqli->connect_error);
    $user   = $result->fetch_assoc();
    $smarty->assign('user', $user);

    $all_users = '';
    if ($isAdmin) {
        $sql       = "SELECT * FROM `users`";
        $result    = $mysqli->query($sql) or die($mysqli->connect_error);
        $all_users = $result->fetch_all(MYSQLI_ASSOC);
    }
    $smarty->assign('all_users', $all_users);
    $smarty->assign('now_user_sn', $user_sn);
}
//加入收藏
function add_to_collects($goods_sn ,$goods_title ,$user_sn)
{   
	global $mysqli;
    foreach ($_POST as $var_name => $var_val) {
        $$var_name = $mysqli->real_escape_string($var_val);
    }
    $collects_date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `collects`(`goods_sn`, `goods_title`, `user_sn`, `collects_date`) VALUES ('{$goods_sn}','{$goods_title}', '{$user_sn}', '{$collects_date}')";
    $mysqli->query($sql) or die($mysqli->connect_error);
    $collects_sn = $mysqli->insert_id;
    return $users_sn;
}
function delete_collects($goods_sn ,$user_sn)
{
    global $mysqli;
    $sql = "DELETE FROM `collects` WHERE `goods_sn`='{$goods_sn}' AND `user_sn`='{$user_sn}'";
    $mysqli->query($sql) or die($mysqli->connect_error);
}
function insert_posts($tweets_sn,$user_id)
{   
	global $mysqli;
    foreach ($_POST as $var_name => $var_val) {
        $$var_name = $mysqli->real_escape_string($var_val);
    }
    $collects_date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `posts`(`posts_sn`,`tweets_sn`, `user_id`,`posts_content`, `posts_date`) VALUES ('{$posts_sn}','{$tweets_sn}', '{$user_id}', '{$posts_content}', '{$posts_date}')";
    $mysqli->query($sql) or die($mysqli->connect_error);
    $posts_sn = $mysqli->insert_id;
    return $users_sn;
}
