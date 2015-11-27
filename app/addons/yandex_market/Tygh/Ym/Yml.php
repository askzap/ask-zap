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

namespace Tygh\Ym;

use Tygh\Registry;

class Yml implements IYml
{

    const ITERATION_ITEMS = 100;
    const IMAGES_LIMIT = 10;

    protected $company_id;
    protected $options = array();
    protected $lang_code = DESCR_SL;
    protected $page = 0;
    protected $debug = false;

    protected $disabled_category_ids = array();

    public function __construct($company_id, $options = array(), $lang_code = DESCR_SL, $page = 0, $debug = false)
    {
        $this->company_id = $company_id;
        $this->options    = $options;
        $this->lang_code  = $lang_code;
        $this->page       = $page;
        $this->debug      = $debug;
    }

    public function get()
    {
        $filename = $this->getFileName();

        if (!file_exists($filename) || $this->debug) {
            $this->generate($filename);
        }

        $this->sendResult($filename);
    }

    public function getFileName()
    {
        $path = sprintf('%syandex_market/%s_yandex_market.yml',
            fn_get_cache_path(false, 'C', $this->company_id),
            $this->company_id
        );

        return $path;
    }

    public function clearCache()
    {
        return fn_rm($this->getFileName());
    }

    public static function clearCaches($company_ids = null)
    {
        if (is_null($company_ids)) {
            if (Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {
                $company_ids = Registry::get('runtime.company_data.company_id');
            } else {
                $company_ids = array_keys(fn_get_short_companies());
            }
        }

        foreach ((array) $company_ids as $company_id) {
            $self = new self($company_id);
            $self->clearCache();
        }
    }

    public function generate($filename)
    {
        @ignore_user_abort(1);
        @set_time_limit(0);

        fn_mkdir(dirname($filename));
        $file = fopen($filename, 'wb');

        $this->head($file);
        $this->body($file);
        $this->bottom($file);

        fclose($file);
    }

    protected function head($file)
    {
        $yml_header = array(
            '<?xml version="1.0" encoding="' . $this->options['export_encoding'] . '"?>',
            '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">',
            '<yml_catalog date="' . date('Y-m-d G:i') . '">',
            '<shop>'
        );

        $yml_data = array(
            'name' => $this->getShopName(),
            'company' => Registry::get('settings.Company.company_name'),
            'url' => Registry::get('config.http_location'),
            'platform' => PRODUCT_NAME,
            'version' => PRODUCT_VERSION,
            'agency' => 'Agency',
            'email' => Registry::get('settings.Company.company_orders_department'),
        );

        $this->buildCurrencies($yml_data);

        $this->buildCategories($yml_data);

        if ($global_local_delivery = $this->options['global_local_delivery_cost']) {
            $yml_data['local_delivery_cost'] = $global_local_delivery;
        }

        fwrite($file, implode(PHP_EOL, $yml_header) . PHP_EOL);
        fwrite($file, fn_yandex_market_array_to_yml($yml_data));
        fwrite($file, '<offers>' . PHP_EOL);
    }

    protected function body($file)
    {
        $offered = array();

        if ($this->options['disable_cat_d'] == "Y") {
            $visible_categories = $this->getVisibleCategories();
        }

        $fields = array(
            'p.product_id',
            'p.product_code',
            'd.lang_code',
            'pc.category_id',
            'cd.category',
            'pp.price',
            'p.list_price',
            'p.status',
            'p.amount',
            'p.weight',
            'p.shipping_freight',
            'p.free_shipping',
            'd.product',
            'd.full_description',
            'p.company_id',
            'p.tracking',
            'p.list_price',
            'p.yml_brand',
            'p.yml_origin_country',
            'p.yml_store',
            'p.yml_pickup',
            'p.yml_delivery',
            'p.yml_adult',
            'p.yml_cost',
            'p.yml_export_yes',
            'p.yml_bid',
            'p.yml_cbid',
            'p.yml_model',
            'p.yml_sales_notes',
            'p.yml_type_prefix',
            'p.yml_market_category',
            'p.yml_manufacturer_warranty',
            'p.yml_seller_warranty'
        );

        $fields[] = "(
                SELECT GROUP_CONCAT(IF(pc2.link_type = 'M', CONCAT(pc2.category_id, 'M'), pc2.category_id))
                FROM ?:products_categories as pc2
                WHERE product_id = p.product_id
            ) as category_ids";

        $joins = array(
            db_quote(
                "LEFT JOIN ?:product_descriptions as d ON d.product_id = p.product_id AND d.lang_code = ?s",
                $this->lang_code
            ),
            db_quote(
                "LEFT JOIN ?:product_prices as pp"
                . " ON pp.product_id = p.product_id AND pp.lower_limit = 1 AND pp.usergroup_id = 0"
            ),
            db_quote(
                "LEFT JOIN ?:products_categories as pc ON pc.product_id = p.product_id AND pc.link_type = ?s",
                'M'
            ),
            db_quote(
                "LEFT JOIN ?:category_descriptions as cd ON cd.category_id = pc.category_id AND cd.lang_code = ?s",
                $this->lang_code
            )
        );

        $condition = '';

        if ($this->company_id > 0) {
            $condition .= db_quote(' AND company_id = ?i', $this->company_id);
        }

        $product_ids = db_get_fields(
            "SELECT product_id FROM ?:products WHERE yml_export_yes = ?s AND status = ?s " . $condition,
            'Y', 'A'
        );

        $offset = 0;
        while ($ids = array_slice($product_ids, $offset, self::ITERATION_ITEMS)) {
            $offset += self::ITERATION_ITEMS;
            $products = db_get_array(
                'SELECT ' . implode(', ', $fields)
                . ' FROM ?:products as p'
                . ' ' . implode(' ', $joins)
                . ' WHERE p.product_id IN(?n)'
                , $ids
            );

            $products_images_main = fn_get_image_pairs($ids, 'product', 'M', false, true, $this->lang_code);
            $products_images_additional = fn_get_image_pairs($ids, 'product', 'A', false, true, $this->lang_code);

            $params = array(
                'get_options' => false,
                'get_taxed_prices' => false,
                'detailed_params' => false,
            );
            fn_gather_additional_products_data($products, $params);

            foreach ($products as $k => &$product) {
                $is_broken = false;

                $price = !floatval($product['price']) ? fn_parse_price($product['price']) : intval($product['price']);

                if ($this->options['export_null_price'] == 'N' && empty($price)) {
                    $is_broken = true;
                }

                if (in_array($product['category_id'], $this->disabled_category_ids)) {
                    $is_broken = true;
                }

                if ($this->options['disable_cat_d'] == 'Y' && !in_array($product['category_id'], $visible_categories)) {
                    $is_broken = true;
                }


                $product['product'] = $this->escape($product['product']);
                $product['full_description'] = $this->escape($product['full_description']);
                $product['product_features'] = $this->getProductFeatures($product);
                $product['brand'] = $this->getBrand($product);

                if ($this->options['export_type'] == 'vendor_model') {
                    if (empty($product['brand']) || empty($product['yml_model'])) {
                        $is_broken = true;
                    }
                }

                if ($product['tracking'] == 'O') {
                    $product['amount'] = db_get_field(
                        "SELECT SUM(amount) FROM ?:product_options_inventory WHERE product_id = ?i",
                        $product['product_id']
                    );
                }

                if ($this->options['export_stock'] == 'Y' && $product['amount'] <= 0) {
                    $is_broken = true;
                }

                if ($is_broken) {
                    unset($products[$k]);
                    continue;
                }
                $product['product_url'] = fn_url('products.view?product_id=' . $product['product_id']);

                // Images
                $images = array_merge(
                    $products_images_main[$product['product_id']],
                    $products_images_additional[$product['product_id']]
                );
                $product['images'] = array_slice($images, 0, self::IMAGES_LIMIT);

                list($key, $value) = $this->offer($product);
                $offered[$key] = $value;
            }

            if (!empty($offered)) {
                fwrite($file, fn_yandex_market_array_to_yml($offered));
                unset($offered);
            }
        }
    }

    protected function bottom($file)
    {
        fwrite($file, '</offers>' . PHP_EOL);
        fwrite($file, '</shop>' . PHP_EOL);
        fwrite($file, '</yml_catalog>' . PHP_EOL);
    }

    protected function sendResult($filename)
    {
        header("Content-Type: text/xml;charset=" . $this->options['export_encoding']);

        readfile($filename);
        exit;
    }

    protected function getShopName()
    {
        $shop_name = $this->options['shop_name'];

        if (empty($shop_name)) {
            if (fn_allowed_for('ULTIMATE')) {
                $shop_name = fn_get_company_name($this->company_id);
            } else {
                $shop_name = Registry::get('settings.Company.company_name');
            }
        }

        return strip_tags($shop_name);
    }

    protected function buildCurrencies(&$yml_data)
    {
        $currencies = Registry::get('currencies');

        if (CART_PRIMARY_CURRENCY != "RUB" && CART_PRIMARY_CURRENCY != "UAH" && CART_PRIMARY_CURRENCY != "BYR" && CART_PRIMARY_CURRENCY != "KZT") {

            if (!empty($currencies['RUB'])) {
                $v_coefficient = $currencies['RUB']['coefficient'];
                $default_currencies = 'RUB';

            } elseif (!empty($currencies['UAH'])) {
                $v_coefficient = $currencies['UAH']['coefficient'];
                $default_currencies = 'UAH';

            } elseif (!empty($currencies['BYR'])) {
                $v_coefficient = $currencies['BYR']['coefficient'];
                $default_currencies = 'BYR';

            } elseif (!empty($currencies['KZT'])) {
                $v_coefficient = $currencies['KZT']['coefficient'];
                $default_currencies = 'KZT';

            } else {
                $v_coefficient = 1;
                $default_currencies = CART_PRIMARY_CURRENCY;
            }
            $primary_coefficient = $currencies[CART_PRIMARY_CURRENCY]['coefficient'];

            foreach ($currencies as $cur) {
                if ($this->currencyIsValid($cur['currency_code']) && $cur['status'] == 'A') {
                    if ($default_currencies == $cur['currency_code']) {
                        $coefficient = '1.0000';
                        $yml_data['currencies']['currency@id=' . $cur['currency_code'] . '@rate=' . $coefficient] = '';

                    } else {
                        $coefficient = $cur['coefficient'] * $primary_coefficient / $v_coefficient;
                        $yml_data['currencies']['currency@id=' . $cur['currency_code'] . '@rate=' . $coefficient] = '';
                    }
                }
            }

        } else {
            foreach ($currencies as $cur) {
                if ($this->currencyIsValid($cur['currency_code']) && $cur['status'] == 'A') {
                    $yml_data['currencies']['currency@id=' . $cur['currency_code'] . '@rate=' . $cur['coefficient']] = '';
                }
            }
        }
    }

    protected function currencyIsValid($currency)
    {
        $currencies = array(
            'RUR',
            'RUB',
            'UAH',
            'BYR',
            'KZT',
            'USD',
            'EUR'
        );

        return in_array($currency, $currencies);
    }

    protected function buildCategories(&$yml_data)
    {
        $params = array (
            'simple' => false,
            'plain' => true,
        );

        if ($this->options['disable_cat_d'] == "Y") {
            $params['status'] = array('A', 'H');
        }

        $disable_cat_full_list = $this->getDisabledCategories();

        list($categories_tree, ) = fn_get_categories($params, $this->lang_code);

        foreach ($categories_tree as $cat) {
            if (isset($cat['category_id']) && !in_array($cat['category_id'], $disable_cat_full_list)) {

                if ($cat['parent_id'] == 0) {
                    $yml_data['categories']['category@id=' . $cat['category_id']] = $cat['category'];

                } else {
                    $yml_data['categories']['category@id=' . $cat['category_id'] . '@parentId=' . $cat['parent_id']] = $cat['category'];
                }
            }
        }
    }

    protected function getDisabledCategories()
    {
        if (isset($this->disabled_category_ids)) {
            $this->disabled_category_ids = array();
            $disable_categories_list = db_get_fields("SELECT id_path FROM ?:categories WHERE yml_disable_cat = ?s", 'Y');
            if (!empty($disable_categories_list)) {
                $like_path = "id_path LIKE '" . implode("%' OR id_path LIKE '", $disable_categories_list) . "%'"; // id_path LIKE '166/196%' OR id_path LIKE '203/204/212%' ...
                $this->disabled_category_ids = db_get_fields("SELECT category_id FROM ?:categories WHERE ?p", $like_path);
            }
        }

        return $this->disabled_category_ids;
    }

    /**
     * Export product features
     */
    protected function getProductFeatures($product)
    {
        static $features;

        $lang_code = $this->lang_code;

        if (!isset($features[$lang_code])) {
            list($features[$lang_code]) = fn_get_product_features(array('plain' => true), 0, $lang_code);
        }

        $product = array(
            'product_id' => $product['product_id'],
            'main_category' => $product['category_id']
        );

        $product_features = fn_get_product_features_list($product, 'A', $lang_code);

        $result = array();

        if (!empty($product_features)) {
            foreach ($product_features as $f) {
                $display_on_catalog = $features[$lang_code][$f['feature_id']]['display_on_catalog'];
                $display_on_product = $features[$lang_code][$f['feature_id']]['display_on_product'];

                if ($display_on_catalog == "Y" || $display_on_product == "Y") {
                    if ($f['feature_type'] == "C") {
                        $result[] = array(
                            'description' => $f['description'],
                            'value' => ($f['value'] == "Y") ? __("yes") : __("no")
                        );
                    } elseif ($f['feature_type'] == "S" && !empty($f['variant'])) {
                        $result[] = array(
                            'description' => $f['description'],
                            'value' => $f['variant']
                        );
                    } elseif ($f['feature_type'] == "T" && !empty($f['value'])) {
                        $result[] = array(
                            'description' => $f['description'],
                            'value' => $f['value']
                        );
                    } elseif ($f['feature_type'] == "M") {
                        if (!empty($f['variants'])) {
                            $_value = '';
                            $counter = count($f['variants']);
                            foreach ($f['variants'] as $_variant) {
                                if ($counter > 1) {
                                    $_value .= $_variant['variant'] . ', ';
                                } else {
                                    $_value = $_variant['variant'];
                                }
                            }
                            $_value = ($counter > 1) ? substr($_value, 0, -2) : $_value;
                            $result[] = array(
                                'description' => $f['description'],
                                'value' => $_value
                            );
                        }
                    } elseif ($f['feature_type'] == "N") {
                        $result[] = array(
                            'description' => $f['description'],
                            'value' => $f['variant']
                        );
                    } elseif ($f['feature_type'] == "O") {
                        $result[] = array(
                            'description' => $f['description'],
                            'value' => $f['value_int']
                        );
                    } elseif ($f['feature_type'] == "E") {
                        $result[] = array(
                            'description' => $f['description'],
                            'value' => $f['variant']
                        );
                    }
                }
            }
        }

        return !empty($result) ? $result : '';
    }

    protected function getImageUrl($image_pair)
    {
        $url = '';

        if ($this->options['image_type'] == 'detailed') {
            $url = $image_pair['detailed']['image_path'];
        } else {
            $image_data = fn_image_to_display(
                $image_pair,
                $this->options['thumbnail_width'],
                $this->options['thumbnail_height']
            );

            if (!empty($image_data) && strpos($image_data['image_path'], '.php')) {
                $image_data['image_path'] = fn_generate_thumbnail(
                    $image_data['detailed_image_path'],
                    $image_data['width'],
                    $image_data['height']
                );
            }

            if (!empty($image_data['image_path'])) {
                $url = $image_data['image_path'];
            }
        }

        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        $url = fn_yandex_market_c_encode($url);

        $url = fn_query_remove($url, 't');

        return str_replace('–', urlencode('–'), $url);
    }

    protected function getVisibleCategories()
    {
        $visible_categories = null;

        if (!isset($visible_categories)) {
            $visible_categories = array();

            if ($this->options['disable_cat_d'] == "Y") {
                $params['plain'] = true;
                $params['status'] = array('A', 'H');
                list($categories_tree, ) = fn_get_categories($params);

                if (!empty($categories_tree)) {
                    foreach ($categories_tree as $value) {
                        if (isset($value['category_id'])) {
                            $visible_categories[] = $value['category_id'];
                        }
                    }
                }
            }
        }

        return $visible_categories;
    }

    protected function getMarketCategories()
    {
        static $market_categories = null;

        if (!isset($market_categories)) {
            $market_categories = array();

            if ($this->options['market_category'] == "Y" && $this->options['market_category_object'] == "category") {
                $market_categories = db_get_hash_single_array(
                    "SELECT category_id, yml_market_category FROM ?:categories WHERE yml_market_category != ?s",
                    array('category_id', 'yml_market_category'), ''
                );
            }
        }

        return $market_categories;
    }

    protected function getBrand($product)
    {
        $brand = '';

        if (!empty($product['yml_brand'])) {
            $brand = $product['yml_brand'];

        } elseif (!empty($product['product_features'])) {

            $feature_for_brand = $this->options['feature_for_brand'];
            $brands = array();

            if (!empty($feature_for_brand)) {

                foreach ($feature_for_brand as $brand_name => $check) {
                    if ($check == 'Y') {
                        $brands[] = $brand_name;
                    }
                }
                $brands = array_unique($brands);
            }

            foreach ($product['product_features'] as $feature) {
                if (in_array($feature['description'], $brands)) {
                    $brand = $feature['value'];
                    break;
                }
            }
        }

        return $brand;
    }

    protected function offer($product)
    {
        $yml_data = array();
        $offer_attrs = '';

        $market_categories = $this->getMarketCategories();

        if (!empty($product['yml_bid'])) {
            $offer_attrs .= '@bid=' . $product['yml_bid'];
        }

        if (!empty($product['yml_cbid'])) {
            $offer_attrs .= '@cbid=' . $product['yml_cbid'];
        }

        $price_fields = array('price', 'yml_cost', 'list_price', 'base_price');

        $currency_data = Registry::get('currencies.' . CART_PRIMARY_CURRENCY);
        foreach ($price_fields as $field) {
            $product[$field] = fn_format_price($product[$field], $currency_data['currency_code'], $currency_data['decimals'], false);
        }

        if (CART_PRIMARY_CURRENCY != "RUB" && CART_PRIMARY_CURRENCY != "UAH" && CART_PRIMARY_CURRENCY != "BYR" && CART_PRIMARY_CURRENCY != "KZT") {
            $currencies = Registry::get('currencies');
            if (isset($currencies['RUB'])) {
                $currency = $currencies['RUB'];

            } elseif (isset($currencies['UAH'])) {
                $currency = $currencies['UAH'];

            } elseif (isset($currencies['BYR'])) {
                $currency = $currencies['BYR'];

            } elseif (isset($currencies['KZT'])) {
                $currency = $currencies['KZT'];
            }

            if (!empty($currency)) {
                foreach ($price_fields as $field) {
                    $product[$field] = fn_format_rate_value($product[$field], 'F', $currency['decimals'], '.', '', $currency['coefficient']);
                }
            }
        }

        foreach ($price_fields as $field) {
            if (empty($product[$field])) {
                $product[$field] = floatval($product[$field]) ? $product[$field] : fn_parse_price($product[$field]);
            }
        }

        $yml_data['url'] = $product['product_url'];

        $yml_data['price'] = !empty($product['price']) ? $product['price'] : "0.00";

        if (!empty($product['base_price']) && $product['price'] < $product['base_price'] * 0.95) {
            $yml_data['oldprice'] = $product['base_price'];
        } elseif (!empty($product['list_price']) && $product['price'] < $product['list_price'] * 0.95) {
            $yml_data['oldprice'] = $product['list_price'];
        }
        $yml_data['currencyId'] = !empty($currency) ? $currency['currency_code'] : CART_PRIMARY_CURRENCY;
        $yml_data['categoryId'] = $product['category_id'];

        if ($this->options['market_category'] == "Y") {

            if ($this->options['market_category_object'] == "category" && isset($market_categories[$product['category_id']])) {
                $yml_data['market_category'] = $market_categories[$product['category_id']];

            } elseif ($this->options['market_category_object'] == "product" && !empty($product['yml_market_category'])) {
                $yml_data['market_category'] = $product['yml_market_category'];
            }

        }

        // Images
        $picture_index = 0;
        while ($image = array_shift($product['images'])) {
            $key = 'picture';
            if ($picture_index) {
                $key .= '+' . $picture_index;
            }
            $yml_data[$key] = $this->getImageUrl($image);

            $picture_index ++;
        }

        $yml_data['store'] = ($product['yml_store'] == 'Y' ? 'true' : 'false');
        $yml_data['pickup'] = ($product['yml_pickup'] == 'Y' ? 'true' : 'false');
        $yml_data['delivery'] = ($product['yml_delivery'] == 'Y' ? 'true' : 'false');

        if ($product['yml_adult'] == 'Y') {
            $yml_data['adult'] = 'true';
        }

        if ($this->options['local_delivery_cost'] == "Y") {
            $yml_data['local_delivery_cost'] = ($product['yml_cost'] == 0 ? '0' : $product['yml_cost']);
        }

        $type = '';
        if ($this->options['export_type'] == 'vendor_model') {

            $type = '@type=vendor.model';

            if ($this->options['type_prefix'] == "Y") {
                if (!empty($product['yml_type_prefix'])) {
                    $yml_data['typePrefix'] = $product['yml_type_prefix'];

                } else {
                    $yml_data['typePrefix'] = $product['category'];
                }
            }

            $yml_data['vendor'] = $product['brand'];
            if ($this->options['export_vendor_code'] == 'Y') {
                $vendor_code = $this->getVendorCode($product);
                if (!empty($vendor_code)) {
                    $yml_data['vendorCode'] = $vendor_code;
                }
            }
            $yml_data['model'] = !empty($product['yml_model']) ? $product['yml_model'] : '';

        } elseif ($this->options['export_type'] == 'simple') {
            $yml_data['name'] = $product['product'];

            if (!empty($product['brand'])) {
                $yml_data['vendor'] = $product['brand'];
            }

            if ($this->options['export_vendor_code'] == 'Y') {
                $vendor_code = $this->getVendorCode($product);
                if (!empty($vendor_code)) {
                    $yml_data['vendorCode'] = $vendor_code;
                }
            }
        }

        if (!empty($product['full_description'])) {
            //Stripping the invalid chars
            $product['full_description'] = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $product['full_description']);
            $yml_data['description'] = $product['full_description'];
        }

        if (!empty($product['yml_sales_notes'])) {
            $yml_data['sales_notes'] = $product['yml_sales_notes'];
        }

        if (!empty($product['yml_manufacturer_warranty'])) {
            $yml_data['manufacturer_warranty'] = $product['yml_manufacturer_warranty'];
        }

        if (!empty($product['yml_seller_warranty'])) {
            $yml_data['seller_warranty'] = $product['yml_seller_warranty'];
        }

        if (!empty($product['yml_origin_country']) && fn_yandex_market_check_country($product['yml_origin_country'])) {
            $yml_data['country_of_origin'] = $product['yml_origin_country'];
        }

        if (!empty($product['product_features'])) {
            foreach ($product['product_features'] as $feature) {
                $yml_data['param@name=' . $this->escape($feature['description'])] = $feature['value'];
            }
        }

        if ($product['amount'] > 0) {
            $avail = 'true';
        } else {
            $avail = 'false';
        }

        return array(
            'offer@id=' . $product['product_id'] . $type . '@available=' . $avail . $offer_attrs,
            $yml_data
        );
    }

    protected function getVendorCode($product)
    {
        if (!empty($product['product_features'])) {
            foreach ($product['product_features'] as $feature) {
                if ($feature['description'] == $this->options['feature_for_vendor_code']) {
                    return $feature['value'];
                }
            }
        }

        return '';
    }

    protected function escape($data)
    {
        return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }

}
