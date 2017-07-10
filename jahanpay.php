<?php
define("IN_MYBB", "1");
require("./global.php");	
global $mybb;

$ui = $mybb->user['uid'];
$ug = $mybb->user['usergroup'];
	
if (!$mybb->user['uid'])
	error_no_permission();

$ban = explode(",",$mybb->settings['jahanpay_ban']) ;
if(in_array($ui,$ban))
	error_no_permission();

$bang = explode(",",$mybb->settings['jahanpay_bang']) ;
if(in_array($ug,$bang))
	error_no_permission();
	
$query = $db->simple_select('usergroups', 'title, gid', '', array('order_by' => 'gid', 'order_dir' => 'asc'));
while($group = $db->fetch_array($query, 'title, gid'))
	$groups[$group['gid']] = $group['title'];


$query = $db->simple_select('jahanpay', '*', '', array('order_by' => 'price', 'order_dir' => 'ASC'));

while ($jahanpay = $db->fetch_array($query))
{
	$bgcolor = alt_trow();
	$jahanpay['num'] = intval($jahanpay['num']);
	$jahanpay['title'] = htmlspecialchars_uni($jahanpay['title']);

	$jahanpay['price'] = floatval($jahanpay['price']).' تومان ';
	$jahanpay['usergroup'] = $groups[$jahanpay['group']];

	if($jahanpay['time']== 1)
		$time= "روز";

	if($jahanpay['time']== 2)
		$time= "هفته";
	
	if($jahanpay['time']== 3)
		$time= "ماه";
	
	if($jahanpay['time']== 4)
		$time= "سال";	

	$period = intval($jahanpay['period']);
	$jahanpay['period'] = intval($jahanpay['period'])." ".$time;
	$uid = $mybb->user['uid'];
	$query5 = $db->query("SELECT * FROM ".TABLE_PREFIX."jahanpay_tractions WHERE uid=$uid AND stauts = 1");
	$check5 = $db->fetch_array($query5);
	if ($check5)
	{
		$note = "<div class=\"red_alert\">به دلیل اینکه شما قبلاً یکی از این بسته ها را خریداری کرده اید و زمان عضویت شما به پایان نرسیده است ، نمی توانید  بسته ی جدیدی را خریداری نمایید </div>";
		$buybutton = "<input type='image' src='{$mybb->settings['bburl']}/images/buy-pack.png' border='0'  name='submit'alt='خرید بسته {$jahanpay['title']}' />";
	}
	else
		$buybutton = "<form action='{$mybb->settings['bburl']}/gotb.php' method='post'>
						<input type='hidden' name='jahanpay_num' value='{$jahanpay['num']}' /> 
						<input type='image' src='{$mybb->settings['bburl']}/images/buy-pack.png' border='0'  name='submit'alt='خرید بسته {$jahanpay['title']}' />
					</form>";

	eval("\$list .= \"".$templates->get('jahanpay_list_table')."\";");
}

if (!$list)
	eval("\$list = \"".$templates->get('jahanpay_no_list')."\";");

eval("\$jahanpaypage = \"".$templates->get('jahanpay_list')."\";");
output_page($jahanpaypage);
?>