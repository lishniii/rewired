<?php
/**
 * Created by PhpStorm.
 * User: lishniii
 * Date: 4/20/2017
 * Time: 8:47 PM
 */
require_once('TwitterAPIExchange.php');

/** set access tokens for twitter v1.1 **/
$settings = array(
    'oauth_access_token' => "227584299-aw6cuhfYfe4yLsRrFWqQSzkrQjkFc60hTceKthxm",
    'oauth_access_token_secret' => "MucbkDcIGhafD4KM7C5BY9FXyWy4Rs5wU8mff39fQFhuU",
    'consumer_key' => "EVO4y3SAXkEARVD7TQ4teV5Df",
    'consumer_secret' => "cO9MTPdrhC1otgQoGs6oOdURuD8KKMNv5ujTwDKrpYZPimTO0E"
);

/** building the API request to ask for what is needed eg: tweets excluding retweets etc **/
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
$requestMethod = "GET";
if (isset($_GET['user']))  {$user = $_GET['user'];}  else {$user  = $_POST["handle"];}
if (isset($_GET['count'])) {$count = $_GET['count'];} else {$count = 500;}
if (isset($_GET['include_rts'])) {$include_rts = $_GET['include_rts'];} else {$include_rts = false;}
if (isset($_GET['exclude_replies'])) {$exclude_replies = $_GET['exclude_replies'];} else {$exclude_replies = 1;}
$getfield = "?screen_name=$user&count=$count&include_rts=$include_rts&exclude_replies=$exclude_replies";
$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest(),$assoc = TRUE);

/** collecting the received tweets and storing them in an array **/
$allTweets = [];
$userDetails = [];
foreach($string as $items)
{
    $userDetails = $items['user'];
    array_push($allTweets, $items['text']);
}

/** MonkeyLearn Functions **/
require 'monkeylearn/autoload.php';

/** set access tokens for MonkeyLearn**/
$ml = new MonkeyLearn\Client('81137672d671ea08af4938ad6a970f4f7bdc8a21');
$module_id = 'cl_5icAVzKR'; //https://app.monkeylearn.com/main/classifiers/cl_YmN3QwVL/
$res = $ml->classifiers->classify($module_id, $allTweets, true);

$result = $res->result;
$keywords = [];
foreach($result as $items)
{
    if (!in_array($items[0]["label"], $keywords))
    {
        array_push($keywords, $items[0]["label"]);
    }

}
?>