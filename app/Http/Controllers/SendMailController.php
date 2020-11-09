<?php

namespace App\Http\Controllers;

use App\Mail\EmailDemo;
use http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use phpQuery;


class SendMailController extends Controller
{

   public function index()
    {
        try {
            DB::connection()->getPdo();
            print("Connect Successfully");

        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e);
        }

        //$users = DB::table('user')->where('user_id','=',37605)->get();

        $subject = env("SUBJECT", "【PPS■ニュース】　2020年11月10日　ナショジオ写真家 アイラ・ブロックの世界");
        $url = env("URL", "http://www.ppsjp.com/news/201110/distro.html");
        $group = env("GROUP", "0");
        //\Mail::to('your_receiver_email@gmail.com')->send(new \App\Mail\MyTestMail($details));
        print($subject);
        print($url);
        print($group);

        $recipients = [
            '0' => ['m-ziauddin@t-mark.co.jp','ziauddin.sarker@gmail.com'],
            '1' => ['m-ziauddin@t-mark.co.jp',],
        ];

        $doc = phpQuery::newDocumentFileHTML($url);
        $email = 'm-ziauddin@t-mark.co.jp';
        if(in_array($email, $recipients[$group])) {
//            dd(in_array($email, $recipients[$group]));
        }


        Mail::to($email)->send(new EmailDemo());

        return response()
            ->json(['message' => 'Email has been sent.']);
    }


}
