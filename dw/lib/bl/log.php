<?php
namespace bl\log;

/**
 * insert logmessages
 * @author Neithan
 * @param int $type
 * @param string $actor
 * @param string $concerned
 * @param string $extra
 */
function saveLog($type, $actor, $concerned, $extra)
{
	if ($actor)
		$nick_actor = \dal\user\uid2nick($actor);

	if ($concerned)
		$nick_concerned = \dal\user\uid2nick($concerned);

	\dal\log\saveLog($type, $nick_actor, $nick_concerned, $extra);
}

/**
 * translate failed log texts
 * @author Neithan
 * @param string $text
 * @param string $user
 * @param string $concerned
 * @param int $type
 * @return string
 */
function translateFailed($text, $user, $concerned, $type)
{
	$e_text = explode("%", $text);
	$count = count($e_text);
	for ($n = 0; $n < $count; $n++)
	{
		if (strcmp($e_text[$n],"DATE") == 0)
			$e_text[$n] = date();

		if (strcmp($e_text[$n],"TIME") == 0)
			$e_text[$n] = time();

		if (strcmp($e_text[$n],"USER") == 0)
			$e_text[$n] = $user;

		if (strcmp($e_text[$n],"CONCERNED") == 0)
			$e_text[$n] = $concerned;

		if (strcmp($e_text[$n],"TYPE") == 0)
			$e_text[$n] = $type;

		$c_text .= $e_text[$n];
	}
	return $c_text;
}

/**
 * translage log texts
 * @param String $text
 * @param String $user
 * @param String $concerned
 * @param String $extra
 * @param \DWDateTime $dateTime
 * @return String
 */
function translateLog($text, $user, $concerned, $extra, \DWDateTime $dateTime)
{
	return str_replace(array(
		'%DATE%',
		'%TIME%',
		'%USER%',
		'%CONCERNED%',
		'%EXTRA%',
	), array(
		$dateTime->format('d.m.Y'),
		$dateTime->format('H:i:s'),
		$user,
		$concerned,
		$extra,
	), $text);
}

/**
 * failed to insert log entry, send error mail
 * @author Neithan
 * @param int $user
 * @param int $con_user
 * @param int $type
 */
function failed($user, $con_user, $type)
{
	$username = \dal\user\uid2nick($user);
	$con_username = \dal\user\uid2nick($con_user);
	$header = "From: Dynasty Wars <support@dynastywars.de>";
	$subject = $lang["failedlog_title"];
	$message = translateFailed($lang["failedlog"], $username, $con_username, $type);
	mail($email, $subject, $message, $header);
}

/**
 * returns the log message
 * @global array $lang
 * @param int $type
 * @param String $actor
 * @param String $concerned
 * @param String $extra
 * @param \DWDateTime $dateTime
 * @return String
 */
function types($type, $actor, $concerned, $extra, \DWDateTime $dateTime)
{
	global $lang;
	\bl\general\loadLanguageFile('log', 'acp');

	return translateLog($lang["t".$type], $actor, $concerned, $extra, $dateTime);
}

/**
 * get all log entries
 * @author Neithan
 * @return array
 */
function getLogEntries()
{
	return \dal\log\getLogEntries();
}

/**
 * get the html for displaying the log
 * @author Neithan
 * @param int $page
 * @return string
 */
function prepareEntries($page)
{
	global $lang;

	$entries = getLogEntries();

	$html = '';
	for (
		$n = 20 * $page - 20,
		$p = 20 * $page,
		$c = 1; //row selector, for better readability
		$n < $p && $n < count($entries);
		$n++)
	{
		$logDateTime = \DWDateTime::createFromFormat('Y-m-d H:i:s', $entries[$n]['log_datetime']);
		$html .= '
			<tr>
				<td class="logAction">
					'.types($entries[$n]['type'], $entries[$n]['actor'], $entries[$n]['concerned'], $entries[$n]['extra'], $logDateTime).'
				</td>
				<td class="logDate">
					'.$logDateTime->format($lang['acptimeformat']).'
				</td>
		   </tr>
	   ';

		if ($c == 1)
			$c++;
		else
			$c--;
	}

	return $html;
}

/**
 * returns the amount of log entries
 * @author Neithan
 * @return int
 */
function getLogCount()
{
	return count(getLogEntries());
}