<?php
/*
 *
 * Licensed under GPL2
 * Copyleft by siyb (siyb@geekosphere.org)
 *
 */

namespace dal\user;

/**
 * Will check if the specified user exists in the database.
 * @author siyb
 * @param <String> $user the user to be checked
 * @return <int> returns 1 if the user has been found, 0 otherwise
 */
function exists($user)
{
    return \util\mysql\query(
		sprintf(
            'SELECT count(*) FROM dw_user WHERE nick=%s',
            \util\mysql\sqlval($user)
        )
    );
}

/**
 * Returns the nick that matches a given uid
 * @author siyb
 * @param <int> $uid the uid of the user
 * @return <string> the nick
 */
function uid2nick($uid)
{
	global $lang;

    if (!$uid)
		return $lang['emperor'];

	$sql = sprintf(
		'SELECT nick FROM dw_user WHERE uid = %s',
		\util\mysql\sqlval($uid)
	);

    return \util\mysql\query($sql);
}

/**
 * Returns the uid that matches a given nick
 * @author siyb
 * @param <int> $uid the uid of the user
 * @return <int> the uid
 */
function nick2uid($nick) {
    return
        \util\mysql\query(
            sprintf(
                'SELECT uid FROM dw_user WHERE nick = %s',
                \util\mysql\sqlval($nick)
            )
        );
}

/**
 * Returns the clanid of the user identified by $uid
 * @param <int> $uid the uid of the user
 * @return <int> the clanid of the user
 */
function returnCID($uid) {
    return
        \util\mysql\query(
            sprintf(
                'SELECT cid FROM dw_user WHERE uid = %d',
                \util\mysql\sqlval($uid)
            )
        );
}

/**
 * Returns a resultset containing all cities of the user with $uid
 * @param <int> $uid userid
 * @return <array> resultset containing city data
 */
function returnAllCities($uid) {
    return
    \util\mysql\query(
        sprintf(
            '
            SELECT map_x, map_y FROM dw_map
            WHERE uid = %d
            ',
            \util\mysql\sqlval($uid)
        )
    );
}

/**
 * Returns the uid from this email
 * @author Neithan
 * @param string $email
 * @return int
 */
function returnUID($email)
{
	$sql = '
		SELECT `uid` FROM `dw_user`
		WHERE `email` LIKE '.\util\mysql\sqlval($email).'
	';
	return \util\mysql\query($sql);
}

/**
 * Returns the clan ID and the rank ID
 * @author Neithan
 * @param int $uid
 * @return array
 */
function getClanRank($uid)
{
	$sql = '
		SELECT cid, rankid FROM dw_user
		WHERE uid = '.\util\mysql\sqlval($uid).'
	';
	return \util\mysql\query($sql);
}

/**
 * get an array with the uids of the clan leaders
 * @author Neithan
 * @param int $cid
 * @return array
 */
function getClanLeaders($cid)
{
	$sql = '
		SELECT uid FROM dw_user
		WHERE cid = '.\util\mysql\sqlval($cid).'
			AND rankid = 1
	';
	return \util\mysql\query($sql, true);
}

/**
 * get the uid
 * @author Neithan
 * @param unknown_type $x
 * @param unknown_type $y
 * @return unknown_type
 */
function getUIDFromMapPosition($x, $y)
{
	$sql = '
		SELECT uid FROM dw_map
		WHERE map_x = '.\util\mysql\sqlval($x).'
			AND map_y = '.\util\mysql\sqlval($y).'
	';
	return \util\mysql\query($sql);
}

/**
 * returns all informations about the user
 * @author Neithan
 * @param int $uid
 * @return array
 */
function getUserInfos($uid)
{
	$sql = '
		SELECT * FROM dw_user
		WHERE uid = '.\util\mysql\sqlval($uid).'
	';
	return \util\mysql\query($sql);
}

/**
 * get the user id from a troop
 * @author Neithan
 * @param int $tid
 * @return int
 */
function getUIDFromTID($tid)
{
	$sql = '
		SELECT uid FROM dw_troops
		WHERE tid = '.\util\mysql\sqlval($tid).'
	';
	return \util\mysql\query($sql);
}

/**
 * get the list of users for the acp user list
 * @author Neithan
 * @return array
 */
function getACPUserList()
{
	$sql = '
		SELECT
			uid,
			nick,
			blocked,
			game_rank
		FROM dw_user
		ORDER BY uid
	';
	return \util\mysql\query($sql, true);
}