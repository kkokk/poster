<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:21
 */

namespace Kkokk\Poster\Interfaces;

interface MyCaptcha
{
    /**
     * 基础配置
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/10
     * Time: 11:53
     * @param array $params
     * @return mixed
     */
    public function config($params = []);

    /**
     * 检查密码是否正确
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/10
     * Time: 11:53
     * @param string $key key值
     * @param string|int|array $value 比对值
     * @param int $leeway 误差
     * @param null $secret 没有缓存的时候，传用户自行储存的密码
     * @return mixed
     */
    public function check($key , $value , $leeway = 0, $secret = null);

    /**
     * 获取图片验证参数
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/10
     * Time: 11:55
     * @param int $expire 设置有效时间（前提：有缓存才能生效）
     * @return mixed
     */
    public function get($expire = 0);
}