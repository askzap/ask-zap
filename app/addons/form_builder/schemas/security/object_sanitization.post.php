<?php
use Tygh\Tools\SecurityHelper;

$schema['form_general_data'] = array(
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => array(
        FORM_SUBMIT => SecurityHelper::ACTION_SANITIZE_HTML
    )
);

return $schema;