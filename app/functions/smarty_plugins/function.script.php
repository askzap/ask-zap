<?php

use Tygh\Registry;

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_script($params, &$smarty)
{
    static $scripts = array();

    if (!isset($scripts[$params['src']])) {
        if (strpos($params['src'], '//') === false) {
            $src = Registry::get('config.current_location') . '/' . fn_link_attach($params['src'], 'ver=' . PRODUCT_VERSION);
        } else {
            $src = $params['src'];
        }

        $scripts[$params['src']] = '<script type="text/javascript"'
                                    . (!empty($params['class']) ? ' class="' . $params['class'] . '" ' : '')
                                    . ' src="' . $src . '" ' . (isset($params['charset']) ? ('charset="' . $params['charset'] . '"') : '') . (isset($params['escape']) ? '><\/script>' : '></script>');

        if (defined('AJAX_REQUEST') || Registry::get('runtime.inside_scripts')) {
            return $scripts[$params['src']];
        } else {

            if (isset($params['no-defer']) && $params['no-defer']) {
                return $scripts[$params['src']];
            } else {
                $cache_name = $smarty->getTemplateVars('block_cache_name');
                if (!empty($cache_name)) {
                    $cached_content = Registry::get($cache_name);
                    if (!isset($cached_content['javascript'])) {
                        $cached_content['javascript'] = '';
                    }
                    $cached_content['javascript'] .= $scripts[$params['src']];

                    Registry::set($cache_name, $cached_content, true);
                }
                $repeat = false;
                $smarty->loadPlugin('smarty_block_inline_script');
                smarty_block_inline_script(array(), $scripts[$params['src']], $smarty, $repeat);

                return '<!-- Inline script moved to the bottom of the page -->';
            }

        }
    }
}
