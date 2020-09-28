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

//                $this->customMultiMessageBuilder($lineChannelSecret,$lineAccessToken,$requestReplyToken,$requestInput);


        $channelSecret = $lineChannelSecret; // Channel secret string
        $httpRequestBody = '...'; // Request body string
        $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
        $signature = base64_encode($hash);

        // Compare X-Line-Signature request header string and the signature
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($lineAccessToken);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $lineAccessToken]);


        switch ($requestInput) {
            case 'T':
                $this->customMultiMessageBuilder($bot, $requestReplyToken, $requestInput);

                break;
            case 'C':
                //            ConfirmTemplateBuilder  ok
                $actions = [
                    new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("是", "ans=Y"),
                    new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("否", "ans=N")
                ];
                $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder("問題 ConfirmTemplateBuilder", $actions);
                $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
                $bot->replyMessage($requestReplyToken, $msg);

                $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("問題", $actions);
                $msg = new \LINE\LINEBot\MessageBuilder\ButtonTemplateBuilder("這訊息要用手機的賴才看的到哦", $button);
                $bot->replyMessage($requestReplyToken, $msg);
                break;
            case 'B' :
                //ButtonTemplateBuilder
                $actions = array(
                    //一般訊息型 action
                    new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("按鈕1", "文字1"),
                    //網址型 action
                    new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("Google", "http://www.google.com"),
                    //下列兩筆均為互動型action
                    new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("下一頁", "page=3"),
                    new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("上一頁", "page=1")
                );

                $img_url = "https://media3.s-nbcnews.com/j/newscms/2019_33/2203981/171026-better-coffee-boost-se-329p_67dfb6820f7d3898b5486975903c2e51.fit-2000w.jpg";
                //"https://raw.githubusercontent.com/kiddchantw/testLineService/master/public/btn_login_base.png";
                $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("按鈕文字 ButtonTemplateBuilder", "說明", $img_url, $actions);
                $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
                $bot->replyMessage($requestReplyToken, $msg);
                break;

            case "L":
                $columns = array();

                $columnInput = array("AAA","BBB","CCC","DDD");
                $columnInput2 = array("Meller 墨樂咖啡","Coffee Cafe","海龜咖啡 SeaTurtle Cafe","ARA Coffee Co");

                $img_url = array(
                    "https://media3.s-nbcnews.com/j/newscms/2019_33/2203981/171026-better-coffee-boost-se-329p_67dfb6820f7d3898b5486975903c2e51.fit-2000w.jpg",
                    "https://media3.s-nbcnews.com/j/newscms/2019_33/2203981/171026-better-coffee-boost-se-329p_67dfb6820f7d3898b5486975903c2e51.fit-2000w.jpg",
                    "https://media3.s-nbcnews.com/j/newscms/2019_33/2203981/171026-better-coffee-boost-se-329p_67dfb6820f7d3898b5486975903c2e51.fit-2000w.jpg",
                    "https://media3.s-nbcnews.com/j/newscms/2019_33/2203981/171026-better-coffee-boost-se-329p_67dfb6820f7d3898b5486975903c2e51.fit-2000w.jpg",
                );
                $gps_url = array(
                    "http://maps.google.com/?cid=4877493905338464027",
                    "http://maps.google.com/?cid=10769310045630248032",
                    "http://maps.google.com/?cid=10754316052420955160",
                    "http://maps.google.com/?cid=5131227195743989137",
                );

                //null; //"圖片網址，必需為 https (圖片非必填欄位)";
                for($index=0;$index<4;$index++) //最多5筆
                {
                    $actions = array(

                        //網址型 action
                        new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("帶我去",$gps_url[$index]),
                          //一般訊息型 action
                        new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("評分","評分")
                    );
                    $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("標題:".$columnInput[$index], "說明:".$columnInput2[$index], $img_url[$index] , $actions);
                    $columns[] = $column;
                }
                $carousel = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns);
                $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $carousel);
                $bot->replyMessage($requestReplyToken,$msg);

                break;
            case "評分":
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("尚未開放此功能");
                $response = $bot->replyMessage($requestReplyToken, $textMessageBuilder);
                break;


            default:
//                $textMessageBuilder ok
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($requestInput);
                $response = $bot->replyMessage($requestReplyToken, $textMessageBuilder);

        }
//        Log::info($response->getHTTPStatus());
//        Log::info($response->getRawBody());

            if ($response->isSucceeded()) {
                logger('reply  successfully');
            } else {
                logger('reply error');
                Log::warning($response->getRawBody());
                Log::warning('reply sticker failure');
            }
    }


    public function customMultiMessageBuilder
    ($bot, $requestReplyToken, $requestInput)
//        ($lineChannelSecret,$lineAccessToken,$requestReplyToken,$requestInput)
    {
//            $channelSecret = $lineChannelSecret; // Channel secret string
//            $httpRequestBody = '...'; // Request body string
//            $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
//            $signature = base64_encode($hash);
//
//            // Compare X-Line-Signature request header string and the signature
//            $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($lineAccessToken);
//            $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $lineAccessToken]);


        $messageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
        // 回覆文字
        $text = new LINEBot\MessageBuilder\TextMessageBuilder($requestInput);
        $messageBuilder->add($text);
        $text2 = new LINEBot\MessageBuilder\TextMessageBuilder($requestInput . " again ");
        $messageBuilder->add($text2);
        //地點
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
    }


}
