<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version4
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
        $this->alterPagesTable();
    }

    private function alterPagesTable()
    {
        $createQuery = <<<SQL
ALTER TABLE `pages` ADD `visits` INT NULL DEFAULT 0 AFTER `last_warm`;
SQL;
        $this->database->exec($createQuery);
    }
}