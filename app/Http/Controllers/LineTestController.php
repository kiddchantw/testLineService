<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Exception;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

use Illuminate\Support\Facades\Log;

use Modules\Line\Constant\LineHookHttpResponse;


class LineTestController extends Controller
{
        //
        public function webhook(Request $request)
        {


                //test01 webhook is working
                // return response()->json([
                //     'status'=>true
                // ]);


                $lineAccessToken = (env('LINE_CHANNEL_ACCESS_TOKEN'));
                $lineChannelSecret = (env('LINE_SECRET'));

                 Log::info(__FUNCTION__);

            $requestReplyToken = $request['events'][0]['replyToken'];
                $requestInput = $request['events'][0]['message']['text'];


                $channelSecret = $lineChannelSecret; // Channel secret string
                $httpRequestBody = '...'; // Request body string
                $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
                $signature = base64_encode($hash);

                // Compare X-Line-Signature request header string and the signature
                $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($lineAccessToken);
                $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $lineAccessToken]);

                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($requestInput);
                $response = $bot->replyMessage($requestReplyToken, $textMessageBuilder);

                Log::info($response->getHTTPStatus());
                Log::info($response->getRawBody());
        }
}
