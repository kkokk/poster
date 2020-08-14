<?php
namespace Kkokk\LangPoster\Lang;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 18:00:15
 */
use Kkokk\LangPoster\InterFaces\MyPoster;

/**
 * 执行
 */
class Poster extends Base implements MyPoster
{
	
	public function buildIm($w,$h,array $rgba=[255,255,255,127],$alpha=false)
	{

		$this->Im($w,$h,$rgba,$alpha);
		return $this;
	}

	public function buildImage($src,$type='normal',$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0)
	{

		$this->CopyImage($src,$type,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h);
		return $this;
	}

	public function buildText(array $param)
	{
		$this->im = imagecreatetruecolor($param['w'],$param['h']);

		// print_r($this->returnImage('png'));

		exit;
		return $this->getData($url);
		return $this;
	}

	public function getPoster(){

		return $this->getData();
	}
}