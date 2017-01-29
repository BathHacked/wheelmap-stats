# wheelmap-stats
Progress bar &amp; project page for crowdsourcing accessibility info in Bath.

Live website: http://crashthatch.com/wheelmap


##Setup & Usage
Clone this repository inside a web accessible folder and serve using Apache & PHP (Tested with Apache/2.4.7, PHP 5.5.9).

Create a MySQL database and run the create_db.sql script to create the tables.

Copy the `config-sample.php` file to `config.php`, and edit to include your config values (API keys, MySQL connection vars etc.)

Set up a cron job to regularly pull the latest counts from wheelmap.org's API: `*/5 * * * * php /path/to/grab_counts.php`

##TODOs:
 - Accessibility by category - individual progress bars for each category?
 - "Venues tagged this week" / day / month.
 - Leaderboard of what users did the tagging?
 