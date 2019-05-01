<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\WebsiteManager;

class CreatePageAction extends BaseController
{
    /**
     * CreatePageAction constructor.
     * @param WebsiteManager $websiteManager
     * @param PageManager $pageManager
     */
    public function __construct(WebsiteManager $websiteManager, PageManager $pageManager)
    {
        $this->onlyAuthorized();
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
    }

    public function execute()
    {
        $url = $_POST['url'];
        $websiteId = $_POST['website_id'];
        $website = $this->websiteManager->getById($websiteId);

        if ($website->getUserId() == $this->user->getUserId()) {
            if (empty($url)) {
                $_SESSION['flash'] = 'URL cannot be empty!';
            } else {
                if ($this->pageManager->create($website, $url)) {
                    $_SESSION['flash'] = 'URL ' . $url . ' added!';
                }
            }
        }

        header('Location: /website/' . $websiteId);
    }
}