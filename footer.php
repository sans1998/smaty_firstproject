<?php
if (isset($_REQUEST['msg'])) {
    $smarty->assign('msg', $_REQUEST['msg']);
}
$smarty->assign('page_name', _PAGE_NAME);
$smarty->assign('op', $op);
$smarty->display('index.html');
