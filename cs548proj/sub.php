<?php
//This php handles search;
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
include "solrExample.php";

if (!isset($_POST['submit'])){
	if (!isset($_GET['query']) || $_GET['query']=="" || $_GET['query']=="Java"){
		$query = "Java";
	}else{
		$query = $_GET['query'];
	}
//	echo "2lalalalala".$query;
}else {
	if ($_POST['search']!=""){
		$query = $_POST['search'];
	}
	else{
		$query = "Java";
	}
//	echo "1lalalalala".$query;
	$_POST['submit']=false;
}
$youtubeResult = searchYoutube($query);
//var_dump($youtubeResult);
addDoc($youtubeResult,1);
$SEResult = searchSE($query);
//var_dump($SEResult);
addDoc($SEResult,2);

$refinedYoutubeResponse = searchSolr(urlencode($query),1);
$refinedYoutubeResult = $refinedYoutubeResponse['response']['docs']; 
$YoutubeClusters = $refinedYoutubeResponse['clusters'][0]['docs'];
$refinedYoutubeResult = findMatch($refinedYoutubeResult,$YoutubeClusters);

$refinedSEResponse = searchSolr(urlencode($query),2);
$refinedSEResult = $refinedSEResponse['response']['docs'];
$SEClusters = $refinedSEResponse['clusters'][0]['docs'];
$refinedSEResult = findMatch($refinedSEResult,$SEClusters);
//echo $refinedResult['docs'][0]['id'];

if ($query == "Java"){
	$wikiResult = searchWiki("Java_(programming_language)");
}
else
	$wikiResult = searchWiki($query);

//var_dump($wikiResult);

function findMatch($array1, $array2)
{
	$result = array();
	foreach($array1 as $value){
		foreach($array2 as $id){
			if($value['id'] == $id){
				$result[] = $value;
			}
		}
	}
	return $result;
}


function searchYoutube($query){
	$maxResults = 50;
	//search in Youtube.
	//set_include_path(get_include_path().PATH_SEPARATOR.'/home/feng/cs548/google-api-php-client/src');
	require_once 'Google/Client.php';
	require_once 'Google/Service/YouTube.php';
	$DEVELOPER_KEY = 'AIzaSyCbJIFdDXUq3WgEUjG_xcWlDTX6DUeAcYE';
	
	$googleClient = new Google_Client();
	$googleClient->setDeveloperKey($DEVELOPER_KEY);
	
	// Define an object that will be used to make all API requests.
	$youtube = new Google_Service_YouTube($googleClient);
	
	$searchResult = array();
	
	try{
		$searchResponse = $youtube->search->listSearch('id,snippet', array(
				'q' => $query,
				'maxResults' => $maxResults,//limited to 25
				'order' => 'relevance',//ordered by relevance
				'regionCode' => 'US',//limited to US region only
				'type' => 'video',//limited to video only, playlist and channel not included.
		));
		//store the video ID, title, and description in a 2-D array
		$index = 0;
		foreach($searchResponse['items'] as $value){
			$searchResult[$index] = array();
			$searchResult[$index]['title'] = $value['snippet']['title'];
			$searchResult[$index]['description'] = $value['snippet']['description'];
			$searchResult[$index]['videoId'] = $value['id']['videoId'];
			$index++;
		}
			
	} catch (Google_ServiceException $e) {
	 	var_dump($e->getMessage());
	  } catch (Google_Exception $e) {
	  	var_dump($e->getMessage());
	}
	return $searchResult;
}

function searchSE($query)
{
	require_once 'stackphp/api.php';
	$searchResult = array();
	try {
	
		$stackoverflow = API::Site('stackoverflow');
		//set filter to get the body of the answer
		$request = $stackoverflow->Search($query)->SortBy("relevance");//->Tagged($query);
	
		$response = $request->Exec();
		//var_dump($response);
		//var_dump($response->Total(TRUE));
		$index = 0;
		while($questions = $response->Fetch(FALSE)){
			if (isset($questions['accepted_answer_id'])){
				$answerID = $questions['accepted_answer_id'];
			}
			/*if($answerID!=''){
				$req = $stackoverflow->Answers()->ID($answerID)->Filter("!9WA((ItYa",NULL);
				$res = $req->Exec();
				$answer = $res->Fetch(FALSE);
			}*/

			$searchResult[$index] = array();
			$searchResult[$index]['qLink'] = $questions['link'];
			$searchResult[$index]['question'] = $questions['title'];
			$searchResult[$index]['answer'] = $answer['body'];

			++$index;
		}
	}
	catch(APIException $e)
	{
		echo $e;
	}
	return $searchResult;
}

function searchWiki($query)
{
	$query = str_replace(' ','_',$query);
	$url = 'http://en.wikipedia.org/w/api.php?action=parse&format=json&prop=text&section=0&page=';
	$url = $url.$query;
//	var_dump($url);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.mysite.com/)');
	
	$response = curl_exec($ch);
//	var_dump($response);
	if (!$response) {
		exit('cURL Error: '.curl_error($ch));
	}
	$json = json_decode($response,true);
	$content = $json['parse']['text']['*'];
	$pos = strrpos($content, "redirectText");
	if ($pos==false){
		$pattern = '#<p>(.*)</p>#Us';
		if(preg_match($pattern, $content, $matches))
		{
			$result = strip_tags($matches[1]); 
		}
	}else{
		//echo "lalalalalalalallalalalalalalal";
		$pattern = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/i';///////////////////////
		preg_match_all($pattern, $content, $matches);
		$found =$matches[4];
		$result = searchWiki($found[0]);		
	}
	return $result;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Computer Science Course Integration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="http://getbootstrap.com/2.3.2/assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 0px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>
    <link href="http://getbootstrap.com/2.3.2/assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://getbootstrap.com/2.3.2/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://getbootstrap.com/2.3.2/assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://getbootstrap.com/2.3.2/assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="http://getbootstrap.com/2.3.2/assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="http://getbootstrap.com/2.3.2/assets/ico/favicon.png">
  </head>
<body>
<div class="hero-unit">
				<h1 id="request_name"><?PHP echo $query;?></h1>
				<p><?PHP echo $wikiResult;?></p>
				<p>
				<?php $wikiLink = '<a href="http://www.wikipedia.com/wiki/'.$query.'" class="btn btn-primary btn-large" target="_blank">Learn more &raquo;</a>';
					echo $wikiLink;?>
				</p>
			</div>
			<div class="page-header">
				<h1>Youtube Videos</h1>
			</div>
<?php 
$counter=0;
foreach($refinedYoutubeResult as $value) {

		if ($counter%3==0){
			echo "<div class='row-fluid'>";
		}
		$url = "http://www.youtube.com/embed/".$value['links'][0];
		echo '<div class="span4"><iframe width="349" height="217" src='.$url.'></iframe></div>';
		if ($counter%3==2){
			echo "</div><!--/row-->";
		}
		$counter++;
		if($counter == 6)
			break;
	
}
?>
          <div class="row-fluid">
			<div class="page-header">
				<h1>Hot Questions on Stackoverflow</h1>
			</div>
<?php 
foreach($refinedSEResult as $value) {
	
		echo '<div class="span2"><h2>Question</h2><p>'.$value['title'][0].'</p><p><a class="btn" href="'.$value['links'][0].'" target="_blank">View details &raquo;</a></p></div>';
	
}
?>
          </div><!--/row-->
        </div><!--/span-->
		
		
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap-transition.js"></script>
    <script src="../assets/js/bootstrap-alert.js"></script>
    <script src="../assets/js/bootstrap-modal.js"></script>
    <script src="../assets/js/bootstrap-dropdown.js"></script>
    <script src="../assets/js/bootstrap-scrollspy.js"></script>
    <script src="../assets/js/bootstrap-tab.js"></script>
    <script src="../assets/js/bootstrap-tooltip.js"></script>
    <script src="../assets/js/bootstrap-popover.js"></script>
    <script src="../assets/js/bootstrap-button.js"></script>
    <script src="../assets/js/bootstrap-collapse.js"></script>
    <script src="../assets/js/bootstrap-carousel.js"></script>
    <script src="../assets/js/bootstrap-typeahead.js"></script>
  </body>
</html>