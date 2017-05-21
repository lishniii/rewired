<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue-grey.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>ReWired</title>
</head>

<body background="assets/images/background.png" class="login" style="background-size: 100%;">
<div>
    <div class="start_descrition" style="z-index: 9; text-align: center;position: absolute;width: 1000px;top: 45%;margin-top: -180px;left: 50%;margin-left: -500px;">
        <img src="assets/images/rewired%20logo.png" style="height: 20%; width: 30%" >
        <br><br><br>
        <h1 style="color: white">welcome to rewired!</h1>
        <p style="color: white">Subreddit recommender according to a per-topic summary of interests under topic specific tweet footprints</p>
        <p style="color: white">Rewired aims to create two main recommenders: one for users to find subreddits they may be interested in according to their tweets and a second to recommend trending subreddits.</p><br>
        <div class="search_promo">
            <form name="form" action="home.php" method="post">
            <div class="input-group">
                <span class="input-group-addon">@</span>
                <input type="text" class="form-control" id="handle" name="handle" placeholder="Twitter handle" required>
                <div class="input-group-btn btn_cat">

                </div>
                <div class="input-group-btn btn_promo_search">
                    <input type="submit" class="btn btn-danger">Login</input>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
</html>
