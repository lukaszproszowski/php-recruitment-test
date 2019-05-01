<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;

class VarnishAction extends BaseController
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
     * @var Website
     */
    private $website;

    /**
     * VarnishAction constructor.
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
     * @param $id website
     */
    public function execute($id)
    {
        $website = $this->websiteManager->getById($id);

        if ($website->getUserId() == $this->user->getUserId()) {
            $this->website = $website;
        }

        require __DIR__ . '/../view/website.phtml';
    }

    /**
     * @return array|\Snowdog\DevTest\Model\Page[]
     */
    protected function getPages()
    {
        if($this->website) {
            return $this->pageManager->getAllByWebsite($this->website);
        }
        return [];
    }
}