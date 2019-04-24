<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
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
     * Get all users varnish servers
     * @param User $user
     * @return Varnish[]
     */
    public function getAllByUser(User $user)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM varnishes WHERE user_id = :user');
        $query->bindParam(':user', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * Get all varnish servers by given IP address
     * @param string $ip
     * @return Varnish[]
     */
    public function getAllByIp($ip)
    {
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM varnishes WHERE ip = :ip');
        $query->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * Get all websites assigned to given varnish server
     * @param Varnish $varnish
     * @return Website[]
     */
    public function getWebsites(Varnish $varnish)
    {
        $varnishId = $varnish->getVarnishId();
        $query = $this->database->prepare('SELECT w.* FROM websites w LEFT JOIN varnishes_websites v ON v.website_id = w.website_id WHERE v.varnish_id = :id');
        $query->bindParam(':id', $varnishId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    /**
     * Get all varnish servers assigned to given website
     * @param Website $website
     * @return array
     */
    public function getByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        $query = $this->database->prepare('SELECT v.* FROM varnishes v LEFT JOIN varnishes_websites vw ON v.varnish_id = vw.varnish_id WHERE vw.website_id = :wid');
        $query->bindParam(':wid', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    /**
     * @param User $user
     * @param $ip
     * @return string
     */
    public function create(User $user, $ip)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes (ip, user_id) VALUES (:ip, :user)');
        $statement->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    /**
     * Delete user varnish server
     * @param User $user
     * @param $varnishId
     */
    public function delete(User $user, $varnishId)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('DELETE FROM varnishes WHERE varnish_id = :id AND user_id = :user');
        $statement->bindParam(':id', $varnishId, \PDO::PARAM_STR);
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Assign websites to given varnish server
     * @param $varnishId
     * @param array $websites
     */
    public function link($varnishId, $websites = [])
    {
        $params = $this->linkParams($varnishId, $websites);

        if ($params === false || empty($params[1])) {
            return;
        }

        $ids = [];
        $vid = $params[0];
        $wids = $params[1];
        $widsStr = implode(',', $wids);

        /** @var \PDOStatement $query */
        $query = $this->database->prepare("SELECT * FROM varnishes_websites WHERE varnish_id = :vid AND website_id in ($widsStr)");
        $query->bindParam(':vid', $vid, \PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        if ( ! empty($result)) {
            foreach ($result as $row) {
                $ids[] = $row['website_id'];
            }
        }

        $wids = array_diff($wids, $ids);

        if (empty($wids)) {
            return;
        }

        $values = [];

        foreach ($wids as $wid) {
            $values[] = "($vid, $wid)";
        }

        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes_websites(varnish_id, website_id) VALUES ' . implode(',', $values));
        $statement->execute();
    }

    /**
     * Unassign websites to given server
     * @param $varnishId
     * @param $websites
     */
    public function unlink($varnishId, $websites)
    {
        $params = $this->linkParams($varnishId, $websites);

        if ($params === false || empty($params[1])) {
            return;
        }

        $vid = $params[0];
        $wids = $params[1];
        $widsStr = implode(',', $wids);

        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare("DELETE FROM varnishes_websites WHERE varnish_id = :vid AND website_id in ($widsStr)");
        $statement->bindParam(':vid', $vid, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Prepare input params to work
     * @param $varnishId
     * @param $websites
     * @return array    of converted parameters
     */
    private function linkParams($varnishId, $websites)
    {
        $vid = (int) $varnishId;

        if ($vid <= 0 || empty($websites)) {
            return false;
        }

        $wids = [];

        foreach ($websites as $id) {
            $wid = (int) $id;

            if ($wid <= 0) {
                continue;
            }

            $wids[]= $wid;
        }

        $wids = array_unique($wids);

        return [$vid, $wids];
    }

}