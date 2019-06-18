<?php
namespace app\index\controller;

use think\Controller;
use Carbon\Carbon;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

class Carbondemo extends Controller
{

    public function testGuzzle()
    {
        $client = new \GuzzleHttp\Client();
        //$response = $client->request('GET', 'https://testzgs.szbchm.com/hm_ucenter/web/index.php?api=Promotion&action=showRewardInformation&userID=123005');
        $response = $client->request('POST', 'https://testzgs.szbchm.com/hm_ucenter/web/index.php',
            [
                'api' => 'Promotion',
                'action' => 'showRewardInformation',
                'userID' => '123005'
            ]
        );
        echo $response->getStatusCode(); # 200
        echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
        echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
    }

    public function getQrcode()
    {

// Create a basic QR code
        $qrCode = new QrCode('Life is too short to be generating QR codes');
        $qrCode->setSize(150);

// Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        //$qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER);
        //$qrCode->setLogoPath(__DIR__.'/../assets/images/symfony.png');
        //$qrCode->setLogoSize(150, 200);
        //$qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        //$qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

// Directly output the QR code
        header('Content-Type: '.$qrCode->getContentType());
        //echo $qrCode->writeString();

// Save it to a file
        $qrCode->writeFile('./qrcode.png');
    }

    //玩玩carbon插件
    public function index()
    {

        /*echo Carbon::now(); //当前年月日时分秒
        echo '<br/>';
        //获取昨天
        echo Carbon::yesterday();
        echo '<br/>';
        //获取今天
        echo Carbon::today();
        echo '<br/>';
        echo Carbon::tomorrow();
        echo '<br/>';
        echo Carbon::now()->addDays(30);*/
        /*echo $dt->addYear(5);
        echo '<br/>';
        echo $dt->addYear();
        echo '<br/>';
        echo $dt->subYear();
        echo '<br/>';
        echo $dt->subYears(5);
        echo '<br/>';
        echo $dt->addMonths(60);
        echo '<br/>';
        echo $dt->addMonth();
        echo '<br/>';
        echo $dt->subMonth();
        echo '<br/>';
        echo $dt->subMonths(60);
        echo $dt->addDays(29);
        echo '<br/>';
        echo $dt->addDay();
        echo '<br/>';
        echo $dt->subDay();
        echo '<br/>';
        echo $dt->subDays(29);
        echo '<br/>';
        //往后四个工作日
        echo $dt->addWeekdays(4);
        echo '<br/>';
        //往后一个工作日
        echo $dt->addWeekday();
        echo '<br/>';
        //往前一个工作日
        echo $dt->subWeekday();
        echo '<br/>';
        echo $dt->subWeekdays(4);
        echo '<br/>';
        //往后三周
        echo $dt->addWeeks(3);
        echo '<br/>';
        //往后一周
        echo $dt->addWeek();
        echo '<br/>';
        //往前一周
        echo $dt->subWeek();
        echo '<br/>';
        echo $dt->subWeeks(3);
        echo '<br/>';*/
        /*echo '<br/>';
        $dt = Carbon::create(2012, 1, 31, 0);
        echo $dt;
        echo '<br/>';
        //往后添加24小时
        echo $dt->addHours(24);
        echo '<br/>';
        //往后添加一个小时
        echo $dt->addHour();
        echo '<br/>';
        //往前一个小时
        echo $dt->subHour();
        echo '<br/>';
        //往前24个小时
        echo $dt->subHours(24);*/
        echo '<br/>';
        $dt = Carbon::now();
        //设置参数
        $dt->year = 2019;
        $dt->month = 06;
        $dt->day = 18;
        $dt->hour = 22;
        $dt->minute = 32;
        $dt->second = 5;
        echo '<br/>';
        var_dump($dt->year);
        echo '<br/>';
        var_dump($dt->month);
        echo '<br/>';
        var_dump($dt->dayOfWeek); //1
        echo '<br/>';
        var_dump($dt->dayOfYear);  //167
        echo '<br/>';
        var_dump($dt->weekOfMonth);  //3
        echo '<br/>';
        var_dump($dt->daysInMonth);  // 30
        echo '<br/>';
        echo md5(12);
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        //return $this->fetch();
    }

}
