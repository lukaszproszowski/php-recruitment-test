<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\User;
use Snowdog\DevTest\Model\UserManager;

class LoginAction extends BaseController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * LoginAction constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->onlyGuest();
        $this->userManager = $userManager;
    }

    public function execute()
    {
        $login = $_POST['login'];
        $password = $_POST['password'];

        /** @var User $user */
        $user = $this->userManager->getByLogin($login);

        if($user && $this->userManager->verifyPassword($user, $password)) {
            $_SESSION['login'] = $login;
            $_SESSION['flash'] = 'Hello ' . $user->getDisplayName() . '!';
            header('Location: /');
            return;
        }

        $_SESSION['flash'] = 'Incorrect login or password';
        header('Location: /');
    }
}