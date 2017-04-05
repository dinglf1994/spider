<?php
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

define('APP_PATH', dirname(__FILE__));
define('DATA_PATH', APP_PATH. '/../../data/');

$configs = array(
    'name' => '大微博',
    'tasknum' => 1,
    //'save_running_state' => true,
    'log_show' => false,
    'interval' => 15000,
    'user_agents' => array(
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.11 Safari/537.36",
        /*"Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13G34 S
afari/601.1",
        "Mozilla/5.0 (Linux; U; Android	6.0.1;zh_cn; Le X820 Build/FEXCNFN5801507014S) AppleWebKit/537.36 (KHTML, like Gecko)Version/4.0 Chrome/49.0.0.0 Mobile	Safari/537.36 EUI Browser/5.8.015S",*/
        ),
    /*'client_ips' => array(

    ),*/
    'domains' => array(
        's.weibo.com',
    ),
    'scan_urls' => array(
        'http://s.weibo.com/weibo/'. urlencode('食品安全'). '&page=1', // 随便定义一个入口，要不然会报没有入口url错误，但是这里其实没用
    ),
    'list_url_regexes' => array(

    ),
    'content_url_regexes' => array(
        'http://s.weibo.com/weibo/.*page=\d+',
    ),
    'export' => array(
        'type' => 'csv',
        'file' => DATA_PATH. 'save.csv',
    ),
    'fields' => array(
        // 微博内容
        array(
            'name' => "content",
            'selector_type' => 'regex',
            'selector' => "/<div class=\"feed_content wbcon\">.*?<\\/div>/",
            'required' => true,
            'repeated'	=>	true,
        ),
    ),
);

$spider = new phpspider($configs);

$spider->on_start = function($phpspider) 
{
//    requests::set_header('Referer','http://www.mafengwo.cn/mdd/citylist/21536.html');
//    $cookie = requests::get_cookie("SUB", "s.weibo.com"); // 设置cookie 到weibo.com 域名下
//    requests::set_cookies("SUB", $cookie, "weibo.com");
      requests::set_cookies("SUB=_2A251312FDeRxGeNI7lcZ8i3MzDmIHXVWrchNrDV8PUNbmtBeLW3akW8L4aIAbwpTwdAjrWcJbSrb3rRkeA..", "s.weibo.com");
};
$spider->on_download_page = function ($page, $phpspider)
{
    $id = requests::$id;
    $page['raw'] = UnicodeToUtf8::remove(UnicodeToUtf8::unicodeDecode($page['raw']));
    file_put_contents(DATA_PATH. $id . '.html', $page['raw']);
    requests::$id++;
    return $page;
};
$spider->on_list_page = function($page, $content, $phpspider)
{
    //
    for($i = 1; $i<20; $i++) {
        $url = 'http://s.weibo.com/weibo/'. urlencode('食品安全'). '&page='. $i;
        $phpspider->add_url($url);
    }
    return true;
};
$spider->on_content_page = function ($page, $content, $phpspider)
{
    if (preg_match('/.*page=1/', $page['request']['url'])) {
        $preg = '/<a href=\"\\/weibo\\/.{0,100}\\"suda-data=\'key=tblog_search_weibo&value=weibo_page_1\' >第(\d+)页<\\/a>/';
        //echo $preg;exit;
        preg_match_all($preg, $content, $info);
        if (is_array($info)) {
            $maxPage = array_pop($info[1]);
        }
        for($i = 1; $i<=$maxPage; $i++) {
            $url = 'http://s.weibo.com/weibo/'. urlencode('食品安全'). '&page='. $i;
            $phpspider->add_url($url);
        }
    }
    return true;
};
/*$spider->on_content_page = function($page, $content, $phpspider)
{
    //添加搜索页
//    $url = 'http://s.weibo.com/weibo/'. urlencode('食品安全'). '&page=1';
//    $phpspider->add_url($url);
    return false;
};*/
$spider->start();
