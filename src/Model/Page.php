<?php

namespace Snowdog\DevTest\Model;

class Page
{

    public $page_id;
    public $url;
    public $website_id;
    public $last_warm;
    public $visits;
    
    public function __construct()
    {
        $this->website_id = intval($this->website_id);
        $this->page_id = intval($this->page_id);
        $this->visits = intval($this->visits);
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->website_id;
    }

    /**
     * @return string
     */
    public function getLastWarm()
    {
        return $this->last_warm;
    }

    /**
     * @return int
     */
    public function getVisits()
    {
        return $this->visits;
    }
    
    
}