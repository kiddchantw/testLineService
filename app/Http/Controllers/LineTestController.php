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
                Log::info($requestInput);

                $str_response = "";




                $channelSecret = $lineChannelSecret; // Channel secret string
                $httpRequestBody = '...'; // Request body string
                $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
                $signature = base64_encode($hash);

                // Compare X-Line-Signature request header string and the signature
                $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($lineAccessToken);
                $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $lineAccessToken]);

//                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($requestInput);
//            $response = $bot->replyMessage($requestReplyToken, $textMessageBuilder);
//            Log::info($response->getHTTPStatus());
//            Log::info($response->getRawBody());



            /////測試區
            $messageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();

            // 回覆文字
            $text = new LINEBot\MessageBuilder\TextMessageBuilder($requestInput);
            $messageBuilder->add($text);

            $text2 = new LINEBot\MessageBuilder\TextMessageBuilder($requestInput."aaaa");
            $messageBuilder->add($text2);

            //地點ok
            $location = new LINEBot\MessageBuilder\LocationMessageBuilder('地點', '台中', '24.147666', '120.673552');
            $messageBuilder->add($location);

            //指定官方的貼圖
            $sticker = new LINEBot\MessageBuilder\StickerMessageBuilder('11537', '52002734');
            $messageBuilder->add($sticker);




            // 回覆相片訊息
            $image = new LINEBot\MessageBuilder\ImageMessageBuilder(
                'https://raw.githubusercontent.com/kiddchantw/testLineService/master/public/btn_login_base.png',
                'https://raw.githubusercontent.com/kiddchantw/testLineService/master/public/btn_login_base.png'
            );
            $messageBuilder->add($image);



            $response = $bot->replyMessage($requestReplyToken, $messageBuilder);

            if ($response->isSucceeded()) {
                logger('reply  successfully');
            } else {
                logger('reply error');
                Log::warning($response->getRawBody());
                Log::warning('reply sticker failure');
            }





//            $actions = array(
//                new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("是", "ans=Y"),
//                new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("否", "ans=N")
//            );
//            $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder("問題", $actions);
//            $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
//            $bot->replyMessage($requestReplyToken,$msg);
////                $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("按鈕文字","說明");
//            $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("按鈕文字","說明", $img_url, $actions);
//
//            $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
//                $bot->replyMessage($requestReplyToken,$msg);


        }
}
