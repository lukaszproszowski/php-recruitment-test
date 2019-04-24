<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

class DeleteVarnishAction
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
    }

    public function execute($id)
    {
        if (isset($_SESSION['login'])) {
            $user = $this->userManager->getByLogin($_SESSION['login']);
            $varnishes = $this->varnishManager->getAllByUser($user);

            foreach ($varnishes as $varnish) {
                if ( ! $varnish->getVarnishId() === $id) {
                    continue;
                }

                $ip = $varnish->getIp();
                $this->varnishManager->delete($user, $id);
                $_SESSION['flash'] = 'Varnish server ' . $ip . ' was deleted!';
                break;
            }
        }

        header('Location: /varnishes');
    }
}