<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

if ($user != $admin) {
    exit("<script>$.alert({title: '警告',content: '你无权访问此页面。',type: 'orange',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){history.go(-1);}}}});</script>");
} 

?>

<?php 

// 清除EPG缓存
if (isset($_POST['clearcache'])) {
    $num = 0;
    $files = glob("../../cache/epgs/*");
    foreach ($files as $file) {
        unlink($file);
        $num++;
    } 
    exit("<script>$.alert({title: '成功',content: '已清除" . $num . "个EPG缓存文件！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

// 清除频道绑定
if (isset($_POST['clearbind'])) {
    if (isset($_POST['id'])) {
        $where = "where id=" . $_POST['id'];
    } 
    $db->mSet("luo2888_epg", "content=NULL", $where);
    exit("<script>$.alert({title: '成功',content: '已清除绑定频道！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

// 绑定频道
if (isset($_POST['bindchannel'])) {
    $result = $db->mQuery("SELECT distinct name FROM luo2888_channels");
    if (!mysqli_num_rows($result)) {
        mysqli_free_result($result);
        exit("<script>$.alert({title: '错误',content: '对不起，暂时没有频道信息，无法匹配！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    }
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $clist[] = $row;
    } 
    unset($row);
    mysqli_free_result($result);

    if (isset($_POST['id'])) {
        if ($_POST["remarks"] == '') {
            exit("<script>$.alert({title: '错误',content: '对不起，备注信息不完整，无法匹配！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
        } 
        $remarks = explode(",", $_POST['remarks']);
        foreach ($clist as $cname => $channel) {
            foreach ($remarks as $remarkstr) {
                if (strstr($channel['name'], $remarkstr) !== false) {
                    $list[$cname] = $channel['name'];
                } 
            } 
        } 
        $content = implode(",", array_unique($list));
        if (empty($content)) {
            exit("<script>$.alert({title: '错误',content: '对不起，没有索引到频道列表！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
        } 
        $db->mSet("luo2888_epg", "content='$content'", "where id=" . $_POST['id']);
        exit("<script>$.alert({title: '成功',content: 'EPG信息已匹配！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    } 

    $result = $db->mQuery("SELECT id,remarks,content from luo2888_epg where remarks != ''");
    if (!mysqli_num_rows($result)) {
        mysqli_free_result($result);
        exit("<script>$.alert({title: '错误',content: '对不起，暂时没有EPG信息，无法匹配！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    } while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        foreach ($clist as $cname => $channel) {
            if (strstr($channel['name'], $row['remarks']) !== false) {
                $list[$cname] = $channel['name'];
                $content = implode(",", array_unique($list));
                $db->mSet("luo2888_epg", "content='$content'", "where id=" . $row['id']);
            } 
        } 
        unset($list);
    } 
    unset($row);
    mysqli_free_result($result);
    exit("<script>$.alert({title: '成功',content: 'EPG信息已匹配！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

// 上线操作
if (isset($_POST['upline'])) {
    $id = !empty($_POST["id"])?$_POST["id"]:exit("<script>$.alert({title: '错误',content: '参数不能为空！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    $db->mSet("luo2888_epg", "status=1", "where id=$id");
    exit("<script>$.alert({title: '成功',content: 'EPG编号 " . $id . " 已上线！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

// 下线操作
if (isset($_POST['downline'])) {
    $id = !empty($_POST["id"])?$_POST["id"]:exit("<script>$.alert({title: '错误',content: '参数不能为空！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    $db->mSet("luo2888_epg", "status=0", "where id=$id");
    exit("<script>$.alert({title: '成功',content: 'EPG编号 " . $id . " 已下线！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

// 删除操作
if (isset($_POST['delchannel'])) {
    $id = !empty($_POST["id"])?$_POST["id"]:exit("<script>$.alert({title: '错误',content: '参数不能为空！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    $db->mDel("luo2888_epg", "where id=$id");
    exit("<script>$.alert({title: '成功',content: 'EPG编号 " . $id . " 已删除！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

// 新增EPG数据
if (isset($_POST['addchannel'])) {
    $epg = !empty($_POST["epg"])?$_POST["epg"]:exit("<script>$.alert({title: '错误',content: '请选择EPG来源！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    $name = !empty($_POST["name"])?$_POST["name"]:exit("<script>$.alert({title: '错误',content: '请填写EPG名称！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    $remarks = $_POST["remarks"];
    $epg_name = $epg . '-' . $name;
    $result = $db->mQuery("select * from luo2888_epg where name=" . "'" . $epg_name . "'"); 
    // EPG是否已经同名或存在
    if (mysqli_num_rows($result)) {
        mysqli_free_result($result);
        exit("<script>$.alert({title: '错误',content: 'EPG名为 " . $epg_name . " 已存在，请不要重复新增！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
    } 
    // 新加EPG数据
    $db->mInt("luo2888_epg", "name,remarks", "'" . $epg_name . "','" . $remarks . "'");
    exit("<script>$.alert({title: '成功',content: 'EPG " . $epg_name . " 已增加！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){self.location=document.referrer;}}}});</script>");
} 

//修改EPG数据
if (isset($_POST['editchannel'])) {
    $id = !empty($_POST["id"])?$_POST["id"]:exit("<script>$.alert({title: '错误',content: '参数为空！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){history.go(-2);}}}});</script>");
    $epg = !empty($_POST["epg"])?$_POST["epg"]:exit("<script>$.alert({title: '错误',content: '请选择EPG来源！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){history.go(-2);}}}});</script>");
    $name = !empty($_POST["name"])?$_POST["name"]:exit("<script>$.alert({title: '错误',content: '请填写EPG名称！',type: 'red',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){history.go(-2);}}}});</script>");
    $epg_name = $epg . '-' . $name;
    $ids = implode(",", array_unique($_POST['ids']));
    $remarks = $_POST["remarks"];
    $db->mSet("luo2888_epg", "name='" . $epg_name . "',content='" . $ids . "',remarks='" . $remarks . "'", "where id=" . $id);
    exit("<script>$.alert({title: '成功',content: 'EPG " . $epg_name . " 修改成功！',type: 'green',buttons: {confirm: {text: '确定',btnClass: 'btn-primary',action: function(){history.go(-2);}}}});</script>");
} 

// 搜索关键字
if (isset($_GET['keywords'])) {
    $keywords = $_GET['keywords'];
    $searchparam = "where name like '%$keywords%' or remarks like '%$keywords%' or content like '%$keywords%'";
} 

// 设置每页显示数量
if (isset($_POST['recCounts'])) {
    $recCounts = $_POST['recCounts'];
    $db->mSet("luo2888_config", "value=$recCounts", "where name='admin_showcounts'");
} 

// 获取每页显示数量
$recCounts = $db->mGet("luo2888_config", "value", "where name='admin_showcounts'");

// 获取当前页
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
} 

// 获取EPG总数并根据每页显示数量计算页数
if ($row = $db->mGetRow("luo2888_epg", "count(*)")) {
    $pageCount = ceil($row[0] / $recCounts);
} else {
    $pageCount = 1;
} 

// 处理跳转逻辑
if (isset($_POST['jumpto'])) {
    $p = $_POST['jumpto'];
    if (($p <= $pageCount) && ($p > 0)) {
        echo "<script>location.href='epgadmin.php' + '?page=$p';</script>";
    } 
} 

?>