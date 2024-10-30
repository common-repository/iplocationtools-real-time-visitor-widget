<?php
/*
Plugin Name: IPLocationTools
Plugin URI: http://www.iplocationtools.com
Description:
Version: 1.1.16
Author: IP2Location
Author URI: http://www.iplocationtools.com
*/

$ip_location_tools = new IPLocationTools();

add_action('widgets_init', [$ip_location_tools, 'register']);
add_action('admin_menu', [$ip_location_tools, 'menu']);
add_action('admin_head', [$ip_location_tools, 'farbtastic']);
add_action('admin_enqueue_scripts', [$ip_location_tools, 'plugin_enqueues']);
add_action('wp_ajax_ip2location_country_blocker_submit_feedback', [$ip_location_tools, 'submit_feedback']);
add_action('admin_footer_text', [$ip_location_tools, 'admin_footer_text']);

class IPLocationTools
{
	public function activate()
	{
		if (!function_exists('register_sidebar_widget')) {
			return;
		}

		$options = ['title' => 'IPLocationTools'];

		if (!get_option('IPLocationTools')) {
			add_option('IPLocationTools', $options);
		} else {
			update_option('IPLocationTools', $options);
		}
	}

	public function deactivate()
	{
		delete_option('IPLocationTools');
	}

	public function control()
	{
		echo '<a href="options-general.php?page=' . basename(__FILE__) . '">Go to Settings</a>';
	}

	public function widget($args)
	{
		$options = get_option('IPLocationTools');

		$timeZone = [
			['value' => '-12', 'name' => '(GMT -12:00) Eniwetok, Kwajalein'],
			['value' => '-11', 'name' => '(GMT -11:00) Midway Island, Samoa'],
			['value' => '-10', 'name' => '(GMT -10:00) Hawaii'],
			['value' => '-9', 'name' => '(GMT -9:00) Alaska'],
			['value' => '-8', 'name' => '(GMT -8:00) Pacific Time (US &amp; Canada)'],
			['value' => '-7', 'name' => '(GMT -7:00) Mountain Time (US &amp; Canada)'],
			['value' => '-6', 'name' => '(GMT -6:00) Central America, El Salvador'],
			['value' => '-6', 'name' => '(GMT -6:00) Central Time (US &amp; Can.), Mexico'],
			['value' => '-5', 'name' => '(GMT -5:00) Eastern Time (US &amp; Can.)'],
			['value' => '-4.5', 'name' => '(GMT -4:30) Caracas'],
			['value' => '-4', 'name' => '(GMT -4:00) Atlantic Time (Can.)'],
			['value' => '-3.5', 'name' => '(GMT -3:30) Newfoundland'],
			['value' => '-3.5', 'name' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown'],
			['value' => '-2', 'name' => '(GMT -2:00) Mid-Atlantic'],
			['value' => '-1', 'name' => '(GMT -1:00 hour) Azores'],
			['value' => '0', 'name' => '(GMT) Reykjavik'],
			['value' => '0', 'name' => '(GMT) Western Europe Time, London, Lisbon'],
			['value' => '1', 'name' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid'],
			['value' => '2', 'name' => '(GMT +2:00) South African Standard Time'],
			['value' => '2', 'name' => '(GMT +2:00) Athens, Cairo'],
			['value' => '3', 'name' => '(GMT +3:00) Baghdad, Moscow, Riyadh'],
			['value' => '3.5', 'name' => '(GMT +3:30) Tehran'],
			['value' => '4', 'name' => '(GMT +4:00) Abu Dhabi, Muscat'],
			['value' => '4.5', 'name' => '(GMT +4:30) Kabul'],
			['value' => '5', 'name' => '(GMT +5:00) Islamabad, Karachi'],
			['value' => '5.5', 'name' => '(GMT +5:30) Bombay, Calcutta, Colombo, New Delhi'],
			['value' => '6', 'name' => '(GMT +6:00) Almaty, Dhaka'],
			['value' => '7', 'name' => '(GMT +7:00) Bangkok, Hanoi, Jakarta'],
			['value' => '8', 'name' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong'],
			['value' => '9', 'name' => '(GMT +9:00) Tokyo, Seoul, Osaka'],
			['value' => '9.5', 'name' => '(GMT +9:30) Adelaide'],
			['value' => '10', 'name' => '(GMT +10:00) Canbera, Melbourne, Sydney'],
			['value' => '10', 'name' => '(GMT +10:00) Brisbane'],
			['value' => '10.5', 'name' => '(GMT +10:30) Lord Howe Island'],
			['value' => '11', 'name' => '(GMT +11:00) Solomon Isl., New Caledonia'],
			['value' => '12', 'name' => '(GMT +12:00) Auckland, Fiji, Kamchatka'],
			['value' => '12.75', 'name' => '(GMT +12:45) Chatham Islands'],
			['value' => '13', 'name' => '(GMT +13:00) New Zealand Daylight Time, Tonga'],
		];

		echo $args['before_widget'] . $args['before_title'] . $options['title'] . $args['after_title'];
		echo '
		<script language="JavaScript">
		<!--
		iplocationtools_width = ' . $options['width'] . '; // The width of API, minimum 160px, maximum 400px
		iplocationtools_height = ' . $options['height'] . '; // The height of API, minimum 80px, maximum 800px
		iplocationtools_border_size = ' . $options['borderSize'] . '; // The size of the border
		iplocationtools_border_color = \'' . substr($options['borderColor'], 1, 6) . '\'; // Color of the border
		iplocationtools_bg_color = \'' . substr($options['backgroundColor'], 1, 6) . '\'; // Background color
		iplocationtools_font_color = \'' . substr($options['fontColor'], 1, 6) . '\'; // Font color
		iplocationtools_font_size = ' . $options['fontSize'] . '; // Font size
		iplocationtools_show_time = ' . $options['displayTime'] . '; // Enable time stamp, 1=Enable,0=Disable
		iplocationtools_time_zone = ' . $timeZone[$options['timeZone']]['value'] . '; // Change this to display correct time
		iplocationtools_custom_bg = \'' . $options['image'] . '\'; // Background image\'s URL
		//-->
		</script>
		<script language="javascript"
		src="https://www.iplocationtools.com/visitor.js?key=' . $options['key'] . '"></script>';

		echo $args['after_widget'];
	}

	public function menu()
	{
		add_submenu_page('options-general.php', 'IPLocationTools', 'IPLocationTools', 'administrator', basename(__FILE__), ['IPLocationTools', 'setting']);
	}

	public function setting()
	{
		$options = get_option('IPLocationTools');

		$borderSize = [1, 2, 3, 4, 5];
		$fontSize = [9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
		$timeZone = [
			['value' => '-12', 'name' => '(GMT -12:00) Eniwetok, Kwajalein'],
			['value' => '-11', 'name' => '(GMT -11:00) Midway Island, Samoa'],
			['value' => '-10', 'name' => '(GMT -10:00) Hawaii'],
			['value' => '-9', 'name' => '(GMT -9:00) Alaska'],
			['value' => '-8', 'name' => '(GMT -8:00) Pacific Time (US &amp; Canada)'],
			['value' => '-7', 'name' => '(GMT -7:00) Mountain Time (US &amp; Canada)'],
			['value' => '-6', 'name' => '(GMT -6:00) Central America, El Salvador'],
			['value' => '-6', 'name' => '(GMT -6:00) Central Time (US &amp; Can.), Mexico'],
			['value' => '-5', 'name' => '(GMT -5:00) Eastern Time (US &amp; Can.)'],
			['value' => '-4.5', 'name' => '(GMT -4:30) Caracas'],
			['value' => '-4', 'name' => '(GMT -4:00) Atlantic Time (Can.)'],
			['value' => '-3.5', 'name' => '(GMT -3:30) Newfoundland'],
			['value' => '-3.5', 'name' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown'],
			['value' => '-2', 'name' => '(GMT -2:00) Mid-Atlantic'],
			['value' => '-1', 'name' => '(GMT -1:00 hour) Azores'],
			['value' => '0', 'name' => '(GMT) Reykjavik'],
			['value' => '0', 'name' => '(GMT) Western Europe Time, London, Lisbon'],
			['value' => '1', 'name' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid'],
			['value' => '2', 'name' => '(GMT +2:00) South African Standard Time'],
			['value' => '2', 'name' => '(GMT +2:00) Athens, Cairo'],
			['value' => '3', 'name' => '(GMT +3:00) Baghdad, Moscow, Riyadh'],
			['value' => '3.5', 'name' => '(GMT +3:30) Tehran'],
			['value' => '4', 'name' => '(GMT +4:00) Abu Dhabi, Muscat'],
			['value' => '4.5', 'name' => '(GMT +4:30) Kabul'],
			['value' => '5', 'name' => '(GMT +5:00) Islamabad, Karachi'],
			['value' => '5.5', 'name' => '(GMT +5:30) Bombay, Calcutta, Colombo, New Delhi'],
			['value' => '6', 'name' => '(GMT +6:00) Almaty, Dhaka'],
			['value' => '7', 'name' => '(GMT +7:00) Bangkok, Hanoi, Jakarta'],
			['value' => '8', 'name' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong'],
			['value' => '9', 'name' => '(GMT +9:00) Tokyo, Seoul, Osaka'],
			['value' => '9.5', 'name' => '(GMT +9:30) Adelaide'],
			['value' => '10', 'name' => '(GMT +10:00) Canbera, Melbourne, Sydney'],
			['value' => '10', 'name' => '(GMT +10:00) Brisbane'],
			['value' => '10.5', 'name' => '(GMT +10:30) Lord Howe Island'],
			['value' => '11', 'name' => '(GMT +11:00) Solomon Isl., New Caledonia'],
			['value' => '12', 'name' => '(GMT +12:00) Auckland, Fiji, Kamchatka'],
			['value' => '12.75', 'name' => '(GMT +12:45) Chatham Islands'],
			['value' => '13', 'name' => '(GMT +13:00) New Zealand Daylight Time, Tonga'],
		];

		if ($_POST['iplocationtools-title']) {
			if (!preg_match('/^[1-9][0-9]+$/', $_POST['iplocationtools-width'])) {
				$_POST['iplocationtools-width'] = 200;
			}
			if (!preg_match('/^[1-9][0-9]+$/', $_POST['iplocationtools-height'])) {
				$_POST['iplocationtools-height'] = 400;
			}
			if (!in_array($_POST['iplocationtools-border-size'], $borderSize)) {
				$_POST['iplocationtools-border-size'] = 1;
			}
			if (!in_array($_POST['iplocationtools-font-size'], $fontSize)) {
				$_POST['iplocationtools-font-size'] = 11;
			}
			if (!isset($timeZone)) {
				$_POST['iplocationtools-time-zone'] = 16;
			}
			if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $_POST['iplocationtools-border-color'])) {
				$_POST['iplocationtools-border-color'] = $options['borderColor'];
			}
			if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $_POST['iplocationtools-font-color'])) {
				$_POST['iplocationtools-font-color'] = $options['fontColor'];
			}
			if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $_POST['iplocationtools-bg-color'])) {
				$_POST['iplocationtools-bg-color'] = $options['backgroundColor'];
			}

			$data['title'] = strip_tags(stripslashes($_POST['iplocationtools-title']));
			$data['key'] = strip_tags(stripslashes($_POST['iplocationtools-key']));
			$data['width'] = strip_tags(stripslashes($_POST['iplocationtools-width']));
			$data['height'] = strip_tags(stripslashes($_POST['iplocationtools-height']));
			$data['borderSize'] = strip_tags(stripslashes($_POST['iplocationtools-border-size']));
			$data['borderColor'] = strip_tags(stripslashes($_POST['iplocationtools-border-color']));
			$data['fontSize'] = strip_tags(stripslashes($_POST['iplocationtools-font-size']));
			$data['fontColor'] = strip_tags(stripslashes($_POST['iplocationtools-font-color']));
			$data['displayTime'] = ($_POST['iplocationtools-display-time'] == 1) ? 1 : 0;
			$data['timeZone'] = strip_tags(stripslashes($_POST['iplocationtools-time-zone']));
			$data['backgroundColor'] = strip_tags(stripslashes($_POST['iplocationtools-bg-color']));
			$data['image'] = strip_tags(stripslashes($_POST['iplocationtools-image']));

			update_option('IPLocationTools', $data);
			$options = get_option('IPLocationTools');

			echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved.</strong></p></div> ';
		}

		if (!is_array($options)) {
			$options = [
			'title'           => 'Online Visitors',
			'key'             => '',
			'width'           => '200',
			'height'          => '400',
			'borderSize'      => '1',
			'borderColor'     => '#000000',
			'fontSize'        => '11',
			'fontColor'       => '#000000',
			'displayTime'     => '1',
			'timeZone'        => 16,
			'backgroundColor' => '#FFFFFF',
			'image'           => '',
		];
		}

		$borderSizeOptions = '';
		foreach ($borderSize as $size) {
			$borderSizeOptions .= '<option value="' . $size . '"' . (($size == $options['borderSize']) ? ' selected="selected"' : '') . '> ' . $size . '</option>';
		}

		$fontSizeOptions = '';
		foreach ($fontSize as $size) {
			$fontSizeOptions .= '<option value="' . $size . '"' . (($size == $options['fontSize']) ? ' selected="selected"' : '') . '> ' . $size . 'px</option>';
		}

		$timeZoneOptions = '';
		$index = 0;
		foreach ($timeZone as $val) {
			$timeZoneOptions .= '<option title="' . $val['name'] . '" value="' . $index . '"' . (($index == $options['timeZone']) ? ' selected="selected"' : '') . '> ' . $val['name'] . '</option>';
			++$index;
		}

		echo '
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>IPLocationTools Settings</h2>
			<p>&nbsp;</p>
			<form id="form-iplocationtools" method="post">
			<table>
			<tr>
				<td>Title</td>
				<td><input style="width:200px;" name="iplocationtools-title" type="text" value="' . htmlspecialchars($options['title'], ENT_QUOTES) . '" /></td>
				<td rowspan="17" valign="top">
					<div style="margin:10px 50px;min-height:600px;min-width:200px;">
						<script language="JavaScript">
						<!--
						var iplocationtools_width = ' . $options['width'] . '; // The width of API, minimum 160px, maximum 400px
						var iplocationtools_height = ' . $options['height'] . '; // The height of API, minimum 80px, maximum 800px
						var iplocationtools_border_size = ' . $options['borderSize'] . '; // The size of the border
						var iplocationtools_border_color = \'' . substr($options['borderColor'], 1, 6) . '\'; // Color of the border
						var iplocationtools_bg_color = \'' . substr($options['backgroundColor'], 1, 6) . '\'; // Background color
						var iplocationtools_font_color = \'' . substr($options['fontColor'], 1, 6) . '\'; // Font color
						var iplocationtools_font_size = ' . $options['fontSize'] . '; // Font size
						var iplocationtools_show_time = ' . $options['displayTime'] . '; // Enable time stamp, 1=Enable,0=Disable
						var iplocationtools_time_zone = ' . $timeZone[$options['timeZone']]['value'] . '; // Change this to display correct time
						var iplocationtools_custom_bg = \'' . $options['image'] . '\'; // Background image\'s URL
						//-->
						</script>
						<div id="preview">
							<script language="javascript" src="https://www.iplocationtools.com/visitor.js?key=' . $options['key'] . '"></script>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>API Key (<a href="https://www.iplocationtools.com/join.html" target="_blank">Get key</a>)</td>
				<td><input style="width:200px;" name="iplocationtools-key" type="text" value="' . htmlspecialchars($options['key'], ENT_QUOTES) . '" /></td>
			</tr>
			<tr>
				<td>Dimension</td>
				<td><input style="width:60px;" name="iplocationtools-width" type="text" value="' . htmlspecialchars($options['width'], ENT_QUOTES) . '" onblur="iplocationtools_width=this.value;refreshPreview();" /><i>px</i> x <input style="width:60px;" id="iplocationtools-height" name="iplocationtools-height" type="text" value="' . htmlspecialchars($options['height'], ENT_QUOTES) . '" onblur="iplocationtools_height=this.value;refreshPreview();" /><i>px</i></td>
			</tr>
			<tr>
				<td>Border Size</td>
				<td>
				<select name="iplocationtools-border-size" style="width:200px;" onchange="iplocationtools_border_size=this.value;refreshPreview();">
					' . $borderSizeOptions . '
				</select>
				</td>
			</tr>
			<tr>
				<td>Border Color</td>
				<td><input style="width:200px;" name="iplocationtools-border-color" id="iplocationtools-border-color" type="text" value="' . htmlspecialchars($options['borderColor'], ENT_QUOTES) . '" maxlength="7" class="color-picker" onblur="iplocationtools_border_color=this.value.replace(\'#\', \'\');refreshPreview();" /></td>
			</tr>
			<tr>
				<td></td>
				<td><div id="farbtastic-iplocationtools-border-color"></div></td>
			</tr>
			<tr>
				<td>Background Color</td>
				<td><input style="width:200px;" name="iplocationtools-bg-color" id="iplocationtools-bg-color" type="text" value="' . htmlspecialchars($options['backgroundColor'], ENT_QUOTES) . '" maxlength="7" class="color-picker" onblur="iplocationtools_bg_color=this.value.replace(\'#\', \'\');refreshPreview();" /></td>
			</tr>
			<tr>
				<td></td>
				<td><div id="farbtastic-iplocationtools-bg-color"></div></td>
			</tr>
			<tr>
				<td>Font Size</td>
				<td>
				<select name="iplocationtools-font-size" style="width:200px;" onchange="iplocationtools_font_size=this.value;refreshPreview();">
					' . $fontSizeOptions . '
				</select>
				</td>
			</tr>
			<tr>
				<td>Font Color</td>
				<td><input style="width:200px;" name="iplocationtools-font-color" id="iplocationtools-font-color" type="text" value="' . htmlspecialchars($options['fontColor'], ENT_QUOTES) . '" maxlength="7" class="color-picker" onblur="iplocationtools_font_color=this.value.replace(\'#\', \'\');refreshPreview();" /></td>
			</tr>
			<tr>
				<td></td>
				<td><div id="farbtastic-iplocationtools-font-color"></div></td>
			</tr>
			<tr>
				<td>Display Time</td>
				<td>
				<input type="radio" name="iplocationtools-display-time" id="iplocationtools-time-on" value="1"' . (($options['displayTime'] == 1) ? ' checked="checked"' : '') . ' onclick="iplocationtools_show_time=1;refreshPreview();" /> <label for="iplocationtools-time-on">On</label>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="iplocationtools-display-time" id="iplocationtools-time-off" value="0"' . (($options['displayTime'] == 0) ? ' checked="checked"' : '') . ' onclick="iplocationtools_show_time=0;refreshPreview();" /> <label for="iplocationtools-time-off">Off</label>
				</td>
			</tr>
			<tr>
				<td>Time Zone</td>
				<td>
				<select name="iplocationtools-time-zone" style="width:200px;" onchange="iplocationtools_time_zone=this.value;refreshPreview();">
					' . $timeZoneOptions . '
				</select>
				</td>
			</tr>
			<tr>
				<td>Background Image</td>
				<td>
				<select name="iplocationtools-image" style="width:200px;" onchange="iplocationtools_custom_bg=this.value;refreshPreview();">
					<option value="">Default</option>
					<option value="https://www.iplocationtools.com/assets/img/bg2.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg2.jpg') ? ' selected' : '') . '> #2</option>
					<option value="https://www.iplocationtools.com/assets/img/bg3.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg3.jpg') ? ' selected' : '') . '> #3</option>
					<option value="https://www.iplocationtools.com/assets/img/bg4.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg4.jpg') ? ' selected' : '') . '> #4</option>
					<option value="https://www.iplocationtools.com/assets/img/bg5.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg5.jpg') ? ' selected' : '') . '> #5</option>
					<option value="https://www.iplocationtools.com/assets/img/bg6.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg6.jpg') ? ' selected' : '') . '> #6</option>
					<option value="https://www.iplocationtools.com/assets/img/bg7.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg7.jpg') ? ' selected' : '') . '> #7</option>
					<option value="https://www.iplocationtools.com/assets/img/bg8.jpg"' . (($options['image'] == 'https://www.iplocationtools.com/assets/img/bg8.jpg') ? ' selected' : '') . '> #8</option>
				</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" class="button-primary" value="Save Changes" />
				</td>
			</tr>
			</table>
			</form>

			<p>&nbsp;</p>

			<p>If you like this plugin, please leave us a <a href="https://wordpress.org/support/view/plugin-reviews/iplocationtools-real-time-visitor-widget">5 stars rating</a>. Thank You!</p>
		</div>

		<script type="text/javascript">
			jQuery(function(){
				jQuery(document).ready(function() {
				    jQuery(\'.color-picker\').each(function() {
				    	jQuery(\'#farbtastic-\'+this.id).hide();
				    	jQuery(\'#farbtastic-\'+this.id).farbtastic(this);
				    	jQuery(this).click(function(){jQuery(\'#farbtastic-\'+this.id).fadeIn()});
						jQuery(this).blur(function(){jQuery(\'#farbtastic-\'+this.id).hide()});
					});
				});
			});

			jQuery(document).mousedown(function() {
				jQuery(\'.color-picker\').each(function() {
					var display = jQuery(\'#\'+this.id).css(\'display\');
					if(display == \'block\') jQuery(\'#\'+this.id).fadeOut();
				});
			});

			function refreshPreview(){
				var $script = jQuery(\'<script>\').attr({ type: \'text/javascript\', src: \'https://www.iplocationtools.com/visitor.js?key=' . $options['key'] . '&target=preview\' });
				jQuery(\'#preview\').empty().append($script);
			}
		</script>

		';
	}

	public function farbtastic()
	{
		global $current_screen;

		if ($current_screen->id == 'IPLocationTools.php') {
			wp_enqueue_style('farbtastic');
			wp_enqueue_script('farbtastic');
		}
	}

	public function register()
	{
		wp_register_sidebar_widget('IPLocationTools_Widget', 'IPLocationTools Widget', ['IPLocationTools', 'widget']);
		wp_register_widget_control('IPLocationTools_Control', 'IPLocationTools Control', ['IPLocationTools', 'control']);

		wp_enqueue_style('farbtastic');
		wp_enqueue_script('farbtastic');
	}

	public function plugin_enqueues($hook)
	{
		if ($hook == 'plugins.php') {
			// Add in required libraries for feedback modal
			wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_style('wp-jquery-ui-dialog');

			wp_enqueue_script('iplocationtools_real_time_visit_widget_admin_script', plugins_url('/assets/js/feedback.js', __FILE__), ['jquery'], null, true);
		}
	}

	public function admin_footer_text($footer_text)
	{
		$plugin_name = substr(basename(__FILE__), 0, strpos(basename(__FILE__), '.'));
		$current_screen = get_current_screen();

		if (($current_screen && strpos($current_screen->id, $plugin_name) !== false)) {
			$footer_text .= sprintf(
				__('Enjoyed %1$s? Please leave us a %2$s rating. A huge thanks in advance!', $plugin_name),
				'<strong>' . __('IPLocationTools Real Time Visitor Widget', $plugin_name) . '</strong>',
				'<a href="https://wordpress.org/support/plugin/' . $plugin_name . '/reviews/?filter=5/#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		if ($current_screen->id == 'plugins') {
			return $footer_text . '
			<div id="iplocationtools-real-time-visitor-widget-feedback-modal" class="hidden" style="max-width:800px">
				<span id="iplocationtools-real-time-visitor-widget-feedback-response"></span>
				<p>
					<strong>Would you mind sharing with us the reason to deactivate the plugin?</strong>
				</p>
				<p>
					<label>
						<input type="radio" name="iplocationtools-real-time-visitor-widget-feedback" value="1"> I no longer need the plugin
					</label>
				</p>
				<p>
					<label>
						<input type="radio" name="iplocationtools-real-time-visitor-widget-feedback" value="2"> I couldn\'t get the plugin to work
					</label>
				</p>
				<p>
					<label>
						<input type="radio" name="iplocationtools-real-time-visitor-widget-feedback" value="3"> The plugin doesn\'t meet my requirements
					</label>
				</p>
				<p>
					<label>
						<input type="radio" name="iplocationtools-real-time-visitor-widget-feedback" value="4"> Other concerns
						<br><br>
						<textarea id="iplocationtools-real-time-visitor-widget-feedback-other" style="display:none;width:100%"></textarea>
					</label>
				</p>
				<p>
					<div style="float:left">
						<input type="button" id="iplocationtools-real-time-visitor-widget-submit-feedback-button" class="button button-danger" value="Submit & Deactivate" />
					</div>
					<div style="float:right">
						<a href="#">Skip & Deactivate</a>
					</div>
				</p>
			</div>';
		}

		return $footer_text;
	}

	public function submit_feedback()
	{
		$feedback = (isset($_POST['feedback'])) ? $_POST['feedback'] : '';
		$others = (isset($_POST['others'])) ? $_POST['others'] : '';

		$options = [
			1 => 'I no longer need the plugin',
			2 => 'I couldn\'t get the plugin to work',
			3 => 'The plugin doesn\'t meet my requirements',
			4 => 'Other concerns' . (($others) ? (' - ' . $others) : ''),
		];

		if (isset($options[$feedback])) {
			if (!class_exists('WP_Http')) {
				include_once ABSPATH . WPINC . '/class-http.php';
			}

			$request = new WP_Http();
			$response = $request->request('https://www.ip2location.com/wp-plugin-feedback?' . http_build_query([
				'name'    => 'iplocationtools-real-time-visitor-widget',
				'message' => $options[$feedback],
			]), ['timeout' => 5]);
		}
	}
}
