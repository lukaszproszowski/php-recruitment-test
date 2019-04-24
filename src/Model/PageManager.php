<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;
use Snowdog\DevTest\Helper\Str;

class PageManager
{
    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Get all pages by given website
     * @param Website $website
     * @return Page[]
     */
    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    /**
     * Get all pages cunt for given user
     * @param User $user
     * @return int
     */
    public function getAllUserPagesCount(User $user)
    {
        if ( ! $user) {
            return 0;
        }

        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('
            SELECT count(`p`.`page_id`) as `count` FROM `pages` `p` 
            LEFT JOIN `websites` `w` ON `p`.`website_id` = `w`.`website_id` 
            WHERE `w`.`user_id` = :user
        ');
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return (int) $result['count'];
    }

    /**
     * Get least recently visited page for given user
     * @param $user
     * @return string
     */
    public function getLeastRecentlyVisitedPage($user)
    {
        if ( ! $user) {
            return '0';
        }

        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('
            SELECT `p`.`url`, `w`.`hostname`, `w`.`name` FROM `pages` `p`
            LEFT JOIN `websites` `w` ON `p`.`website_id` = `w`.`website_id`
            WHERE `w`.`user_id` = :user
            ORDER BY `p`.`visits` ASC, `p`.`last_warm` DESC, `p`.`page_id` DESC
            LIMIT 1
        ');
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();

        return Str::preparePopularityString($statement->fetch());
    }

    /**
     * Get most receltny visited page for given user
     * @param $user
     * @return int|string
     */
    public function getMostRecentlyVisitedPage($user)
    {
        if ( ! $user) {
            return '0';
        }

        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('
            SELECT `p`.`url`, `w`.`hostname`, `w`.`name` FROM `pages` `p`
            LEFT JOIN `websites` `w` ON `p`.`website_id` = `w`.`website_id`
            WHERE `w`.`user_id` = :user AND `p`.`visits` > 0
            ORDER BY `p`.`visits` DESC, `p`.`last_warm` DESC
            LIMIT 1
        ');
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
        
        return Str::preparePopularityString($statement->fetch());
    }

    /**
     * Create page with url for given website
     * @param Website $website
     * @param $url
     * @return int
     */
    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    /**
     * Update page last warm date
     * @param $pageId
     * @param $date
     */
    public function updatePageLastWarmDate($pageId, $date)
    {
        $this->database->prepare('UPDATE pages SET last_warm = ? WHERE page_id = ?')->execute([$date, $pageId]);
    }

    /**
     * Update number of page visits
     * @param $pageId
     * @param $oldValue
     */
    public function updatePageVisitsValue($pageId, $oldValue)
    {
        $this->database->prepare('UPDATE pages SET visits = ? WHERE page_id = ?')->execute([$oldValue + 1, $pageId]);
    }
}