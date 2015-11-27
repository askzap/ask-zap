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

class Url
{
    const PUNYCODE_PREFIX = 'xn--';

    /**
     * @var string Input URL
     */
    protected $string_url;

    /**
     * @var array Result of parse_url() function
     */
    protected $parsed_url;

    /**
     * @var array Query parameters list that will be used when building URL
     */
    protected $query_params = array();

    /**
     * @var bool Was input URL encoded
     */
    protected $is_encoded = false;

    /**
     * Creates URL object and parses given URL to its components.
     *
     * @param string $url Input URL
     */
    public function __construct($url)
    {
        $this->string_url = trim($url);

        $this->parsed_url = parse_url($this->string_url);

        // Gracefully supress potential errors
        if ($this->parsed_url === false) {
            $this->parsed_url = array();
        }

        if (isset($this->parsed_url['query'])) {
            $query_string = $this->parsed_url['query'];

            if (strpos($query_string, '&amp;') !== false) {
                $this->is_encoded = true;
                $query_string = str_replace('&amp;', '&', $query_string);
            }

            parse_str($query_string, $this->query_params);
        }
    }

    /**
     * Sets URL schema.
     *
     * @param $protocol
     */
    public function setProtocol($protocol)
    {
        $this->parsed_url['scheme'] = $protocol;
    }

    /**
     * @return string|null URL schema if it exists, null otherwise.
     */
    public function getProtocol()
    {
        return isset($this->parsed_url['scheme']) ? $this->parsed_url['scheme'] : null;
    }

    /**
     * @return bool Whether input URL was encoded
     */
    public function getIsEncoded()
    {
        return $this->is_encoded;
    }

    /**
     * @return array List of query parameters and their values
     */
    public function getQueryParams()
    {
        return $this->query_params;
    }

    /**
     * Sets query parameters
     *
     * @param array $params Query parameters and their values
     */
    public function setQueryParams(array $params)
    {
        $this->query_params = $params;
    }

    /**
     * Removes given query parameters from query string.
     *
     * @param array $param_names Parameter names
     */
    public function removeParams(array $param_names)
    {
        foreach ($param_names as $param_name) {
            if (isset($this->query_params[$param_name])) {
                unset ($this->query_params[$param_name]);
            }
        }
    }

    /**
     * Creates string representation of URL from current state of the object.
     *
     * @param bool $encode Whether to encode ampersands
     *
     * @return string Result URL
     */
    public function build($encode = false)
    {
        $query_string = http_build_query($this->query_params, null, ($encode ? '&amp;' : '&'));

        if (!empty($query_string)) {
            $this->parsed_url['query'] = $query_string;
        } elseif (isset($this->parsed_url['query'])) {
            unset ($this->parsed_url['query']);
        }

        $scheme = isset($this->parsed_url['scheme']) ? $this->parsed_url['scheme'] . '://' : '';
        $host = isset($this->parsed_url['host']) ? $this->parsed_url['host'] : '';
        $port = isset($this->parsed_url['port']) ? ':' . $this->parsed_url['port'] : '';
        $user = isset($this->parsed_url['user']) ? $this->parsed_url['user'] : '';
        $pass = isset($this->parsed_url['pass']) ? ':' . $this->parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($this->parsed_url['path']) ? $this->parsed_url['path'] : '';
        $query = isset($this->parsed_url['query']) ? '?' . $this->parsed_url['query'] : '';
        $fragment = isset($this->parsed_url['fragment']) ? '#' . $this->parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Normalize URL to pass it to parse_url function
     * @param  string $url URL
     * @return string normalized URL
     */
    private static function fix($url)
    {
        $url = trim($url);
        $url = preg_replace('/^(http[s]?:\/\/|\/\/)/', '', $url);

        if (!empty($url)) {
            $url = 'http://' . $url;
        }

        return $url;
    }

    /**
     * Cleans up URL, leaving domain and path only
     * @param  string $url URL
     * @return string cleaned up URL
     */
    public static function clean($url)
    {
        $url = self::fix($url);
        if ($url) {
            $domain = self::normalizeDomain($url);
            $path = parse_url($url, PHP_URL_PATH);

            return $domain . rtrim($path, '/');
        }

        return '';
    }

    /**
     * Normalizes domain name and punycode's it
     * @param  string $url URL
     * @return mixed  string with normalized domain on success, boolean false otherwise
     */
    public static function normalizeDomain($url)
    {
        $url = self::fix($url);
        if ($url) {
            $domain = parse_url($url, PHP_URL_HOST);
            $port = parse_url($url, PHP_URL_PORT);
            if (!empty($port)) {
                $domain .= ':' . $port;
            }
            if (strpos($domain, self::PUNYCODE_PREFIX) !== 0) {
                $idn = new \Net_IDNA2();
                $domain = $idn->encode($domain);
            }

            return $domain;
        }

        return false;
    }

    /**
     * Decodes punycoded'd URL
     * @param  string $url URL
     * @return mixed  string with decoded URL on success, boolean false otherwise
     */
    public static function decode($url)
    {
        $url = self::fix($url);
        if ($url) {
            $components = parse_url($url);
            $host = $components['host'] . (empty($components['port']) ? '' : ':' . $components['port']);

            if (strpos($host, self::PUNYCODE_PREFIX) !== false) {
                $idn = new \Net_IDNA2();
                $host = $idn->decode($host);
            }

            $path = !empty($components['path']) ? $components['path'] : '';

            return $host . rtrim($path, '/');
        }

        return false;
    }

    /**
     * Resolves relative url
     *
     * @param string $url  relative url
     * @param string $base url base
     *
     * @return string $url resolved url
     */
    public static function resolve($url, $base)
    {
        if ($url[0] == '/') {
            $_pbase = parse_url(self::fix($base));
            $url = $_pbase['protocol'] . '://' . $_pbase['host'] . $url;
        } else {
            $url = $base . '/' . $url;
        }

        return $url;
    }
}
