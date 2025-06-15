<?php

namespace Proxy\Api;

require_once __DIR__ . "/../vendor/autoload.php";

class Api
{
    const apiEndpoint = "https://core-api.vabase.com/"; 
    public static $client;
    public static $token;

    public static function __constructStatic()
    {
        self::$client = new \GuzzleHttp\Client([
            'verify' => false,
            'http_errors' => false,
        ]);
    }

    private static function getAccessToken()
    {
        if (isset($_SESSION['token'])) {
            if (isset($_SESSION['token_expires'])) {
                if (($_SESSION['token_expires'] > new \DateTime())) {
                    return $_SESSION['token'];
                }
            }
        }
        $res = self::$client->request('POST', self::apiEndpoint . 'v1/token', [
            'json' => client_api_key,
        ]);
        self::$token = json_decode($res->getBody());
        $expires = new \DateTime();
        $expires->modify("+120 minutes");
        $_SESSION['token_expires'] = $expires;
        $_SESSION['token'] = self::$token;
        return self::$token;
    }

    public static function updateAuthAccessToken(string $token)
    {
        $expires = new \DateTime();
        $expires->modify("+150 minutes");
        $_SESSION['token_expires'] = $expires;
        $_SESSION['token'] = $token;
    }

    public static function sendAsync(string $method, string $action, $data)
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . self::getAccessToken(),
                'content-type' => 'application/json',
            ],
        ]);

        $request = new \GuzzleHttp\Psr7\Request($method, self::apiEndpoint . $action, [
            'json' => $data,
            'http_errors' => false,
        ]);
        $promise = $client->sendAsync($request)->then(function ($res) {
            return $res;
        });
        return $promise->wait();
    }

    public static function sendSync(string $method, string $action, $data)
    {
        $res = self::$client->request($method, self::apiEndpoint . $action, [
            'json' => $data,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . self::getAccessToken(),
                'content-type' => 'application/json',
            ],
        ]);
        return $res;
    }
}
