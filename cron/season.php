<?php
require_once(dirname(__FILE__).'/../dw/lib/config.php');
require_once(dirname(__FILE__).'/../dw/lib/util/mysql.php');
require_once(dirname(__FILE__).'/../dw/lib/bl/unit.php');
require_once(dirname(__FILE__).'/../dw/lib/dal/unit.php');

$con = @mysql_connect($server, $seruser, $serpw);
mysql_select_db($serdb, $con) || die('Fehler, keine Datenbank!');
if (date('d.m.') == '1.10.')
	$sql = 'UPDATE `dw_game` SET `season` = 2';
elseif (date('d.m.') == '1.4.')
	$sql = 'UPDATE `dw_game` SET `season` = 1';
util\mysql\query($sql);