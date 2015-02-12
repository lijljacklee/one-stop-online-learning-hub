<?php
//$url = 'http://en.wikipedia.org/w/api.php?action=query&titles=Java_(programming_language)&prop=revisions&rvprop=content&rvsection=0&format=json&rvparse';
$url = 'http://en.wikipedia.org/w/api.php?action=parse&page=Java_(programming_language)&format=json&prop=text&section=0';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.mysite.com/)');

$response = curl_exec($ch);
//var_dump($response);
if (!$response) {
	exit('cURL Error: '.curl_error($ch));
}
$result = json_decode($response,true);

//var_dump($result);

$content = $result['parse']['text']['*'];

$pattern = '#<p>(.*)</p>#Us'; // http://www.phpbuilder.com/board/showthread.php?t=10352690
if(preg_match($pattern, $content, $matches))
{
	// print $matches[0]; // content of the first paragraph (including wrapping <p> tag)
	print strip_tags($matches[1]); // Content of the first paragraph without the HTML tags.
}


var_dump($content);
