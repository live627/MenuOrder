<?php
function template_main()
{
	global $txt, $context, $scripturl, $boarddir, $modSettings;

	// Table header.
	echo '
	<div id="manage_boards">
		<h3 class="titlebg"><span class="left"></span>
			', $txt['menu_order'], '
		</h3>';

	if (!empty($context['menuorder_info']))
		echo '
		<div class="information">
			<p>', $context['menuorder_info'], '</p>
		</div>';

	echo '
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">
				<form action="', $scripturl, '?action=admin;area=menu_order" method="post">
					', $txt['menuorder_howsort?'], '
					<select name="menusort_type">
						<option value="buttons"', (($context['menuorder_sortmethod'] == 'buttons') ? ' selected="selected"' : ''), '>', $txt['menuorder_sortbybuttons'], '</option>
						<option value="js"', (($context['menuorder_sortmethod'] == 'js') ? ' selected="selected"' : ''), '>', $txt['menuorder_sortbyjs'], '</option>
					</select>
					<input type="Submit" value="', $txt['menuorder_save'], '" />
				</form>
			</div>
			<span class="botslice"><span></span></span>
		</div>
		<br />
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">';
	if ($context['menuorder_sortmethod'] == 'buttons') {
		echo '<ul>';

		$alternate = false;
		foreach($context['menu_buttons'] as $key => $button) {
			$alternate = !$alternate;
			echo '
			<li class="windowbg', (($alternate) ? '' : '2'), '" style="padding-' . (($context['right_to_left']) ? 'right' : 'left') . ': 5px;', /*$board['move'] ? 'color: red;' : '',*/ '">
			<span class="align_left">';
			if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == $key) {
				echo '
				<form action="', $scripturl, '?action=admin;area=menu_order;edit=', $key, '" method="post">
				<input type="text" name="menuitemtext" value="', ((isset($button['no_change']) && $button['no_change']==true) ? '_no_change_' : $button['title']), '" />
				<input type="submit" value="', $txt['menuorder_save'], '" />
				</form>
				';
			} else {
				echo $button['title'];
			}
			echo '</span>
			<span class="align_right">
			<span class="modify_boards">',((isset($_REQUEST['edit']) && $_REQUEST['edit'] == $key) ? $txt['menuorder_edit'] : '<a href="' . $scripturl . '?action=admin;area=menu_order;edit=' . $key . '">' . $txt['menuorder_edit'] . '</a>'), '</span>';
			if (empty($context['menuorder_visible']) || (!empty($context['menuorder_visible'][$key]) && $context['menuorder_visible'][$key] == true)) {
				echo '<span class="modify_boards"><a href="' . $scripturl . '?action=admin;area=menu_order;hide=' . $key . '">' . $txt['menuorder_hide'] . '</a></span>';
			} else {
				echo '<span class="modify_boards"><a href="' . $scripturl . '?action=admin;area=menu_order;show=' . $key . '">' . $txt['menuorder_show'] . '</a></span>';
			}
			echo '<span class="modify_boards">',(($context['menuorder'][$key]==0) ? $txt['menuorder_up'] : '<a href="' . $scripturl . '?action=admin;area=menu_order;up=' . $key . '">' . $txt['menuorder_up'] . '</a>'), '</span>
			<span class="modify_boards">',((in_array($context['menuorder'][$key]+1,$context['menuorder'])) ? '<a href="' . $scripturl . '?action=admin;area=menu_order;down=' . $key . '">' . $txt['menuorder_down'] . '</a>' : $txt['menuorder_down']), '</span>
			</span><br style="clear: right;" />
			</li>';
		}
		echo '</ul>';
	} else if ($context['menuorder_sortmethod'] == 'js') {
		
		echo '<ul id="sortable">';

		$alternate = false;
		foreach($context['menu_buttons'] as $key => $button) {
			$alternate = !$alternate;
			echo '
			<li class="windowbg', (($alternate) ? '' : '2'), '" style="padding-' . (($context['right_to_left']) ? 'right' : 'left') . ': 5px;cursor:move;', /*$board['move'] ? 'color: red;' : '',*/ '" id="', $key, '">
			<span class="align_left">';
			if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == $key) {
				echo '
				<form action="', $scripturl, '?action=admin;area=menu_order;edit=', $key, '" method="post">
				<input type="text" name="menuitemtext" value="', (($button['no_change']==true) ? '_no_change_' : $button['title']), '" />
				<input type="submit" value="', $txt['menuorder_save'], '" />
				</form>
				';
			} else {
				echo $button['title'];
			}
			echo '</span>
			<span class="align_right">
			<span class="modify_boards">',((isset($_REQUEST['edit']) && $_REQUEST['edit'] == $key) ? $txt['menuorder_edit'] : '<a href="' . $scripturl . '?action=admin;area=menu_order;edit=' . $key . '">' . $txt['menuorder_edit'] . '</a>'), '</span>';
			if (empty($context['menuorder_visible']) || (!empty($context['menuorder_visible'][$key]) && $context['menuorder_visible'][$key] == true)) {
				echo '<span class="modify_boards"><a href="' . $scripturl . '?action=admin;area=menu_order;hide=' . $key . '">' . $txt['menuorder_hide'] . '</a></span>';
			} else {
				echo '<span class="modify_boards"><a href="' . $scripturl . '?action=admin;area=menu_order;show=' . $key . '">' . $txt['menuorder_show'] . '</a></span>';
			}
			//echo '<span class="modify_boards">',(($context['menuorder'][$key]==0) ? $txt['menuorder_up'] : '<a href="' . $scripturl . '?action=admin;area=menu_order;up=' . $key . '">' . $txt['menuorder_up'] . '</a>'), '</span>
			//<span class="modify_boards">',((in_array($context['menuorder'][$key]+1,$context['menuorder'])) ? '<a href="' . $scripturl . '?action=admin;area=menu_order;down=' . $key . '">' . $txt['menuorder_down'] . '</a>' : $txt['menuorder_down']), '</span>';
			echo '</span><br style="clear: right;" />
			</li>';
		}
		echo '</ul>
			<form action="', $scripturl, '?action=admin;area=menu_order" method="post">
			<input type="hidden" id="newmenuorder" name="newmenuorder" value="', (isset($modSettings['menuorder'])?htmlspecialchars($modSettings['menuorder']):''), '"/>
			<input type="submit" value="', $txt['menuorder_saveorder'], '" class="align_right" />
			<br class="clear" />
			';
	}
	echo '
			</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>
	<br class="clear" />';
}

?>