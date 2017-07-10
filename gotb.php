<?php
session_start();
define("IN_MYBB", "1");
require("./global.php");

$num = $_POST['jahanpay_num'];
$query = $db->query("SELECT * FROM ".TABLE_PREFIX."jahanpay WHERE num={$num}");
$jahanpay = $db->fetch_array($query);

$callBackUrl = $mybb->settings['bburl'] . '/jahanpay_verfy.php?num=' .$jahanpay['num'];
	
$client = new SoapClient("http://www.jpws.me/directservice?wsdl");
$res = $client->requestpayment($mybb->settings['api_jahanpay'], $jahanpay['price'], $callBackUrl, $num);
	
if( ! empty($res['result']) and $res['result'] == 1){
        $_SESSION['au']=$res['au'];
		  echo ('<div style="display:none;">'.$res['form'].'</div><script>document.forms["jahanpay"].submit();</script>');
}else{
$res=$res['result'];
	switch($res) 
	{ 
		case '' : $res = '-6'; $prompt = "ارتباط با بانک برقرار نشد"; break; 
		case '-20' : $prompt = "api نامعتبر است"; break; 
		case '-21' : $prompt = "آی پی نامعتبر است"; break; 
		case '-22' : $prompt = "مبلغ از کف تعریف شده کمتر است"; break; 
		case '-23' : $prompt = "مبلغ از سقف تعریف شده بیشتر است"; break; 
		case '-24' : $prompt = "مبلغ نامعتبر است"; break; 
		case '-6' : $prompt = "ارتباط با بانک برقرار نشد"; break; 
		case '-26' : $prompt = "درگاه غیرفعال است"; break; 
		case '-27' : $prompt = "آی پی شما مسدود است"; break; 
		case '-9' : $prompt = "خطای ناشناخته"; break; 
		case '-29' : $prompt = "آدرس کال بک خالی است"; break; 
		case '-30' : $prompt = "چنین تراکنشی یافت نشد"; break;
		case '-31' : $prompt = "تراکنش انجام نشده"; break; 
		case '-32' : $prompt = "تراکنش انجام شده اما مبلغ نادرست است"; break; 	
		case '-33' : $prompt = "تراکنش قبلا پرداخت شده است"; break; 				
	}
	echo '<meta charset=utf-8><font color=red> خطا (' . $res . ') : ' . $prompt . '</font>';
}
?>