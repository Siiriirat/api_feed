<?php

class Api_tmbbank extends CI_Controller{
	var $api = 'https://www.tmbbank.com/rss/view/news';
	var $parent_category_id = 11;
	var $category_id = 185;
	var $credit = 'www.tmbbank.com';

	function index(){
    set_time_limit(3000);

    $content = file_get_contents($this->api);
    $content = str_replace("media:content","mediaContent",$content);
    $content = str_replace("content:encoded","contentEncoded",$content);
    $content = str_replace("media:thumbnail","mediaThumbnail",$content);

    $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
    $json = json_encode($xml);
    $data = json_decode($json,TRUE);
    echo "<pre>";

    $id = 0;
    foreach($data['channel']['item'] as $index=>$value){
      $arr['title'] = $value['title'];
      if(isset($value['description'])){
        $arr['description'] = $value['description'];

        if (empty($arr['description'])) {
          $arr['description'] = '';
        }

      }else{
        $arr['description'] = '';
      }


      $arr['contentdata'] = $value['description'];
      $arr['guid'] = $value['link'];
      $arr['pubDate'] = $value['pubDate'];

      $id = $this->Rss->insert($arr);

      if($id != false){

        $news['category_id'] = $this->category_id;


        $url = trim($value['link']);
        $str = file_get_contents($url);
        @$doc = new DOMDocument();
        @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $str);
        @$doc->saveXML();
        $xpath = new DomXPath($doc);


        $src = $xpath->evaluate("string(//meta[@property='og:image']/@content)");


        $news['title'] = $value['title'];
        $news['description'] = $value['title'];

        if(isset($value['description'])){
          if (empty($value['description'])) {
            $news['description'] = '';
          }
          else {
            $news['description'] = strip_tags($value['description']);
          }
        }else{
          $news['description'] = '';
        }
        $contentdata = $value['description'];
        $news['detail'] = $contentdata;

        if($news['description'] == ''){
          $news['description'] = strip_tags($contentdata);
        }

        $news_id = $this->Content->insert($news);
        unset($news);

        $year_path = 'uploads/'.date('Y');
        if(is_dir($year_path) == false){
          mkdir($year_path,0777);
        }

        $month_path = $year_path.'/'.date('m');
        if(is_dir($month_path) == false){
          mkdir($month_path,0777);
        }

        if($src){
          $filename = $month_path.'/'.$this->Content->slug($arr['title']).'.jpg';
          $content_photo = @file_get_contents($src);
          if($content_photo){
            file_put_contents($filename, $content_photo);
            $filename2 = 'uploads/'.date('Y').'/'.date('m').'/'.$this->Content->slug($arr['title']).'.jpg';
            $news_update['thumbnail'] = $filename2;
          }
        }
        $content_photo = '';


        $news_update['status'] = 1;

        $this->Content->update($news_id,$news_update);
        unset($news_update);

      }
    }
  }
}
