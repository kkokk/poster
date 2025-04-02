<?php
/**
 * User: lang
 * Date: 2025/4/2
 * Time: 14:10
 */

namespace Kkokk\Poster\Captcha\Strategies\Click;

use Kkokk\Poster\Captcha\Strategies\ClickCaptchaInterface;
use Kkokk\Poster\Captcha\Traits\ClickTrait;
use Kkokk\Poster\Exception\PosterException;

class ClickCaptcha extends ClickCaptchaStrategy implements ClickCaptchaInterface
{
    use ClickTrait;

    public function check($key, $value, $leeway = 0, $secret = null)
    {
        if (!is_array($value)) {
            throw new PosterException('Array format required');
        }

        $contents = $this->cache->pull($key) ?: $secret;

        if (!$contents) {
            return false;
        }

        if (!is_array($contents)) {
            $points = json_decode($contents, true);
        } else {
            $points = $contents;
        }

        if (count($points) != count($value)) {
            return false;
        }

        foreach ($points as $k => $v) {
            $point = $v['point'];

            // 任意坐标点
            $p = [$value[$k]['x'], $value[$k]['y']];
            $p1 = [$point[0], $point[1]]; // 左下
            $p2 = [$point[2], $point[3]]; // 右下
            $p3 = [$point[4], $point[5]]; // 右上
            $p4 = [$point[6], $point[7]]; // 左上

            // 叉积计算 点在四条平行线内部则是在矩形内 p1->p2 p1->p3 参考点 p1  叉积大于0点p3在p2逆时针方向 等于0 三点一线 小于0 点p3在p2顺时针防线
            $isCross = cross_product($p1, $p2, $p) * cross_product($p3, $p4, $p) >= 0
                && cross_product($p2, $p3, $p) * cross_product($p4, $p1, $p) >= 0;
            if ($isCross) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    public function get($expire = 0)
    {
        $data = $this->draw();
        list($key, $baseData) = $this->create('click');
        $res = [
            'key'            => $key,
            'img'            => $baseData,
            'content_width'  => $data['content_width'],
            'content_height' => $data['content_height'],
            'x'              => $data['x'],
            'y'              => $data['y'],
        ];
        $setCache = $this->put($key, json_encode($data['contents']), $expire);
        if (!$setCache) {
            $res['secret'] = json_encode($data['contents']);
        }
        return $res;
    }
}