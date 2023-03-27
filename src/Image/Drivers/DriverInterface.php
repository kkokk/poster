<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 11:08
 */

namespace Kkokk\Poster\Image\Drivers;

interface DriverInterface
{
    public function Im($w, $h, $rgba, $alpha);

    /**
     * 获取文件路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:23
     * @param string $path
     * @return array
     */
    public function getData($path = '');

    /**
     * 输出流
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:23
     * @return mixed
     */
    public function getStream();

    /**
     * 获取base64文件
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:23
     * @return string
     */
    public function getBaseData();

    /**
     * 设置图片
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/27
     * Time: 11:24
     * @return array|\Kkokk\Poster\Exception\PosterException
     */
    public function setData();
}