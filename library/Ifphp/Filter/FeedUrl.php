<?php

class Ifphp_Filter_FeedUrl implements Zend_Filter_Interface
{
    public function filter($feedUrl)
    {
        //strip http://
        $feedUrl = ltrim($feedUrl,"http://");
        $feedUrl = rtrim($feedUrl,"/");
        return rtrim($feedUrl);
    }
}
