<?php

namespace app\components;

/**
 * Helper Curl
 * @author gambit
 */
class Curl
{

    const ERROR_CURL = 100;

    public static $option = [];

    public static function getData($uid, Provider $vendor)
    {
        $login = $vendor->username; // Логин 'efin2012@yandex.ru'
        $passwd = $vendor->password; // Пароль
        $user_cookie_file = dirname(__DIR__) . '/../cookies.txt';

        $ch = curl_init($vendor->urlResponse);
        self::setCurlOption($ch);
        curl_setopt($ch, CURLOPT_URL, $vendor->urlResponse);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "from=passport&display=page&login=$login&passwd=$passwd");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file);
        $headerText = curl_exec($ch);
        if (!$headerText) {
            $error = curl_error($ch) . '(' . curl_errno($ch) . ')';
            return "Ошибка:" . $error;
        } else {
            return $headerText;
        }
        curl_close($ch);
    }

    public static function setCurlOption($ch)
    {
        foreach (self::$option as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (!isset(self::$option['user_agent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        }
    }

    public static function auth(Provider $vendor)
    {
        $login = $vendor->username; //Логин
        $passwd = $vendor->password; //Пароль
        $user_cookie_file = $_SERVER['DOCUMENT_ROOT'] . '/cookies.txt';

        $ch = curl_init($vendor->urlAuth);
        curl_setopt($ch, CURLOPT_URL, $vendor->urlAuth);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "from=passport&display=page&login=$login&passwd=$passwd");
        $headerText = curl_exec($ch);
        if (!$headerText) {
            $error = curl_error($ch) . '(' . curl_errno($ch) . ')';
            $vendor->setIsAuth(false);
            return "Ошибка:" . $error;
        } else {
            $vendor->setIsAuth(true);
            return $headerText;
        }
        curl_close($ch);
    }
}
