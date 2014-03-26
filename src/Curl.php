<?php

namespace CL\ComposerInit;

use Exception;
use Closure;

class Curl
{
    private static function execute(array $options)
    {
        $curl = curl_init();

        $defaultOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'Composer Init Script',
        );

        $options = $defaultOptions + $options;

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        if (($error = self::getError($curl))) {
            throw new Exception($error);
        }

        curl_close($curl);

        return $response;
    }

    private static function getError($curl)
    {
        $error = curl_error($curl);

        if ($error) {
            $url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
            return sprintf('Request for %s returned %s', $url, $error);
        }

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code and $code < 200 or $code >= 300) {
            return sprintf('The server returned error code %s', $code);
        }
    }

    public static function get($url)
    {
        $options = array(CURLOPT_URL => $url);

        return self::execute($options);
    }

    public static function getJSON($url)
    {
        $data = json_decode(self::get($url), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception('Unable to parse response body into JSON: ' . json_last_error());
        }

        return $data === null ? array() : $data;
    }

    public static function download($url, $to)
    {
        $file = fopen($to, 'w');
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FILE => $file,
        );

        self::execute($options);

        fclose($file);
    }
}
