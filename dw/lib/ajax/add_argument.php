<?php
session_start();
include_once('../config.php');

header('Content-Type: application/json');

$con = @mysql_connect($server, $seruser, $serpw);
if ($con)
{
	mysql_select_db($serdb, $con) || die('Fehler, keine Datenbank!');

	include_once('../bl/general.ajax.inc.php');
	include_once('../bl/login.php');
	include_once('../dal/tribunal.php');
	include_once('../bl/tribunal.php');

	$item_list = json_decode($_GET['items']);
	$new_item_list = array();
	foreach ($item_list as $part)
		$new_item_list[$part->name] = utf8_decode($part->value);
	$item_list = $new_item_list;
	$own_uid = false;

	if (isset($_SESSION['own_uid']) || isset($_COOKIE['own_uid']))
		$own_uid = true;

	$_SESSION['user'] = new UserCls();
	$_SESSION['user']->loadByUID($_SESSION['user']->getUIDFromId($_SESSION['lid']));

	$lang['lang'] = $_SESSION['user']->getLanguage();
	lib_bl_general_loadLanguageFile('general', '', true);
	lib_bl_general_loadLanguageFile('tribunal', 'loggedin', true);

	$msgid = $item_list['msgid'];
	if (!$msgid)
		$msgid = $item_list['msgid_manual'];

	$aid = lib_bl_tribunal_addArgument($item_list['ajax_id'], array($msgid), $_SESSION['user']->getUID());
	$aid = $aid[0];

	$argument = lib_bl_tribunal_getArgument($aid);
	$message = lib_bl_messages_getMessage($argument['msgid']);
	$hearing = lib_bl_tribunal_getHearing($item_list['ajax_id']);

	if ($_SESSION['user']->getGameRank() > 1 && $_SESSION['user']->getUID() != $hearing['accused']
		&& $_SESSION['user']->getUID() != $hearing['suitor'] && !$own_uid)
	{
		if ($argument['approved'] == 1)
			$approved = ' ['.$lang['approved'].']';
		elseif ($argument['approved'] == -1)
			$approved = ' ['.$lang['not_approved'].']';
		else
			$approved = ' ['.$lang['no_approve'].']';

		$html = '<div class="argument">
			'.htmlentities($lang['argument'].' #'.$argument['aid']).htmlentities($approved).'<br />
			<a href="javascript:;" onclick="'.htmlentities('show_dialog("argument_'.$argument['aid'].'")').'">'.htmlentities($message['title']).'</a><br />
			<div id="argument_'.$argument['aid'].'" class="argument_text">
				'.nl2br($message['message']).'
			</div>
			'.htmlentities(sprintf($lang['added_by'], $argument['added_datetime']->format($lang['acptimeformat']), lib_dal_user_uid2nick($_SESSION['user']->getUID()))).'
			<br />
		';

		if ($argument['approved'] == -1 || !$argument['approved'])
			$html .= '<a href="index.php?chose=tribunal&amp;sub=hearings&amp;id='.$item_list['ajax_id'].'&amp;aid='.$argument['aid'].'&amp;action=accept">';
		$html .= htmlentities($lang['accept']);
		if ($argument['approved'] == -1 || !$argument['approved'])
			$html .= '</a>';
		$html .= ' ';
		if ($argument['approved'] == 1)
			$html .= '<a href="index.php?chose=tribunal&amp;sub=hearings&amp;id='.$item_list['ajax_id'].'&amp;aid='.$argument['aid'].'&amp;action=decline">';
		$html .= htmlentities($lang['decline']);
		if ($argument['approved'] == 1)
			$html .= '</a>';

		$html .= '</div>';
	}

	if ($aid && count($argument) > 0)
	{
		echo json_encode(array(
			'status' => 'ok',
			'html' => $html,
		));
	}
	else
		echo json_encode(array('status' => 'failed'));
}