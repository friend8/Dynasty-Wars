<div class="hearing">
	<div class="subheading">{$lang.hearing|htmlentities}</div>
	<div class="row">
		<div class="left">{$lang.suitor_text|htmlentities}:</div>
		<div class="middle">{$hearing.suitorNick|htmlentities}</div>
		<div class="right">
			{if (($hearing.suitor == $smarty.session.user->getUID() && !$hearing.judge) ||
				($smarty.session.user->getGameRank() >= 1 && $smarty.session.user->getUID() != $hearing.accused))
				&& !$own_uid && !$hearing.decision}
				<a href="index.php?chose=tribunal&amp;sub=hearings&amp;id={$hearing.tid}&amp;action=recall">{$lang.recall|htmlentities}</a>
			{/if}
		</div>
	</div>
	<div class="row">
		<div class="left">{$lang.accused_text|htmlentities}:</div>
		<div class="middle">{$hearing.accusedNick|htmlentities}</div>
		<div class="right">
			{if ($hearing.judge == $smarty.session.user->getUID() ||
				($smarty.session.user->getGameRank() >= 1 && $smarty.session.user->getUID() != $hearing.accused
				&& $smarty.session.user->getUID() != $hearing.suitor)) && !$own_uid && !$hearing.decision}
				<a href="javascript:;" onclick="showDecisionDialog(this, 'make_decission', 'lib/ajax/make_decision.php', '{$lang.save|htmlentities}', '{$lang.cancel|htmlentities}', 500)">{$lang.decide|htmlentities}</a>
				<div id="make_decission" class="hidden" title="{$lang.decide|htmlentities}">
					<form method="post" action="index.php?chose=tribunal" name="decide">
						<div class="row">
							<div class="left">{$lang.decision|htmlentities}:</div>
							<div class="right">
								<select name="decision">
									<option value="nocent">{$lang.nocent|htmlentities}</option>
									<option value="innocent">{$lang.innocent|htmlentities}</option>
									<option value="rejected">{$lang.rejected|htmlentities}</option>
									<option value="other">{$lang.other|htmlentities}</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="left">{$lang.reason|htmlentities}:</div>
							<div class="right">
								<textarea name="reason" rows="10" cols="50"></textarea>
							</div>
						</div>
						<input type="hidden" name="ajax_id" value="{$smarty.get.id}" />
					</form>
				</div>
			{/if}
		</div>
	</div>
	<div class="row">
		<div class="left">{$lang.cause_text|htmlentities}:</div>
		<div class="middle">{$hearing.cause.cause|htmlentities}</div>
		<div class="right">
			{if $smarty.session.user->getGameRank() >= 1 && !$own_uid && !$hearing.decision}
				<a href="javascript:;" onclick="blockComments(this, 'lib/ajax/block_comments.php', {if $hearing.block_comments == 1}0{else}1{/if}, '{$smarty.get.id}')">
					{if $hearing.block_comments == 1}{$lang.unblock_comments|htmlentities}{else}{$lang.block_comments|htmlentities}{/if}
				</a>
			{/if}
		</div>
	</div>
	<div class="row">
		<div class="left">{$lang.description|htmlentities}:</div>
		<div class="right" style="width: 500px;">{$hearing.parsedDescription}</div>
	</div>
	{if $hearing.decision}
		<div class="row">
			<div class="left">{$lang.decision|htmlentities}:</div>
			<div class="right" style="width: 500px;">{$lang[$hearing.decision]}</div>
		</div>
		<div class="row">
			<div class="left">{$lang.reason|htmlentities}:</div>
			<div class="right" style="width: 500px;">{$hearing.parsedReason}</div>
		</div>
	{/if}
	{if $hearing.arguments || $smarty.session.user->getGameRank() > 1}
		<div class="row">
			<div class="left" style="font-weight: bold; text-align: center; width: 290px;">{$lang.arguments_text|htmlentities}</div>
			<div class="right" style="font-weight: bold; text-align: center;">{$lang.comments_text|htmlentities}</div>
		</div>
		<div class="row">
			<div class="left" style="text-align: left; width: 300px;">
				{foreach from=$hearing.arguments item=argument}
					<div class="argument">
						{$lang.argument|htmlentities} #{$argument.aid}<span id="argument_approved{$argument.aid}">{$argument.approvedText|htmlentities}</span><br />
						<a href="javascript:;" onclick="showDialog('argument_{$argument.aid}', 500)">{$argument.message.title|htmlentities}</a><br />
						<div id="argument_{$argument.aid}" class="argument_text" title="{$argument.message.title|htmlentities}">
							{$argument.message.message|nl2br}
						</div>
						{$lang.added_by|sprintf:$argument.formattedDateAdded:$argument.fromNick|htmlentities}
						{if $smarty.session.user->getGameRank() > 1 && $smarty.session.user->getUID() != $hearing.accused
							&& $smarty.session.user->getUID() != $hearing.suitor && !$own_uid && !$hearing['decision']}<br />
							<span id="approval_links{$argument.aid}">
								{if ($argument.approved == -1 || !$argument.approved) && !$own_uid}<a href="javascript:;" onclick="argumentApproval('lib/ajax/argument_approval.php', 'accept', '{$argument.aid}');">{/if}{$lang.accept|htmlentities}{if ($argument.approved == -1 || !$argument.approved) && !$own_uid}</a>{/if}
								{if ($argument.approved == 1 || !$argument.approved) && !$own_uid}<a href="javascript:;" onclick="argumentApproval('lib/ajax/argument_approval.php', 'decline', '{$argument.aid}');">{/if}{$lang.decline}{if ($argument.approved == 1 || !$argument.approved) && !$own_uid}</a>{/if}
							</span>
						{/if}
					</div>
				{/foreach}
				{if !$hearing.decision && !$own_uid}
					<div class="argument" style="text-align: center;" id="new_argument">
						{if $hearing.suitor == $smarty.session.user->getUID() || $hearing.accused == $smarty.session.user->getUID() || $smarty.session.user->getGameRank() >= 1}
							<a href="javascript:;" onclick="showEditingDialog(this, 'new_argument_dialog', 'lib/ajax/add_argument.php', '{$lang.save|htmlentities}', '{$lang.cancel|htmlentities}')">{$lang.add_argument|htmlentities}</a>
						{/if}
						<div id="new_argument_dialog" class="hidden" title="{$lang.add_argument|htmlentities}">
							<form method="post" action="index.php?chose=tribunal" name="new_argument_form">
								<div>
									{$lang.argument|htmlentities}:
									<select name="msgid">
										<option value="0">&nbsp;</option>
										{foreach from=$hearing.messages item=message}
											<option value="{$message.msgid}"{if is_array($_POST['arguments']) && in_array($message['msgid'], $_POST['arguments'])} selected="selected"{/if}>{$message.title|htmlentities}</option>
										{/foreach}
									</select>
								</div>
								{if $smarty.session.user->getGameRank() >= 1}
									<div>
										{$lang.add_msgid|htmlentities}:
										<input type="text" name="msgid_manual" />
									</div>
								{/if}
								<input type="hidden" name="ajax_id" value="{$smarty.get.id}" />
							</form>
						</div>
					</div>
				{/if}
			</div>
			{$commentReadyScript}
			<div class="right" style="width: 300px;" id="comments"></div>
		</div>
	{/if}
</div>