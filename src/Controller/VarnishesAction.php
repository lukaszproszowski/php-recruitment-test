<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\Varnish;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\WebsiteManager;

class VarnishesAction extends BaseController
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;
    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * VarnishesAction constructor.
     * @param VarnishManager $varnishManager
     * @param WebsiteManager $websiteManager
     */
    public function __construct(VarnishManager $varnishManager, WebsiteManager $websiteManager)
    {
        $this->onlyAuthorized();
        $this->varnishManager = $varnishManager;
        $this->websiteManager = $websiteManager;
    }

    /**
     * @return array|Varnish[]
     */
    public function getVarnishes()
    {
        if($this->user) {
            return $this->varnishManager->getAllByUser($this->user);
        }
        return [];
    }

    /**
     * @return array
     */
    public function getWebsites()
    {
        if($this->user) {
            return $this->websiteManager->getAllByUser($this->user);
        }
        return [];
    }

    /**
     * @param Varnish $varnish
     * @return array
     */
    public function getAssignedWebsiteIds(Varnish $varnish)
    {
        $websites = $this->varnishManager->getWebsites($varnish);
        $ids = [];
        foreach($websites as $website) {
            $ids[] = $website->getWebsiteId();
        }
        return $ids;
    }

    public function execute() {

        include __DIR__ . '/../view/varnish.phtml';
    }

}