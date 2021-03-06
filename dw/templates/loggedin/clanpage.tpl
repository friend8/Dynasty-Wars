{if $smarty.get.del}
	<div style="text-align: center;">
		{$lang.clandeleted|sprintf:$clanData.clanname}<br />
		<a href='index.php?chose=clan'>{$lang.back}</a>
	</div>
{elseif $smarty.get.cid != $smarty.session.user->getCID() && $smarty.get.enter}
	{if $applicationSaved}
		<div style="text-align: center;">
			{$lang.appsaved|sprintf:$clanData.clanname}
		</div>
	{/if}
	<form method='post' action='index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;clanshow=1&amp;enter=2'>
		<div class="clan_name"><strong>{$lang.application}</strong></div>
		<div class="application" style="text-align: center;">
			<textarea name='applicationtext' cols='50' rows='10'></textarea><br />
			<input type='submit' name='sub' value='{$lang.applicate}' />
		</div>
	</form>
{else}
	{if !$smarty.get.cmode && $smarty.get.cid}
		<div class="clan_name"><strong>{$clanData.clanname} [{$clanData.clantag}]</strong></div>
		<div class="clan_info">
			<table>
				<tbody>
					<tr>
						<td class="description">
							{$lang.clanfounder}:
						</td>
						<td class="content" colspan="2">
							{$clanData.founder}
						</td>
					</tr>
					<tr>
						<td class="description">
							{$lang.members}:
						</td>
						<td class="content" colspan="2">
							{$clanData.userCount}
						</td>
					</tr>
					<tr>
						<td class="heading" colspan="3">{$lang.points}</td>
					</tr>
					<tr>
						<td class="description" style="text-align: center;">
							{$lang.buildings}
						</td>
						<td class="description" style="text-align: center;">
							{$lang.units}
						</td>
						<td class="description" style="text-align: center;">
							{$lang.total}
						</td>
					</tr>
					<tr>
						<td class="content" style="text-align: center;">
							{$clanData.buildingPoints|number_format:0:$lang.decimal:$lang.thousands}
						</td>
						<td class="content" style="text-align: center;">
							{$clanData.unitPoints|number_format:0:$lang.decimal:$lang.thousands}
						</td>
						<td class="content" style="text-align: center;">
							{$clanData.points|number_format:0:$lang.decimal:$lang.thousands}
						</td>
					</tr>
				</tbody>
			</table>
			{if $clanData.applications && $smarty.session.user->getCID() == $smarty.get.cid}
			<div class="applications">
				<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=3">{$lang.newapps|sprintf:$applicationCount:$applicationEnding}</a>
			</div>
			{/if}
		</div>
		<div class="settings">
			{if $smarty.session.user->getCID() == $smarty.get.cid && !$own_uid}
			<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=1">{$lang.memberlist}</a><br />
			<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=3">{$lang.msgtoall}</a><br />
			{if $smarty.session.user->getRankID() == 1}
			<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2">{$lang.clanoptions}</a><br />
			{/if}
			{/if}
			{if !$smarty.session.user->getCID()}
			<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;clanshow=1&amp;enter=1">{$lang.applicate}</a>
			{/if}
		</div>
		<div class="clear"></div>
		{if $clanData.public_text}
		<div class="public_text">
			<strong>{$lang.clandescription}</strong><br />
			{$clanData.public_text|nl2br}
		</div>
		{/if}
		{if $smarty.session.user->getCID() == $smarty.get.cid && $clanData.internal_text}
		<div class="internal_text">
			<strong>{$lang.claninternal}</strong><br />
			{$clanData.internal_text|nl2br}
		</div>
		{/if}
	{elseif $smarty.get.cmode == 1 && $smarty.session.user->getCID() == $smarty.get.cid}
		<div class="clan_name"><strong>{$lang.memberlist}</strong></div>
		<div class="members_list">
			<table>
				<thead>
					<tr>
						<th style="width: 30px;">&nbsp;</th>
						<th style="width: 150px;">{$lang.name}</th>
						<th>{$lang.rank}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$membersListData item=member name=foreachMembersList}
					<tr>
						<td style="width: 30px;">{$smarty.foreach.foreachMembersList.iteration}</td>
						<td style="width: 150px;">
							{if $usermapEnabled == 1}
							<a href="index.php?chose=usermap&amp;reguid={$member.uid}&amp;fromc={$encodeString}">
							{/if}
								{$member.nick}
							{if $usermapEnabled == 1}
							</a>
							{/if}
						</td>
						<td>{$member.rankname}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="backlink">
			<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}">{$lang.back}</a>
		</div>
	{elseif $smarty.get.cmode == 2 && $smarty.session.user->getCID() == $smarty.get.cid}
		{if !$smarty.get.umode}
			<script type="text/javascript">{literal}
				$(function() {
					$('#delete_clan_dialog').dialog({
						bgiframe: true,
						modal: true,
						autoOpen: false,
						buttons: {
							'{/literal}{$lang.cancel}{literal}': function() {
								$('#delete_clan_dialog').dialog('close');
							},
							'{/literal}{$lang.delete}{literal}': function() {
								window.location.href = 'index.php?chose=clan&cid={/literal}{$smarty.session.user->getCID()}{literal}&cmode=2&del=1';
							}
						}
					});
					$('#delete_clan').click(function() {
						$('#delete_clan_dialog').dialog('open');
					});
				});
			</script>{/literal}
			<div class="clan_name"><strong>{$clanData.clanname} [{$clanData.clantag}]</strong></div>
			<form class="info_texts" method="post" action="index.php?chose=clan&amp;cid={$smarty.session.user->getCID()}&amp;cmode=2">
				<div class="left">
					<div class="description">
						{$lang.clandescription}
						<img src="pictures/help.gif" alt="{$lang.help}" title="{$lang.clandescrinfo}" /><br />
						<textarea name="public_text" rows="10" cols="50">{$clanData.public_text}</textarea>
					</div>
					<div class="description">
						{$lang.claninternal}
						<img src="pictures/help.gif" alt="{$lang.help}" title="{$lang.claninterninfo}" /><br />
						<textarea name="intern" rows="10" cols="50">{$clanData.internal_text}</textarea>
					</div>
					<div class="button">
						<input type="submit" value="{$lang.change}" />
					</div>
				</div>
				<div class="right">
					<div><a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=1">{$lang.editranks}</a></div>
					<div><a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=2">{$lang.managemembers}</a></div>
					<div><a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=3">{$lang.applications} ({$clanData.applications})</a></div>
					<div><a id="delete_clan" href="javascript:">{$lang.deleteclan}</a></div>
					<div id="delete_clan_dialog">
						{$lang.reallydelete}
					</div>
					<div class="backlink" style="margin-top: 10px;">
						<a href="index.php?chose=clan&amp;cid={$smarty.session.user->getCID()}">{$lang.back}</a>
					</div>
				</div>
				<div class="clear"></div>
			</form>
		{elseif $smarty.get.umode == 1}
			{if !$smarty.get.do || $smarty.get.do == 'del'}
				<div class="clan_name"><strong>{$lang.rankmanagement}</strong></div>
				<div class="create_new_rank">
					<a href="index.php?chose=clan&amp;cid={$smarty.session.user->getCID()}&amp;cmode=2&amp;umode=1&amp;do=new">{$lang.createnewrank}</a>
				</div>
				<div class="clan_ranks">
					{if $deleted}
					<div class="rank_deleted">
						{$lang.rankdeleted}
					</div>
					{/if}
					<table>
						<thead>
							<tr>
								<th class="rank_name">{$lang.rank}</th>
								<th class="rank_options">{$lang.options}</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$clanRanks item=clanRank}
							<tr>
								<td class="rank_name">
									{$clanRank.rankname}{if $clanRank.standard} *{/if}
								</td>
								<td class="rank_options">
									<a href="index.php?chose=clan&amp;cid={$smarty.session.user->getCID()}&amp;cmode=2&amp;umode=1&amp;do=new&amp;rank={$clanRank.rankid}">{$lang.change}</a>
									{if $clanRank.rankid > 2}
									<a href="index.php?chose=clan&amp;cid={$smarty.session.user->getCID()}&amp;cmode=2&amp;umode=1&amp;do=del&amp;rank={$clanRank.rankid}">{$lang.delete}</a>
									{/if}
								</td>
							</tr>
							{/foreach}
							<tr>
								<td colspan="2">
									* = {$lang.standardrank}
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="backlink">
					<a href="index.php?chose=clan&amp;cid={$smarty.session.user->getCID()}&amp;cmode=2">{$lang.back}</a>
				</div>
			{elseif $smarty.get.do == 'new'}
				<div class="clan_name"><strong>{$lang.createnewrank}</strong></div>
				<form method='post' action='index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=1&amp;do=new{if $smarty.get.rank > 0}&amp;rank={$smarty.get.rank}{/if}'>
					<table class="new_clan_rank">
						<tr>
							<td class="title">{$lang.rankname}:</td>
							<td class="value">
								<input type="text" name="rankname" value="{$rankRes.rankname}" />
							</td>
						</tr>
						<tr>
							<td class="title">{$lang.standardrank}:</td>
							<td class="value">
								<input type="checkbox" name="standard"{if $rankRes.standard} checked="checked"{/if} />
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: center;">
								<input type="submit" name="ranksub" value="{if $smarty.get.rank > 0}{$lang.change}{else}{$lang.create}{/if}" />
							</td>
						</tr>
					</table>
				</form>
				<div class="backlink">
					<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=1">{$lang.back}</a>
				</div>
			{/if}
		{elseif $smarty.get.umode == 2}
			<div class="clan_name"><strong>{$lang.membermanagement}</strong></div>
			{if $changedRank}
			<div class="changed_rank">{$lang.rankchanged}</div>
			{/if}
			<div class="member_list">
				<table>
					<tr>
						<td class="user">{$lang.daimyo}</td>
						<td class="rank">{$lang.rank}</td>
					</tr>
					{foreach from=$userList item=user}
					<tr>
						<td class="user">
							<a href='index.php?&amp;reguid={$user.uid}&amp;profil=1&amp;fromc=10'>{$user.nick}</a>
						</td>
						<td class="rank">
							{if $user.uid == $smarty.session.user->getUID()}
								{foreach from=$rankList item=rankname key=rankid}
									{if $user.rankid == $rankid}
										{$rankname}
									{/if}
								{/foreach}
							{else}
								<form method="post" action="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=2">
									{html_options name=rankid options=$rankList selected=$user.rankid}&nbsp;<input type="submit" name="rankchange" value="{$lang.change}" />
									<input type="hidden" name="member" value="{$user.uid}" />
								</form>
							{/if}
						</td>
					</tr>
					{/foreach}
				</table>
			</div>
			<div class="backlink">
				<a href='index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2'>{$lang.back}</a>
			</div>
		{elseif $smarty.get.umode == 3}
			<div class="clan_name"><strong>{$lang.applications}</strong></div>
			{if !$smarty.get.appid}
				<div class="application_list">
					{if $applications}
						<table>
							<tr>
								<td class="applicant">{$lang.applicant}</td>
								<td class="date">{$lang.date}</td>
							</tr>
							{foreach from=$applications item=application}
							<tr>
								<td class="applicant">
									<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=3&amp;appid={$application.appid}">{$application.nick}</a>
								</td>
								<td class="date">{$application.create_datetime}</td>
							</tr>
							{/foreach}
						</table>
					{else}
						<div style="text-align: center; margin: 10px 0;">
							{$lang.noapps}
						</div>
					{/if}
				</div>
			{else}
				<div class="application">
					<form method='post' action='index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2&amp;umode=3'>
						<strong>{$lang.appfrom}{$application.nick}</strong><br />
						{$lang.date}: {$application.create_datetime}<br />
						<br />
						{$application.applicationtext|nl2br}
						<div class="buttons">
							<input type='submit' name='accept' value='{$lang.accept}' />&nbsp;
							<input type='submit' name='decline' value='{$lang.decline}' />
							<input type='hidden' name='entuid' value='{$application.uid}' />
							<input type='hidden' name='appid' value='{$smarty.get.appid}' />
						</div>
					</form>
				</div>
			{/if}
			<div class="backlink">
				<a href='index.php?chose=clan&amp;cid={$smarty.get.cid}&amp;cmode=2{if $smarty.get.appid}&amp;umode=3{/if}'>{$lang.back}</a>
			</div>
		{/if}
	{elseif $smarty.get.cmode == 3 && $smarty.session.user->getCID() == $smarty.get.cid && !$smarty.post.sent}
		<div class="clan_name"><strong>{$lang.msgtoall}</strong></div>
		{include file='write_message.tpl' recipient='' title=$title submit=$submit}
		<div class="backlink">
			<a href="index.php?chose=clan&amp;cid={$smarty.get.cid}">{$lang.back}</a>
		</div>
	{/if}
{/if}