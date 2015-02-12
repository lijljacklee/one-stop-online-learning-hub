<?php
$htmlBody = <<<END
This is test search.
END;

// This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
//if ($_GET['q'] && $_GET['maxResults']) {
  // Call set_include_path() as needed to point to your client library.
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
$query = "Java";
$maxResults = 25;
set_include_path(get_include_path().PATH_SEPARATOR.'/home/feng/cs548/google-api-php-client/src');
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';

  /*
* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
* {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
* Please ensure that you have enabled the YouTube Data API for your project.
*/
  $DEVELOPER_KEY = 'AIzaSyCbJIFdDXUq3WgEUjG_xcWlDTX6DUeAcYE';

  $client = new Google_Client();
  $client->setDeveloperKey($DEVELOPER_KEY);

  // Define an object that will be used to make all API requests.
  $youtube = new Google_Service_YouTube($client);

  try {
    // Call the search.list method to retrieve results matching the specified
    // query term.
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => $query,
      'maxResults' => $maxResults,
      'order' => 'relevance',
      'regionCode' => 'US',
    ));

    $videos = '';
    $channels = '';
    $playlists = '';
 //   $searchArray = array();
    // Add each result to the appropriate list, and then display the lists of
    // matching videos, channels, and playlists.
    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<li>%s (%s)</li>',
              $searchResult['snippet']['title'], $searchResult['id']['videoId']);
          break;
        case 'youtube#channel':
          $channels .= sprintf('<li>%s (%s)</li>',
              $searchResult['snippet']['title'], $searchResult['id']['channelId']);
          break;
        case 'youtube#playlist':
          $playlists .= sprintf('<li>%s (%s)</li>',
              $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
          break;
      }
    }

    $htmlBody .= <<<END
<h3>Videos</h3>
<ul>$videos</ul>
<h3>Channels</h3>
<ul>$channels</ul>
<h3>Playlists</h3>
<ul>$playlists</ul>
END;
  } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
//}
?>

<!doctype html>
<html>
<head>
<title>YouTube Search</title>
</head>
<body>
<?=$htmlBody?>
</body>
</html>
