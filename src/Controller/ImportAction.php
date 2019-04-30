<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Helper\WebsitesAndPages;
use Pro\Importer;
use Exception;

class ImportAction
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var WebsiteManager
     */
    private $websiteManager;
    /**
     * @var PageManager
     */
    private $pageManager;

    public function __construct(UserManager $userManager, WebsiteManager $websiteManager, PageManager $pageManager)
    {
        $this->userManager = $userManager;
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
    }

    public function execute()
    {
        if (isset($_SESSION['login']) && ($user = $this->userManager->getByLogin($_SESSION['login']))) {

            try {
                $importer = new Importer();
                $importer->uploadFile('web', 'file', APP_BASE_DIR . 'uploads');
                $websites = $importer->loadPages();
                $counters = WebsitesAndPages::add($websites, $user, $this->websiteManager, $this->pageManager);
                $_SESSION['flash'] = "Successfully imported {$counters['websites']} websites and {$counters['pages']} pages!";
                $_SESSION['flash_status'] = true;
            } catch (Exception $ex) {
                $_SESSION['flash'] = $ex->getMessage();
                $_SESSION['flash_status'] = false;
            }
        }

        header('Location: /');
    }
}