<?php
/**
 * get all messages of the specified type(s)
 * @author Neithan
 * @param int/array $type
 * @return array
 */
function lib_bl_messages_getMessages($uid, $type)
{
	$messagesArray = array();
	if (is_array($type))
	{
		foreach ($type as $t)
		{
			$messages = lib_dal_messages_getMessages($uid, $t, 'recipient');
			if ($messages)
				$messagesArray += $messages;
		}
	}
	else
		$messagesArray = lib_dal_messages_getMessages($uid, $type, 'recipient');

	if ($messagesArray)
	{
		foreach ($messagesArray as &$message)
			$message['sender'] = lib_bl_general_uid2nick($message['uid_sender']);
		unset($message);
	}

	return $messagesArray;
}

/**
 * get the specified message
 * @author Neithan
 * @param int $msgid
 * @return array
 */
function lib_bl_messages_getMessage($msgid)
{
	global $lang;

	$message = lib_dal_message_getMessage($msgid, 'recipient');

	if ($message)
	{
		$message['sender'] = lib_bl_general_uid2nick ($message['uid_sender']);
		$message['sentDate'] = date($lang['messageTimeFormat'], $message['date']);
	}

	$parser = new wikiparser;
	$message['message'] = preg_replace('#(\\\\r\\\\n|\\\\\\\\r\\\\\\\\n|\\\\n|\\\\\\\\n)#', "\r\n", $message['message']);
	$message['message'] = $parser->parseIt($message['message']);

	return $message;
}

/**
 * get all sent messages of the specified type(s)
 * @author Neithan
 * @param int $uid
 * @param int/array $type
 * @return array
 */
function lib_bl_messages_getSentMessages($uid, $type)
{
	$messagesArray = array();
	if (is_array($type))
	{
		foreach ($type as $t)
		{
			$messages = lib_dal_messages_getMessages($uid, $t, 'sender');
			if ($messages)
				$messagesArray += $messages;
		}
	}
	else
		$messagesArray = lib_dal_messages_getMessages($uid, $type, 'sender');

	if ($messagesArray)
	{
		foreach ($messagesArray as &$message)
			$message['recipient'] = lib_bl_general_uid2nick($message['uid_recipient']);
		unset($message);
	}

	return $messagesArray;
}

/**
 * get the specified message
 * @author Neithan
 * @param int $msgid
 * @return array
 */
function lib_bl_messages_getSentMessage($msgid)
{
	global $lang;

	$message = lib_dal_message_getMessage($msgid, 'sender');

	if ($message)
	{
		$message['recipient'] = lib_bl_general_uid2nick ($message['uid_recipient']);
		$message['sentDate'] = date($lang['messageTimeFormat'], $message['date']);
	}

	$parser = new wikiparser;
	$message['message'] = preg_replace('#(\\\\r\\\\n|\\\\\\\\r\\\\\\\\n|\\\\n|\\\\\\\\n)#', "\r\n", $message['message']);
	$message['message'] = $parser->parseIt($message['message']);

	return $message;
}

/**
 * get all messages of the specified type(s)
 * @author Neithan
 * @param int/array $type
 * @return array
 */
function lib_bl_messages_getArchivedMessages($uid, $type)
{
	$messagesArray = array();
	if (is_array($type))
	{
		foreach ($type as $t)
		{
			$messages = lib_dal_messages_getMessages($uid, $t, 'recipient', 1);
			if ($messages)
				$messagesArray += $messages;
		}
	}
	else
		$messagesArray = lib_dal_messages_getMessages($uid, $type, 'recipient', 1);

	if ($messagesArray)
	{
		foreach ($messagesArray as &$message)
			$message['sender'] = lib_bl_general_uid2nick($message['uid_sender']);
		unset($message);
	}

	return $messagesArray;
}

/**
 * get the specified message
 * @author Neithan
 * @param int $msgid
 * @return array
 */
function lib_bl_messages_getArchivedMessage($msgid)
{
	global $lang;

	$message = lib_dal_message_getMessage($msgid, 'recipient', 1);

	if ($message)
	{
		$message['sender'] = lib_bl_general_uid2nick ($message['uid_sender']);
		$message['sentDate'] = date($lang['messageTimeFormat'], $message['date']);
	}

	$parser = new wikiparser;
	$message['message'] = preg_replace('#(\\\\r\\\\n|\\\\\\\\r\\\\\\\\n|\\\\n|\\\\\\\\n)#', "\r\n", $message['message']);
	$message['message'] = $parser->parseIt($message['message']);

	return $message;
}

/**
 * return the amount of read and unread messages
 * @author Neithan
 * @param array $messages
 * @return int
 */
function lib_bl_messages_getCounts($messages)
{
	$counter = array(
		'totalMessages' => 0,
		'unreadMessages' => 0,
	);

	foreach ($messages as $message)
	{
		if ($message['unread'])
			$counter['unreadMessages']++;
		$counter['totalMessages']++;
	}

	return $counter;
}

/**
 * mark the specified message as deleted for the sender
 * @author Neithan
 * @param int $msgid
 * @param boolean $forceDeletion (default: false) will force the deletion of unread messages
 * @return int
 */
function lib_bl_messages_markAsDeletedSender($msgid, $forceDeletion = false)
{
	return lib_dal_messages_markAsDeleted($msgid, 'sender', $forceDeletion);
}

/**
 * mark the specified message as deleted for the recipient
 * @author Neithan
 * @param int $msgid
 * @param boolean $forceDeletion (default: false) will force the deletion of unread messages
 * @return int
 */
function lib_bl_messages_markAsDeletedRecipient($msgid, $forceDeletion = false)
{
	return lib_dal_messages_markAsDeleted($msgid, 'recipient', $forceDeletion);
}

/**
 * mark the specified message as read
 * @author Neithan
 * @param int $msgid
 * @return int
 */
function lib_bl_messages_markRead($msgid)
{
	if (is_int($msgid))
		return lib_dal_messages_markRead ($msgid);
	else
		return 0;
}

/**
 * check if there are read messages that are older than 14 days and mark them
 * as deleted.
 * @author Neithan
 */
function lib_bl_messages_checkReadMessages($uid)
{
	$messages = lib_bl_messages_getMessages($uid, array(1, 2, 3, 4));

	foreach ($messages as $message)
	{
		if (!$message['unread'])
		{
			$messageDate = new DateTime(date('Y-m-d H:i:s', $message['date_read']));
			$currentDate = new DateTime();
			$dateDiff = $currentDate->diff($messageDate);

			if ($dateDiff->d >= 14 || $dateDiff->m || $dateDiff->y)
				lib_bl_messages_markAsDeletedRecipient($message['msgid']);
		}
	}
}

/**
 * archive a message
 * @author Neithan
 * @param int $msgid
 * @return int
 */
function lib_bl_messages_archive($msgid)
{
	return lib_dal_messages_archive($msgid);
}

/**
 * check if the message is for this user
 * @author Neithan
 * @param int $msgid
 * @param int $uid
 * @param int $mode 1 for check recipient, 2 for check sender
 * @return <int> returns 1 if the message is for this user, otherwise 0
 */
function lib_bl_messages_checkUser($msgid, $uid, $mode)
{
	if ($mode == 1)
		$checkuid = lib_dal_messages_checkRecipient($msgid);
	elseif ($mode == 2)
		$checkuid = lib_dal_messages_checkSender($msgid);

	if ($uid == $checkuid)
		return 1;
	else
		return 0;
}