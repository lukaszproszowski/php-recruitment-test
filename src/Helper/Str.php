<?php

namespace Snowdog\DevTest\Helper;

/**
 * Helper class to operate with strings
 * @package Snowdog\DevTest\Helper
 */
class Str
{
    /**
     * Prepare popularity string
     * @param $parts    array of strings
     * @return string
     */
	public static function preparePopularityString($parts)
	{
		if (empty($parts)) {
			return '-';
		}

		$hostname = substr($parts['hostname'], -1) === '/'? substr($parts['hostname'], 0, -1) : $parts['hostname'];
        $url = substr($parts['url'], 0, 1) === '/'? substr($parts['url'], 1) : $parts['url'];
        $website = $parts['name'];

        return "($website) $hostname/$url";
	}

}