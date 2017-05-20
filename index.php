<!DOCTYPE html>
<html>
<title>ReWired</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue-grey.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    html, body, h1, h2, h3, h4, h5 {
        font-family: "Open Sans", sans-serif
    }
</style>

<?php
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "227584299-aw6cuhfYfe4yLsRrFWqQSzkrQjkFc60hTceKthxm",
    'oauth_access_token_secret' => "MucbkDcIGhafD4KM7C5BY9FXyWy4Rs5wU8mff39fQFhuU",
    'consumer_key' => "EVO4y3SAXkEARVD7TQ4teV5Df",
    'consumer_secret' => "cO9MTPdrhC1otgQoGs6oOdURuD8KKMNv5ujTwDKrpYZPimTO0E"
);

$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
$requestMethod = "GET";
if (isset($_GET['user']))  {$user = $_GET['user'];}  else {$user  = "Lishniii";}
if (isset($_GET['count'])) {$count = $_GET['count'];} else {$count = 500;}
if (isset($_GET['include_rts'])) {$include_rts = $_GET['include_rts'];} else {$include_rts = false;}
if (isset($_GET['exclude_replies'])) {$exclude_replies = $_GET['exclude_replies'];} else {$exclude_replies = 1;}
$getfield = "?screen_name=$user&count=$count&include_rts=$include_rts&exclude_replies=$exclude_replies";
$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest(),$assoc = TRUE);

$allTweets = [];
foreach($string as $items)
{
    array_push($allTweets, $items['text']);
}

require 'monkeylearn/autoload.php';

$ml = new MonkeyLearn\Client('81137672d671ea08af4938ad6a970f4f7bdc8a21');
$module_id = 'cl_5icAVzKR';
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

//var_dump($keywords);


?>



<body class="w3-theme-l5" onload="onLoadScripts()">

<!-- Navbar -->
<div class="w3-top">
    <div class="w3-bar w3-theme-d2 w3-left-align w3-large">
        <a><img src="assets/images/rewired%20logo.png" style="height:65px; margin-left: 5%"></a>
        <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-theme-d2"
           href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>
        <a href="#" class="w3-bar-item w3-button w3-hide-small w3-right w3-padding-large w3-hover-white" title="Logout"><img
                    src="assets/images/logout.png" class="w3-circle" style="height:45px;"></a>
    </div>
</div>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
    <!-- The Grid -->
    <div class="w3-row">
        <!-- Left Column -->
        <div class="w3-col m4">
            <!-- Profile -->
            <div class="w3-card-2 w3-round w3-white">
                <div class="w3-container">
                    <h4 class="w3-center">My Profile</h4>
                    <p class="w3-center"><img src="assets/images/user.jpg" class="w3-circle"
                                              style="height:106px;width:106px" alt="Avatar"></p>
                    <hr>
                    <p><i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i> Designer, UI</p>
                    <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> London, UK</p>
                    <p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i> April 1, 1988</p>
                </div>
            </div>
            <br>

            <!-- Accordion -->
            <div class="w3-card-2 w3-round">
                <div class="w3-white">
                    <button onclick="myFunction('Demo1')" class="w3-button w3-block w3-theme-l1 w3-left-align"><i
                                class="fa fa-circle-o-notch fa-fw w3-margin-right"></i> My Groups
                    </button>
                    <div id="Demo1" class="w3-hide w3-container">
                        <p>Some text..</p>
                    </div>
                    <button onclick="myFunction('Demo2')" class="w3-button w3-block w3-theme-l1 w3-left-align"><i
                                class="fa fa-calendar-check-o fa-fw w3-margin-right"></i> My Events
                    </button>
                    <div id="Demo2" class="w3-hide w3-container">
                        <p>Some other text..</p>
                    </div>
                    <button onclick="myFunction('Demo3')" class="w3-button w3-block w3-theme-l1 w3-left-align"><i
                                class="fa fa-users fa-fw w3-margin-right"></i> My Photos
                    </button>
                    <div id="Demo3" class="w3-hide w3-container">
                        <div class="w3-row-padding">
                            <br>
                            <div class="w3-half">
                                <img src="/w3images/lights.jpg" style="width:100%" class="w3-margin-bottom">
                            </div>
                            <div class="w3-half">
                                <img src="/w3images/nature.jpg" style="width:100%" class="w3-margin-bottom">
                            </div>
                            <div class="w3-half">
                                <img src="/w3images/mountains.jpg" style="width:100%" class="w3-margin-bottom">
                            </div>
                            <div class="w3-half">
                                <img src="/w3images/forest.jpg" style="width:100%" class="w3-margin-bottom">
                            </div>
                            <div class="w3-half">
                                <img src="/w3images/nature.jpg" style="width:100%" class="w3-margin-bottom">
                            </div>
                            <div class="w3-half">
                                <img src="/w3images/fjords.jpg" style="width:100%" class="w3-margin-bottom">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <!-- Interests -->
            <div class="w3-card-2 w3-round w3-white w3-hide-small">
                <div class="w3-container">
                    <p>Interests</p>
                    <div id = "interestsSection">

                    </div>
<!--                    <p>-->
<!--                        <span class="w3-tag w3-small w3-red" onclick="printSomething()">News</span>-->
<!--                        <span class="w3-tag w3-small w3-red">Design & tech</span>-->
<!--                        <span class="w3-tag w3-small w3-red">Art</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-d2">Games</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-d1">Friends</span>-->
<!--                        <span class="w3-tag w3-small w3-theme">Games</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-l1">Friends</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-l2">Food</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-l3">cars</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-l4">Dogs</span>-->
<!--                        <span class="w3-tag w3-small w3-theme-l5">Photos</span>-->
<!--                    </p>-->
                </div>
            </div>
            <br>

            <!-- Alert Box -->
            <div class="w3-container w3-display-container w3-round w3-theme-l4 w3-border w3-theme-border w3-margin-bottom w3-hide-small">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-theme-l3 w3-display-topright">
          <i class="fa fa-remove"></i>
        </span>
                <p><strong>Hey!</strong></p>
                <p> Take a look at our reccomendations for you --></p>
            </div>


            <!-- End Left Column -->
        </div>

        <!-- Middle Column -->
        <div class="w3-col m4">

            <div class="w3-row-padding">
                <div class="w3-col m12">
                    <div class="w3-card-2 w3-round w3-white">
                        <div class="w3-container w3-padding">
                            <center><h3>Recommended</h3></center>
                            <div id = "interestEntries">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--<div class="w3-container w3-card-2 w3-white w3-round w3-margin"><br>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
                <div class="w3-row-padding" style="margin:0 -16px">
                </div>
                <button type="button" class="w3-button w3-theme-d1 w3-margin-bottom"><i class="fa fa-thumbs-up"></i>  Like</button>
                <button type="button" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i>  Comment</button>
            </div>-->


            <!-- End Middle Column -->
        </div>

        <!-- Right Column -->
        <!-- Middle Column -->
        <div class="w3-col m4">

            <div class="w3-row-padding">
                <div class="w3-col m12">
                    <div class="w3-card-2 w3-round w3-white">
                        <div class="w3-container w3-padding">
                            <center><h3>Trending</h3></center>
                            <div id = "popularEntries">

                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- End Right Column -->
        </div>

        <!-- End Grid -->
    </div>

    <!-- End Page Container -->
</div>
<br>

<!-- Footer -->

<footer class="w3-container w3-theme-d5">
    <center><p><a style="font-size: x-small">Subreddit recommendation according to a per-topic summary of interests
                under topic specific tweet footprints</a></p></center>
</footer>

<script>
    // Accordion
    function myFunction(id) {
        var x = document.getElementById(id);
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
            x.previousElementSibling.className += " w3-theme-d1";
        } else {
            x.className = x.className.replace("w3-show", "");
            x.previousElementSibling.className =
                x.previousElementSibling.className.replace(" w3-theme-d1", "");
        }
    }

    // Used to toggle the menu on smaller screens when clicking on the menu button
    function openNav() {
        var x = document.getElementById("navDemo");
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
        } else {
            x.className = x.className.replace(" w3-show", "");
        }
    }

    var menuEl = document.querySelector('#menu');
    var entriesEl = document.querySelector('#entries');

    function fetchSubreddit(url) {
        if (url) {
            fetch('https://www.reddit.com/r/' + url + '.json').then(function (response) {
                return response.json();
            }).then(function (json) {
                var links = '';
                for (var i = 0; i < json.data.children.length; i++) {
                    links += '<li><a href="' + json.data.children[i].data.url + '">' +
                        json.data.children[i].data.url + '</a></li>';
                }
                entriesEl.innerHTML = '<ul>' + links + '</ul>';
            });
        }
    }

    var subredditsByTopicUrl = 'https://www.reddit.com/api/subreddits_by_topic.json?query=javascript';
    fetch(subredditsByTopicUrl).then(function (response) {
        return response.json();
    }).then(function (json) {
        var select = document.createElement('select');
        var links = '';
        for (var k = 0; k < json.length; k++) {
            links += '<option value="' + json[k].name + '">' + json[k].name +
                '</option>';
        }
        select.innerHTML = links;
        select.addEventListener('change', function (e) {
            fetchSubreddit(e.target.value);
        });
        menuEl.appendChild(select);
    }).catch(function (ex) {
        ChromeSamples.log('Parsing failed:', ex);
    });

    function fetchPopularSubreddits(){
        var popularEntries = document.querySelector('#popularEntries');
        fetch('https://www.reddit.com/subreddits/popular.json').then(function (response) {
            return response.json();
        }).then(function (json) {
            var links = '<table class="w3-table"> <tr> <th>Subreddit</th> <th>Subscribers</th> </tr>';
            for (var i = 0; i < json.data.children.length; i++) {
                links += '<tr><td><a href="' + 'https://www.reddit.com/' + json.data.children[i].data.url + '" target="_blank">' +
                    json.data.children[i].data.display_name + '</a></td> <td>' + json.data.children[i].data.subscribers + '</td> </tr>';
            }

            links += '</table>';

            popularEntries.innerHTML = '<ul>' + links + '</ul>';
        });
    }

    function onLoadScripts(){
        fetchPopularSubreddits();
        buildInterestIcons();
    }

    function fetchSubredditsForInterest(url){
        var interestEntries = document.querySelector('#interestEntries');
        fetch('https://www.reddit.com/api/subreddits_by_topic.json?query=' + url).then(function (response) {
            return response.json();
        }).then(function (json) {
            var links = '<table class="w3-table"> <tr> <th>Subbreddit</th></tr>';
            for (var i = 0; i < json.length; i++) {
                links += '<tr><td><a href="' + 'https://www.reddit.com/' + json[i].path + '" target="_blank">' +
                    json[i].display_name_prefixed + '</a></td></tr>';
            }

            links += '</table>';

            interestEntries.innerHTML = '<ul>' + links + '</ul>';
        });
    }

    function buildInterestIcons(){
        var keywords = <?php echo json_encode($keywords); ?>;
        var interestsSection = document.querySelector('#interestsSection');
        var links = '<p>';

        for (var i = 0; i < keywords.length; i++) {
            links += '<span class="w3-tag w3-small w3-red" onclick="fetchSubredditsForInterest('+"\'"+keywords[i]+"\'"+')">' + keywords[i] + '</span> &nbsp;';
        }

        links += '</p>';

        interestsSection.innerHTML = links;
    }
</script>


</body>
</html>
