<?php

namespace Snowdog\DevTest\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use DI\ContainerBuilder;

class WarmCommand extends SymfonyCommand
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
     * @inheritdoc
     */
    protected function configure()
    {
        $container = ContainerBuilder::buildDevContainer();
        $this->varnishManager = $container->get(VarnishManager::class);
        $this->websiteManager = $container->get(WebsiteManager::class);
        $this->pageManager = $container->get(PageManager::class);

        $this
            ->setName('warm')
            ->setDescription('Warm cache for all pages of given website')
            ->addArgument('id', InputArgument::REQUIRED, 'Website id');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $website = $this->websiteManager->getById($id);

        if (empty($website)) {
            $output->writeln('<error>Website with ID ' . $id . ' does not exists!</error>');
            return;
        }

        /** load assigned servers */
        $servers = $this->varnishManager->getByWebsite($website);

        if (empty($servers)) {
            $output->writeln('<error>Website with ID ' . $id . ' not assign to any server!</error>');
            return;
        }

        $resolver = new \Old_Legacy_CacheWarmer_Resolver_Method();
        $actor = new \Old_Legacy_CacheWarmer_Actor();

        $actor->setActor(function ($hostname, $ip, $url) use ($output) {
            $output->writeln('Visited <info>http://' . $hostname . $url . '</info> via IP: <comment>' . $ip . '</comment>');
        });

        $pages = $this->pageManager->getAllByWebsite($website);

        /** Warm with all assigned servers */
        foreach ($servers as $server) {
            $warmer = new \Old_Legacy_CacheWarmer_Warmer();
            $warmer->setResolver($resolver);
            $warmer->setHostname($website->getHostname());
            $warmer->setActor($actor);
            $warmer->setServer($server);

            foreach ($pages as $page) {
                $warmer->warm($page->getUrl());
                $this->pageManager->updatePageLastWarmDate($page->getPageId(), date('Y-m-d H:i:s'));
                $this->pageManager->updatePageVisitsValue($page->getPageId(), $page->getVisits());
            }
        }
    }
}