<?php

/*$content = array(
			array(
				'title' => 'More test',
				'description' => 'hope this work',
				'videoId' => '23456'));
addDoc($content, 1);
*/
//include_once "solrBootStrap.php";

//$response = searchSolr(urlencode('java programming'));
//var_dump($response['response']);

function addDoc($content, $cat)
{
	include "solrBootStrap.php";
	static $index = 0;
	$options = array
	(
			'hostname' => SOLR_SERVER_HOSTNAME,
			'login'    => SOLR_SERVER_USERNAME,
			'password' => SOLR_SERVER_PASSWORD,
			'port'     => SOLR_SERVER_PORT,
	);
	
	$client = new SolrClient($options);	
	
	//$cat = 1 Youtube, $cat = 2 stackoverflow
	if($cat == 1){
		foreach($content as $value){
			$doc = new SolrInputDocument();
			$doc->addField('id', $index++);
			$doc->addField('cat',$cat);
			$doc->addField('title', $value['title']);
			$doc->addField('text', $value['description']);
			$doc->addField('links', $value['videoId']);
			try{
				$updateResponse = $client->addDocument($doc);
			} catch(SolrClientException $e){
				echo $e;
			}
		}
	} elseif($cat == 2){
		foreach($content as $value){
			$doc = new SolrInputDocument();
			$doc->addField('id', $index++);
			$doc->addField('cat',$cat);
			$doc->addField('title',$value['question']);
			$doc->addField('text', $value['answer']);
			$doc->addField('links', $value['qLink']);
			try{
				$updateResponse = $client->addDocument($doc);
			} catch(SolrClientException $e){
				echo $e;
			}
		}
	} else {
		echo "wrong category input";
	}
	//softcommit the added doc
	$url = "http://localhost:8983/solr/collection1/update?commit=true&softCommit=true";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.mysite.com/)');
	$response = curl_exec($ch);
	//$client->commit($softCommit = true);
	//var_dump($updateResponse);
	//print_r($updateResponse->getResponse());
}

function searchSolr($query,$cat)
{	
	//$url = "http://localhost:8983/solr/collection1/select?q=".$query."&rows=20&wt=json&indent=true&fq=cat%3A".$cat;
	$url = "http://localhost:8983/solr/clustering?q=".$query."&rows=100&LingoClusteringAlgorithm.desiredClusterCountBase=5&wt=json&carrot.title=title&fq=cat%3A".$cat;
	//$url = "http://localhost:8983/solr/clustering?q=java%20programming&rows=50&LingoClusteringAlgorithm.desiredClusterCountBase=5&wt=json&fq=cat%3A1&carrot.title=title";
	//var_dump($url);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'MyBot/1.0 (http://www.mysite.com/)');
	$json = curl_exec($ch);
	//var_dump($json);
	$response = json_decode($json,true);
	//var_dump($response);
	return $response;	
	
}

?>