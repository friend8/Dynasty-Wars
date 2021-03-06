<?php
include ('loggedout/header.php');
bl\general\loadLanguageFile('login', 'loggedout');

$login = $_POST['login'];
$user = $_POST['nick'];
$pw = $_POST['pw'];
$pws = md5($pw);
$save_login = $_POST['save_login'];

$smarty->assign('heading', $lang['login']);
$smarty->assign('login', $login);

if ($login == 1)
{
	$login = bl\login\getAllData($user);
	$uid = $login['uid'];
	$reguser = $login['nick'];
	$regpw = $login['password'];
	$blocked = $login['blocked'];
	$status = $login['status'];
	$admin = $login['game_rank'];
	$lang['lang'] = $login['language'];
	$deactivated = $login['deactivated'];
	$login_closed = bl\login\checkLogin();
	if ($status)
		$err['status'] = 1;

	if ($blocked)
		$err['blocked'] = 1;

	if (is_object($_SESSION['user']))
	{
		if (($login_closed == 1) && ($_SESSION['user']->getGameRank() < 1))
			$err['login_closed'] = 1;
		elseif (($login_closed == 2) && ($_SESSION['user']->getGameRank() < 2))
			$err['login_closed'] = 2;
	}

	if (
		(strcasecmp($user, $reguser) == 0 && $pws === $regpw)
		&& !$err['status']
		&& !$err['blocked']
		&& !$err['login_closed']
		&& !$deactivated
	)
	{
		bl\login\setLastLogin($uid);
		$city = bl\login\getMainCity($uid);
		$id = bl\login\createID($uid);
		if ($save_login)
		{
			setcookie('lid', $id, time()+604800, '', '.dynasty-wars.de');
			setcookie('city', $city, time()+604800, '', '.dynasty-wars.de');
			setcookie('language', $lang['lang'], time()+604800, '', '.dynasty-wars.de');
		} else
		{
			$_SESSION['lid'] = $id;
			$_SESSION['city'] = $city;
			$_SESSION['language'] = $lang['lang'];
		}
		bl\general\redirect(util\html\createLink(array('chose' => 'home'), true));
	}
	else
	{
		if (strcasecmp($user, $reguser) != 0 || $pws !== $regpw)
			$err['failed_login'] = true;

		if ($err['failed_login'])
			$error = $lang['loginfailed'];
		elseif ($err['status'])
			$error = nl2br($lang['noactivation']);
		elseif ($err['blocked'])
			$error = nl2br($lang['blocked']);
		elseif ($err['login_closed'] == 1)
			$error = $lang['onlyadmin'];
		elseif ($err['login_closed'] == 2)
			$error = $lang['loginclosed'];
		elseif ($deactivated)
			$error = nl2br($lang['deactivated']);

		$smarty->assign('error', $error);
		$smarty->assign('back', $lang['back']);
	}
}
else
{
	$smarty->assign('name', $lang['name']);
	$smarty->assign('password', $lang['password']);
	$smarty->assign('remind_login', $lang['remindlogin']);
	$smarty->assign('login_button', $lang['login']);
	$smarty->assign('lost_password', $lang['lost_password']);
}

include ('loggedout/footer.php');

$smarty->display($smarty->template_dir[0].'login.tpl');