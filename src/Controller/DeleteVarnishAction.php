<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\VarnishManager;

class DeleteVarnishAction extends BaseController
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(VarnishManager $varnishManager)
    {
        $this->onlyAuthorized();
        $this->varnishManager = $varnishManager;
    }

    /**
     * Delete varnish server
     * @param $id
     */
    public function execute($id)
    {
        $varnishes = $this->varnishManager->getAllByUser($this->user);

        foreach ($varnishes as $varnish) {
            if ( ! $varnish->getVarnishId() === $id) {
                continue;
            }

            $ip = $varnish->getIp();
            $this->varnishManager->delete($this->user, $id);
            $_SESSION['flash'] = 'Varnish server ' . $ip . ' was deleted!';
            break;
        }

        header('Location: /varnishes');
    }
}