<?php
session_start();
date_default_timezone_set('Asia/Taipei');
require_once 'sdk/src/Facebook/autoload.php';

//設定FB App參數
$fb = new Facebook\Facebook([
  'app_id' => '1160450904066047',
  'app_secret' => '{app+secret}',
  'default_graph_version' => 'v2.8',
  ]);

$helper = $fb->getRedirectLoginHelper();

//取得AccessToken
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
//檢查AccessToken
if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}
//取得response
/*
  $request = $fb->request('GET', '/1433663288_10209146876175702/comments?access_token='.$accessToken);
  try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->getClient()->sendRequest($request);
  //$response = $fb->get('/1433663288_10209146876175702/likes', "$accessToken");
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}*/
$response = json_decode(file_get_contents('https://graph.facebook.com/v2.8/1433663288_10209146876175702/comments?limit=100&access_token='.$accessToken));
//echo $response->data[0]->message;
function GMT_convert($time){
	$timesp = strtotime($time);
	$GMT =  date("Y-m-d H:i:s",$timesp);
	return $GMT;
}

function time_is_valid($time){
	$timesp = strtotime($time);
	if($timesp<1490630400)
		return true;
	else
		return false;
}

function content_is_valid($content){
	if (preg_match("/(羿方|好帥|星巴克|遮羞布)/", $content))
		return true;
	else
		return false;
}
function user_no_exists($userid, $array){
	if (!in_array($userid, $array))
		return true;
	else
		return false;
}
?>
<html>
<header>
<!-- 最新編譯和最佳化的 CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<!-- 選擇性佈景主題 -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<!-- 最新編譯和最佳化的 JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<style>
.bs-callout {
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #eee;
    border-left-width: 5px;
    border-radius: 3px;
}
.bs-callout h4 {
    margin-top: 0;
    margin-bottom: 5px;
}
.bs-callout p:last-child {
    margin-bottom: 0;
}
.bs-callout code {
    border-radius: 3px;
}
.bs-callout+.bs-callout {
    margin-top: -5px;
}
.bs-callout-default {
    border-left-color: #777;
}
.bs-callout-default h4 {
    color: #777;
}
.bs-callout-primary {
    border-left-color: #428bca;
}
.bs-callout-primary h4 {
    color: #428bca;
}
.bs-callout-success {
    border-left-color: #5cb85c;
}
.bs-callout-success h4 {
    color: #5cb85c;
}
.bs-callout-danger {
    border-left-color: #d9534f;
}
.bs-callout-danger h4 {
    color: #d9534f;
}
.bs-callout-warning {
    border-left-color: #f0ad4e;
}
.bs-callout-warning h4 {
    color: #f0ad4e;
}
.bs-callout-info {
    border-left-color: #5bc0de;
}
.bs-callout-info h4 {
    color: #5bc0de;
}
</style>
</header>
<body>
<div class="container">
<div class="bs-callout bs-callout-info">
	<h4>總覽</h4>
	<h6>執行時間：<?php echo date("Y-m-d H:i:s");?></h6>
	<h6>檢索目標：https://graph.facebook.com/v2.8/1433663288_10209146876175702/comments?limit=100&access_token={Access_Token}</h6>
	<h6>留言總數：<?php echo count($response->data);?></h6>
	<h6>條件1：使用者不重複(user_no_exists_in_arr)</h6>
	<h6>條件2：內容比對(preg_match:/(羿方|好帥|星巴克|遮羞布)/)</h6>
	<h6>條件3：時間戳記驗證(UNIX_timestamp < 1490630400)</h6>
</div>
<table class="table table-striped">
  <tr>
	<td style="width:5%">項次</td>
	<td style="width:15%">貼文者</td>
	<td style="width:40%">內容</td>
	<td style="width:20%">時間</td>
	<td style="width:5%">抽獎</td>
  </tr>
  <?php
	$list[] = array();
	//$arr_uid[] =array();
	for($i=0;$i<count($response->data);$i++)
	{
		
		echo "<tr>";
		echo "<td>".($i+1)."</td>";
		echo "<td>".$response->data[$i]->from->name." \n(".$response->data[$i]->from->id.") ";
		echo "</td>";
		$message = $response->data[$i]->message;
		echo "<td>".$message." ";
		if(content_is_valid($message))
			echo "<span class='label label-success'>有效</span>";
		else
			echo "<span class='label label-danger'>無效</span>";
		echo "</td>";
		
		$time = GMT_convert($response->data[$i]->created_time);
		echo "<td>".$time." ";
		if(time_is_valid($response->data[$i]->created_time))
			echo "<span class='label label-success'>有效</span>";
		else
			echo "<span class='label label-danger'>無效</span>";
		echo "</td>";
		
		$userid = $response->data[$i]->from->id;
		echo "<td>";
		if(user_no_exists($userid, $list) && content_is_valid($message) && time_is_valid($response->data[$i]->created_time))
		{
			echo "<span class='label label-success'>Y</span>";
			array_push($list, "$userid");
			//array_push($arr_uid, array($userid=>$response->data[$i]->from->name));
		}
		echo "</td>";
		echo "</tr>";
	}

  ?>
</table>
	<div class="bs-callout bs-callout-success">
		<h4>篩選與設定</h4>
		<h6>符合條件的留言數：<?php echo count($list);?></h6>
		<h6>打亂排序：是 <?php shuffle($list);?></h6>
		<h6>應抽數量：1</h6>
		<h4>抽獎結果</h4>
		<?php $lottery = $list[rand(0,count($list)-1)];?>
		<h5>中獎者：<?php echo $lottery;?></h6>
	</div>
</div>
</body>
</html>
