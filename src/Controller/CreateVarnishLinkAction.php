<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\VarnishManager;
use Exception;

class CreateVarnishLinkAction extends BaseController
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    /**
     * CreateVarnishLinkAction constructor.
     * @param VarnishManager $varnishManager
     */
    public function __construct(VarnishManager $varnishManager)
    {
        $this->onlyAuthorized();
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $out = ['status' => 'error', 'message' => ''];

        try {
            if ( ! isset($_POST['servers'])) {
                throw new Exception('No varnishes.');
            }

            $servers = $_POST['servers'];

            foreach ($servers as $varnishId => $actions) {

                if (array_key_exists('c', $actions)) {
                    $this->varnishManager->link($varnishId, $actions['c']);
                }

                if (array_key_exists('u', $actions)) {
                    $this->varnishManager->unlink($varnishId, $actions['u']);
                }
            }

            $out['status'] = 'ok';
            $out['message'] = 'Success!';

        } catch (Exception $ex) {
            $out['message'] = $ex->getMessage();
        }

        header('content-type: application/json; charset=utf-8');
        echo json_encode($out);
    }
}