<?php
/*
 *
 * Licensed under GPL2
 * Copyleft by siyb (siyb@geekosphere.org)
 *
 */

namespace bl\unit;

/**
 * openlist for a-star algorithm
 * @author Neithan
 */
class OpenList extends ClosedList
{
	/**
	 * clears the openlist
	 * @author Neithan
	 * @return void
	 */
	function clear()
	{
		unset($this->x, $this->y, $this->g, $this->h, $this->f, $this->px, $this->py);
	}
}

/**
 * closedlist for a-star algorithm
 * @author Neithan
 */
class ClosedList
{
	var $x;
	var $y;
	var $g;
	var $h;
	var $f;
	var $px;
	var $py;

	/**
	 * returns the length of the closedlist
	 * @author Neithan
	 * @return int
	 */
	function getLength()
	{
		return count($this->x);
	}

	/**
	 * gives an array with the keys
	 * @author Neithan
	 * @return array
	 */
	function getKeys()
	{
		return array_keys($this->x);
	}
}

/**
 * A wrapper for \dal\unit\calcUnitPoints, please use this function instead
 * of the dal one.
 * @author siyb
 * @return array containing nick and points already calculated
 */
function calcUnitPoints()
{
    return \dal\unit\calcUnitPoints();
}

/**
 * Will calculate the hourly cost of the specified unit (food or koku!)
 * @author siyb
 * @param <int> $unitType the type of the unit identified by an int (consult excel sheet!)
 * @param <int> $amount how many men of this trooptype are there
 * @param <boolean> $type the type of resource, must be true for food or false for koku
 * @return <float> the hourly cost of koku or food
 */
function calcFoodOrKoku($unitType, $amount, $type)
{
    // hours of the day
    $hotd = 24;

    // koku division factor, $level / $kdf = kokuprice
    $kdf = 2;

    // the cost per unit per day
    $level0_cost = 0; // only for koku, not for food
    $level1_cost = 3;
    $level2_cost = 4;
    $level3_cost = 5;
    $level4_cost = 6;
    $level5_cost = 7;
    $level6_cost = 8;

    // mapping units to costs, this array is grouped by unit types (types as in: costs the same)
    if ($type) { // food map
        $costMap = array(
            1 => $level1_cost, 2 =>  $level1_cost, 3 => $level1_cost,
            4 => $level2_cost, 5 => $level2_cost, 6 => $level2_cost,
            7 => $level3_cost, 8 => $level3_cost,
            9 => $level4_cost, 10 => $level4_cost, 11 => $level4_cost, 12 => $level4_cost, 13 => $level4_cost, 19 => $level4_cost,
            14 => $level5_cost, 15 => $level5_cost,
            16 => $level6_cost, 17 => $level6_cost, 18 => $level6_cost
        );
    } else { // koku map
        // divide food prices by koku division factor
        $level1_cost /= $kdf;$level2_cost /= $kdf;$level3_cost /= $kdf;
        $level4_cost /= $kdf;$level5_cost /= $kdf;$level6_cost /= $kdf;

        $costMap = array(
            1 => $level0_cost, 2 =>  $level0_cost, 3 => $level0_cost,
            4 => $level2_cost, 5 => $level2_cost, 6 => $level2_cost,
            7 => $level3_cost, 8 => $level3_cost,
            9 => $level4_cost, 10 => $level4_cost, 11 => $level4_cost, 12 => $level4_cost, 13 => $level4_cost, 19 => $level4_cost,
            14 => $level5_cost, 15 => $level5_cost,
            16 => $level6_cost, 17 => $level6_cost, 18 => $level6_cost
        );
    }

    return ($costMap[$unitType] * $amount) / $hotd;
}

/**
 * Calculates the total amount of food to be spend per hour for all units of the
 * user with $uid.
 * @author siyb
 * @param <int> $uid the userid of the user
 * @return <int> total food cost per hour for all units
 */
function calcTotalFoodCost($uid)
{
    if (!\dal\unit\calculateUnitCosts()) return 0;
    $result = \dal\unit\getUnitCount($uid);
    $total = 0;

    if (count($result) > 1)
    {
	    foreach ($result as $row)
	        $total += calcFoodOrKoku($row['kind'], $row['count'], true);
    }
    elseif (count($result) == 1)
    	$total = calcFoodOrKoku($result['kind'], $result['count'], true);

	if ($total > 0 && $total < 1)
		$total = ceil($total);

    return $total;
}

/**
 * Does the same as calcTotalFoodCost() for koku
 * @author siyb
 * @param <int> $uid the userid of the user
 * @return <int> total koku cost per hour for all units
 */
function calcTotalKokuCost($uid)
{
    if (!\dal\unit\calculateUnitCosts()) return 0;
    $result = \dal\unit\getUnitCount($uid);
    $total = 0;

    if (count($result) > 1)
    {
	    foreach ($result as $row)
        	$total += calcFoodOrKoku($row['kind'], $row['count'], false);
    }
    elseif (count($result) == 1)
    	$total = calcFoodOrKoku($result['kind'], $result['count'], false);

	if ($total > 0 && $total < 1)
		$total = ceil($total);

    return $total;
}

/**
 * select the units from the current user
 * @author Neithan
 * @param int $kind
 * @param int $uid
 * @return array
 */
function getUnits($kind, $uid)
{
  	return \dal\unit\getUnits($kind, $uid);
}

/**
 * check for an existing daimyo-unit for the user
 * @author Neithan
 * @param int $uid
 * @return void
 */
function checkDaimyo($uid)
{
	if (!\dal\unit\checkDaimyo($uid))
	{
		$pos = \dal\login\getMainCity($uid);

		if (count($pos) > 0)
		{
			$pos_x = $pos['map_x'];
			$pos_y = $pos['map_y'];
		}
		\dal\unit\createDaimyo($uid, $pos_x, $pos_y);
	}
}