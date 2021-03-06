<?php

use Snowdog\DevTest\Command\MigrateCommand;
use Snowdog\DevTest\Command\WarmCommand;
use Snowdog\DevTest\Command\ImportCommand;
use Snowdog\DevTest\Component\CommandRepository;
use Snowdog\DevTest\Component\Menu;
use Snowdog\DevTest\Component\Migrations;
use Snowdog\DevTest\Component\RouteRepository;
use Snowdog\DevTest\Controller\CreatePageAction;
use Snowdog\DevTest\Controller\CreateWebsiteAction;
use Snowdog\DevTest\Controller\ImportAction;
use Snowdog\DevTest\Controller\IndexAction;
use Snowdog\DevTest\Controller\LoginAction;
use Snowdog\DevTest\Controller\LoginFormAction;
use Snowdog\DevTest\Controller\LogoutAction;
use Snowdog\DevTest\Controller\RegisterAction;
use Snowdog\DevTest\Controller\RegisterFormAction;
use Snowdog\DevTest\Controller\WebsiteAction;
use Snowdog\DevTest\Controller\VarnishesAction;
use Snowdog\DevTest\Controller\CreateVarnishAction;
use Snowdog\DevTest\Controller\DeleteVarnishAction;
use Snowdog\DevTest\Controller\CreateVarnishLinkAction;
use Snowdog\DevTest\Menu\LoginMenu;
use Snowdog\DevTest\Menu\RegisterMenu;
use Snowdog\DevTest\Menu\WebsitesMenu;
use Snowdog\DevTest\Menu\VarnishesMenu;

RouteRepository::registerRoute('GET', '/', IndexAction::class, 'execute');
RouteRepository::registerRoute('GET', '/login', LoginFormAction::class, 'execute');
RouteRepository::registerRoute('POST', '/login', LoginAction::class, 'execute');
RouteRepository::registerRoute('GET', '/logout', LogoutAction::class, 'execute');
RouteRepository::registerRoute('GET', '/register', RegisterFormAction::class, 'execute');
RouteRepository::registerRoute('POST', '/register', RegisterAction::class, 'execute');
RouteRepository::registerRoute('POST', '/website', CreateWebsiteAction::class, 'execute');
RouteRepository::registerRoute('POST', '/import', ImportAction::class, 'execute');
RouteRepository::registerRoute('GET', '/website/{id:\d+}', WebsiteAction::class, 'execute');
RouteRepository::registerRoute('POST', '/page', CreatePageAction::class, 'execute');
RouteRepository::registerRoute('GET', '/varnishes', VarnishesAction::class, 'execute');
RouteRepository::registerRoute('POST', '/varnish', CreateVarnishAction::class, 'execute');
RouteRepository::registerRoute('GET', '/varnish/{id:\d+}/delete', DeleteVarnishAction::class, 'execute');
RouteRepository::registerRoute('POST', '/link', CreateVarnishLinkAction::class, 'execute');

CommandRepository::registerCommand(MigrateCommand::class);
CommandRepository::registerCommand(WarmCommand::class);
CommandRepository::registerCommand(ImportCommand::class);

Menu::register(WebsitesMenu::class, 10);
Menu::register(VarnishesMenu::class, 20);
Menu::register(RegisterMenu::class, 250);
Menu::register(LoginMenu::class, 200);

Migrations::registerComponentMigration('Snowdog\\DevTest', 5);
