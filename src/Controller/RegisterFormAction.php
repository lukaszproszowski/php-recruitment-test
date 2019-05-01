<?php

namespace Snowdog\DevTest\Controller;

class RegisterFormAction extends BaseController
{
    /**
     * RegisterFormAction constructor.
     */
    public function __construct()
    {
        $this->onlyGuest();
    }

    public function execute()
    {
        require __DIR__ . '/../view/register.phtml';
    }
}