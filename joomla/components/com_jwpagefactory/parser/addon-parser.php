<?php
/**
 * @author       JoomWorker
 * @email        info@joomla.work
 * @url          http://www.joomla.work
 * @copyright    Copyright (c) 2010 - 2019 JoomWorker
 * @license      GNU General Public License version 2 or later
 * @date         2019/01/01 09:30
 */
//no direct accees
defined('_JEXEC') or die ('Restricted access');

use Joomla\Registry\Registry;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once __DIR__ . '/addons.php';
require_once __DIR__ . './../helpers/helper.php';

require_once JPATH_ROOT . '/administrator/components/com_jwpagefactory/builder/classes/base.php';
require_once JPATH_ROOT . '/administrator/components/com_jwpagefactory/builder/classes/config.php';

class JwpfAddonParser
{
	public static $loaded_addon = array();
	public static $css_content = array();
	public static $module_css_content = array();
	public static $js_content = '';
	private static $jwpagefactoryAddonTags = array();
	private static $template = '';
	public static $authorised = array();
	public static $addon_interactions = array();

	public static function addAddon($tag, $func)
	{
		if (is_callable($func))
			self::$jwpagefactoryAddonTags[$tag] = $func;
	}

	public static function jwDoAddon($content)
	{

		if (false === strpos($content, '[')) {
			return $content;
		}
		if (empty(self::$jwpagefactoryAddonTags) || !is_array(self::$jwpagefactoryAddonTags))
			return $content;
		$pattern = self::getAddonRegex();
		return preg_replace_callback("/$pattern/s", array('JwpfAddonParser', 'doAddonTag'), $content);
	}

	/**
	 * Import/Include addon file
	 *
	 * @param string $file_name The addon name. Optional
	 *
	 * @since 1.0.8
	 */
	public static function getAddonPath($addon_name = '')
	{

		$template_path = JPATH_ROOT . '/templates/' . self::$template;
		$plugins = self::getPluginsAddons();

		if (file_exists($template_path . '/jwpagefactory/addons/' . $addon_name . '/site.php')) {
			return $template_path . '/jwpagefactory/addons/' . $addon_name;
		} elseif (file_exists(JPATH_ROOT . '/components/com_jwpagefactory/addons/' . $addon_name . '/site.php')) {
			return JPATH_ROOT . '/components/com_jwpagefactory/addons/' . $addon_name;
		} else {
			// Load from plugin
			if (isset($plugins[$addon_name]) && $plugins[$addon_name]) {
				return $plugins[$addon_name];
			}
		}
	}


	private static function getAddonRegex()
	{
		$tagnames = array_keys(self::$jwpagefactoryAddonTags);
		$tagregexp = join('|', array_map('preg_quote', $tagnames));
		// WARNING! Do not change this regex without changing do_addon_tag() and strip_addon_tag()
		// Also, see addon_unautop() and shortcode.js.
		return
			'\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)"                     // 2: Shortcode name
			. '(?![\\w-])'                       // Not followed by word character or hyphen
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			. '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			. '(?:'
			. '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			. '[^\\]\\/]*'               // Not a closing bracket or forward slash
			. ')*?'
			. ')'
			. '(?:'
			. '(\\/)'                        // 4: Self closing tag ...
			. '\\]'                          // ... and closing bracket
			. '|'
			. '\\]'                          // Closing bracket
			. '(?:'
			. '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			. '[^\\[]*+'             // Not an opening bracket
			. '(?:'
			. '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			. '[^\\[]*+'         // Not an opening bracket
			. ')*+'
			. ')'
			. '\\[\\/\\2\\]'             // Closing shortcode tag
			. ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}


	private static function doAddonTag($m)
	{
		// allow [[foo]] syntax for escaping a tag
		if ($m[1] == '[' && $m[6] == ']') {
			return substr($m[0], 1, -1);
		}
		$tag = $m[2];
		$attr = self::addonParseAtts($m[3]);
		if (isset($m[5])) {
			// enclosing tag - extra parameter
			return $m[1] . call_user_func(self::$jwpagefactoryAddonTags[$tag], $attr, $m[5], $tag) . $m[6];
		} else {
			// self-closing tag
			return $m[1] . call_user_func(self::$jwpagefactoryAddonTags[$tag], $attr, null, $tag) . $m[6];
		}
	}


	private static function addonParseAtts($text)
	{
		$atts = array();
		$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
			foreach ($match as $m) {
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) and strlen($m[7]))
					$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
					$atts[] = stripcslashes($m[8]);
			}
		} else {
			$atts = ltrim($text);
		}
		return $atts;
	}


	public static function getAddons()
	{
		self::$template = self::getTemplateName();

		require_once JPATH_ROOT . '/components/com_jwpagefactory/addons/module/site.php';//include module manually

		$template_path = JPATH_ROOT . '/templates/' . self::$template;
		$tmpl_folders = array();
		if (file_exists($template_path . '/jwpagefactory/addons')) {
			$tmpl_folders = JFolder::folders($template_path . '/jwpagefactory/addons');
		}


		$folders = JFolder::folders(JPATH_ROOT . '/components/com_jwpagefactory/addons');
		if ($tmpl_folders) {
			$merge_folders = array_merge($folders, $tmpl_folders);
			$folders = array_unique($merge_folders);
		}

		if (count((array)$folders)) {
			foreach ($folders as $folder) {
				$tmpl_file_path = $template_path . '/jwpagefactory/addons/' . $folder . '/site.php';
				$com_file_path = JPATH_ROOT . '/components/com_jwpagefactory/addons/' . $folder . '/site.php';
				if ($folder != 'module') {
					if (file_exists($tmpl_file_path)) {
						require_once $tmpl_file_path;
					} else if (file_exists($com_file_path)) {
						require_once $com_file_path;
					}
				}
			}
		}
	}

	private function checkObjectKeyValue($object)
	{
		if (!is_object($object)) {
			return flase;
		}
	}

	public static function viewAddons($content, $fluid = 0, $pageName = 'none')
	{

		JwPageFactoryBase::loadAddons();
		$addon_list = JwAddonsConfig::$addons;

		self::$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

		$layout_path = JPATH_ROOT . '/components/com_jwpagefactory/layouts';

		$layouts = new stdClass;

		$layouts->row_start = new JLayoutFile('row.start', $layout_path);
		$layouts->row_end = new JLayoutFile('row.end', $layout_path);
		$layouts->row_css = new JLayoutFile('row.css', $layout_path);

		$layouts->column_start = new JLayoutFile('column.start', $layout_path);
		$layouts->column_end = new JLayoutFile('column.end', $layout_path);
		$layouts->column_css = new JLayoutFile('column.css', $layout_path);

		$layouts->addon_start = new JLayoutFile('addon.start', $layout_path);
		$layouts->addon_end = new JLayoutFile('addon.end', $layout_path);
		$layouts->addon_css = new JLayoutFile('addon.css', $layout_path);

		$doc = JFactory::getDocument();

		if (is_array($content)) {
			$output = '';

			foreach ($content as $row) {
				$row->settings->dynamicId = $row->id;

				// Row Visibility and ACL
				if (isset($row->visibility) && !$row->visibility) {
					continue;
				}

				if ($fluid == 1 || (isset($row->type) && $row->type == 'inner_row')) {
					$row->settings->fullscreen = 1;
				}

				$row_css = $layouts->row_css->render(array('options' => $row->settings));
				if ($pageName == 'module') {
					array_push(self::$module_css_content, $row_css);
				} else {
					array_push(self::$css_content, $row_css);
				}

				$output .= $layouts->row_start->render(array('options' => $row->settings));

				foreach ($row->columns as $column) {

					$column->settings->cssClassName = $column->class_name;
					$column->settings->cssClassName = str_replace('column-parent ', '', $column->settings->cssClassName);
					$column->settings->cssClassName = str_replace('active-column-parent', '', $column->settings->cssClassName);
					$column->settings->dynamicId = $column->id;

					// Column Visibility and ACL
					if (isset($column->visibility) && !$column->visibility) {
						continue;
					}

					$column_css = $layouts->column_css->render(array('options' => $column->settings));
					if ($pageName == 'module') {
						array_push(self::$module_css_content, $column_css);
					} else {
						array_push(self::$css_content, $column_css);
					}

					$output .= $layouts->column_start->render(array('options' => $column->settings));

					foreach ($column->addons as $key => $addon) {

						// interaction
						if (isset($addon->settings->mouse_movement) || isset($addon->settings->while_scroll_view)) {
							$selectors = ['while_scroll_view', 'mouse_movement'];
							self::parseInteractions($addon->id, $addon->settings, $selectors);
						}

						// Addon Visibility and ACL
						if (isset($addon->visibility) && !$addon->visibility) {
							continue;
						}

						// ACL
						$access = self::checkAddonACL($addon);
						if (!$access) {
							continue;
						} // End ACL

						if (isset($addon->type) && $addon->type === 'inner_row') {
							$newPageName = $pageName == 'module' ? 'module' : 'none';
							$output .= self::viewAddons(array($addon), 0, $newPageName);
						} else {
							$addon->settings->row_id = $row->id;
							$addon->settings->column_id = $column->id;
							$output .= self::getAddonHtmlView($addon, $layouts, $pageName);
						}
					}

					$output .= $layouts->column_end->render(array('options' => $column->settings));
				}
				$output .= $layouts->row_end->render(array('options' => $row->settings));
			}

			// interaction js
			if (count(self::$addon_interactions) > 0 && $pageName != 'none' && $pageName != 'module') {
				$doc->addScriptDeclaration('var addonInteraction = ' . json_encode(self::$addon_interactions) . ';');
			}

			if ($pageName == 'module') {
				return JwpfAddonParser::jwDoAddon($output) . '<style type="text/css">' . self::convertCssArrayToString(self::minifyCss(self::$module_css_content)) . '</style>';
			} else {
				if ($pageName != 'none') {
					$app = JFactory::getApplication();
					$params = $app->getParams('com_jwpagefactory');
					$production_mode = $params->get('production_mode', 0);

					$inline_css = self::convertCssArrayToString(self::minifyCss(self::$css_content));

					if ($production_mode) {
						$css_folder_path = JPATH_ROOT . '/media/com_jwpagefactory/css';
						$css_file_path = $css_folder_path . '/' . $pageName . '.css';
						$css_file_url = JURI::base(true) . '/media/com_jwpagefactory/css/' . $pageName . '.css';

						if (!JFolder::exists($css_folder_path)) {
							JFolder::create($css_folder_path);
						}

						file_put_contents($css_file_path, $inline_css);

						if (file_exists($css_file_path)) {
							$doc->addStylesheet($css_file_url);
						} else {
							$doc->addStyleDeclaration($inline_css);
						}
					} else {
						$doc->addStyleDeclaration($inline_css);
					}
				}
				return JwpfAddonParser::jwDoAddon($output);
			}
		} else {
			return '<p>' . $content . '</p>';
		}

	}

	public static function getAddonHtmlView($addon, $layouts, $pageName = 'none')
	{

		$addon_list = JwAddonsConfig::$addons;

		$addon_name = $addon->name;
		$class_name = 'JwpagefactoryAddon' . ucfirst($addon_name);
		$addon_path = JwpfAddonParser::getAddonPath($addon_name);

		$doc = JFactory::getDocument();

		$output = '';

		if (file_exists($addon_path . '/site.php')) {

			$addon_options = array();
			if (isset($addon_list[$addon->name]['attr']) && $addon_list[$addon->name]['attr']) {
				$addon_groups = $addon_list[$addon->name]['attr'];
				if (is_array($addon_groups)) {
					foreach ($addon_groups as $addon_group) {
						$addon_options += $addon_group;
					}
				}
			}

			foreach ($addon->settings as $key => &$setting) {

				if (isset($setting->md)) {
					$md = isset($setting->md) ? $setting->md : "";
					$sm = isset($setting->sm) ? $setting->sm : "";
					$xs = isset($setting->xs) ? $setting->xs : "";
					$unit = (isset($setting->unit)) ? $setting->unit : "";
					$setting = $md . $unit;
					$addon->settings->{$key . '_sm'} = $sm . $unit;
					$addon->settings->{$key . '_xs'} = $xs . $unit;
				}

				if (isset($addon_options[$key]['selector'])) {
					$addon_selector = $addon_options[$key]['selector'];
					if (isset($addon->settings->{$key}) && !empty($addon->settings->{$key})) {
						$selector_value = $addon->settings->{$key};
						$addon->settings->{$key . '_selector'} = str_replace('{{ VALUE }}', $selector_value, $addon_selector);
					}
				}

				// Repeatable
				if ((!isset($addon->type) || $addon->type !== 'inner_row') && (($key == 'jw_' . $addon->name . '_item') || ($key == $addon->name . '_item'))) {
					if (count((array)$setting)) {
						foreach ($setting as &$options) {
							foreach ($options as $key2 => &$opt) {

								if (isset($opt->md)) {
									$md = isset($opt->md) ? $opt->md : "";
									$sm = isset($opt->sm) ? $opt->sm : "";
									$xs = isset($opt->xs) ? $opt->xs : "";
									$unit = (isset($opt->unit)) ? $opt->unit : "";
									$opt = $md . $unit;
									$options->{$key2 . '_sm'} = $sm . $unit;
									$options->{$key2 . '_xs'} = $xs . $unit;
								}

								if (isset($addon_options[$key]['attr'][$key2]['selector'])) {
									$addon_selector = $addon_options[$key]['attr'][$key2]['selector'];
									if (isset($options->{$key2}) && !empty($options->{$key2})) {
										$selector_value = $options->{$key2};
										$options->{$key2 . '_selector'} = str_replace('{{ VALUE }}', $selector_value, $addon_selector);
									}
								}

							}
						}
					}
				}
			}

			//sbou start
			//plugin support for addonRender
			JPluginHelper::importPlugin('system');
			// Get the dispatcher and load the content plugins.
			$dispatcher = JEventDispatcher::getInstance();
			$results = $dispatcher->trigger('onBeforeAddonRender', array(&$addon));
			//sbou end

			$output .= $layouts->addon_start->render(array('addon' => $addon)); // start addon
			require_once $addon_path . '/site.php';

			$hasRepeatableItems = self::checkRepeatableItems($addon->settings, $addon->name);
			if (count((array)$hasRepeatableItems)) {
				self::processRepeatableItems($hasRepeatableItems, $addon->name, $layouts, $pageName);
			}

			if (class_exists($class_name)) {
				$addon_obj = new $class_name($addon);  // initialize addon class
				$output .= $addon_obj->render();

				// Scripts
				if (method_exists($class_name, 'scripts')) {
					$scripts = $addon_obj->scripts();
					if (count((array)$scripts)) {
						foreach ($scripts as $key => $script) {
							$doc->addScript($script);
						}
					}
				}

				// JS
				if (method_exists($class_name, 'js')) {
					$doc->addScriptDeclaration($addon_obj->js());
				}

				// Stylesheets
				if (method_exists($class_name, 'stylesheets')) {
					$stylesheets = $addon_obj->stylesheets();
					if (count((array)$stylesheets)) {
						foreach ($stylesheets as $key => $stylesheet) {
							$doc->addStyleSheet($stylesheet);
						}
					}
				}

				$addon_css = $layouts->addon_css->render(array('addon' => $addon));
				if ($pageName == 'module') {
					$output .= '<style type="text/css">' . $addon_css . '</style>';
				} else {
					array_push(self::$css_content, $addon_css);
				}

				// css
				if (method_exists($class_name, 'css')) {
					if ($pageName == 'module') {
						$output .= '<style type="text/css">' . $addon_obj->css() . '</style>';
					} else {
						array_push(self::$css_content, $addon_obj->css());
					}
				}

			} else {
				$output .= htmlspecialchars_decode(JwpfAddonParser::jwDoAddon(JwpfAddonParser::generateShortcode($addon)));
			}
			$output .= $layouts->addon_end->render(); // end addon
		}

		return $output;
	}

	public static function checkRepeatableItems($settings, $addon_name)
	{
		$repeateItems = array();
		$isRepeatbleItem = isset($settings->{'jw_' . $addon_name . '_item'}) && count((array)$settings->{'jw_' . $addon_name . '_item'});
		if ($isRepeatbleItem) {
			array_push($repeateItems, $settings->{'jw_' . $addon_name . '_item'});
		}
		if (isset($settings->{$addon_name . '_item'}) && count((array)$settings->{$addon_name . '_item'})) {
			array_push($repeateItems, $settings->{$addon_name . '_item'});
		}
		return $repeateItems;
	}

	public static function processRepeatableItems($repeateItems, $addon_name, $layouts, $pageName)
	{
		foreach ($repeateItems as $r_settings) {
			if (count((array)$r_settings)) {

				foreach ($r_settings as $key => &$item) {
					if (isset($item->content) && is_array($item->content)) {
						$newConetnt = '';

						foreach ($item->content as $contentAddon) {

							// Addon Visibility and ACL
							if (isset($addon->visibility) && !$addon->visibility) {
								continue;
							}

							// ACL
							$access = self::checkAddonACL($contentAddon);
							if (!$access) {
								continue;
							} // End ACL

							$newConetnt .= self::getAddonHtmlView($contentAddon, $layouts, $pageName);
						}
						$item->content = $newConetnt;
					} else {
						$re_checkRepeatableItems = self::checkRepeatableItems($item, $addon_name);
						if (count((array)$re_checkRepeatableItems)) {
							self::processRepeatableItems($re_checkRepeatableItems, $addon_name, $layouts, $pageName);
						}

					}
				}
			}
		}
	}

	public static function minifyCss($css_code)
	{
		// Remove comments
		$css_code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_code);

		// Remove space after colons
		$css_code = str_replace(': ', ':', $css_code);

		// Remove whitespace
		$css_code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css_code);

		// Remove Empty Selectors without any properties
		$css_code = preg_replace('/(?:(?:[^\r\n{}]+)\s?{[\s]*})/', '', $css_code);

		// Remove Empty Media Selectors without any properties or selector
		$css_code = preg_replace('/@media\s?\((?:[^\r\n,{}]+)\s?{[\s]*}/', '', $css_code);

		return $css_code;
	}

	public static function generateShortcode($addon)
	{

		if (!empty($addon->settings)) {
			$addon->settings->dynamicId = $addon->id;
			$ops = JwpfAddonParser::generateShortcodeOps($addon->settings);
		}

		$output = '[jw_' . $addon->name;
		if (isset($ops['default'])) {
			$output .= $ops['default'];
		}
		$output .= ']';
		if (isset($ops['repeat'])) {
			$output .= $ops['repeat'];
		}
		$output .= '[/jw_' . $addon->name . ']';

		return $output;
	}

	public static function generateShortcodeOps($ops)
	{
		$default = '';
		$repeat = '';

		foreach ($ops as $key => $val) {
			if (!is_array($val)) {
				$default .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
			} else {
				$temp = '';
				foreach ($val as $innerKey => $innerVal) {
					$temp .= '[' . $key;
					foreach ($innerVal as $inner_key => $inner_val) {
						$temp .= ' ' . $inner_key . '="' . htmlspecialchars($inner_val) . '"';
					}
					$temp .= '][/' . $key . ']';
				}
				$repeat .= $temp;
			}
		}

		if ($default) $result['default'] = $default;
		if ($repeat) $result['repeat'] = $repeat;

		return $result;
	}


	// Get list of plugin addons
	private static function getPluginsAddons()
	{
		$path = JPATH_PLUGINS . '/jwpagefactory';
		if (!JFolder::exists($path)) return;

		$plugins = JFolder::folders($path);
		if (!count((array)$plugins)) return;

		$elements = array();
		foreach ($plugins as $plugin) {
			if (JPluginHelper::isEnabled('jwpagefactory', $plugin)) {
				$addons_path = $path . '/' . $plugin . '/addons';
				if (JFolder::exists($addons_path)) {
					$addons = JFolder::folders($addons_path);
					foreach ($addons as $addon) {
						$path = $addons_path . '/' . $addon;
						if (JFile::exists($path . '/site.php')) {
							$elements[$addon] = $path;
						}
					}
				}
			}
		}

		return $elements;
	}

	private static function getTemplateName()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('template')));
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('home') . ' = 1');
		$db->setQuery($query);

		return $db->loadObject()->template;
	}

	public static function convertCssArrayToString($cssArray = array())
	{
		$cssString = '';
		if (count((array)$cssArray) > 0) {
			foreach ($cssArray as $cssItem) {
				$cssString .= $cssItem;
			}
		}

		return $cssString;
	}

	public static function checkAddonACL($addon)
	{
		$access = true;
		if (isset($addon->settings->acl) && $addon->settings->acl) {
			$access_list = $addon->settings->acl;
			$access = false;
			foreach ($access_list as $acl) {
				if (in_array($acl, self::$authorised)) {
					$access = true;
				}
			}
			unset($addon->settings->acl);
		}

		return $access;
	}

	/*
  * Print interaction css and javascript object
  */
	private static function parseInteractions($addonId, $addonSettings, $selectors)
	{
		foreach ($selectors as $selector) {
			$interactions = isset($addonSettings->{$selector}) ? $addonSettings->{$selector} : [];

			if (is_array($interactions) && count($interactions)) {
				$interactions = $interactions[0];
				$animationCollection = new stdClass();
				$animationCollection->addonId = $addonId;
				$animationCollection->enable_mobile = isset($interactions->enable_mobile) && $interactions->enable_mobile;
				$animationCollection->enable_tablet = isset($interactions->enable_tablet) && $interactions->enable_tablet;

				if ($selector == 'while_scroll_view' && $interactions->enable_while_scroll_view) {
					$animation = isset($interactions->on_scroll_actions) ? $interactions->on_scroll_actions : [];
					if (count($animation) > 1) {
						usort($animation, function ($x, $y) {
							return $x->keyframe - $y->keyframe;
						});
					}
					$animationCollection->animation = $animation;
					$animationCollection->name = 'custom';

					$xOffset = isset($interactions->transition_origin_x) && $interactions->transition_origin_x ? $interactions->transition_origin_x : 'center';
					$yOffset = isset($interactions->transition_origin_y) && $interactions->transition_origin_y ? $interactions->transition_origin_y : 'center';

					$animationCollection->origin = ['x_offset' => $xOffset, 'y_offset' => $yOffset];

					if (isset(self::$addon_interactions[$selector])) {
						array_push(self::$addon_interactions[$selector], $animationCollection);
					} else {
						self::$addon_interactions[$selector] = array($animationCollection);
					}
				}
				if ($selector == 'mouse_movement' && $interactions->enable_tilt_effect) {
					$animationCollection->animation = $interactions;
					if (isset(self::$addon_interactions[$selector])) {
						array_push(self::$addon_interactions[$selector], $animationCollection);
					} else {
						self::$addon_interactions[$selector] = array($animationCollection);
					}
				}
			}
		}
	}

	private static function shortByKeyFrame($x, $y)
	{
		return $x['keyframe'] - $y['keyframe'];
	}
}

function jwAddonAtts($pairs, $atts, $shortcode = '')
{
	$atts = (array)$atts;
	$out = array();
	foreach ($pairs as $name => $default) {
		if (array_key_exists($name, $atts))
			$out[$name] = $atts[$name];
		else
			$out[$name] = $default;
	}

	return $out;
}

JwpfAddonParser::getAddons();