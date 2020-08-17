<?php
namespace Kkokk\Poster\PosterAbstract;
use Kkokk\Poster\Lang\PosterAbstract;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:49:51
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-17 14:06:25
 */
/**
 * 
 */
class AbstractTest extends PosterAbstract
{
	
	/**
	 * [buildIm description] 创建画布
	 * @Author   lang
	 * @DateTime 2020-08-14T20:52:41+0800
	 * @param    number                  $w [description] 画布宽
	 * @param    number                  $h [description] 画布高
	 * @param    array                   $rgba [description] 颜色rbga
	 * @param    boolean                 $alpha [description] 是否透明
	 */
	public function buildIm($w,$h,$rgba=[],$alpha=false)
	{

		$this->Im($w,$h,$rgba,$alpha);
		return $this;
	}

	/**
	 * [buildImDst description] 创建指定图片为画布
	 * @Author   lang
	 * @DateTime 2020-08-15T11:14:48+0800
	 * @param    [src]                    $src   [description] 图像资源
	 * @param    integer                  $w     [description]
	 * @param    integer                  $h     [description]
	 * @param    array                    $rgba  [description]
	 * @param    boolean                  $alpha [description]
	 * @return   [type]                          [description]
	 */
	public function buildImDst($src,$w=0,$h=0){
		$this->ImDst($src,$w,$h);
		return $this;
	}
	
	/**
	 * [buildImage description] 合成图片
	 * @Author   lang
	 * @DateTime 2020-08-14T20:56:54+0800
	 * @param    [type]                   $src   [description]
	 * @param    integer                  $dst_x [description]
	 * @param    integer                  $dst_y [description]
	 * @param    integer                  $src_x [description]
	 * @param    integer                  $src_y [description]
	 * @param    integer                  $src_w [description]
	 * @param    integer                  $src_h [description]
	 * @param    string                   $type  [description]
	 * @param    boolean                  $alpha [description] 透明
	 * @return   [type]                          [description]
	 */
	public function buildImage($src,$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0,$alpha=false,$type='normal')
	{

		$this->CopyImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha,$type);
		return $this;
	}

	/**
	 * [buildText description] 合成文字
	 * @Author   lang
	 * @DateTime 2020-08-14T22:09:20+0800
	 * @param    [type]                   $content     [description]
	 * @param    integer                  $dst_x       [description]
	 * @param    integer                  $dst_y       [description]
	 * @param    integer                  $font        [description]
	 * @param    array                    $rgba        [description]
	 * @param    integer                  $max_w       [description]
	 * @param    string                   $font_family [description]
	 * @return   [type]                                [description]
	 */
	public function buildText($content,$dst_x=0,$dst_y=0,$font=16,$rgba=[],$max_w=0,$font_family='')
	{
		
		$this->CopyText($content,$dst_x,$dst_y,$font,$rgba,$max_w,$font_family);
		return $this;
	}

	/**
	 * [getPoster description] 获取合成后图片地址
	 * @Author   lang
	 * @DateTime 2020-08-16T15:45:57+0800
	 * @return   [type]   [description]
	 */
	public function getPoster(){

		return $this->getData();
	}

	/**
	 * [setPoster description] 处理当前图片
	 * @Author   lang
	 * @DateTime 2020-08-16T15:46:01+0800
	 */
	public function setPoster(){

		return $this->setData();
	}
}