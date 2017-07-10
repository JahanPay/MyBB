<?php
if (!defined("IN_MYBB"))
	die ("You can directly not access this file ");

$plugins->add_hook("admin_user_menu", "jahanpay_plugin_admin_cp");
$plugins->add_hook('admin_user_action_handler', 'jahanpay_handle');



function mybb_jahanpay_info()
{
 return array(
 
    "name"  => "عضویت آنی پس از پرداخت (جهان پی)",
	"description"   => "اين پلاگين بلافاصله پس از پرداخت توسط کاربر او را به گروه کاربري مورد نظر انتقال مي دهد.",
	"website"   => "http://jahanpay.me/",
	"author"   => "jahanpay",
	"authorsite"   => "http://jahanpay.me/",
	"version"    => "4.1",
	"compatibility"   => "18*",
	);
}

function mybb_jahanpay_install()
{
global $db, $mybb, $settings;

     $settings_group = array(
        'gid'          => NULL,
        'name'         => 'jahanpays',
        'title'        => 'تنظيمات پلاگين درگاه پرداخت جهان پی',
        'description'  => '',
        'disporder'    => $rows++,
        'isdefault'    => 'no'
    );
    $db->insert_query('settinggroups', $settings_group);
    $gid = $db->insert_id();

	$jahanpay2= array(
	'name' => 'api_jahanpay',
	'title' =>'ApiID',
	'description' =>'API کدی را که از سایت جهان پی دریافت کرده اید در این قسمت وارد کنید.',
	'optionscode' => 'text',
	'value' =>'12345',
	'disporder' => 2,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay2);
	
	$jahanpay3 = array(
	'name' => 'jahanpay_uid',
	'title' =>'شناسه کاربر مدیرکل',
	'description' =>'شناسه کاربری مدیر کل که پیام خصوصی پس از پرداخت توسط او به کاربر ارسال می شود.',
	'optionscode' => 'text',
	'value' =>'1',
	'disporder' => 3,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay3);
	$jahanpay4 = array(
	'name' => 'jahanpay_pm',
	'title' =>'متن پیام خصوصی',
	'description' =>"پیام خصوصی‌ای که پس از عضویت کاربر در سایت به عنوان <strong>سند</strong> به او ارسال می‌شود. (BBCODE) </br>
	راهنما : {username} = نام کاربری | {group} = گروه جدید | {refid} = شماره تراکنش | {expdate} = تاریخ پایان عضویت | {bank} = افزایش موجودی دربانک </br>
	<script type=\"text/javascript\">
function insertText(value, textarea)
{
	// Internet Explorer
	if(document.selection)
	{
		textarea.focus();
		var selection = document.selection.createRange();
		selection.text = value;
	}
	// Firefox
	else if(textarea.selectionStart || textarea.selectionStart == \'0\')
	{
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		textarea.value = textarea.value.substring(0, start)	+ value	+ textarea.value.substring(end, textarea.value.length);
	}
	else
	{
		textarea.value += value;
	}
}
</script>
<br />
<b onclick=\"insertText(\'{username}\', $(\'setting_jahanpay_pm\'));\">{username}</b>
<b onclick=\"insertText(\'{group}\', $(\'setting_jahanpay_pm\'));\">{group}</b>
<b onclick=\"insertText(\'{refid}\', $(\'setting_jahanpay_pm\'));\">{refid}</b>
<b onclick=\"insertText(\'{expdate}\', $(\'setting_jahanpay_pm\'));\">{expdate}</b>
<b onclick=\"insertText(\'{bank}\', $(\'setting_jahanpay_pm\'));\">{bank}</b>",

	'optionscode' => 'textarea',
	'value' =>'[B]{username}[/B] گرامی، درود!
 عضویت شما در گروه [B]{group}[/B] انجام شد و شما به این گروه منتقل شدید. 
 شماره ی تراکنش شما: [B]{refid}[/B] 
 تاريخ پايان عضويت شما: [B]{expdate}[/B]
 مقدار افزایش موجودی در بانک: [B]{bank}[/B]
 ',
	'disporder' => 5,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay4);

	$jahanpay5 = array(
	'name' => 'jahanpay_soap',
	'title' =>'Curl',
	'description' =>'curl می بایست بر روی هاست شما فعال باشد',
	'optionscode' => 'select\n0=Curl',
	'value' =>'0',
	'disporder' => 7,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay5);
	
	$jahanpay6 = array(
	'name' => 'jahanpay_note',
	'title' =>'پیام پس از عضویت',
	'description' =>"پیامی که بلافاصله پس از انتقال کاربر از جهان پی به سایت شما به او نمایش داده می‌شود. (HTML) </br>
	راهنما : {username} = نام کاربری | {group} = گروه جدید | {refid} = شماره تراکنش | {expdate} = تاریخ پایان عضویت | {bank} = افزایش موجودی دربانک </br>
	<script type=\"text/javascript\">
function insertText(value, textarea)
{
	// Internet Explorer
	if(document.selection)
	{
		textarea.focus();
		var selection = document.selection.createRange();
		selection.text = value;
	}
	// Firefox
	else if(textarea.selectionStart || textarea.selectionStart == \'0\')
	{
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		textarea.value = textarea.value.substring(0, start)	+ value	+ textarea.value.substring(end, textarea.value.length);
	}
	else
	{
		textarea.value += value;
	}
}
</script>
<br />
<b onclick=\"insertText(\'{username}\', $(\'setting_jahanpay_note\'));\">{username}</b>
<b onclick=\"insertText(\'{group}\', $(\'setting_jahanpay_note\'));\">{group}</b>
<b onclick=\"insertText(\'{refid}\', $(\'setting_jahanpay_note\'));\">{refid}</b>
<b onclick=\"insertText(\'{expdate}\', $(\'setting_jahanpay_note\'));\">{expdate}</b>
<b onclick=\"insertText(\'{bank}\', $(\'setting_jahanpay_note\'));\">{bank}</b>",

	'optionscode' => 'textarea',
	'value' =>'<strong>{username}</strong> گرامی٬ از عضویت شما در گروه <strong>{group}</strong> سپاس گزاریم! </br> عضویت شما در این گروه انجام شد و شما به این گروه منتقل شدید. </br> اطلاعات عضویت شما:</br>
نام کاربری: <strong>{username}</strong> </br> گروه: <strong>{group}</strong> </br> شماره تراکنش: <strong>{refid}</strong> </br>تاریخ پایان عضویت: <strong>{expdate}</strong> </br>مقدار افزایش موجودی در بانک: <strong>{bank}</strong> </br> ضمناً یک پیام خصوصی به عنوان <strong>سند</strong> برای شما ارسال شد. لطفاً این پیام را برای اطمنیان نزد خود نگه دارید.</br>
با سپاس از عضویت شما.',
	'disporder' => 6,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay6);
	
	$jahanpay7 = array(
	'name' => 'jahanpay_ban',
	'title' =>'اعضای محروم',
	'description' =>'شناسه کاربرانی را که می‌خواهید از خرید بسته‌ها محروم شوند را وارد کنید. (به وسیله‌ی کاما(,) متمایز کنید)',
	'optionscode' => 'text',
	'value' =>'',
	'disporder' => 4,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay7);

	$jahanpay8 = array(
	'name' => 'jahanpay_bang',
	'title' =>'گروه‌های محروم',
	'description' =>'شناسه گروه‌هایی را که می‌خواهید از خرید بسته‌ها محروم شوند را وارد کنید. (به وسیله‌ی کاما(,) متمایز کنید)',
	'optionscode' => 'text',
	'value' =>'7,5',
	'disporder' => 4,
	'gid' => intval($gid));
    $db->insert_query('settings',$jahanpay8);

	
		 $jahanpay_task = array(
			"title" => "بررسی ابطال عضویت اعضای گروه ویژه",
			"description" => "این وظیفه که هر‌دقیقه اجرا می‌شود٬ ابطال عضویت اعضای گروه ویژه را بررسی می‌کند",
			"file" => "jahanpay",
			"minute" => '0',
			"hour" => '*',
			"day" => '*',
			"month" => '*',
			"weekday" => '*',
			"enabled" => '1',
			"logging" => '1',
			"nextrun" => time()
		);
	 $db->insert_query("tasks", $jahanpay_task);

	
	    rebuild_settings();


	$db->write_query("CREATE TABLE `".TABLE_PREFIX."jahanpay` (
	  `num` bigint(30) UNSIGNED NOT NULL auto_increment,
	  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
	  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	  `time` varchar(1) NOT NULL default '',
	  `period` int(5) UNSIGNED NOT NULL default '0',
	  `price` int(5) UNSIGNED NOT NULL default '0',
	  `group` smallint(5) UNSIGNED NOT NULL default '0',
	  `bank` int(5) UNSIGNED NOT NULL default '0',	  
	  PRIMARY KEY  (`num`)
	  ) ENGINE=MyISAM");
	  
	  	$db->write_query("CREATE TABLE `".TABLE_PREFIX."jahanpay_tractions` (
	  `tid` bigint(30) UNSIGNED NOT NULL auto_increment,
	  `packnum` bigint(30) UNSIGNED NOT NULL default '0',
	  `uid` int(10) UNSIGNED NOT NULL default '0',
	  `gid` smallint(5) UNSIGNED NOT NULL default '0',
	  `pgid` smallint(5) UNSIGNED NOT NULL default '0',
	  `stdateline` bigint(30) UNSIGNED NOT NULL default '0',	  
	  `dateline` bigint(30) UNSIGNED NOT NULL default '0',
	  `trackid` int UNSIGNED NOT NULL default '0',	  
	  `payed` int(5) UNSIGNED NOT NULL default '0',
	  `stauts` int(5) UNSIGNED NOT NULL default '0',

	  PRIMARY KEY  (`tid`)
	  ) ENGINE=MyISAM");


}	  
	 function mybb_jahanpay_is_installed()
{
	global $db;
		return $db->table_exists("jahanpay");
}

function mybb_jahanpay_uninstall()
{
global $db;
	if ($db->table_exists('jahanpay'))
		$db->drop_table('jahanpay');
	if ($db->table_exists('jahanpay_tractions'))
		$db->drop_table('jahanpay_tractions');
		
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_activation', 'jahanpays')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_uid', 'jahanpays')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('api_jahanpay', 'jahanpays')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_pm', 'jahanpays')");	
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_soap', 'jahanpays')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_note', 'jahanpays')");	
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_ban', 'jahanpays')");										
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('jahanpay_bang', 'jahanpays')");										
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups where name='jahanpays'");
	$db->delete_query("tasks", "file='jahanpay'");

		rebuild_settings();
}
 
function mybb_jahanpay_activate()

{
global $db, $template, $lang;

	$tmp_list = array(
		"title" => 'jahanpay_list',
		"template" => $db->escape_string('
<html>
<head>
<title>بسته های عضویت ویژه</title>
{$headerinclude}
</head>
<body>
{$header}
{$note}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="10"><strong>بسته های عضویت ویژه</strong></td>
</tr>
<tr>
<td class="tcat" width="25%"><strong>نام بسته</strong></td>
<td class="tcat" width="25%"><strong>توضیحات</strong></td>
<td class="tcat" width="15%" align="center"><strong>گروه کاربری</strong></td>
<td class="tcat" width="15%" align="center"><strong>مدت زمان عضویت</strong></td>
<td class="tcat" width="15%" align="center"><strong>هزینه عضویت</strong></td>
<td class="tcat" width="15%" align="center"><strong>خرید</strong></td>
</tr>
{$list}
</table>
</br>
<div align="center" class="smalltext">Powered by <a href="http://www.jahanpay.com">.:.jahanpay.:.</a> jahanpay Instant Payment Plugin</div>
{$footer}
</body>
</html>'),
		"sid" => "-1",
		);
	$db->insert_query("templates", $tmp_list);

	$tmp_table = array(
		"title" => 'jahanpay_list_table',
		"template" => $db->escape_string('<html>
<tr>
<td class="{$bgcolor}" width="25%"><strong>{$jahanpay[\'title\']}</strong></td>
<td class="{$bgcolor}" width="25%">{$jahanpay[\'description\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$jahanpay[\'usergroup\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$jahanpay[\'period\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$jahanpay[\'price\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$buybutton}</td>
</tr>'),
		"sid" => "-1",
		);

	$db->insert_query("templates", $tmp_table);
	
	$tmp_emp = array(
		"title" => 'jahanpay_no_list',
		"template" => $db->escape_string('<html>
<tr>
<td class="trow1" width="100%" colspan="10">در حال حاضر بسته ی عضویت ویژه ای در این انجمن ثبت نشده است.</td>
</tr>'),
		"sid" => "-1",
		);

	$db->insert_query("templates", $tmp_emp);
	
	$tmp_info = array(
		"title" => 'jahanpay_payinfo',
		"template" => $db->escape_string('<html>
<head>
<title>{$mybb->settings[bbname]} - گزارش پرداخت</title>
{$headerinclude}
</head>
<body>
{$header}
<br />

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>گزارش پرداخت</strong></td>
</tr>
<tr>
<td class="trow1" colspan="6">
{$info}
</td>
</tr>
</table>
{$footer}
</body>
</html>
'),
		"sid" => "-1",
		);

	$db->insert_query("templates", $tmp_info);
	
	}
	


function mybb_jahanpay_deactivate()	
{
	global $db;
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='jahanpay_list'");
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='jahanpay_list_table'");
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='jahanpay_no_list'");	
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='jahanpay_payinfo'");	
	
	rebuild_settings();
}

function jahanpay_plugin_admin_cp($sub_menu)
{
	global $mybb, $lang;
			
	end($sub_menu);
	$key = (key($sub_menu))+10;
		
	if(!$key)
		$key = '20';
		
	$sub_menu[$key] = array('id' => 'jahanpay', 'title' => "بسته های عضویت آنی (جهان پی)", 'link' => "index.php?module=user-jahanpay");
     return $sub_menu;
}

function jahanpay_handle($action)
{
	$action['jahanpay'] = array('active' => 'jahanpay', 'file' => 'jahanpay.php');
	return $action;
}

?>