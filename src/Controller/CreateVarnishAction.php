<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;
use Exception;

class CreateVarnishAction
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

    public function execute()
    {
        $msg = '';

        try {

            if ( ! isset($_SESSION['login'])) {
                throw new Exception();
            }

            if ( ! isset($_POST['ip'])) {
                throw new Exception('IP address is empty!');
            }

            $ip = $_POST['ip'];

            if ( ! preg_match_all('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip, $match)) {
                throw new Exception('check IP address format!');
            }

            $varnishes = $this->varnishManager->getAllByIp($ip);

            if ( ! empty($varnishes)) {
                throw new Exception('Varnish server with given IP already exists / is not available!');
            }

            $user = $this->userManager->getByLogin($_SESSION['login']);
            $this->varnishManager->create($user, $ip);
            $msg = 'Varnish server was created!';

        } catch (Exception $ex) {
            $msg = $ex->getMessage();
        }

        if ( ! empty($msg)) {
            $_SESSION['flash'] = $msg;
        }

        header('Location: /varnishes');
    }
}