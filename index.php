<?php
include_once('simple_html_dom.php');
$steps = 2222;
$pause = 15;
$post_count = 5;
$url = 'https://vk.com/page-name';
$all_text = [];
$options = [ 
	CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0',
	CURLOPT_HEADER => 0, 
	CURLOPT_URL => $url, 
	CURLOPT_SSL_VERIFYPEER => 0,
	CURLOPT_FOLLOWLOCATION => 1,
	CURLOPT_RETURNTRANSFER => 1, 
];
$ch = curl_init(); 
curl_setopt_array($ch, $options);
$html = new simple_html_dom();

for ($i=0;$i<$steps;$i++) {
	if( ! $result = iconv('CP1251', 'UTF-8', curl_exec($ch)) ) 
	{ 
		trigger_error(curl_error($ch)); 
	} 
	$html->load($result);	
	$wall_posts = $html->find('.post');
	for ($j=0;$j<$post_count;$j++) {
		if ( isset($wall_posts[$j]) ) {
			$elements = $wall_posts[$j]->find('.reply');
			foreach ($elements as $element) {
				$id = $element->id;
				$author = $element->find('.author');
				$reply_text = $element->find('.wall_reply_text');
				if ( !isset($all_text[$id]) ) {
					$all_text[$id]['time'] = date('d-m-Y H:i:s');
					$all_text[$id]['author'] = $author[0]->innertext;
					if (isset($reply_text[0])) $all_text[$id]['reply_text'] = $reply_text[0]->innertext; else $all_text[$id]['reply_text'] = '';
					$filename = 'all_text.txt';
					$file = fopen($filename, 'a+');
					fputcsv($file, $all_text[$id], ';');
					fclose($file);
				}
			}
		}
	}
	sleep($pause);
}

curl_close($ch);
foreach ($all_text as $text) {
	echo $text['time'].' '.$text['author'].' '.$text['reply_text'].'<br>';
}