<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 10:57
 */

namespace Kkokk\Poster\Cache;

class SessionAdapter extends AbstractAdapter
{
    protected $session;

    public function __construct()
    {
        $this->configure();
    }

    public function configure()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->session = &$_SESSION;
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->session[$key] : $default;
    }

    public function pull($key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    public function put($key, $value, $ttl = null)
    {
        $this->session[$key] = $value;
        if ($ttl !== null) {
            // 设置过期时间戳
            $this->session[$key . '_expires'] = time() + $ttl;
        }
        return true;
    }

    public function forget($key)
    {
        unset($this->session[$key]);
        unset($this->session[$key . '_expires']);
        return true;
    }

    public function flush()
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return true;
    }

    public function has($key)
    {
        if (!isset($this->session[$key])) {
            return false;
        }
        // 检查是否有过期时间戳并且是否已过期
        if (isset($this->session[$key . '_expires']) && $this->session[$key . '_expires'] < time()) {
            $this->forget($key);
            return false;
        }
        return true;
    }
}