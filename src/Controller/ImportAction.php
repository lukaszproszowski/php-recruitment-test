<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Helper\WebsitesAndPages;
use Pro\Importer;
use Exception;

class ImportAction extends BaseController
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
     * ImportAction constructor.
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
        try {
            $importer = new Importer();
            $importer->uploadFile('web', 'file', APP_BASE_DIR . 'uploads');
            $websites = $importer->loadPages();
            $counters = WebsitesAndPages::add($websites, $this->user, $this->websiteManager, $this->pageManager);
            $_SESSION['flash'] = "Successfully imported {$counters['websites']} websites and {$counters['pages']} pages!";
            $_SESSION['flash_status'] = true;
        } catch (Exception $ex) {
            $_SESSION['flash'] = $ex->getMessage();
            $_SESSION['flash_status'] = false;
        }

        header('Location: /');
    }
}