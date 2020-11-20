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

        $users = DB::table('users')->get();

        $subject = env("SUBJECT", "【PPS■ニュース】　2020年11月10日　ナショジオ写真家 アイラ・ブロックの世界");
        $url = env("URL", "http://www.ppsjp.com/news/201110/distro.html");
        $group = env("GROUP", "0");

        $recipients = [
            '0' => ['m-ziauddin@t-mark.co.jp','ziauddin.sarker@gmail.com'],
            '1' => ['m-ziauddin@t-mark.co.jp',],
        ];

        $doc = phpQuery::newDocumentFileHTML($url);

       foreach ($users as $user) {
           Mail::to($user->email)->send(new EmailDemo());
       }
        return response()
            ->json(['message' => 'Email has been sent.']);
    }
}
