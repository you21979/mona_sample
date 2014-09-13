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


if(isset($_GET['param']) && isset($_GET['username'])) {
	/* monacoind への接続アドレス */
	$coindaddr = "http://$rpcuser:$rpcpassword@$host:$rpcport/";
	$coind = new jsonRPCClient($coindaddr);

	/* 入金アドレスのlabel。このサンプルではユーザー名をそのままラベルに使用しています */
	$addrlabel = $_GET['username'];

	if($_GET['param'] == "アドレス取得") {	/* value="アドレス取得" のボタンの処理 */
		try{
			/* アドレス取得 */
			$receiveaddress = $coind->getaccountaddress($addrlabel);
			echo "入金先アドレス：$receiveaddress<br /><img src=https://chart.googleapis.com/chart?chs=300&cht=qr&chl=monacoin:MWswpBA9p5V8WHPGYJ7wr1Srzkfr5g51Mn><br />getaccountaddress()で取得するアドレスは、１回入金が行われるたびに変わります。<br />変わった後も、以前に取得したアドレスへの入金は有効です。";
		} catch (Exception $e) {
			echo 'エラー<br />';
		}
	}
	else if($_GET['param'] == "入金チェック") {	/* value="入金チェック" のボタンの処理 */
		try{
			/* 指定のラベル（このサンプルではユーザー名＝ラベル）のトランザクションを
			   最新のものから$historyNum分だけ取得。
			   第三引数の最新のトランザクションからのオフセットです。（省略可）*/
			$transactions = $coind->listtransactions($addrlabel, $historyNum, 0);

			echo $_GET['username']."さんの入金履歴<br />";
			foreach($transactions as $transaction) {
				/* 取得したトランザクションから入金のものだけ抽出 */
				if($transaction['category']=="receive") {
					echo date('Y/m/d H:i:s',$transaction['time']).'：　'.$transaction['amount'].' MONA　';
					/* 2014/02/03 追加
					   一定期間経過したものだけを入金扱いとする
					   このサンプルではトランザクション発生から 6 block経過したものを承認済みとする */
					echo ($transaction['confirmations'] < 6)?'[承認待ち]':'[承認済み]';
					echo "<br>";
				}
			}

		} catch (Exception $e) {
			echo 'エラー<br />';
		}

	}
}
?>

<br />
<form>
<input type="button" value="戻る" onClick="location.href='index.php'">
</form>

</body>
</html>

