<?php

namespace Snowdog\DevTest\Helper;

class WebsitesAndPages
{
    /**
     * @param $websites
     * @param $user
     * @param $websiteManager
     * @param $pageManager
     * @return array
     */
    public static function add($websites, $user, $websiteManager, $pageManager)
    {
        $counters = ['websites' => 0, 'pages' => 0];

        foreach ($websites as $website => $pages) {
            if (empty($pages)) {
                continue;
            }

            /** Create website */
            $websiteId = $websiteManager->create($user, $website, $website);

            if ( ! $websiteId) {
                continue;
            }

            $_website = $websiteManager->getById($websiteId);

            if (empty($_website)) {
                continue;
            }

            $counters['websites']++;

            foreach ($pages as $page) {
                /** create page */
                if ($pageManager->create($_website, $page)) {
                    $counters['pages']++;
                }
            }
        }

        return $counters;
    }
}


