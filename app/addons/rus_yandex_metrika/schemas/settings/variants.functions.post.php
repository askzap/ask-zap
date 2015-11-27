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

/**
 * Get predefined goals
 */
function fn_settings_variants_addons_rus_yandex_metrika_collect_stats_for_goals()
{
    $goals_scheme = fn_get_schema('rus_yandex_metrika', 'goals');
    $goals = array();

    foreach ($goals_scheme as $goal_key => $goal) {
        $goals[$goal_key] = $goal['name'];
    }

    return $goals;
}

