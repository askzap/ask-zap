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
use Tygh\RestClient;
use Tygh\Settings;

class ApiClient
{

    protected $options;
    protected $campaign_id;
    protected $client;

    public function __construct()
    {
        $this->options = Registry::get('addons.yandex_market');

        // OAuth
        $auth = sprintf('OAuth oauth_token="%s", oauth_client_id="%s", oauth_login="%s"',
            $this->options['ym_auth_token'],
            $this->options['ym_application_id'],
            $this->options['user_login']
        );

        $headers = array(
            'Authorization: ' . $auth
        );

        // Client
        $this->client = new RestClient($this->options['ym_api_url'], null, null, null, $headers, 'json');

        // Campaign_id
        $this->campaign_id = $this->options['campaign_id'];
        if ($pos = strpos($this->campaign_id, '-')) {
            $this->campaign_id = substr($this->campaign_id, $pos + 1);
        }
    }

    public function orderStatusUpdate($ym_order_id, $data)
    {
        $path = 'campaigns/' . $this->campaign_id . '/orders/' . $ym_order_id . '/status.json';
        $data = array(
            'order' => $data
        );

        $counter = 1;
        while (true) {
            try {
                $res = $this->client->put($path, $data);
                return $res;
            } catch (\Pest_BadRequest $e) {
                $data = json_decode($e->getMessage(), true);
                if (!empty($data['error']['message'])) {
                    $message = $data['error']['message'];
                    if (!empty($data['error']['code'])) {
                        $message = $data['error']['code'] . ': ' . $message;
                    }
                    fn_set_notification('E', __('yandex_market'), $message);
                }
                return false;
            } catch (\Pest_Exception $e) {
                if ($counter >= YM_REQUEST_ERROR_REPEATS) {
                    return false;
                }
                $counter ++;
                sleep(YM_REQUEST_ERROR_SLEEP_SECONDS);
            }
        }
    }

    public function auth($code)
    {
        $client = new RestClient(
            'https://oauth.yandex.ru/',
            Registry::get('addons.yandex_market.ym_application_id'),
            Registry::get('addons.yandex_market.ym_application_password'),
            'basic',
            array(),
            ''
        );
        
        try {
            $res = $client->post('token', array(
                'grant_type' => 'authorization_code',
                'code' => $code,
            ));
            $result = json_decode($res, true);
            if (!empty($result['access_token'])) {
                Settings::instance()->updateValue('ym_auth_token', $result['access_token'], 'yandex_market');
            }
        } catch (Pest_Exception $e) {
            throw $e;
        }
    }

    public function test()
    {
        try {
            $res = $this->client->get('campaigns/' . $this->campaign_id . '/region');
        } catch (Pest_Exception $e) {
            $res = $e->getMessage();
        }

        return $res;
    }

}
