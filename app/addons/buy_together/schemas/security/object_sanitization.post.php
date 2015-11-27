<?php
use Tygh\Tools\SecurityHelper;

$schema['buy_together_chain'] = array(
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => array(
        'name' => SecurityHelper::ACTION_REMOVE_HTML,
        'description' => SecurityHelper::ACTION_SANITIZE_HTML,
    )
);

return $schema;