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

// Global update handlers
$schema = array(
    'update_handlers' => array(
        'addons',
        'settings_objects',
        'bm_blocks',
        'bm_blocks_descriptions',
        'bm_blocks_content',
        'bm_block_statuses',
        'bm_snapping',
        'languages',
        'language_values',
        'promotions',
    )
);

if (fn_allowed_for('ULTIMATE')) {
    // Very common block cache dependency
    $schema['update_handlers'][] = 'ult_objects_sharing';
}

return $schema;