<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use phpQuery;

class EmailDemo extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '【PPS■ニュース】　2020年11月10日　ナショジオ写真家 アイラ・ブロックの世界';
        $url = 'http://ziauddins.com/';
        $doc = phpQuery::newDocumentFileHTML($url,'utf-8');
        function rel2abs($rel, $base)
        {
            if(strpos($rel,"//")===0)
            {
                return "http:".$rel;
            }

            /* return if  already absolute URL */
            if  (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
            /* queries and  anchors */
            if ($rel[0]=='#'  || $rel[0]=='?') return $base.$rel;
            /* parse base URL  and convert to local variables:
            $scheme, $host,  $path */
            extract(parse_url($base));
            /* remove  non-directory element from path */
            $path = preg_replace('#/[^/]*$#',  '', $path);
            /* destroy path if  relative url points to root */
            if ($rel[0] ==  '/') $path = '';
            /* dirty absolute  URL */
            $abs =  "$host$path/$rel";
            /* replace '//' or  '/./' or '/foo/../' with '/' */
            $re =  array('#(/.?/)#', '#/(?!..)[^/]+/../#');
            for($n=1; $n>0;  $abs=preg_replace($re, '/', $abs, -1, $n)) {}
            /* absolute URL is  ready! */
            return  $scheme.'://'.$abs;
        }

        foreach($doc['img'] as $img) {
            $src = $img->getAttribute('src');
            $src = rel2abs($src, $url);
            $img->setAttribute('src', $src);
        }
        return $this->subject($subject)->html($doc);
    }
}
