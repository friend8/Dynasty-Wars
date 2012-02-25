<?php
require_once(dirname(__FILE__)."/../dw/lib/config.php");
require_once(dirname(__FILE__).'/../dw/lib/util/mysql.php');
require_once(dirname(__FILE__)."/../dw/lib/dal/unit.php");
require_once(dirname(__FILE__).'/../dw/lib/dal/login.php');
require_once(dirname(__FILE__)."/../dw/lib/bl/unit.php");

$con = @mysql_connect($server, $seruser, $serpw);
mysql_select_db($serdb, $con) || die("Fehler, keine Datenbank!");
$sql = 'SELECT uid FROM dw_user WHERE !deactivated';
$users = util\mysql\query($sql, true);
$lines = count($users);

$u_points = bl\unit\calcUnitPoints();
foreach ($u_points as $points)
	$unit_points[$points['uid']] = $points['points'];

for ($n = 0; $n < $lines; $n++)
{
	$building_points = 0;
	$sql = '
		SELECT lvl, upgrade_lvl
		FROM dw_buildings
		WHERE uid = "'.$users[$n]['uid'].'"
	';
	$buildings = util\mysql\query($sql, true);
	$lines2 = count($buildings);

	for ($m = 0; $m < $lines2; $m++)
		$building_points += ($buildings[$m]['upgrade_lvl']*25+50)+($buildings[$m]['lvl']*round(pow(1.2,5*($buildings[$m]['upgrade_lvl']+0.5))+15,0));

	if (!$unit_points[$users[$n]['uid']])
		$unit_points[$users[$n]['uid']] = 0;

	bl\unit\checkDaimyo($users[$n]['uid']);
	$sql = '
		UPDATE dw_points
		SET unit_points = "'.round($unit_points[$users[$n]['uid']], 0).'",
			building_points = '.$building_points.'
		WHERE uid = "'.$users[$n]['uid'].'"
	';
	$erg3 = util\mysql\query($sql);
}
?>