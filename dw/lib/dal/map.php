<?php
/*
 *
 * Licensed under GPL2
 * Copyleft by siyb (siyb@geekosphere.org)
 *
 */

namespace dal\map;

/**
 * Returns the complete map (x and y coords as well as terrain type) sorted by
 * x and y.
 * @author siyb
 * @return array mapdata
 */
function getSortedMapData() {
    return
    \util\mysql\query(
        sprintf(
            "
            SELECT map_x, map_y, terrain, city from dw_map
            ORDER by map_x, map_y
            "
        )
    );
}

/**
 * Return a list of map_x and map_y of all cities
 * @author siyb
 * @return array citydata
 */
function returnAllCities() {
    return
    \util\mysql\query(
        sprintf(
            "
            SELECT map_x, map_y from dw_map
            WHERE city <> ''
            AND city <> '-'
            ORDER by map_x, map_y
            "
        )
    );
}

/**
 * Return the terrain at this position
 * @author Neithan
 * @param int $x
 * @param int $y
 * @return int
 */
function getTerrain($x, $y)
{
	$sql = '
		SELECT terrain FROM dw_map
		WHERE map_x = '.\util\mysql\sqlval($x).'
			AND map_y = '.\util\mysql\sqlval($y).'
	';
	return \util\mysql\query($sql);
}

/**
 * returns the maincity of the user
 * @author Neithan
 * @param int $uid
 * @return array
 */
function getUsersMainCity($uid)
{
	$sql = '
		SELECT map_x, map_y FROM dw_map
		WHERE uid = '.\util\mysql\sqlval($uid).'
			AND maincity = 1
	';
	return \util\mysql\query($sql);
}

/**
 * @author siyb
 * @todo: finish
 */
function setTerrainType($x, $y, $type) {
die("not supported yet");
	$sql =
		\util\mysql\query(
			"INSERT INTO
			dw_map (map_x, map_x, terrain, isle, harbour)

			"
		);
	//INSERT INTO users (username, email) VALUES ('Jo', 'jo@email.com')
	//ON DUPLICATE KEY UPDATE email = 'jo@email.com'
}
?>
