<?php
namespace Kkokk\Poster\Abstracts;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Lang\Base;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 09:40:46
 * 抽象方法，只有继承所有定义的方法，才能被实例化
 */

/**
 * 执行
 */
abstract class PosterAbstract extends Base
{	

	abstract public function buildIm($w,$h,$rgba=[],$alpha=false);
	abstract public function buildImDst($src,$w=0,$h=0);
    abstract public function buildImage($src,$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0,$alpha=false,$type='normal');
	abstract public function buildImageMany($arr = []);
    abstract public function buildText($content,$dst_x=0,$dst_y=0,$font=16,$rgba=[],$max_w=0,$font_family='',$weight=1,$space=0);
	abstract public function buildTextMany($arr = []);
    abstract public function buildQr($text,$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0,$size=4,$margin=1);
    abstract public function buildQrMany($arr = []);
    abstract public function Qr($text,$outfile=false,$level='L',$size=4,$margin=1,$saveandprint=0);
	abstract public function getPoster();
	abstract public function setPoster();
	abstract public function stream();

	public function __construct($params = [])
	{	
        parent::__construct($params);

	}

}