<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version5
{
    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->createVarnishesTable();
        $this->createVarnishesWebsitesTable();
    }

    private function createVarnishesTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes` (
  `varnish_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`varnish_id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `varnish_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }

    private function createVarnishesWebsitesTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes_websites` (
  `varnish_website_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `varnish_id` int(11) unsigned NOT NULL,
  `website_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`varnish_website_id`),
  KEY `varnish_id` (`varnish_id`),
  CONSTRAINT `varnish_varnish_fk` FOREIGN KEY (`varnish_id`) REFERENCES `varnishes` (`varnish_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }
}