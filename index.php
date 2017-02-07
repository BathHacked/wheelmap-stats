<?php

include_once('config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

//Get the latest values from the database for "all" category.
foreach( ['yes','limited','no','unknown'] as $wheelchair ){
    $res = $mysqli->query("SELECT * FROM venue_counts WHERE category = 0
     AND wheelchair = '$wheelchair'
     ORDER BY TIME DESC
     LIMIT 0,1");
    $row = $res->fetch_assoc();
    $wheelchair_venues[$wheelchair] = 0 + $row['count'];
}

//Get the values from the database for "all" category from last week.
foreach( ['yes','limited','no','unknown'] as $wheelchair ){
    $res = $mysqli->query("SELECT * FROM venue_counts WHERE category = 0
     AND time < NOW() - '1 week'
     AND wheelchair = '$wheelchair'
     ORDER BY TIME DESC
     LIMIT 0,1");
    $row = $res->fetch_assoc();
    $week_ago_wheelchair_venues[$wheelchair] = 0 + $row['count'];
    $week_increase[$wheelchair] = $wheelchair_venues[$wheelchair] - $week_ago_wheelchair_venues[$wheelchair];
}


$known_venues = $wheelchair_venues['yes'] + $wheelchair_venues['limited'] + $wheelchair_venues['no'];
$total_venues = $known_venues + $wheelchair_venues['unknown'];

$percent_yes = round($wheelchair_venues['yes'] / $total_venues * 100);
$percent_no = round($wheelchair_venues['no'] / $total_venues * 100);
$percent_limited = round($wheelchair_venues['limited'] / $total_venues * 100);
$percent_known = $percent_yes + $percent_no + $percent_limited;
$percent_unknown = 100 - $percent_known; //Do it this way so it always sums to 100 and the progress bar is the right length.



?>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link rel="stylesheet" href="styles.css">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    </head>
<body>
    <div class="container">
        <h1>WheelMap Bath Progress</h1>

        <div class="row">
            <div class="col-12">
                <h3>Wheelchair accessibility is known for <strong><?php echo $percent_known; ?>%</strong> of venues in Bath</h3>
                <div class="progress">
                    <div class="progress-bar wheelchair-yes" role="progressbar" style="width: <?php echo $percent_yes; ?>%" aria-valuenow="<?php echo $percent_yes; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent_yes; ?>%</div>
                    <div class="progress-bar wheelchair-limited" role="progressbar" style="width: <?php echo $percent_limited; ?>%" aria-valuenow="<?php echo $percent_limited; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent_limited; ?>%</div>
                    <div class="progress-bar wheelchair-no" role="progressbar" style="width: <?php echo $percent_no; ?>%" aria-valuenow="<?php echo $percent_no; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent_no; ?>%</div>
                    <div class="progress-bar wheelchair-unknown" role="progressbar" style="width: <?php echo $percent_unknown; ?>%" aria-valuenow="<?php echo $percent_unknown; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent_unknown; ?>%</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <ul>
                    <li><strong><?php echo $total_venues; ?></strong> venues within Bath</li>
                    <li class="wheelchair-yes"><strong><?php echo $wheelchair_venues['yes']; ?></strong> wheelchair accessible venues (<?php echo $percent_yes; ?>%) <?php printf('%+d', $week_increase['yes']); ?> this week</li>
                    <li class="wheelchair-limited"><strong><?php echo $wheelchair_venues['limited']; ?></strong> venues with limited wheelchair accessibility (<?php echo $percent_limited; ?>%) <?php printf('%+d', $week_increase['limited']); ?> this week</li>
                    <li class="wheelchair-no"><strong><?php echo $wheelchair_venues['no']; ?></strong> venues not wheelchair accessible (<?php echo $percent_no; ?>%) <?php printf('%+d', $week_increase['no']); ?> this week</li>
                    <li class="wheelchair-unknown"><strong><?php echo $wheelchair_venues['unknown']; ?></strong> venues not yet tagged (<?php echo $percent_unknown; ?>%) - <a href="https://wheelmap.org/en/map#/?lat=51.382281056660254&lon=-2.370600700378418&zoom=14">Help us by tagging venues you know about</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <iframe class="wheelmap-embed" src="//wheelmap.org/en/embed/9ZVsvrTsjWpyPMYTftm9#/?lat=51.3813864&lon=-2.3596962&zoom=15"></iframe>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h3>What Is This?</h3>
                <p>
                    <a href="https://wheelmap.org/en/map#/?lat=51.382281056660254&lon=-2.370600700378418&zoom=14">Wheelmap.org</a> is an online map for gathering crowd-sourced information about the wheelchair accessibility of public places. A simple
                    traffic light system is used for marking places according to their wheelchair accessibility.
                </p>
                <p>
                    This page shows progress for the venues in Bath, Somerset - as more venues are tagged with their wheelchair accessibility, the progress bar above will fill up.
                </p>
                <h3>How Can I Help?</h3>
                <p>
                    You can go to <a href="https://wheelmap.org/en/map#/?lat=51.382281056660254&lon=-2.370600700378418&zoom=14">WheelMap.org</a>
                    to tag venues you know about with their wheelchair accessibility. Apps are also available for Android and iOS.
                </p>
                <?php if(strtotime('now') < mktime(0,0,0,2,9,2017)){ ?>
                <p>
                    Bath:Hacked are running a <a href="https://www.meetup.com/Bath-Hacked/events/237213318/">public meetup on 8th February 2017</a> to kick off this project if you would like to learn more.
                </p>
                <?php }; ?>

                <h3>Is This Data Openly Available?</h3>
                <p>
                    Yes. Wheelmap is based on the free world map <a href="http://www.openstreetmap.org/">OpenStreetMap</a> and all the data is stored there.
                    The data sets are published under the <a href="http://en.wikipedia.org/wiki/Open_Database_License">Open Database License</a> (ODbL)
                    and are available to anyone and can be used free of charge.
                </p>

                <h3>How Can I Get More Information?</h3>
                <p>
                    The <a href="https://wheelmap.org">wheelmap website</a> has all of the information about their worldwide crowdsourcing project, with an extensive <a href="https://news.wheelmap.org/en/faq/">FAQ</a>.
                </p>
                <p>
                    <a href="https://bathhacked.org">Bath:Hacked</a> can provide help or training on how to use wheelmap to improve accessibility in Bath.<BR />
                    You can tweet us at <a href="https://twitter.com/bathhacked">@BathHacked</a> or email <a href="mailto:leigh@bathhacked.org">leigh@bathhacked.org</a>.
                </p>

                <h3>Disclaimer</h3>
                <p>
                    This page is not affiliated with <a href="https://wheelmap.org/en/map#/?lat=51.382281056660254&lon=-2.370600700378418&zoom=14">wheelmap.org</a>,
                    but uses its API to show progress. This page was created by <a href="https://bathhacked.org">Bath:Hacked</a> as a way of
                    tracking progress as we try to crowdsource accessibility open data in BANES.
                </p>
            </div>
        </div>


    </div>
</html>