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


class LineTestController extends Controller
{
    //
    public function webhook(Request $request)
    {

        //test01 webhook is working
        // return response()->json([
        //     'status'=>true
        // ]);



        //m2.1 回傳user打的資料


        $lineAccessToken = (env('LINE_CHANNEL_ACCESS_TOKEN'));
        $lineChannelSecret = (env('LINE_SECRET'));


        // $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        // if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {

        //     return;
        // }

        // $httpClient = new CurlHTTPClient ($lineAccessToken);
        // $lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

        // try {
        //     $events = $lineBot->parseEventRequest($request->getContent(), $signature);
        //     Log::debug($events);
        //     foreach ($events as $event) {

        //         $replyToken = $event->getReplyToken();
        //           $text = $event->getText();// 得到使用者輸入
        //           dd($text);
        //           $lineBot->replyText($replyToken, $text);// 回復使用者輸入
        //         //$textMessage = new TextMessageBuilder("你好");
        //       //  $lineBot->replyMessage($replyToken, $textMessage);
        //     }
        // } catch (Exception $e) {  
        //     return;
        // }
        // return;

        //m2.2
        Log::info("m2.2");
        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {
            // TODO 不正アクセス
            Log::info("m2.2 TODO 不正アクセス");

            return;
        }

        $httpClient = new CurlHTTPClient($lineAccessToken);
        $lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

        try {
            // イベント取得
            $events = $lineBot->parseEventRequest($request->getContent(), $signature);
            Log::info("events :$events");

            foreach ($events as $event) {
                // ログファイルの設定
                // $file = __DIR__ . "/log.txt"
                // file_put_contents($file, print_r($event, true) . PHP_EOL, FILE_APPEND);
                // 入力した文字取得
                $message = $event->getText();
                Log::info($message);

                $replyToken = $event->getReplyToken();
                $textMessage = new TextMessageBuilder($message);
                $lineBot->replyMessage($replyToken, $textMessage);
            }
        } catch (Exception $e) {
            // TODO 例外
            return;
        }
        return;
    }
}
