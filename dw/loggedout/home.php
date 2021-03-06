<?php
include (__DIR__ . '/header.php');
bl\general\loadLanguageFile('home', 'loggedout');
bl\general\loadLanguageFile('news', 'loggedout');
$parser = new bl\wikiParser\WikiParser();

$smarty->assign('heading', $lang['lastnews']);

$news_home = util\mysql\query('
	SELECT
		n.*,
		u1.nick AS creator,
		u1.email AS creator_email,
		u2.nick AS changer
	FROM dw_news n
	LEFT JOIN dw_user u1 ON (u1.uid = n.uid)
	LEFT JOIN dw_user u2 ON (u2.uid = n.changed_uid)
	ORDER BY nid DESC
	LIMIT 1
');
if ($news_home)
{
	$news = array();
	$news[0]['title'] = $news_home['title'];

	if ($news_home['creator'])
	{
		$news[0]['nick'] = $news_home['creator'];
		$news[0]['email'] = $news_home['creator_email'];
	}

	$newsDate = \DWDateTime::createFromFormat('Y-m-d H:i:s', $news_home['create_datetime']);
	$news[0]['time'] = $newsDate->format($lang['acptimeformat']);
	$news[0]['text'] = nl2br($parser->parseIt($news_home['text']));

	if ($news_home['changed'])
	{
		if ($news_home['changed'] > 1)
			$count = $news_home['changed'];
		else
			$count = 'ein';

		$changedDate = \DWDateTime::createFromFormat('Y-m-d H:i:s', $news_home['changed_datetime']);
		$news[0]['changed'] = sprintf($lang['newschanged'], $count, $changedDate->format($lang['timeformat']), $news_home['changer']);
	}
	$smarty->assign('news', $news);

	$smarty->assign('more_news', $lang['morenews']);

	$smarty->assign('news_from', $lang['from']);
}
else
{
	$smarty->assign('no_news', $lang['nonews']);
}

include (__DIR__ . '/footer.php');

$smarty->display($smarty->template_dir[0].'home.tpl');