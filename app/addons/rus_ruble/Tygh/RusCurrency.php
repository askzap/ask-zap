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

namespace Tygh;

class RusCurrency
{
    public static function rub_create()
    {
        $currencies = Registry::get('currencies');
        $symbol = SYMBOL_RUBL;

        if (empty($currencies[CURRENCY_RUB])) {

            $rub = array(
                'currency_code' => CURRENCY_RUB,
                'after' => 'Y',
                'symbol' => $symbol,
                'coefficient' => '1',
                'is_primary' => 'Y',
                'position' => '0',
                'decimals_separator' => '',
                'thousands_separator' => '',
                'decimals' => '0',
                'status' => 'A',
            );

            db_query("UPDATE ?:currencies SET is_primary = 'N' WHERE is_primary = 'Y'");

            $rub_id = db_query('INSERT INTO ?:currencies ?e', $rub);
            if (!empty($rub_id)) {

                $rub_array = $rub;
                $rub_array['currency_id'] = $rub_id;

                Registry::set('currencies.RUB', $rub_array) ;

                foreach (fn_get_translation_languages() as $lang_code => $v) {
                    db_query("REPLACE INTO ?:currency_descriptions (`currency_code`, `description`, `lang_code`) VALUES (?s, 'Рубли', ?s)", CURRENCY_RUB, $lang_code);
                }

                fn_set_notification('N', __('notice'), __('rus_ruble.symbol_rub_created'));
            }
        } else {
            if ($currencies[CURRENCY_RUB]['is_primary'] == 'N') {
                db_query("UPDATE ?:currencies SET is_primary = 'N' WHERE is_primary = 'Y'");
                db_query("UPDATE ?:currencies SET is_primary = 'Y', coefficient = 1 WHERE currency_code = ?s", CURRENCY_RUB);
            }

            if ($currencies[CURRENCY_RUB]['symbol'] != $symbol) {
                self::symbol_update($symbol);
            }
        }

        return true;
    }

    public static function symbol_update($symbol = SYMBOL_RUBL)
    {
        db_query("UPDATE ?:currencies SET symbol = ?s WHERE currency_code = ?s", $symbol, CURRENCY_RUB);
        fn_set_notification('N', __('notice'), __('rus_ruble.symbol_rub_updated'));

        return true;
    }

    public static function currency_sync_generate_key($length = CRON_IMPORT_KEY_LENGTH)
    {
        $chars = str_split('1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, count($chars) -1)];
        }

        return $key;
    }

    public static function process_sbrf_currencies($primary_currency = CART_PRIMARY_CURRENCY)
    {
        $date = date('d/m/Y');
        $link = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $date;

        $xml = @simplexml_load_string(fn_get_contents($link));

        $sbrf_currencies = self::format_sbrf_currensies($xml);

        if (empty($sbrf_currencies) || ($primary_currency != CURRENCY_RUB && !isset($sbrf_currencies[$primary_currency]))) {
            return false;
        }

        $currencies = Registry::get('currencies');

        if ($primary_currency != CURRENCY_RUB) {

            if (isset($sbrf_currencies[$primary_currency]) && isset($currencies[CURRENCY_RUB])) {
                $primary_coefficient = $sbrf_currencies[$primary_currency]['nominal'] / $sbrf_currencies[$primary_currency]['value'];
                $currency_data = array(
                    'coefficient' => $primary_coefficient,
                );
                db_query("UPDATE ?:currencies SET ?u WHERE  currency_code = ?s", $currency_data, CURRENCY_RUB);
            }

            unset($sbrf_currencies[$primary_currency]);
            foreach ($currencies as $curr_code => $curr_data) {
                if (isset($sbrf_currencies[$curr_code])) {
                    $coefficient_rub = $sbrf_currencies[$curr_code]['nominal'] / $sbrf_currencies[$curr_code]['value'];
                    $currency_data = array(
                        'coefficient' => $primary_coefficient / $coefficient_rub,
                    );

                    db_query("UPDATE ?:currencies SET ?u WHERE currency_code = ?s ", $currency_data, $curr_code);
                }
            }

        } else {

            foreach ($currencies as $curr_code => $curr_data) {
                if (isset($sbrf_currencies[$curr_code])) {
                    $currency_data = array(
                        'coefficient' => $sbrf_currencies[$curr_code]['value'] / $sbrf_currencies[$curr_code]['nominal'],
                    );

                    db_query("UPDATE ?:currencies SET ?u WHERE currency_code = ?s ", $currency_data, $curr_code);
                }
            }
        }

        return true;
    }

    public static function format_sbrf_currensies($xml)
    {
        if (!is_object($xml) && !isset($xml->Valute)) {
            return array();
        }

        $sbrf_currencies = array();

        foreach ($xml->Valute as $valute) {
            $sbrf_cur_code = (string) $valute->CharCode;
            $sbrf_cur_rate = floatval(str_replace(',', '.', $valute->Value));
            $sbrf_cur_nominal = floatval(str_replace(',', '.', $valute->Nominal));
            $sbrf_currencies[$sbrf_cur_code]['value'] = $sbrf_cur_rate;
            $sbrf_currencies[$sbrf_cur_code]['nominal'] = $sbrf_cur_nominal;
        }

        return $sbrf_currencies;
    }

}
