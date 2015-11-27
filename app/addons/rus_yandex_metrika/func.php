<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Registry;
use Tygh\Settings;
use Tygh\RestClient;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Handlers
 */

function fn_yandex_metrika_oauth_info()
{
    if (
        !fn_string_not_empty(Registry::get('addons.rus_yandex_metrika.application_id'))
        || !fn_string_not_empty(Registry::get('addons.rus_yandex_metrika.application_password'))
    ) {
        return __('yandex_metrika_oauth_info_part1', array(
            '[callback_uri]' => fn_url('yandex_metrika_tools.oauth')
        ));
    } else {
        $client_id = Registry::get('addons.rus_yandex_metrika.application_id');

        return __('yandex_metrika_oauth_info_part2', array(
            '[auth_uri]' => "https://oauth.yandex.ru/authorize?response_type=code&client_id=" . $client_id,
            '[edit_app_uri]' => "https://oauth.yandex.ru/client/edit/" . $client_id,
        ));
    }
}

/**
 * \Handlers
 */

/**
 * Common functions
 */

function fn_yandex_metrika_sync_goals()
{
    $oauth_token = Settings::instance()->getValue('auth_token', 'rus_yandex_metrika');
    $counter_number = Settings::instance()->getValue('counter_number', 'rus_yandex_metrika');

    if (empty($oauth_token) || empty($counter_number)) {
        return false;
    }

    $goals_scheme = fn_get_schema('rus_yandex_metrika', 'goals');
    $selected_goals = Settings::instance()->getValue('collect_stats_for_goals', 'rus_yandex_metrika');

    $client = new RestClient('https://api-metrika.yandex.ru/');

    $ext_goals = array();
    $res = $client->get("/counter/$counter_number/goals.json", array('oauth_token' => $oauth_token));
    if (!empty($res['goals'])) {
        foreach ($res['goals'] as $goal) {
            $ext_goals[$goal['name']] = $goal;
        }
    }

    foreach ($goals_scheme as $goal_name => $goal) {
        $ext_goal_name = '[auto] ' . $goal['name'];
        if (!empty($ext_goals[$ext_goal_name])) {
            if (empty($selected_goals[$goal_name]) || $selected_goals[$goal_name] == 'N') {
                $client->delete("/counter/$counter_number/goal/" . $ext_goals[$ext_goal_name]['id'] . "?oauth_token=$oauth_token");
            }
        } else {
            if (!empty($selected_goals[$goal_name]) && $selected_goals[$goal_name] == 'Y') {
                $goal['name'] = $ext_goal_name;
                $client->post("/counter/$counter_number/goals?oauth_token=$oauth_token", array('goal' => $goal));
            }
        }
    }

    return true;
}

/**
 * \Common functions
 */
