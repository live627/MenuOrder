<?php
function MenuOrder() {
	global $txt, $context, $modSettings, $boardurl;

	$context['page_title'] = $txt['menu_order'];

	setupThemeContext();
	loadTemplate('MenuOrder');

	$context['menuorder'] = array();

	$i = 0;
	foreach($context['menu_buttons'] as $key => $button) {
		$context['menuorder'][$key] = $i;
		$i++;
	}
	$menubuttons_sorted = array();
	foreach($context['menuorder'] as $key => $button) {
		$menubuttons_sorted[$key] = $context['menu_buttons'][$key];
	}
	
	if (!empty($modSettings['menuorder_sortmethod'])) {
		$context['menuorder_sortmethod'] = $modSettings['menuorder_sortmethod'];
	} else {
		$context['menuorder_sortmethod'] = 'buttons';
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['edit'])) {
		$oldtitle = $context['menu_buttons'][$_REQUEST['edit']]['title'];
		$menutitles = array();
		foreach ($context['menu_buttons'] as $key => $value) {
			if (isset($value['no_change']) && $value['no_change'] == true) {
				$menutitles[$key] = '_no_change_';
			} else {
				$menutitles[$key] = $value['title'];
			}
		}
		$menutitles[$_REQUEST['edit']] = strip_tags($_REQUEST['menuitemtext']);

		updateSettings(array(
			'menuorder_titles' => serialize($menutitles),
		));

		$context['menu_buttons'][$_REQUEST['edit']]['title'] = $_REQUEST['menuitemtext'];

		$context['menuorder_info'] = sprintf($txt['menuorder_renamesuccess'], $oldtitle, $context['menu_buttons'][$_REQUEST['edit']]['title']);

		unset($_REQUEST['edit']);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['menusort_type'])) {
		$context['menuorder_sortmethod'] = $_REQUEST['menusort_type'];
		updateSettings(array(
			'menuorder_sortmethod' => $context['menuorder_sortmethod'],
		));
		$context['menuorder_info'] = sprintf($txt['menuorder_sortchanged'], $txt['menuorder_sortby'.$_REQUEST['menusort_type']]);
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['newmenuorder'])) {
		updateSettings(array(
			'menuorder' => $_REQUEST['newmenuorder'],
		));
		$modSettings['menuorder'] = $_REQUEST['newmenuorder'];
		setupMenuContext();
		$context['menuorder_info'] = $txt['menuorder_jssortsuccess'];
	} else if (isset($_REQUEST['hide'])) {
		$context['menuorder_visible'][$_REQUEST['hide']] = false;
		updateSettings(array(
			'menuorder_visible' => serialize($context['menuorder_visible']),
		));
		$context['menuorder_info'] = sprintf($txt['menuorder_hidesuccess'], $context['menu_buttons'][$_REQUEST['hide']]['title']);
	} else if (isset($_REQUEST['show'])) {
		$context['menuorder_visible'][$_REQUEST['show']] = true;
		updateSettings(array(
			'menuorder_visible' => serialize($context['menuorder_visible']),
		));
		$context['menuorder_info'] = sprintf($txt['menuorder_showsuccess'], $context['menu_buttons'][$_REQUEST['show']]['title']);
	} else if (isset($_REQUEST['up'])) {
		foreach($context['menuorder'] as $key => $button) {
			if ($button == $context['menuorder'][$_REQUEST['up']]-1) {
				$context['menuorder'][$key] = $button+1;
			}
		}
		$context['menuorder'][$_REQUEST['up']]--;
		asort($context['menuorder']);
		$context['menuorder_info'] = sprintf($txt['menuorder_upsuccess'], $context['menu_buttons'][$_REQUEST['up']]['title']);
	} else if (isset($_REQUEST['down'])) {
		foreach($context['menuorder'] as $key => $button) {
			if ($button == $context['menuorder'][$_REQUEST['down']]+1) {
				$context['menuorder'][$key] = $button-1;
			}
		}
		$context['menuorder'][$_REQUEST['down']]++;
		asort($context['menuorder']);
		$context['menuorder_info'] = sprintf($txt['menuorder_downsuccess'], $context['menu_buttons'][$_REQUEST['down']]['title']);
	}
	
	if (isset($_REQUEST['up']) || isset($_REQUEST['down'])) {
		updateSettings(array(
			'menuorder' => serialize($context['menuorder']),
		));
		setupMenuContext();
	}
	
	
	
	
	if ($context['menuorder_sortmethod'] == 'js') {
		$context['html_headers'] .= '
			<script type="text/javascript" src="' . $boardurl . '/Themes/default/scripts/menuorder.php"></script>
			<style type="text/css">
				.menuorder_placeholder {
					height:1.3em;
				}
			</style>
			<script type="text/javascript">
			$(document).ready(function() {
				$("#sortable").sortable({
					placeholder: "titlebg windowbg2 menuorder_placeholder",
					delay: 300,
					stop: function() {
						var r = "a:"+$("#sortable li").size()+":{";
						var o = 0;
						$("#sortable li").each(function() {
							r = r + "s:" + $(this).attr("id").length + ":\"" + $(this).attr("id") + "\";i:" + o + ";";
							o++;
						});
						r = r + "}";
						$("#newmenuorder").val(r);
						//alert("\' . addslashes($modSettings[\'menuorder\']) . \'\n" + r);
					}
				});
			});
			</script>

		';
	}
}
?>