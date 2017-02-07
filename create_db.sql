CREATE TABLE `wheelmap_history`.`venue_counts` ( `category` INT NOT NULL , `wheelchair` ENUM('yes','limited','no','unknown') NOT NULL , `time` DATETIME NOT NULL , `count` INT NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `wheelmap_history`.`venue_counts` ADD PRIMARY KEY (`time`, `category`, `wheelchair`);

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `identifier` text NOT NULL,
  `localized_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `categories` (`id`, `identifier`, `localized_name`) VALUES
  (1, 'public_transfer', 'Public transport'),
  (2, 'food', 'Food'),
  (3, 'leisure', 'Leisure'),
  (4, 'money_post', 'Bank / Post office'),
  (5, 'education', 'Education'),
  (6, 'shopping', 'Shopping'),
  (7, 'sport', 'Sport'),
  (8, 'tourism', 'Tourism'),
  (9, 'accommodation', 'Accomodation'),
  (10, 'misc', 'Miscellaneous'),
  (11, 'government', 'Government'),
  (12, 'health', 'Health');

ALTER TABLE `categories`
ADD KEY `id` (`id`);