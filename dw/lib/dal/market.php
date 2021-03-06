<?php
/*
 *
 * Licensed under GPL2
 * Copyleft by siyb (siyb@geekosphere.org)
 *
 */

namespace dal\market;

/**
 * Places an offer on the market.
 * @param <int> $uid the user that places the order
 * @param <string> $sellResource the resource to be sold
 * @param <float> $sellAmount the amount of the resource to be sold
 * @param <string> $exchangeResource the resources demanded in exchange
 * @param <float> $exchangeAmount and the resources amount ...
 * @param <float> $tax the taxes one has to pay
 */
function placeOnMarket(
	$uid, $x, $y, $sellResource, $sellAmount,
	$exchangeResource, $exchangeAmount, $tax)
{
	$new = new \DWDateTime();
	\util\mysql\query(
		sprintf(
			'
			INSERT INTO dw_market
			SET `sid` = %s,
				`sx` = %s,
				`sy` = %s,
				`s_resource` = %s,
				`s_amount` = %s,
				`e_resource` = %s,
				`e_amount` = %s,
				`tax` = %s,
				`complete` = %s,
				`create_datetime` = '.\util\mysql\sqlval($now->format()).'
			',
			\util\mysql\sqlval($uid),
			\util\mysql\sqlval($x),
			\util\mysql\sqlval($y),
			\util\mysql\sqlval($sellResource),
			\util\mysql\sqlval($sellAmount),
			\util\mysql\sqlval($exchangeResource),
			\util\mysql\sqlval($exchangeAmount),
			\util\mysql\sqlval($tax),
			0
		)
	);
}

/**
 * Removes an item from the market. Will not delete the database entry but change
 * the completion flag to 1, which indicates a finished transaction
 * @author siyb
 * @param <int> $mid the id of the marketitem to remove
 * @param int $uid
 */
function removeFromMarket($mid, $uid) {
	\util\mysql\query(
		sprintf(
			'
			UPDATE dw_market
			SET complete = 1, bid = %s
			WHERE mid = %s
			',
			\util\mysql\sqlval($uid),
			\util\mysql\sqlval($mid)

		)
	);
}

/**
 * Returns the uid of the user that placed this offer on the market.
 * @author siyb
 * @param <int> $mid the id of the auction
 * @return int the uid of the owner
 */
function getOwner($mid)
{
	return
		\util\mysql\query(
			sprintf(
				'
				SELECT sid FROM dw_market
				WHERE mid = %s
				',
				\util\mysql\sqlval($mid)
			)
		);
}

/**
 * Will return all available detail on an offer
 * @author siyb
 * @param <int> $mid the id of the offer to get information about
 * @return <array> an array containing the data
 */
function getOfferDetails($mid) {
	return
		\util\mysql\query(
			sprintf(
				'
				SELECT * FROM dw_market
				WHERE mid = %s
				',
				\util\mysql\sqlval($mid)
			)
		);
}

/**
 * Returns an array containing all active offers that are on the market
 * @author siyb
 * @return <array> containg all active offers of the market
 */
function returnAllOffers()
{
	return
	\util\mysql\query(
		sprintf(
			'
			SELECT * FROM dw_market
			WHERE complete = 0
			'
		),
		true
	);
}

/**
 * Checks if an offer is open
 * @author siyb
 * @param int $mid the id of the offer
 * @return int returns 0 if the offer is closed and 1 if the offer is open
 */
function isOpen($mid) {
	return
		\util\mysql\query(
			sprintf(
				'
				SELECT count(*) FROM dw_market
				WHERE mid = %s AND complete = 0
				',
				\util\mysql\sqlval($mid)
			)
		);
}

/**
 * Return an array of all offers the user with uid $uid was ever involved in
 * @param <int> $uid uid of the user
 * @param <string> $filter can be BUYER, SELLER or ALL, if $filter is invalid
 * @param <string> $order the sorting order, may be ASC or DESC, will default to
 * DESC
 * ALL will be used
 * @return <array> containing all offers the user was ever involved in
 */
function userOffers($uid, $filter, $order = 'DESC')
{
	$join = 'JOIN dw_user ON dw_user.uid = dw_market.sid OR dw_user.uid = dw_market.bid';
	if ($filter == 'BUYER')
		$join = 'JOIN dw_user ON dw_user.uid = dw_market.bid';
	if ($filter == 'SELLER')
		$join = 'JOIN dw_user ON dw_user.uid = dw_market.sid';
	$sql = sprintf(
		'
		SELECT sid, bid, s_resource, s_amount, e_resource, e_amount, tax, create_datetime, mid FROM dw_market
		%s
		WHERE dw_user.uid = %s
		ORDER BY create_datetime %s
		',
		$join,
		\util\mysql\sqlval($uid),
		$order
	);
	return \util\mysql\query($sql, true);
}

/**
 * Creates a sales table for the specified period.
 * @author siyb
 * @param <int> $limitS marks the last $limitS rows that should be considered for
 * the calculation, standard is 25
 * @param <int> $limitE marks the last $limitE rows that should be considered for
 * the calculation, standard is 25
 * @param <bool> $completeS 0 will include the sales of running offers, 1 the sales
 * for finished ones, standard is 1
 * @param <bool> $completeE 0 will include the sales of running offers, 1 the sales
 * for finished ones, standard is 1
 * @return <array> containing the sales data
 */
function sales($limitS = 25, $limitE = 25, $completeS = 1, $completeE = 1)
{
	$sql = sprintf(
		'
		SELECT resource, sum(amount) AS amount FROM
		(
		 SELECT s_resource AS resource, sum(s_amount) AS amount FROM
		 (
		   SELECT s_resource, s_amount
		   FROM dw_market
		   WHERE complete = %s
		   AND sid <> bid
		   ORDER BY create_datetime DESC
		   LIMIT %d
		 ) AS dw_market_sales_sub1
		 GROUP BY resource

		 UNION ALL

		 SELECT e_resource AS resource, sum(e_amount) AS amount FROM
		 (
		   SELECT e_resource, e_amount
		   FROM dw_market
		   WHERE complete = %s
		   AND sid <> bid
		   ORDER BY create_datetime DESC
		   LIMIT %d
		 ) AS dw_market_sales_sub2
		 GROUP BY resource
		) AS dw_market_sales
		GROUP BY resource
		',
		\util\mysql\sqlval($completeS),
		\intval($limitS), //using intval because sqlval will kill the query
		\util\mysql\sqlval($completeE),
		\intval($limitE)
	);

	return \util\mysql\query($sql);
}

/**
 * Searches the offerlist according to the given criteria
 * @author siyb
 * @param <String> $Sresource default %
 * @param <int> $SvalueRangeStart default 0
 * @param <int> $SvalueRangeEnd default 200000000
 * @param <String> $Eresource default %
 * @param <int> $EvalueRangeStart default 0
 * @param <int> $EvalueRangeEnd default 200000000
 * @param <int> $complete default 0
 * @param <String> $seller default %
 * @return <array>
 */
function search(
	$Sresource = '%', $SvalueRangeStart = 0, $SvalueRangeEnd = 200000000,
	$Eresource = '%', $EvalueRangeStart = 0, $EvalueRangeEnd = 200000000,
	$complete = 0, $seller = '%'
)
{
	$sql = sprintf(
		'
		SELECT * FROM dw_market
		JOIN dw_user ON dw_user.uid = dw_market.sid
		WHERE s_resource LIKE %s
		AND e_resource LIKE %s
		AND s_amount BETWEEN %s AND %s
		AND e_amount BETWEEN %s AND %s
		AND complete = %s
		AND nick LIKE %s
		',
		\util\mysql\sqlval($Sresource),
		\util\mysql\sqlval($Eresource),
		\util\mysql\sqlval($SvalueRangeStart),
		\util\mysql\sqlval($SvalueRangeEnd),
		\util\mysql\sqlval($EvalueRangeStart),
		\util\mysql\sqlval($EvalueRangeEnd),
		\util\mysql\sqlval($complete),
		\util\mysql\sqlval($seller)
	);
	return \util\mysql\query($sql, true);
}