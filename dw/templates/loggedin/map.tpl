{include file="header.tpl"}
<script language="javascript" type="text/javascript">
	var mapX = {$mapX};
	var mapY = {$mapY};
	var backgroundPath = '{$backgroundPath}';
	var uidList = JSON.parse('{$uidList}');
</script>
<div class="map">
	<div class="heading">{$lang.map}</div>
	<div class="map_body">
		<div class="navi_top">
			{strip}
			<img id="navi_top_left" src="pictures/map/arrow_leftup.png" name="leftup" onmouseover="changePic('leftup', 'i10')" onclick="changePic('leftup', 'i12')" onmouseout="changePic('leftup', 'i11')" alt="{$lang.arrows.up_left}" />
			<img id="navi_top" src="pictures/map/arrow_up.png" name="up" onmouseover="changePic('up', 'i13')" onclick="changePic('up', 'i15')" onmouseout="changePic('up', 'i14')" alt="{$lang.arrows.up}" />
			<img id="navi_top_right" src="pictures/map/arrow_rightup.png" name="rightup" onmouseover="changePic('rightup', 'i16')" onclick="changePic('rightup', 'i18')" onmouseout="changePic('rightup', 'i17')" alt="{$lang.arrows.up_right}" />
			{/strip}
		</div>
		<div class="navi_left">
			<img id="navi_left" src="pictures/map/arrow_left.png" name="left" onmouseover="changePic('left', 'i07')" onclick="changePic('left', 'i09')" onmouseout="changePic('left', 'i08')" alt="{$lang.arrows.left}" />
		</div>
		<div class="viewport">
			{foreach from=$mapData item=row key=yCoord}
			<div class="row y{$yCoord}">
				{foreach from=$row item=position}
				<div class="position x{$position.map_x}" style="background-image: url('{$backgroundPath}{$position.image}');">
					{if $position.uid && !$position.deactivated}
					<a href="index.php?chose=usermap&amp;reguid={$position.uid}&amp;fromc=map">
						<img class="city i1" src="{$backgroundPath}{if $position.uid}city{if $position.terrain == 4}_mountain{/if}{elseif $position.uid == -1}harbor{/if}.gif" />
					</a>
					{/if}
				</div>
				{/foreach}
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>
		<div class="navi_right">
			<img id="navi_right" src="pictures/map/arrow_right.png" name="right" onmouseover="changePic('right', 'i19')" onclick="changePic('right', 'i21')" onmouseout="changePic('right', 'i20')" alt="{$lang.arrows.right}" />
		</div>
		<div class="clear"></div>
		<div class="navi_bottom">
			{strip}
			<img id="navi_bottom_left" src="pictures/map/arrow_leftdown.png" name="leftdown" onmouseover="changePic('leftdown', 'i04')" onclick="changePic('leftdown', 'i06')" onmouseout="changePic('leftdown', 'i05')" alt="{$lang.arrows.down_left}" />
			<img id="navi_bottom" src="pictures/map/arrow_down.png" name="down" onmouseover="changePic('down', 'i01')" onmouseout="changePic('down', 'i02')" alt="{$lang.arrows.down}" />
			<img id="navi_bottom_right" src="pictures/map/arrow_rightdown.png" name="rightdown" onmouseover="changePic('rightdown', 'i22')" onclick="changePic('rightdown', 'i24')" onmouseout="changePic('rightdown', 'i23')" alt="{$lang.arrows.down_right}" />
			{/strip}
		</div>
		<div class="position_search">
			<form id="position_change" method="post" action="index.php?chose=map">
				<input type="text" maxlength="3" size="4" name="x" />:<input type="text" maxlength="3" size="4" name="y" />
				<input type="submit" value="{$lang.change}" />
			</form>
		</div>
	</div>
</div>
{include file="footer.tpl"}