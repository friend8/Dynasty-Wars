<?php
session_start();
include_once(__DIR__.'/../config.php');

header('Content-Type: application/json');

include_once(__DIR__.'/../dal/general.inc.php');

$item_list = bl\general\jsonDecode($_GET['items'], true);
$target = new bl\user\UserCls();

$_SESSION['user'] = new bl\user\UserCls();
$_SESSION['user']->loadByUID($_SESSION['user']->getUIDFromId($_SESSION['lid']));

$lang['lang'] = $_SESSION['user']->getLanguage();
bl\general\loadLanguageFile('general', '');
bl\general\loadLanguageFile('units', 'loggedin');

$error_text = '';
$error_type = 0;

foreach ($item_list['errors'] as $key => $value)
	$error_text .= $lang['errors'][$key]."\n";
if (strlen($error_text) > 0)
{
	$error_text = substr($error_text, 0, -2);
	$error_type = 1;
}
else
{
	$points = $_SESSION['user']->getPoints();
	$target->loadByUID(dal\user\getUIDFromMapPosition($item_list['target']['tx'], $item_list['target']['ty']));
	$tPoints = $target->getPoints();

	if (($tPoints['unit_points'] + $tPoints['building_points']) < (($tPoints['unit_points'] + $tPoints['building_points']) * .75))
	{
		$error_type = 2;
		$error_text = $lang['points_warning'];
	}
}

echo bl\general\jsonEncode(array(
	'ok' => true,
	'type' => $error_type,
	'text' => $error_text,
));