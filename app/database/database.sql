create database `xml-expressions-parser`;

use `xml-expressions-parser`;

create table `expressions` (
   `id` int(64) not null auto_increment primary key,
   `expression` text,
   `total` varchar(255) default null
);

