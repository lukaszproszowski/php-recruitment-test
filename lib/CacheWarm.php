<?php

interface Old_Legacy_CacheWarmer_Resolver_Interface
{
    public function getIp($server);
}

class Old_Legacy_CacheWarmer_Resolver_Method implements Old_Legacy_CacheWarmer_Resolver_Interface 
{
    public function getIp($server)
    {
        return $server->getIp();
    }
}

class Old_Legacy_CacheWarmer_Actor
{
    private $callable;

    public function setActor($callable) {
        $this->callable = $callable;
    }
    
    public function act($hostname, $ip, $url)
    {
        call_user_func($this->callable, $hostname, $ip, $url);
    }
}

class Old_Legacy_CacheWarmer_Warmer
{
    /** @var Old_Legacy_CacheWarmer_Actor */
    private $actor;
    /** @var Old_Legacy_CacheWarmer_Resolver_Interface */
    private $resolver;
    /** @var string */
    private $hostname;
    /** @var   */
    private $server;

    /**
     * @param Old_Legacy_CacheWarmer_Actor $actor
     */
    public function setActor($actor)
    {
        $this->actor = $actor;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @param $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * @param Old_Legacy_CacheWarmer_Resolver_Interface $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }

    public function warm($url) {
        $ip = $this->resolver->getIp($this->server);
        sleep(1); // this emulates visit to http://$hostname/$url via $ip
        $this->actor->act($this->hostname, $ip, $url);
    }
    
}

