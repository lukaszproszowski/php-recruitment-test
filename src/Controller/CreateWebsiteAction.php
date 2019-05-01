<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\WebsiteManager;

class CreateWebsiteAction extends BaseController
{
    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * CreateWebsiteAction constructor.
     * @param WebsiteManager $websiteManager
     */
    public function __construct(WebsiteManager $websiteManager)
    {
        $this->onlyAuthorized();
        $this->websiteManager = $websiteManager;
    }

    public function execute()
    {
        $name = $_POST['name'];
        $hostname = $_POST['hostname'];

        if(empty($name) || empty($hostname)) {
            $_SESSION['flash'] = 'Name and Hostname cannot be empty!';
        } elseif ($this->websiteManager->create($this->user, $name, $hostname)) {
            $_SESSION['flash'] = 'Website ' . $name . ' added!';
        }

        header('Location: /');
    }
}