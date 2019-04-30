<?php

namespace Snowdog\DevTest\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Helper\WebsitesAndPages;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use DI\ContainerBuilder;
use Pro\Importer;
use Exception;

class ImportCommand extends SymfonyCommand
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
     * @var PageManager
     */
    private $pageManager;
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $container = ContainerBuilder::buildDevContainer();
        $this->varnishManager = $container->get(VarnishManager::class);
        $this->websiteManager = $container->get(WebsiteManager::class);
        $this->pageManager = $container->get(PageManager::class);
        $this->userManager = $container->get(UserManager::class);

        $this
            ->setName('import')
            ->setDescription('Import websites and pages from sitemap.xml file')
            ->addArgument('path', InputArgument::REQUIRED, 'Sitemap.xml file path')
            ->addArgument('login', InputArgument::REQUIRED, 'User login');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $path = $input->getArgument('path');
            $login = $input->getArgument('login');
            $user = $user = $this->userManager->getByLogin($login);

            if (empty($user)) {
                throw new Exception('User not exists!');
            }

            $importer = new Importer();
            $importer->uploadFile('path', APP_BASE_DIR . $path);
            $websites = $importer->loadPages();
            $counters = WebsitesAndPages::add($websites, $user, $this->websiteManager, $this->pageManager);
            $output->writeln("Successfully imported {$counters['websites']} websites and {$counters['pages']} pages!");
        } catch (Exception $ex) {
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
        }
    }
}