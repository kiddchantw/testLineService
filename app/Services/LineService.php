<?php

namespace App\Services;

use GuzzleHttp\Client;

class LineService
{
    public function getLoginBaseUrl()
    {
        // 組成 Line Login Url
        $url = config('line.authorize_base_url') . '?';
        $url .= 'response_type=code';
        $url .= '&client_id=' . config('line.channel_id');
        $url .= '&redirect_uri=' . config('app.url') . '/callback';
        // $url .= '&redirect_uri=' . config('app.url') . '/callback/login';
        // $url .= '&redirect_uri=http://localhost/callback/login';

        $url .= '&state=test'; // 暫時固定方便測試
        $url .= '&scope=openid';
        // $url .= '&scope=openid%20profile';



        // var URL = 'https://access.line.me/oauth2/v2.1/authorize?';
        // URL += 'response_type=code';
        // URL += '&client_id=1654949919';
        // URL += '&redirect_uri=http://localhost/callback';
        // URL += '&state=abcde';
        // URL += '&scope=openid%20profile';

        //https://access.line.me/dialog/oauth/weblogin?response_type=code&client_id=1654949919&redirect_uri=http://localhost/callback&state=test

        return $url;
    }

    public function getLineToken($code)
    {
        
        $client = new Client();
        $response = $client->request('POST', config('line.get_token_url'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                // 'redirect_uri' => config('app.url').'/callback/login',
                'redirect_uri' => config('app.url').'/callback',

                'client_id' => config('line.channel_id'),
                'client_secret' => config('line.secret')
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUserProfile($token)
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        $response = $client->request('GET', config('line.get_user_profile_url'), [
            'headers' => $headers
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}