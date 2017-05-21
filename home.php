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

<?php include 'Classifier.php'; ?>

<body class="w3-theme-l5" onload="onLoadScripts()">

<!-- Title bar -->
<div class="w3-top">
    <div class="w3-bar w3-theme-d2 w3-left-align w3-large">
        <a><img src="assets/images/rewired%20logo.png" style="height:65px; margin-left: 5%"></a>
        <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-theme-d2"
           href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>
        <a href="index.php" class="w3-bar-item w3-button w3-hide-small w3-right w3-padding-large w3-hover-white"
           title="Logout"><img
                    src="assets/images/logout.png" class="w3-circle" style="height:45px;"></a>
    </div>
</div>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
    <!-- The Grid -->
    <div class="w3-row">
        <!-- Left Column -->
        <div class="w3-col m4">
            <!-- Profile Tab-->
            <div class="w3-card-2 w3-round w3-white">
                <div class="w3-container">
                    <h4 class="w3-center">Profile</h4>
                    <p class="w3-center"><img src="<?php echo $userDetails['profile_image_url']; ?>" class="w3-circle"
                                              style="height:106px;width:106px" alt="Avatar"></p>
                    <hr>
                    <p>
                        <i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i> <?php echo $userDetails['name']; ?>
                    </p>
                    <p>
                        <i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?php echo $userDetails['location']; ?>
                    </p>
                </div>
            </div>
            <br>

            <!-- Interests -->
            <div class="w3-card-2 w3-round w3-white w3-hide-small">
                <div class="w3-container">
                    <p>Interests</p>
                    <div id="interestsSection">

                    </div>

                </div>
            </div>
            <br>

            <!-- Alert Box -->
            <div class="w3-container w3-display-container w3-round w3-theme-l4 w3-border w3-theme-border w3-margin-bottom w3-hide-small">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-theme-l3 w3-display-topright">
          <i class="fa fa-remove"></i>
        </span>
                <p><strong>Hey!</strong></p>
                <p> Take a look at our recommendations for you --></p>
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
                            <div id="interestEntries">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                            <div id="popularEntries">

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

    function fetchPopularSubreddits() {
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

    function onLoadScripts() {
        fetchPopularSubreddits();
        buildInterestIcons();

        var keywords = <?php echo json_encode($keywords); ?>;
        fetchSubredditsForInterest(keywords[0]);
    }

    function fetchSubredditsForInterest(url) {
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

    function buildInterestIcons() {
        var keywords = <?php echo json_encode($keywords); ?>;
        var interestsSection = document.querySelector('#interestsSection');
        var links = '<p>';

        for (var i = 0; i < keywords.length; i++) {
            links += '<a href="#"><span class="w3-tag w3-small w3-red" onclick="fetchSubredditsForInterest(' + "\'" + keywords[i] + "\'" + ')">' + keywords[i] + '</span></a> &nbsp;';
        }

        links += '</p>';

        interestsSection.innerHTML = links;
    }
</script>


</body>
</html>
