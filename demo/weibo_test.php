<?php
/**
 * Created by PhpStorm.
 * User: lyon
 * Date: 17-3-25
 * Time: 下午7:50
 */

ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

define('APP_PATH', dirname(__FILE__));
define('DATA_PATH', APP_PATH. '/../../data/');

$searchName = '食品安全';
$url = 'http://s.weibo.com/weibo/'. urlencode($searchName). '&page=1';

requests::set_cookies("SUB=_2A2510i0zDeRxGeNI7lcZ8i3MzDmIHXVWphn7rDV8PUNbmtBeLWjukW9e2ITE9pYvuLi0IrusFV1ZGa8ojw..", "s.weibo.com");
//$html = UnicodeToUtf8::remove(UnicodeToUtf8::unicodeDecode(requests::get($url)));
//file_put_contents(DATA_PATH. '1.html', $html);
//exit;
$html = file_get_contents(DATA_PATH. 'save.csv');

$regex = '/<div class="feed_content wbcon">.*?<\\/div>/';
$data = selector::select($html, $regex, 'regex');
//file_put_contents(DATA_PATH. 'deal/'. 'aa.html', $data);

foreach ($data as $key => $value) {
    $fileName = $searchName. '_'. $key. '.txt';
    $value = strip_tags($value);
    file_put_contents(DATA_PATH. 'deal/'. $fileName, $value);
}




exit;