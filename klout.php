<?php

include('config.php');

$user = $_GET["user"];
$response = array('user' => $user);
$allTopics = array();

$topicUrl = "http://api.klout.com/1/users/topics.json?users=$user&key=$key";

$json = @file_get_contents($topicUrl,0,null,null);
if (!$json) {
	$response['topics'] = null;
}
else {
	$topics = json_decode($json);
	$i = 0;
	$response['topics'] = $topics->users[0]->topics;
	array_splice($allTopics, count($allTopics), 0, $topics->users[0]->topics);
}

$inflUrl = "http://api.klout.com/1/soi/influenced_by.json?users=$user&key=$key";

$json = @file_get_contents($inflUrl, 0, null, null);
if (!$json) {
	$response['influencers'] = null;
} 
else {
	$friends = json_decode($json);
	if (($friends->users != null) && count($friends->users) > 0) {
		$friendArray = array();
		foreach($friends->users[0]->influencers as $friend) {
			array_push($friendArray, $friend->twitter_screen_name);	
		}

		$user = implode($friendArray, ',');
		$topicUrl = "http://api.klout.com/1/users/topics.json?users=$user&key=$key";
		$json = @file_get_contents($topicUrl, 0, null, null);
		$topics = json_decode($json);
		$i = 0;
		foreach($topics->users as $user) {
			$response['influencers'][$i] = $user;			
			array_splice($allTopics, count($allTopics), 0, $user->topics);
			$i++;
		}
	}
}

$hash = array();
foreach($allTopics as $topic) {
	if (array_key_exists($topic,$hash)) {
		$hash[$topic]++;		
	}
	else {
		$hash[$topic] = 1;
	}
}

arsort($hash);

$response['allTopics'] = array_keys($hash);
print_r(json_encode($response));
?>
