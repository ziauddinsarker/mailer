<?php

namespace App\Http\Controllers;

use http\Env;
use Illuminate\Http\Request;
use phpQuery;
use Swift;
use Swift_Mailer;
use Swift_DependencyContainer;
use Swift_Preferences;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $subject = env('SUBJECT');
        $url = env('URL');
        $group = env('GROUP');

        return view('home', compact('subject','url','group'));
    }

    public function sendMail(Request $request)
    {
        $subject = $request->subject;
        $url = $request->url;
        $group = $request->group;


        $recipients = [
            '0' => ['matthew.long@t-mark.co.jp', 'muhammad.suhaib@t-mark.co.jp'],
            '1' => ['m-ziauddin@t-mark.co.jp',],
            '2' => ['shinsuke.shinozaki@t-mark.co.jp', 'imaptest@t-mark.co.jp', 'pps@t-mark.co.jp', 'yomura@t-mark.co.jp'],
            '3' => ['nshimizu@yuzawa-kodama.org', 'suketto@ppsjp.com', 't-mark@ppsjp.com'],
            '4' => ['matthew.long@t-mark.co.jp', 't-marknonexistent@nonexistentdomainasdf.com', 't-marknonexistentgmail@gmail.com']
        ];


        $doc = phpQuery::newDocumentFileHTML($url);
//        function rel2abs($rel, $base)
//        {
//            if(strpos($rel,"//")===0)
//            {
//                return "http:".$rel;
//            }
//            /* return if  already absolute URL */
//            if  (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
//            /* queries and  anchors */
//            if ($rel[0]=='#'  || $rel[0]=='?') return $base.$rel;
//            /* parse base URL  and convert to local variables:
//            $scheme, $host,  $path */
//            extract(parse_url($base));
//            /* remove  non-directory element from path */
//            $path = preg_replace('#/[^/]*$#',  '', $path);
//            /* destroy path if  relative url points to root */
//            if ($rel[0] ==  '/') $path = '';
//            /* dirty absolute  URL */
//            $abs =  "$host$path/$rel";
//            /* replace '//' or  '/./' or '/foo/../' with '/' */
//            $re =  array('#(/.?/)#', '#/(?!..)[^/]+/../#');
//            for($n=1; $n>0;  $abs=preg_replace($re, '/', $abs, -1, $n)) {}
//            /* absolute URL is  ready! */
//            return  $scheme.'://'.$abs;
//        }
//
//        foreach($doc['img'] as $img) {
//            $src = $img->getAttribute('src');
//            $src = rel2abs($src, $url);
//            $img->setAttribute('src', $src);
//        }

        Swift::init(function () {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');

            Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
        });

        // Create the mail transport configuration
        //$transport = Swift_MailTransport::newInstance(getenv('SMTP_HOST'), 25);
//        $transport = (new Swift_SmtpTransport('52.199.33.93', 25))
//            ->setUsername('your username')
//            ->setPassword('your password')
//        ;
//        $transport = Swift_Mailer::newInstance();
        $mailer = Swift_Mailer::newInstance($transport);

        $mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));


        if ($group == '9') {
            $res = $mysqli->query('SELECT * FROM user WHERE user_opt_in = 1');
        } else {
            //This query is to check if its group 9 or not and  user is in the database or not.
            //If you don't add email or add domain like t-mark.co.jp added, then email will not sent out to your newly added email addresses
            $res = $mysqli->query('SELECT * FROM user WHERE user_email = "t-marknonexistent@nonexistentdomainasdfa.com" OR user_email = "t-marknonexistentgmail@gmail.com" OR user_email = "nshimizu@yuzawa-kodama.org" OR user_email LIKE "%t-mark.co.jp" OR user_email LIKE "%ppsjp.com"');
        }

//$message = Swift_Message::newInstance(getenv('SMTP_HOST'), 25);
        $message = Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom(array('webuser@ppsjp.com' => 'PPS通信社'));

        $count = 0;

#Create a recipient file if it does not exist, and slurp it in.
//        touch($recipient_file);
//        $previous_recipients = explode("\n", file_get_contents($recipient_file));

        while ($row = $res->fetch_assoc()) {

            $count++;

            //Every 10 emails we pause for 2 seconds so we don't overload the server.
            if ($count % 10 == 0) {
                sleep(2);
            }

            $email = $row['user_email'];
            $memo = $row['user_memo'];

            #We skip over people with particular emails, along with people who have "trouble" in their memo.
            if (strpos($email, 'mail.dnp.co.jp') !== false || strpos($email, 'mitsumura.com') !== false || strpos($memo, 'trouble') !== false) {
                echo "SKIPPING $email, bad account.\n";
                continue;
            }

            if (!($group == '9' || in_array($email, $recipients[$group]))) {
                continue;
            }

//            if(in_array($email, $previous_recipients)) {
//                echo "SKIPPING $email, already sent\n";
//                continue;
//            }

            $body_with_substitutions = $doc;

            $body_with_substitutions = preg_replace('/<%%user_name%%>/', $email, $body_with_substitutions);
            $body_with_substitutions = preg_replace('/&lt;%%user_name%%&gt;/', $email, $body_with_substitutions);
            $body_with_substitutions = preg_replace('/%3C%%user_name%%%3E/', $email, $body_with_substitutions);
            $body_with_substitutions = preg_replace('/<%%user_email%%>/', $email, $body_with_substitutions);
            $body_with_substitutions = preg_replace('/<%%user_real_name%%>/', $row['user_real_name'], $body_with_substitutions);
            $body_with_substitutions = preg_replace('/<%%user_pass%%>/', $row['user_pass'], $body_with_substitutions);
            $body_with_substitutions = preg_replace('/&lt;%%user_pass%%&gt;/', $row['user_pass'], $body_with_substitutions);
            $body_with_substitutions = preg_replace('/%3C%%user_pass%%%3E/', $row['user_pass'], $body_with_substitutions);

            $message->setBody($body_with_substitutions, "text/html");

            echo "SENDING TO: $email\n";

            $sender = 'user-' . $row['user_id'] . '@pps-bounce.t-mark.co.jp';

            try {
                $message->setTo(array(
                    $email
                ));

                $message->setSender(
                    $sender
                );

                if (getenv('REAL_SEND')) {
                    $mailer->send($message);
                }
            } catch (Exception $e) {
                echo "skipping email $email, $e.\n";
            }

//            $previous_recipients[] = $email;
//            file_put_contents($recipient_file, join("\n", $previous_recipients));

        }
    }




}
