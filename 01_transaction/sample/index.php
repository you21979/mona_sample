<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8"/>
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script>
	var TIMER_MSEC = 1 * 1000;
	var UPDATE_INTERVAL_MSEC = 30 * 1000;
	var proc = function(nexttick, callback){
		console.log($("#balance").text())
		var now = (new Date()).getTime();
		uiupdate((nexttick - now) / 1000 | 0);
		if(now < nexttick){
			return callback(nexttick);
		}
		uiupdate("updating");
		$.get( "https://api.monatr.jp/ticker?market=BTC_MONA", {}, function(d){
			[JSON.parse(d)].
			map(function(v){return {id:v.base+v.counter,bid:v.current_bid, ask:v.current_ask}}).
			forEach(function(v){
				var b = 1/$("#balance").text();
				var text = "" + (v.bid ) + " - " + v.ask;
				$("#"+v.id).html(text);
			});
			return callback(now + UPDATE_INTERVAL_MSEC);
		} );
	}
	var uiupdate = function(countdown){
		$("#COUNTDOWN").html(countdown);
	}
	var update = function(nexttick, msec){
		proc(nexttick, function(nexttick){
			setTimeout(function(){
				update(nexttick, msec);
			}, msec);
		});
	}
	update((new Date()).getTime(), TIMER_MSEC);
</script>
</head>
<body>
<form action="sample.php" method="get">
ユーザー名:
<input type=text size="40" name="username" value=""><br />
<input type="submit" name="param" value="アドレス取得">
<input type="submit" name="param" value="入金チェック">
</form>
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
	echo "Balance: <div id=balance>{$info['balance']}</div>";

	$list = $coind->listaccounts();
	print_r($list);

?>
<div id="COUNTDOWN">0</div>
<div id="BTCMONA">wait</div>
</body>
</html>

