<?php

namespace CL\ComposerInit;

use Exception;
use Closure;

class Curl {

    private static function execute(array $options)
    {
        $curl = curl_init();

        $defaultOptions = array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
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

        if ($code < 200 OR $code >= 300) {
            return sprintf('The server returned error code %s',  $code);
        }
    }

    private static function progress_options(Closure $callback)
    {
        return array(
            CURLOPT_NOPROGRESS => false,
            CURLOPT_PROGRESSFUNCTION => function($total, $downloaded) use ($callback) {
                $progress = ($downloaded and $total) ? ($downloaded / $total) : 0;
                $callback($progress);
            },
        );
    }

    public static function get($url)
    {
        $options = array(CURLOPT_URL => $url);

        return self::execute($options);
    }

    public static function post($url, array $data)
    {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
        );

        return self::execute($options);
    }

    public static function download($url, $to, Closure $progress = null)
    {
        $file = fopen($to, 'w');
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => FALSE,
            CURLOPT_FILE => $file,
        );

        if ($progress !== null) {
            $options = $options + self::progress_options($progress);
        }

        self::execute($options);

        fclose($file);
    }
}
