<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
</head>
<body>

<?php

require_once(__DIR__ . '/jsonRPCClient.php');

$host = 'localhost';		/* monacoind 又は monacoin-qt を実行中のホストのアドレス */
$rpcuser = 'monacoinrpc';	/* monacoin.conf で指定した rpcユーザー名 */
$rpcpassword = 'aaaaa';		/* monacoin.conf で指定した rpcパスワード */
$rpcport = '4000';			/* monacoin.conf で指定した rpcポート */
$historyNum = 50;			/* 取得するトランザクション数 */


	/* monacoind への接続アドレス */
	$coindaddr = "http://$rpcuser:$rpcpassword@$host:$rpcport/";
	$coind = new jsonRPCClient($coindaddr);

	$info = $coind->getinfo();
	echo "Balance: {$info['balance']} <br>";
?>

<br />

</body>
</html>

