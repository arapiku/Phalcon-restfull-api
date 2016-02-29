<?php
require_once './PhalApiClient.php';

$config = array(
    'host' => 'http://api.familysafehero.com/Public/msg',
    'secrect' => '83bfcc4af0fab5304535e445fc1151c0'
);
$sendMessage=array('contents'=>'hello','fromUser'=>'fu','toUser'=>'tu','time'=>time());
$receiveMessage=array('toUser'=>'tu');
$client = new PhalApiClient($config);
if(!empty($_GET['send'])){
	// echo "send";exit;
	$rs = $client->request('Message.send',$sendMessage);
}elseif(!empty($_GET['receive'])){
	// echo "receive";exit;
	$rs = $client->request('Message.receive', $receiveMessage);//array('data' => 'index','msg'=>'hehe'));
}

    echo json_encode($rs);

// if ($client->getRet() == PhalApiClient::RET_OK) {
//     echo json_encode($rs);
// } else {
//     var_dump($client->getMsg());
//     echo '</br>';
//     var_dump($client->getUrl());
// }