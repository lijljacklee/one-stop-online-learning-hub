<html>

<body>

<ul>

<?php
set_include_path(get_include_path().PATH_SEPARATOR.'/home/feng/cs548/google-api-php-client/src');
require_once 'stackphp/api.php';

try {

$stackoverflow = API::Site('stackoverflow');
//set filter to get the body of the answer
$request = $stackoverflow->Search("TCP/IP")->SortBy("relevance")->Tagged("TCP");
//$request = $stackoverflow->Answers()->ID("6841479")->Filter("!9WA((ItYa",NULL);
//var_dump($request);
//$request2 = $request->SortByHot();
//var_dump($request);

$response = $request->Exec();
//var_dump($response);
//var_dump($response->Total(TRUE));
while($questions = $response->Fetch(FALSE)){
	$answerID = $questions['accepted_answer_id'];
	if($answerID!=''){
		$req = $stackoverflow->Answers()->ID($answerID)->Filter("!9WA((ItYa",NULL);
		$res = $req->Exec();
		$answer = $res->Fetch(FALSE);
	}
	var_dump($questions);
//	echo "<li>{$questions['title']}</li>";
//   echo "<li>{$answer['body']}</li>";
}
}
catch(APIException $e)
{
	echo $e;
}
?>

</ul>

</body>

</html>