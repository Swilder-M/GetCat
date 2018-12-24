<?php
error_reporting(0);//忽略错误
session_start(); //开启session
header("Content-type: text/html; charset=utf8");//编码方式
date_default_timezone_set("Asia/Shanghai");//时区设置

$t1=microtime(true);//计时开始


$code = $_SESSION['id'];
$imgfile = dirname(__FILE__) . '/verify/'.$code.'.gif';//验证码保存地址
unlink($imgfile);//删除登录过的验证码
?>

<!DOCTYPE html>
<html lang="zh">

  <head>
    <meta charset="UTF-8">
    <title>选课结果 - 秒速选课,大带宽服务器助力选课成功</title>
    <meta name="Description" content="破晓选课工具是破晓论坛明子开发的一款在线秒速抢课工具，提交你的信息，大带宽服务器在线秒速帮你选课成功"/>
    <meta name="Keywords" content="破晓选课,齐大选课工具,选课工具"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="//cdn.bootcss.com/mdui/0.4.0/css/mdui.min.css" /></head>
    <link rel="stylesheet" href="/layui/css/layui.css"  media="all">
    <script src="../dist/clipboard.min.js"></script>
  <body style="max-width:520px; margin:0px auto;">

<ul class="layui-nav" lay-filter="">
  <li class="layui-nav-item"> <a href="index.php"><img src="/logo.png" class="layui-nav-img"><img src="/yinxiang.png" class="layui-nav-img"></a></li>
  <li class="layui-nav-item layui-this"><a href="index.php">选课工具</a></li>
  <li class="layui-nav-item"><a href="https://poxiaobbs.com">By 明子</a></li>
</ul>


<div style="max-width: 520px;">
    <a href="https://poxiaobbs.com/thread-3247-1-1.html" target="_blank"><img src="/title.png" style="max-width: 100%;max-height: 100%;"></a>
</div>
    <a id="anchor-top"></a>
    <div class="mdui-container doc-container doc-no-cover">
     <br><br>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
  <legend>选课结果</legend>
</fieldset>
<?php

$sid = dowith_sql($_POST['sid']);
$sid = trim($sid);

$psd = dowith_sql($_POST['psd']);
$psd = trim($psd);

$yzm = dowith_sql($_POST['yzm']);
$yzm = trim($yzm);

$jhkc = dowith_sql($_POST['jhkc']);//计划课程
$jhkc = trim($jhkc);

$xrxk = dowith_sql($_POST['xrxk']);//校任选课
$xrxk = trim($xrxk);

setcookie('sid',$sid ,time() + 3600*3);
setcookie('psd',$psd,time() + 3600*3);
setcookie('jhkc',$jhkc,time() + 3600*3);
setcookie('xrxk',$xrxk,time() + 3600*3);

$cookie = dirname(__FILE__) . '/cookies/'.$_SESSION['id'].'.txt'; //cookie路径
$xk_url="http://111.43.36.153/xkAction.do";//选课链接通用
$faid = -1; //方案计划，默认为-1

/*
    登录需要的数据
    http://111.43.36.153/loginAction.do
    zjh:2016021011
    mm:18222
    v_yzm:az6w  验证码
*/

//构造登录数据
$login_post="zjh=".$sid."&mm=".$psd."&v_yzm=".$yzm;
$login_url = "http://111.43.36.153/loginAction.do";


$login_rs = login_post($login_url, $cookie, $login_post);//提交登录信息
$login_rs = mb_convert_encoding($login_rs,'utf8',"gb2312");//转码
//var_dump($login_rs);

//校验登录数据
    $loginnum = strstr($login_rs,'你输入的验证码错误，请您重新输入！');
    if($loginnum !='')
    {
        header('location: index.php?code=1');
        exit;
    }
    $loginnum = strstr($login_rs,'您的密码不正确，请您重新输入！');
    if($loginnum !='')
    {
        setcookie('psd','',time() - 3600);
        header('location: index.php?code=2');
        exit;
    }
    $loginnum = strstr($login_rs,'数据库');
    if($loginnum !='')
    {
        //复杂情况【cookie丢失】
        header('location: index.php?code=3');
        exit;
    }
//校验登录数据结束

//抓取姓名
$kb_url = "http://111.43.36.153/userInfo.jsp";
$kb = kb($kb_url, $cookie);
$kb = mb_convert_encoding($kb,'utf8',"gb2312");//转码

preg_match_all('#<td valign="middle">&nbsp;<b>(.*?)</b>#', $kb, $user);//提取姓名信息
echo '<ul class="layui-timeline">
<li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
    <div class="layui-timeline-content layui-text">
      <h3 class="layui-timeline-title">'.date("h:i:s a").'</h3>
      <blockquote class="layui-elem-quote layui-quote-nm"><i class="layui-icon" style="font-size: 19px; color: #006633;">&#x1005;</i>    <span style="color:#000000;font-size:16px;">'.$user[1][0].'同学，登录选课系统成功！</span></blockquote>
    </div>
  </li>
</ul>';

if(empty($user[1][0]))
    {
        //登录失败
        header('location: index.php?code=3');
        exit;
    }

//抓取姓名成功

//抓取方案号 【最多循环3次】
$fa_url = "http://111.43.36.153/xkAction.do";
$i = 0;
for( $i = 1;$i<=3;$i++){

        $fa = kb($fa_url, $cookie);
        $fa = mb_convert_encoding($fa,'utf8',"gb2312");//转码
        preg_match_all('#<input type="radio" name="fajhh" value=(.*?)>(.*?)</td>#', $fa, $faxx);//提取方案信息
        //var_dump($faxx);
        $faxx[1][0] = str_replace("'","",$faxx[1][0]);
        if($faxx[2][0] != ''){
            $faid = $faxx[1][0];
            echo '<ul class="layui-timeline">
            <li class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
                <div class="layui-timeline-content layui-text">
                  <h3 class="layui-timeline-title">'.date("h:i:s a").'</h3>
                  <blockquote class="layui-elem-quote layui-quote-nm"><i class="layui-icon" style="font-size: 19px; color: #006633;">&#x1005;</i>    <span style="color:#000000;font-size:16px;">已自动选择【'.$faxx[2][0].'】方案，方案号为：'.$faxx[1][0].'</span></blockquote>
                </div>
              </li>
            </ul>';

            /*

                //提交方案课程表
                $fakcb_url = 'http://111.43.36.153/xkAction.do?actionType=-1&fajhh='.$faid;
                $fakcb = kb($fakcb_url, $cookie);
                $fakcb_rs = mb_convert_encoding($fakcb,'utf8',"gb2312");//转码
                //echo '方案课程表输出结果：';
                //var_dump($fakcb_rs);
                //不用管响应
                //
                $fakcb2_url = 'http://111.43.36.153/xkAction.do?actionType=2&pageNumber=-1&oper1=ori';
                $fakcb2 = kb($fakcb2_url, $cookie);

                */
            break;
        }
        else{
                echo '<ul class="layui-timeline">
        <li class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
            <div class="layui-timeline-content layui-text">
              <h3 class="layui-timeline-title">'.date("h:i:s a").'</h3>
              <blockquote class="layui-elem-quote layui-quote-nm"><i class="layui-icon" style="font-size: 19px; color: #FF0000;">&#x1007;</i>    <span style="color:#FF0000;font-size:16px;">未能查询到方案计划，第'.$i.'次重试中...</span></blockquote>
            </div>
          </li>
        </ul>';
        }

}//循环结束

if($i == 4){

    echo '<ul class="layui-timeline">
        <li class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
            <div class="layui-timeline-content layui-text">
              <h3 class="layui-timeline-title">'.date("h:i:s a").'</h3>
              <blockquote class="layui-elem-quote layui-quote-nm"><i class="layui-icon" style="font-size: 19px; color: #FF0000;">&#x1007;</i>    <span style="color:#FF0000;font-size:16px;">未能查询到方案计划，可能是选课未开始，请返回重新填写信息</span></blockquote>
            </div>
          </li>
        </ul>';
}
//抓取方案号 结束


if($jhkc!='' && $faid !=-1)//计划课程不为空且方案号查询到了
{
    //选课信息处理$jhkc  计划课程
    //提交地址：http://111.43.36.153/xkAction.do
    //数据 kcId:10315010_01  课程ID  格式为 课程号_序号【多选】
    // kcId=10315010_01&kcId=10315010_01&preActionType=1&actionType=9
    $jhkc_arry = strsToArray($jhkc);
    $jhkc_num = count($jhkc_arry);
    $jhkc_post= "";
    $jhxkkk_rsss = '';
    /*foreach ($jhkc_arry as $value) {
      $jhkc_post = $jhkc_post.$value;
    }*/


    $succ_ming = jh_xk_befo($cookie,$faid);
    $succ_ming = mb_convert_encoding($succ_ming,'utf8',"gb2312");//转码
    //var_dump($succ_ming);

    for($i=0;$i<$jhkc_num;$i++){


    $ghfsaaa = strstr($succ_ming,$jhkc_arry[$i]);
        if($ghfsaaa  =='') // 未找到
        {
            $jhxkkk_rsss = $jhxkkk_rsss . '<br>未找到课程 '.$jhkc_arry[$i].',可能是该课已满';
        }else
        {
            $jhkc_post=$jhkc_post."&kcId=".$jhkc_arry[$i];
        }



    }

    $jhkc_post=substr($jhkc_post, 1 );
    $jhkc_post=$jhkc_post."&preActionType=1&actionType=9";
    //echo "<br>计划课程数据为：".$jhkc_post;


    $jhkc_rs=xk_post($xk_url, $cookie, $jhkc_post);//计划课程
    $jhkc_rs = mb_convert_encoding($jhkc_rs,'utf8',"gb2312");//转码
    //var_dump($jhkc_rs);

    $time_num=0;//记录非选课阶段数量
    preg_match_all('#<font color="\#990000">(.*?)</font>#', $jhkc_rs, $jhkc_xx);//抓取结果

    $succ_num = strstr($jhkc_rs,'成功');
    if($succ_num !='')
    {
        xk_succ($sid,$user[1][0]);
    }

    //echo "查找结果为：";
    //var_dump($xrxk_xx);
    //echo "<br><pre>";print_r($jhkc_xx);echo "<pre><br>";

    $jhkc_xxnum = count($jhkc_xx[1]);
    echo '<ul class="layui-timeline">
<li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
    <div class="layui-timeline-content layui-text">
      <h3 class="layui-timeline-title">'.date("h:i:s a").'</h3>
      <blockquote class="layui-elem-quote layui-quote-nm"><i class="layui-icon" style="font-size: 19px; color: #006633;">&#x1005;</i>    <span style="color:#000000;font-size:16px;">计划课程结果：';
      echo $jhxkkk_rsss;

    for($xxnum = 0;$xxnum < $jhkc_xxnum ; $xxnum++){
      //var_dump($jhkc_xx[$xxnum]);
        echo '<br>'.$jhkc_xx[1][$xxnum];
    }
    echo '</span></blockquote>
    </div>
  </li>
</ul>';

}//计划完成




if($xrxk!='' && $faid !=-1)//校任选课不为空且方案号查询到了
{
    //选课信息处理$xrxk  校任选课
    //提交地址：http://111.43.36.153/xkAction.do
    //数据 kcId:10315010_01  课程ID  格式为 课程号_序号【多选】
    // kcId=10315010_01&kcId=10315010_01&preActionType=3&actionType=9
    $xrxk_arry = strsToArray($xrxk);
    $xrxk_num = count($xrxk_arry);
    $xrxk_post= "";

    $xrxkkk_rsss = '';

    for($i=0;$i<$xrxk_num;$i++){

          $rxrxkcid = substr($xrxk_arry[$i] , 0 , 8);

          $succ_mings = rx_xk_befo_new($cookie,$rxrxkcid);
          $succ_mings = mb_convert_encoding($succ_mings,'utf8',"gb2312");//转码
          //var_dump($succ_mings);


          $ghfsaaa = strstr($succ_mings,'共0页');
        if($ghfsaaa  !='')
        {
            $xrxkkk_rsss = $xrxkkk_rsss . '<br>未找到课程 '.$rxrxkcid.',可能是该课已满';
            continue;
        }

          $xrxk_post= "kcId=".$xrxk_arry[$i]."&preActionType=3&actionType=9";

          $xrxk_rs=xk_post($xk_url, $cookie, $xrxk_post);

        $xrxk_rs = mb_convert_encoding($xrxk_rs,'utf8',"gb2312");//转码

        $time_num=0;//记录非选课阶段数量
        preg_match_all('#<strong><font color="\#990000">(.*?)</font></strong>#', $xrxk_rs, $xrxk_xx);//抓取结果
        //echo "查找结果为：";
        //var_dump($xrxk_xx);
        //echo "<br><pre>";print_r($xrxk_xx);echo "<pre><br>";

        $xrxk_xxnum = count($xrxk_xx[1])/2;

        if ($xrxk_xxnum == 0) {
            $xrxkkk_rsss = $xrxkkk_rsss . "<br>系统返回状态为空，可能是课程号有误";
          }
        for($xxnum = 0;$xxnum < $xrxk_xxnum ; $xxnum++){
            $xrxkkk_rsss = $xrxkkk_rsss . '<br>'.$xrxk_xx[1][$xxnum];
        }



    } // 循环结束

    $succ_num_s = strstr($xrxkkk_rsss,'成功');
    if($succ_num_s !='')
    {
        xk_succ($sid,$user[1][0]);
    }

        echo '<ul class="layui-timeline">
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
        <div class="layui-timeline-content layui-text">
          <h3 class="layui-timeline-title">'.date("h:i:s a").'</h3>
          <blockquote class="layui-elem-quote layui-quote-nm"><i class="layui-icon" style="font-size: 19px; color: #006633;">&#x1005;</i>    <span style="color:#000000;font-size:16px;">校任选课结果：';

          echo $xrxkkk_rsss;
        echo '</span></blockquote>
        </div>
      </li>
    </ul>';



}
//校任选完成

//提交完成
echo '<ul class="layui-timeline">
<li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">选课状态以下面课表为准【不含已置入课程】：</div>
    </div>
  </li>
</ul>';

//抓课表
$kb_url = "http://111.43.36.153/xkAction.do?actionType=6";
$kb = kb($kb_url, $cookie);
$kb = mb_convert_encoding($kb,'utf8',"gb2312");//转码

$seach = $kb;
//print_r($seach);
preg_match_all("/<html(.*?)<\/html>/s",$seach,$trs);
//匹配出html中的部分（页面不规范，考试安排下的部分有个html标签）
$str= str_replace(array(" ","\r\n","\t","\n","\r"), "", $trs[0][0]);    //去掉空格换行避免影响匹配
preg_match_all("#this.className='evenfocus'(.*?)<\/td><\/tr>#",$str,$str2);//old <trclass
preg_match_all("#<tdrowspan=(.*?)>(.*?)<\/td>#",$str2[0][1],$str3);
//var_dump($str3);
$arr_num = count($str2[0]);
 echo '<table class="layui-table">
  <colgroup>
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
    <tr>
      <th>课程号</th>
      <th>序号</th>
      <th>课程名称</th>
      <th>教师</th>
      <th>课程状态</th>
      <th>学分</th>
    </tr>
  </thead>
  <tbody>';

  if ($arr_num == 0) {

      echo '<tr>';
      echo '<td>'.$kb_kch.'</td>'. '<td>'.$kb_kxh.'</td>'.'<td>'.$kb_kmz.'</td>'.'<td>'.$kb_kls.'</td>'.'<td>'.$kb_kzt.'</td>'.'<td>'.$kb_kxf.'</td>';
      echo ' </tr>';
  }

for($i=0;$i<$arr_num;$i++)
{
    preg_match_all("#<tdrowspan=(.*?)>(.*?)<\/td>#",$str2[0][$i],$str4);
    //var_dump($str4);
    $xiangmu_num = count($str4[0]);
   // echo "<br><br>项目数为".$xiangmu_num."<br>";

        $kb_kch='无';//课程号
        $kb_kmz='无';//课名字
        $kb_kxh='无';//课序号
        $kb_kxf='无';//课学分
        $kb_kls='无';//课老师
        $kb_kzt='无';//课程状态

    if($xiangmu_num > 0)
    {
        $kb_kch=tihuan($str4[0][1]);//课程号
        $kb_kmz=tihuan($str4[0][2]);//课名字
        $kb_kxh=tihuan($str4[0][3]);//课序号
        $kb_kxf=tihuan($str4[0][4]);//课学分
        $kb_kls=tihuan($str4[0][7]);//课老师
        $kb_kzt=tihuan($str4[0][10]);//课程状态
        if($kb_kzt != '置入')
        {

            echo '<tr>';



            echo '<td>'.$kb_kch.'</td>'. '<td>'.$kb_kxh.'</td>'.'<td>'.$kb_kmz.'</td>'.'<td>'.$kb_kls.'</td>'.'<td>'.$kb_kzt.'</td>'.'<td>'.$kb_kxf.'</td>';
            echo ' </tr>';
        }

    }
}
echo '  </tbody>
</table>';

function dowith_sql($str)
{
   $str = str_replace("execute","",$str);
   $str = str_replace("update","",$str);
   $str = str_replace("count","",$str);
   $str = str_replace("truncate","",$str);
   $str = str_replace("declare","",$str);
   $str = str_replace("select","",$str);
   $str = str_replace("create","",$str);
   $str = str_replace("delete","",$str);
   $str = str_replace("insert","",$str);
   $str = str_replace("'","",$str);
   $str = str_replace('"',"",$str);
   $str = str_replace("=","",$str);
   $str = str_replace("%20","",$str);
   $str = str_replace(";","",$str);
   $str = str_replace("/","",$str);
   $str = str_replace("\\","",$str);
   //echo $str;
   return $str;
}

function tihuan($str) {
    $str1 = str_replace("&nbsp;","",$str);
    $str2 = str_replace("</td>","",$str1);
    $str3 = str_replace('<tdrowspan="3">',"",$str2);
    $str4 = str_replace('<tdrowspan="2">',"",$str3);
    $str5 = str_replace('<tdrowspan="1">',"",$str4);
    return $str5;
}


//函数区

function login_post($url, $cookie, $post) {//模拟登录
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie);//同时发送Cookie
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);//数组形式http_build_query用处理下再提交
    $rs = curl_exec($curl);
    curl_close($curl);
    return $rs;
}
function kb($url, $cookie) {
//抓取课表 【个人信息】
    $ch = curl_init() ;
    curl_setopt($ch, CURLOPT_URL,$url) ;
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);
    $ss= curl_exec($ch);
    curl_close($ch);
    return $ss;
}
function strsToArray($strs) {//去空格填入数组
    $result = array();
    $array = array();
    $strs = str_replace('，', ',', $strs);
    $strs = str_replace("n", ',', $strs);
    $strs = str_replace("rn", ',', $strs);
    $strs = str_replace(' ', ',', $strs);
    $array = explode(',', $strs);
    foreach ($array as $key => $value) {
        if ('' != ($value = trim($value))) {
            $result[] = $value;
        }
    }
    return $result;
}

function xk_post($url, $cookie, $post) {//提交选课

  $header = array(
      "Host: 111.43.36.153",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
    "Accept-Encoding: gzip, deflate",
    "Referer: http://111.43.36.153/xkAction.do?actionType=1",
    "Content-Type: application/x-www-form-urlencoded",
    "Content-Length: 45",
    "Cookie: JSESSIONID=fab-1bHNlghUf6sKs3Dqw",
    "Connection: keep-alive",
    "Upgrade-Insecure-Requests: 1"
    );
  $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    //curl_setopt($curl, CURLOPT_TIMEOUT,300);   //只需要设置一个秒的数量就可以  5分钟
    curl_setopt($curl, CURLOPT_HEADER, $header);
   // curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
    curl_setopt($curl, CURLOPT_REFERER, 'http://111.43.36.153/xkAction.do?actionType=3&pageNumber=-1');   // 伪造来源网址
    curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie);//同时发送Cookie
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);//数组形式http_build_query用处理下再提交
    $rs = curl_exec($curl);
    curl_close($curl);
    return $rs;
}

function xk_succ($sid,$name) {//成功更新数据库
  /*
        $time = time();
        include 'config.php';
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
        if (mysqli_errno($conn)) {
            mysqli_error($conn);
            exit;
        }
        mysqli_set_charset($conn, 'utf8');
        $sql = "insert into succsid(times,name,sid) values('" . $time . "','" . $name . "','" . $sid . "')";
        $result = mysqli_query($conn, $sql);
        mysqli_close($conn);
        */
//更新数据结束

}



function jh_xk_befo($cookie,$faid) {
//在选课之前 计划课程
  $url = 'http://111.43.36.153/xkAction.do?actionType=1';
  $reurl = 'http://111.43.36.153/xkAction.do?actionType=-1&fajhh='.$faid;
    $ch = curl_init() ;
    curl_setopt($ch, CURLOPT_URL,$url) ;
    curl_setopt($ch, CURLOPT_REFERER, $reurl);   // 伪造来源网址
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);
    $ss= curl_exec($ch);
    curl_close($ch);
    return $ss;
}


function rx_xk_befo($cookie,$faid) {
//在选课之前 任选课
  $url = 'http://111.43.36.153/xkAction.do?actionType=3&pageNumber=-1';
  $reurl = 'http://111.43.36.153/xkAction.do?actionType=-1&fajhh='.$faid;
    $ch = curl_init() ;
    curl_setopt($ch, CURLOPT_URL,$url) ;
    curl_setopt($ch, CURLOPT_REFERER, $reurl);   // 伪造来源网址
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);
    $ss= curl_exec($ch);
    curl_close($ch);
    return $ss;
}

function rx_xk_befo_new($cookie,$kcid) {
//在选课之前 任选课 新版
  $url = 'http://111.43.36.153/xkAction.do?kch='.$kcid.'&kcm=&actionType=3&pageNumber=-1';
  $reurl = 'http://111.43.36.153/xkAction.do';
    $ch = curl_init() ;
    curl_setopt($ch, CURLOPT_URL,$url) ;
    curl_setopt($ch, CURLOPT_REFERER, $reurl);   // 伪造来源网址
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);
    $ss= curl_exec($ch);
    curl_close($ch);
    return $ss;
}



$cookiefile = dirname(__FILE__) . '/cookies/'.$_SESSION['id'].'.txt';
unlink($cookiefile);

?>

<br><br><fieldset class="layui-elem-field layui-field-title">
  <legend>我们的小伙伴</legend>
  <div class="layui-field-box">
    <img style="text-align:center;max-width: 100%;max-height: 100%;" src="https://xk.poxiaobbs.com/yxqd.png" alt="" />
    <div style="text-align:center;"><a href="https://poxiaobbs.com">  <h3><i class="layui-icon" style="font-size: 20px; color: #1E9FFF;">&#xe609;</i>&nbsp;破晓论坛 - 干净专注的技术交流资源分享平台 </h3></a></div>
  </div>
</fieldset>
<hr><br><br>
<div style="text-align:center;">
    &nbsp;Powered&nbsp; by&nbsp; 明子&nbsp;&nbsp;<span style="color:#222222;font-family:Consolas, &quot;background-color:#FFFFFF;">&copy;2018&nbsp;</span><span style="color:#222222;font-family:&quot;">Comsenz&nbsp; Inc.&nbsp; </span>
</div>
 <?php $t2=microtime(true); ///计时结束
$elapsed_time=round(($t2-$t1),4);
echo '<br><div style="text-align:center;">Execution time '.$elapsed_time.' s </div>';?>


<script src="/layui/layui.js" charset="utf-8"></script>

  </body>

</html>
