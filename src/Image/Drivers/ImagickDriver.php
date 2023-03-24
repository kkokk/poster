<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:31
 */

namespace Kkokk\Poster\Image\Drivers;


class ImagickDriver extends Driver
{
    protected $result = null;

    function __construct()
    {
        var_dump('imagick');
    }

    public function execute($query) {

        foreach ($query as $item){
            $this->run($item);
        }

        return $this->result;
    }

    protected function run($item){
        switch ($item['type']) {
            case 'im':
                break;
            case 'qr':
                $this->result = $this->createQr(...$item['params']);
                break;
        }
    }
}