<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\LineService;
use Illuminate\Support\Str;



class LineLoginController extends Controller
{
    public function lineLogin()
    {
        // CSRF防止のためランダムな英数字を生成
        $state = Str::random(32);
  
        // リプレイアタックを防止するためランダムな英数字を生成
        $nonce  = Str::random(32);
      
        $uri ="https://access.line.me/oauth2/v2.1/authorize?";
        $response_type = "response_type=code";
        $client_id = "&client_id=1654949919";
        $redirect_uri ="&redirect_uri=http://localhost:8000/callback";
        // $state_uri = "&state=".$state;
        $state_uri = "&state=test";
        // $scope = "&scope=openid";
        $scope = "&scope=openid%20profile";
        // $prompt = "&prompt=consent";
        $nonce_uri = "&nonce=$nonce ";
  
        $uri = $uri . $response_type . $client_id . $redirect_uri . $state_uri . $scope. $nonce_uri; ;
        // . $prompt . $nonce_uri;
  
        return redirect($uri);
  

        //https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=1654949919&redirect_uri=http://localhost/callback&state=12345abcde&scope=profile%20openid&nonce=09876xyz


        //https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=剛剛的ChannelID&redirect_uri=剛剛設定的Callback網址&scope=openid%20profile&nonce=隨意亂數

    }


    public function getAccessToken($req)
    {
  
      $headers = [ 'Content-Type: application/x-www-form-urlencoded' ];
      $post_data = array(
        'grant_type'    => 'authorization_code',
        'code'          => $req['code'],
        'redirect_uri'  => 'http://localhost:8000/callback',
        'client_id'     => '1654949919',
        'client_secret' => '50cd3fbeaca7ad2f4e2d388b2a3bcdbd'
      );
      $url = 'https://api.line.me/oauth2/v2.1/token';
  
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
  
      $res = curl_exec($curl);
      curl_close($curl);
  
      $json = json_decode($res);
      $accessToken = $json->access_token;
  
      return $accessToken;
  
    }


    public function getProfile($at)
    {
  
      $curl = curl_init();
  
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $at));
      curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/profile');
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  
      $res = curl_exec($curl);
      curl_close($curl);
  
      $json = json_decode($res);
  
      return $json;
  
    }

    public function callback(Request $request)
    {
        // var_dump($request);
      //LINEからアクセストークンを取得
      $accessToken = $this->getAccessToken($request);
    //   dd($accessToken);
      //プロフィール取得
      $profile = $this->getProfile($accessToken);
    dd($profile);
      return view('callback', compact('profile'));
  
    }
    
    
    //v1
    // protected $lineService;

    // public function __construct(LineService $lineService)
    // {
    //     $this->lineService = $lineService;
    // }

    // public function pageLine()
    // {
    //     $url = $this->lineService->getLoginBaseUrl();
    //     return view('line')->with('url', $url);
    // }

    // public function LoginCallBack(Request $request)
    // {
    //     try {
    //         $error = $request->input('error', false);
    //         if ($error) {
    //             throw new Exception($request->all());
    //         }
    //         $code = $request->input('code', '');
    //         $response = $this->lineService->getLineToken($code);
    //         $user_profile = $this->lineService->getUserProfile($response['access_token']);
    //         echo "<pre>"; print_r($user_profile); echo "</pre>";
    //     } catch (Exception $ex) {
    //         Log::error($ex);
    //     }
    // }
}
