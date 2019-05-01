<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManager;

class IndexAction extends BaseController
{
    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * IndexAction constructor.
     * @param WebsiteManager $websiteManager
     * @param PageManager $pageManager
     */
    public function __construct(WebsiteManager $websiteManager, PageManager $pageManager)
    {
        $this->onlyAuthorized();
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
    }

    /**
     * @return array
     */
    protected function getWebsites()
    {
        return $this->websiteManager->getAllByUser($this->user);
    }

    /**
     * @return int
     */
    public function getAllUserPagesCount()
    {
        return $this->pageManager->getAllUserPagesCount($this->user);
    }

    /**
     * @return string
     */
    public function getLeastRecentlyVisitedPage()
    {
        return $this->pageManager->getLeastRecentlyVisitedPage($this->user);
    }

    /**
     * @return int|string
     */
    public function getMostRecentlyVisitedPage()
    {
        return $this->pageManager->getMostRecentlyVisitedPage($this->user);
    }

    public function execute()
    {
        require __DIR__ . '/../view/index.phtml';
    }
}