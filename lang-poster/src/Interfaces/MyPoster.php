<?php
namespace Kkokk\LangPoster\Interfaces;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 10:38:17
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 16:24:35
 */

interface MyPoster{
	/**
	 * @Author lang
	 * @Date   2020-08-14T10:40:54+0800
	 * @param  string
	 * @return [type]
	 */
	public function buildIm($w,$h,array $rgba=[255,255,255,127],$alpha=false);
	public function buildImage($src,$type='normal',$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0);
	public function buildText(array $param);
	public function getPoster();
}