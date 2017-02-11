<?php

include_once('config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$res = $mysqli->query("SELECT * FROM categories ORDER BY id");
while($row = $res->fetch_assoc() ){
    $category_names[$row['id']] = $row['localized_name'];
};

$compareTime = gmdate("Y-m-d H:i:s", time()-7*24*60*60);

for( $categoryId = 0; $categoryId <= 12; $categoryId++ ){
    //Get the latest values from the database for "all" category.
    foreach( ['yes','limited','no','unknown'] as $wheelchair ){
        $res = $mysqli->query("SELECT * FROM venue_counts WHERE category = $categoryId
         AND wheelchair = '$wheelchair'
         ORDER BY TIME DESC
         LIMIT 0,1");
        $row = $res->fetch_assoc();
        $wheelchair_venues[$categoryId][$wheelchair] = 0 + $row['count'];
    }

    //Get the values from the database for "all" category from last week.
    foreach( ['yes','limited','no','unknown'] as $wheelchair ){
        $sql = "SELECT * FROM venue_counts WHERE category = $categoryId
         AND time < '$compareTime'
         AND wheelchair = '$wheelchair'
         ORDER BY time DESC
         LIMIT 0,1";
        $res = $mysqli->query($sql);
        $row = $res->fetch_assoc();
        $week_ago_wheelchair_venues[$categoryId][$wheelchair] = 0 + $row['count'];
        $week_increase[$categoryId][$wheelchair] = $wheelchair_venues[$categoryId][$wheelchair] - $week_ago_wheelchair_venues[$categoryId][$wheelchair];
    }


    $known_venues[$categoryId] = $wheelchair_venues[$categoryId]['yes'] + $wheelchair_venues[$categoryId]['limited'] + $wheelchair_venues[$categoryId]['no'];
    $total_venues[$categoryId] = $known_venues[$categoryId] + $wheelchair_venues[$categoryId]['unknown'];
    $week_increase_known[$categoryId] = $week_increase[$categoryId]['yes'] + $week_increase[$categoryId]['limited'] + $week_increase[$categoryId]['no'];

    $percent[$categoryId]['yes'] = round($wheelchair_venues[$categoryId]['yes'] / $total_venues[$categoryId] * 100);
    $percent[$categoryId]['no'] = round($wheelchair_venues[$categoryId]['no'] / $total_venues[$categoryId] * 100);
    $percent[$categoryId]['limited'] = round($wheelchair_venues[$categoryId]['limited'] / $total_venues[$categoryId] * 100);
    $percent[$categoryId]['known'] = $percent[$categoryId]['yes'] + $percent[$categoryId]['no'] + $percent[$categoryId]['limited'];
    $percent[$categoryId]['unknown'] = 100 - $percent[$categoryId]['known']; //Do it this way so it always sums to 100 and the progress bar is the right length.
}


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
        <h1 style="padding-top: 10px;">WheelMap Bath Progress</h1>

        <div class="row">
            <div class="col-12">
                <h3>Wheelchair accessibility is known for <strong><?php echo $percent[0]['known']; ?>%</strong> of venues in Bath</h3>
                <div class="progress">
                    <div class="progress-bar wheelchair-yes" role="progressbar" style="width: <?php echo $percent[0]['yes']; ?>%" aria-valuenow="<?php echo $percent[0]['yes']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[0]['yes']; ?>%</div>
                    <div class="progress-bar wheelchair-limited" role="progressbar" style="width: <?php echo $percent[0]['limited']; ?>%" aria-valuenow="<?php echo $percent[0]['limited']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[0]['limited']; ?>%</div>
                    <div class="progress-bar wheelchair-no" role="progressbar" style="width: <?php echo $percent[0]['no']; ?>%" aria-valuenow="<?php echo $percent[0]['no']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[0]['no']; ?>%</div>
                    <div class="progress-bar wheelchair-unknown" role="progressbar" style="width: <?php echo $percent[0]['unknown']; ?>%" aria-valuenow="<?php echo $percent[0]['unknown']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[0]['unknown']; ?>%</div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-12">
                <div><strong><?php echo $total_venues[0]; ?></strong> venues within Bath</div>
                <div class="wheelchair-yes"><strong><?php echo $wheelchair_venues[0]['yes']; ?></strong> wheelchair accessible venues (<?php echo $percent[0]['yes']; ?>%) <?php printf('%+d', $week_increase[0]['yes']); ?> this week</div>
                <div class="wheelchair-limited"><strong><?php echo $wheelchair_venues[0]['limited']; ?></strong> venues with limited wheelchair accessibility (<?php echo $percent[0]['limited']; ?>%) <?php printf('%+d', $week_increase[0]['limited']); ?> this week</div>
                <div class="wheelchair-no"><strong><?php echo $wheelchair_venues[0]['no']; ?></strong> venues not wheelchair accessible (<?php echo $percent[0]['no']; ?>%) <?php printf('%+d', $week_increase[0]['no']); ?> this week</div>
                <div class="wheelchair-unknown"><strong><?php echo $wheelchair_venues[0]['unknown']; ?></strong> venues not yet tagged (<?php echo $percent[0]['unknown']; ?>%) - <a href="https://wheelmap.org/en/map#/?lat=51.382281056660254&lon=-2.370600700378418&zoom=14">Help us by tagging venues you know about</a></div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <H3>By Category</H3>
            </div>
        </div>
        <div class="row">
            <div class="col-3"></div>
            <div class="col-5"></div>
            <div class="col-2"><B>Known / Total</B></div>
            <div class="col-2"><B>Weekly Change</B></div>
        </div>

        <?php for( $categoryId=1; $categoryId <= 12; $categoryId++ ){ ?>
            <div class="row">
                <div class="col-3">
                    <?php echo $category_names[$categoryId]; ?>
                </div>
                <div class="col-5">
                    <div class="progress progress-category">
                        <div class="progress-bar wheelchair-yes" role="progressbar" style="width: <?php echo $percent[$categoryId]['yes']; ?>%" aria-valuenow="<?php echo $percent[$categoryId]['yes']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[$categoryId]['yes']; ?>%</div>
                        <div class="progress-bar wheelchair-limited" role="progressbar" style="width: <?php echo $percent[$categoryId]['limited']; ?>%" aria-valuenow="<?php echo $percent[$categoryId]['limited']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[$categoryId]['limited']; ?>%</div>
                        <div class="progress-bar wheelchair-no" role="progressbar" style="width: <?php echo $percent[$categoryId]['no']; ?>%" aria-valuenow="<?php echo $percent[$categoryId]['no']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[$categoryId]['no']; ?>%</div>
                        <div class="progress-bar wheelchair-unknown" role="progressbar" style="width: <?php echo $percent[$categoryId]['unknown']; ?>%" aria-valuenow="<?php echo $percent[$categoryId]['unknown']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent[$categoryId]['unknown']; ?>%</div>
                    </div>
                </div>

                <div class="col-2">
                    <?php echo $known_venues[$categoryId]; ?> / <?php echo $total_venues[$categoryId]; ?>
                </div>
                <div class="col-2">
                    <?php printf('%+d', $week_increase_known[$categoryId] ); ?>
                </div>
            </div>
        <?php } ?>
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