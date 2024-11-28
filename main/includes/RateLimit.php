<?php
class RateLimit {
    private $redis;
    
    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }
    
    public function checkLimit($ip) {
        $key = "rate_limit:$ip";
        $current = $this->redis->get($key);
        
        if (!$current) {
            $this->redis->setex($key, RATE_LIMIT_WINDOW, 1);
            return true;
        }
        
        if ($current >= RATE_LIMIT_REQUESTS) {
            return false;
        }
        
        $this->redis->incr($key);
        return true;
    }
    
    public function getRemainingRequests($ip) {
        $key = "rate_limit:$ip";
        $current = $this->redis->get($key);
        return $current ? RATE_LIMIT_REQUESTS - $current : RATE_LIMIT_REQUESTS;
    }
} 