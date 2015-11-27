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

namespace Tygh\Tools;

use Tygh\Exceptions\DeveloperException;
use Tygh\Registry;

/**
 * SecurityHelper provides a set of static methods used for security purposes.
 *
 * @package Tygh
 * @since   4.3.1
 * @TODO    Move password-, crypto- and other security-related stuff here.
 */
class SecurityHelper
{
    /** Keep HTML tags as is, but remove any JavaScript and malicious code */
    const ACTION_SANITIZE_HTML = 'ACTION_SANITIZE_HTML';

    /** Remove all HTML tags */
    const ACTION_REMOVE_HTML = 'ACTION_REMOVE_HTML';

    /** Replace all HTML symbols with special codes */
    const ACTION_ESCAPE_HTML = 'ACTION_ESCAPE_HTML';

    const SCHEMA_SECTION_FIELD_RULES = 'SCHEMA_SECTION_FIELD_RULES';

    /**
     * @return bool Whether user-inputted HTML should be sanitized (true) or used as-is (false).
     */
    public static function shouldSanitizeUserHtml()
    {
        static $should = null;

        if ($should === null) {
            $should = Registry::get('config.tweaks.sanitize_user_html');
            if ($should === 'auto') {
                $should = fn_allowed_for('MULTIVENDOR');
            }
        }

        return (bool)$should;
    }

    /**
     * Performs securing object data basing on rule list related to object type. Rule list is specified at schema file.
     * Note that this function checks setting about whether HTML should be sanitized or not.
     * If no, field rule "ACTION_SANITIZE_HTML" will be omitted and no modifications to field data will be made.
     *
     * @param string $object_type  Object type specified at schema file
     * @param array  &$object_data Array with object data passed by reference
     *
     * @return void
     */
    public static function sanitizeObjectData($object_type, array &$object_data)
    {
        $schema = fn_get_schema('security', 'object_sanitization');

        if (isset($schema[$object_type][self::SCHEMA_SECTION_FIELD_RULES])) {
            $object_data = self::sanitizeData(
                $object_data,
                $schema[$object_type][self::SCHEMA_SECTION_FIELD_RULES],
                self::shouldSanitizeUserHtml() ? array() : array(self::ACTION_SANITIZE_HTML)
            );
        }
    }

    /**
     * Performs securing user-inputed data basing on given rule list.
     *
     * @param array $data             Data inputted by user.
     * @param array $rules            Rules in format: array(input_data_key => action_type), where action_type should be
     *                                one of constants: ACTION_SANITIZE_HTML, ACTION_REMOVE_HTML, ACTION_ESCAPE_HTML.
     * @param array $disabled_actions List of disallowed actions.
     *
     * @return array Sanitized data
     */
    public static function sanitizeData(array $data, array $rules, array $disabled_actions = array())
    {
        foreach ($rules as $field_name => $action_type) {
            if (isset($data[$field_name]) || array_key_exists($field_name, $data)) {
                // Make it possible to handle nested arrays
                if (is_array($data[$field_name])) {
                    if (is_array($action_type)) {
                        $data[$field_name] = self::sanitizeData($data[$field_name], $action_type, $disabled_actions);
                    }
                } elseif (!in_array($action_type, $disabled_actions)) {
                    // Perform data transformation
                    switch ($action_type) {
                        case self::ACTION_SANITIZE_HTML:
                            $data[$field_name] = self::sanitizeHtml($data[$field_name]);
                            break;
                        case self::ACTION_ESCAPE_HTML:
                            $data[$field_name] = self::escapeHtml($data[$field_name]);
                            break;
                        case self::ACTION_REMOVE_HTML:
                            $data[$field_name] = strip_tags($data[$field_name]);
                            break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Used to sanitize user-inputed HTML from any XSS code.
     * Use this function when you want to use HTML-code inputed by users safely.
     *
     * @param string $raw_html Input HTML code
     *
     * @return string Sanitized HTML ready to be safely displayed on page.
     */
    public static function sanitizeHtml($raw_html)
    {
        try {
            $cache_dir = fn_get_cache_path(false) . 'html_purifier/';
            if (!is_dir($cache_dir)) {
                fn_mkdir($cache_dir);
            }

            $config_instance = \HTMLPurifier_Config::createDefault();
            $config_instance->set('HTML.DefinitionID', PRODUCT_NAME . '_' . PRODUCT_VERSION);
            $config_instance->set('HTML.DefinitionRev', 1);
            $config_instance->set('Cache.SerializerPath', $cache_dir);
            $config_instance->set('Cache.SerializerPermissions', DEFAULT_DIR_PERMISSIONS);

            $config_instance->autoFinalize = false;

            if ($html_definition = $config_instance->maybeGetRawHTMLDefinition()) {
                $html_definition->addAttribute('a',
                    'target',
                    new \HTMLPurifier_AttrDef_Enum(
                        array('_blank', '_self', '_target', '_top')
                    ));
            }

            $purifier_instance = \HTMLPurifier::instance($config_instance);

            $html_purify = $purifier_instance->purify($raw_html);

            return html_entity_decode($html_purify, ENT_QUOTES, 'UTF-8');

        } catch (\Exception $e) {
            throw new DeveloperException($e->getMessage());
        }
    }

    /**
     * Encodes special characters into HTML entities.
     * Use this function when you don't want HTML from user to be displayed on page.
     *
     * @param string|array $data   Input data
     * @param bool         $revert Whether to decode or encode.
     *
     * @return array|string Data with special characters converted to HTML entities.
     */
    public static function escapeHtml($data, $revert = false)
    {
        if (is_array($data)) {
            foreach ($data as $k => $sub) {
                if (is_string($k)) {
                    $_k = ($revert == false)
                        ? htmlspecialchars($k, ENT_QUOTES, 'UTF-8')
                        : htmlspecialchars_decode($k, ENT_QUOTES);
                    if ($k != $_k) {
                        unset($data[$k]);
                    }
                } else {
                    $_k = $k;
                }
                if (is_array($sub) === true) {
                    $data[$_k] = self::escapeHtml($sub, $revert);
                } elseif (is_string($sub)) {
                    $data[$_k] = ($revert == false)
                        ? htmlspecialchars($sub, ENT_QUOTES, 'UTF-8')
                        : htmlspecialchars_decode($sub, ENT_QUOTES);
                }
            }
        } else {
            $data = ($revert == false)
                ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8')
                : htmlspecialchars_decode($data, ENT_QUOTES);
        }

        return $data;
    }
}