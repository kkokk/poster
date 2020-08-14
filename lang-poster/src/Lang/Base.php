<?php
namespace Kkokk\LangPoster\Lang;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:21:08
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 18:11:03
 */

use Kkokk\LangPoster\Exception\PosterException;

/**
 * 
 */
class Base
{
	
	protected $im;
	protected $im_w;
	protected $im_h;
	protected $dst;
	protected $src;
	protected $dst_x;
	protected $dst_y;
	protected $src_x;
	protected $src_y;
	protected $src_w;
	protected $src_h;
	protected $pathname = 'poster';
	protected $filename;
	protected $type     = 'png';
	protected $path;

	protected $poster_type = [
		'gif'  => 'imagegif',
		'jpg'  => 'imagejpeg',
		'png'  => 'imagepng',
		'wbmp' => 'imagewbmp'
	];
	


	public function __construct($params = [])
	{	
		if (isset($params) && isset($params[0])) {

			if (strripos($params[0],"\\")!==false) {

				$this->pathname = substr($params[0], 0,strripos($params[0],"\\"));

				$this->filename = substr($params[0], strripos($params[0],"\\")+1);

			}else{
				$this->filename = $params[0];
			}
			
			if (strripos($params[0],".")===false) {
				throw new PosterException('The file naming format is incorrect');
			}

			$this->type = substr($params[0], strripos($params[0],".")+1);

			if (!in_array($this->type, ['jpg','png','gif','wbmp'])) {

				throw new PosterException('The file naming format is incorrect');
			}

        }else{
        	if (empty($this->filename)) {
	        	$this->filename = time().'.'.$this->type;
	        }
        }

		$this->path     = iconv("UTF-8", "GBK", $_SERVER['DOCUMENT_ROOT'])?:__DIR__.'/../../tests/';

	}

	/**
	 * @Author lang
	 * @Date   2020-08-14T14:06:27+0800
	 * @return [type]
	 */
	protected function getData()
	{	
		return $this->returnImage($this->type);
	}

	/**
	 * 返回图片流或者图片
	 * @Author lang
	 * @Date   2020-08-14T14:29:57+0800
	 * @return [type]
	 */
	protected function returnImage($type){

		if (isset($this->pathname) && !empty($this->pathname)) {

			$this->dirExists($this->pathname);	
			$this->poster_type[$type]($this->im,$this->pathname.'\\'.$this->filename);

			# 释放资源
			$this->destroyImage($this->im);

			return ['url'=>$this->pathname.'\\'.$this->filename];
		}
		header('Content-Type: image/'.$type);
		echo $this->poster_type[$type]($this->im);

		$this->destroyImage($this->im);
		exit;
	}

	/**
	 * @Author lang
	 * @Date   2020-08-14T15:32:04+0800
	 * @param  [type]
	 * @return [type]
	 */
	protected function dirExists($pathname){

		if (!file_exists($this->path.$pathname)) {

	        return mkdir($path.$pathname,0777,true);
	    }

	}

	/**
	 * 创建指定宽高，颜色，透明的画布
	 */
	protected function Im($w,$h,$rgba,$alpha){
		$this->im_w = $w;
		$this->im_h = $h;
    	$this->im   = $this->createIm($w,$h,$rgba,$alpha);
	}

	/**
	 * 创建画布
	 */
	protected function createIm($w,$h,$rgba,$alpha){
		$cut = imagecreatetruecolor($w,$h);

		if (count($rgba)!=4) throw new PosterException('The length is 4');
		foreach ($rgba as $k => $value) {
			if(!is_int($rgba[$k])){
				throw new PosterException('The value must be an integer');
			}elseif ($k<3 && ($rgba[$k]>255||$rgba[$k]<0)) {
				throw new PosterException('The color value is between 0-255');
			}elseif($k==3 && ($rgba[$k]>127||$rgba[$k]<0)){
				throw new PosterException('The alpha value is between 0-127');
			}
		}

		$white    = imagecolorallocatealpha($cut, $rgba[0], $rgba[1], $rgba[2], $rgba[3]);
		if ($alpha) imagecolortransparent($cut,$white);
    	imagefill($cut, 0, 0, $white);

    	return $cut;
	}

	/**
	 * 创建图片，合并到画布，释放内存
	 */
	protected function CopyImage($src,$type,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h){

		if (strpos($src,"http")===false) {
            $path = $this->path;
        }else{
            $path = "";
        }

		$pic = imagecreatefromstring(file_get_contents($path.$src));

        list($Width, $Hight, $bgType) = getimagesize($path.$src);

        $bgWidth = !empty($src_w)?$src_w:$Width;
        $bgHight = !empty($src_h)?$src_h:$Hight;

        switch ($type) {
            case 'normal':

                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    $circle_new       = imagecreatetruecolor($bgWidth, $bgHight);
                    $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
                    imagecolortransparent($circle_new,$circle_new_white);
                    imagefill($circle_new, 0, 0, $circle_new_white);
                    $w_circle_new = $bgWidth;
                    $h_circle_new = $bgHight;
                    # 按比例缩放
                    imagecopyresized($circle_new, $pic,0, 0,0, 0,$w_circle_new, $h_circle_new, $Width, $Hight);
                    $pic = $circle_new;
                }
                
                break;
            case 'circle':

                $circle        = imagecreatetruecolor($bgWidth, $bgHight);
                $circle_new    = imagecreatetruecolor($bgWidth, $bgHight);
                // $circle_new_bg = imagecreatetruecolor($bgWidth+2, $bgHight+2);
                
                $w_circle = $bgWidth;
                $h_circle = $bgHight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic,0, 0,0, 0,$w_circle, $h_circle, $Width, $Hight);

                //这一句一定要有
                imagesavealpha($circle, true);
                //拾取一个完全透明的颜色,最后一个参数127为全透明
                $bg = imagecolorallocatealpha($circle, 255, 255, 255, 127);
                imagefill($circle, 0, 0, $bg);
                $r   = ($w_circle / 2 ); //圆半径
                for ($x = 0; $x < $w_circle; $x++) {
                    for ($y = 0; $y < $h_circle; $y++) {
                        $rgbColor = imagecolorat($circle_new, $x, $y);
                        if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                            imagesetpixel($circle, $x, $y, $rgbColor);
                        }
                    }
                }

                $pic = $circle;
                break;
            default:
                # code...
                break;
        }


        //整合海报
        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHight);

        if (isset($circle)&&is_resource($circle)) $this->destroyImage($circle);       
        if (isset($circle_new)&&is_resource($circle_new)) $this->destroyImage($circle_new);       
	}

	/**
	 * 释放资源
	 * @Author lang
	 * @Date   2020-08-14T14:29:46+0800
	 * @param  Resource
	 * @return [type]
	 */
	protected function destroyImage(Resource $Resource){

		imagedestroy($Resource);
	}
}