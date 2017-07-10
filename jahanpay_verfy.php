<?php
session_start();
define("IN_MYBB", "1");
require("./global.php");
if (!$mybb->user['uid'])
	error_no_permission();

$au = $_SESSION['au'];
if(empty($au))
	$info = "شما از انجام تراکنش منصرف شده اید.";
else
{
	$api = $mybb->settings['api_jahanpay'];
	$num = $_GET['num'];
	$query0 = $db->query("SELECT * FROM ".TABLE_PREFIX."jahanpay WHERE num={$num}");
	$jahanpay0 = $db->fetch_array($query0);
	
	$amount = $jahanpay0['price']; 
	$gid = $jahanpay0['group'];
	$pgid = $mybb->user['usergroup'];
	$uid = $mybb->user['uid'];
	$time = $jahanpay0['time'];		
	$period = $jahanpay0['period'];
	$bank = $jahanpay0['bank'];

	$client = new SoapClient("http://www.jpws.me/directservice?wsdl");
	$res = $client->verification($api , $amount , $au , $num, $_POST + $_GET );

	if( ! empty($res['result']) and $res['result'] == 1){
	
		$query1 = $db->simple_select("jahanpay_tractions", "*", "trackid='$au'");
		$check1 = $db->fetch_array($query1);
		if ($check1)
			$info = "این تراکنش قبلاً ثبت شده است. بنابراین شما نمی‌توانید به صورت غیر مجاز از این سیستم استفاده کنید.";
		else
		{
			$query2 = $db->simple_select("jahanpay", "*", "`num` = '$num'");
			while($check = $db->fetch_array($query2))
			{
				if ($amount != $check['price'])
					$info = "اطلاعات داده شده اشتباه می باشد . به همین دلیل عضویت انجام نشد.";

				$query1 = $db->simple_select('usergroups', 'title, gid', '1=1');
				while($group = $db->fetch_array($query1))
					$groups[$group['gid']] = $group['title'];
					
				$query5 = $db->simple_select('users', 'username, uid', '');
				while($uname1 = $db->fetch_array($query5, 'username, uid'))
					$usname[$uname1['uid']] = $uname1['username'];
		
				if($time == "1")
					$dateline = strtotime("+{$period} days");
				if($time == "2")
					$dateline = strtotime("+{$period} weeks");
				if($time == "3")
					$dateline = strtotime("+{$period} months");
				if($time == "4")
					$dateline = strtotime("+{$period} years");
		
				$stime = time();
				$add_traction = array(
					'packnum' => $num,
					'uid' => $uid,
					'gid' => $gid ,
					'pgid' => $pgid ,
					'stdateline' => $stime,
					'dateline' => $dateline,
					'trackid' => $au,
					'payed' => $amount,
					'stauts' => "1",
				);
				
				if ($db->table_exists("bank_pey") && $bank != 0)
				{
						$query7 = $db->simple_select("bank_pey", "*", "`uid` = '$uid'");
						$bankadd = $db->fetch_array($query7);
						$bank_traction = array(
						'uid' => $uid,
						'tid' => 0,
						'pid' => 0,
						'pey' => $bank ,
						'type' => '<img src="'.$mybb->settings['bburl'].'/images/inc.gif">',
						'username' => "مدیریت",
						'time' => $stime,
						 'info' => "خرید از درگاه پردانو",
					);
						
							if(!$bankadd)
							{
					$add_money = array(
					'uid' => $uid,
					'username' => $usname[$uid],
					'pey' => $bank ,
					);
									   $db->insert_query("bank_pey", $add_money);
									   $db->insert_query("bank_buy", $bank_traction);
							}
							if($bankadd)
							{
							$pey = $bankadd['pey'];
							$type='<img src="'.$mybb->settings['bburl'].'/images/inc.gif">';
									   $db->query("update ".TABLE_PREFIX."bank_pey set pey=$pey+$bank where uid=$uid");
									   $db->insert_query("bank_buy", $bank_traction);

							}
							
				}
				else
					$bank = "0";

				$db->insert_query("jahanpay_tractions", $add_traction);
				$db->update_query("users", array("usergroup" => $gid), "`uid` = '$uid'");
				$expdate = my_date($mybb->settings['dateformat'], $dateline).", ".my_date($mybb->settings['timeformat'], $dateline);
				$profile_link = "[url={$mybb->settings['bburl']}/member.php?action=profile&uid={$uid}]{$usname[$uid]}[/url]";
				$profile_link1 = build_profile_link($usname[$uid], $uid, "_blank");
				$info = preg_replace(
							array(
								'#{username}#',
								'#{group}#',
								'#{refid}#',
								'#{expdate}#',
								'#{bank}#',	
							),array(
									$profile_link1,
									$groups[$gid],
									$au,
									$expdate,
									$bank,
									
							),$mybb->settings['jahanpay_note']
						);
				$username = $mybb->user['username'];
				// Notice User By PM
				require_once MYBB_ROOT."inc/datahandlers/pm.php";
				$pmhandler = new PMDataHandler();
				$from_id = intval($mybb->settings['jahanpay_uid']);
				$recipients_bcc = array();
				$recipients_to = array(intval($uid));
				$subject = "گزارش پرداخت";
				$message = preg_replace(
							array(
								'#{username}#',
								'#{group}#',
								'#{refid}#',
								'#{expdate}#',
								'#{bank}#',
								
							),array(
								$profile_link,
								$groups[$gid],
								$au,
								$expdate,
								$bank,
								
							),$mybb->settings['jahanpay_pm']
						);
				$pm = array(
							'subject' => $subject,
							'message' => $message,
							'icon' => -1,
							'fromid' => $from_id,
							'toid' => $recipients_to,
							'bccid' => $recipients_bcc,
							'do' => '',
							'pmid' => ''
						);
						
				$pm['options'] = array(
							"signature" => 1,
							"disablesmilies" => 0,
							"savecopy" => 1,
							"readreceipt" => 1
						);
					
				$pm['saveasdraft'] = 0;
				$pmhandler->admin_override = true;
				$pmhandler->set_data($pm);
				if($pmhandler->validate_pm())
					$pmhandler->insert_pm();

					// Notice Admin By PM
				require_once MYBB_ROOT."inc/datahandlers/pm.php";
				$pmhandler = new PMDataHandler();
				$uidp=$mybb->settings['jahanpay_uid'];
				$from_id = intval($mybb->settings['jahanpay_uid']);
				$recipients_bcc = array();
				$recipients_to = array(intval($uidp));
				$subject = "عضویت کاربر در گروه ویژه";
				$message = preg_replace(
							array(
								'#{username}#',
								'#{group}#',
								'#{refid}#',
								'#{expdate}#',
								'#{bank}#',
								
							),
							array(
								$profile_link,
								$groups[$gid],
								$au,
								$expdate,
								$bank,
								
							),
							"کاربر [B]{username}[/B] با شماره تراکنش [B]{refid}[/B] در گروه [B]{group}[/B] عضو شد.
							تاریخ پایان عضویت:[B]{expdate}[/B]"
							);
					$pm = array(
							'subject' => $subject,
							'message' => $message,
							'icon' => -1,
							'fromid' => $from_id,
							'toid' => $recipients_to,
							'bccid' => $recipients_bcc,
							'do' => '',
							'pmid' => ''
						);
						
					$pm['options'] = array(
							"signature" => 1,
							"disablesmilies" => 0,
							"savecopy" => 1,
							"readreceipt" => 1
						);
					
					$pm['saveasdraft'] = 0;
					$pmhandler->admin_override = true;
					$pmhandler->set_data($pm);
						
					if($pmhandler->validate_pm())
						$pmhandler->insert_pm();
			}
		}			
	}
	else
	{
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
		$info = 'خطا (' . $res . ') : ' . $prompt;		
	}
}
eval("\$verfypage = \"".$templates->get('jahanpay_payinfo')."\";");
output_page($verfypage);
?>	