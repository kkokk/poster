<?php
namespace Kkokk\Poster\PosterAbstract;
use Kkokk\Poster\Lang\PosterAbstract;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:49:51
 * @Last Modified by:   lang
 * @Last Modified time: 2020-12-11 18:46:21
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
	 * [buildQr description] 合成二维码
	 * @Author lang
	 * @Date   2020-10-14T12:14:06+0800
	 * @param  [type]                   $text   [内容]
	 * @param  integer                  $dst_x  [目标位置x]
	 * @param  integer                  $dst_y  [目标位置y]
	 * @param  integer                  $src_x  [自身位置x]
	 * @param  integer                  $src_y  [自身位置y]
	 * @param  integer                  $size   [大小]
	 * @param  integer                  $margin [百变大小]
	 * @return [type]                           [description]
	 */
	public function buildQr($text,$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0,$size=4,$margin=1){
		$this->CopyQr($text,$size,$margin,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h);
		return $this;
	}

	/**
	 * [Qr description]
     * @Author lang
     * @Date   2020-10-14T10:59:28+0800
     * @param  [type]                   $text         [二维码包含的内容，可以是链接、文字、json字符串等等]
     * @param  [type]                   $outfile      [默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径]
     * @param  [type]                   $level        [容错级别，默认为L]
     *      可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)。
     *      这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别
     * @param  [type]                   $size         [控制生成图片的大小，默认为4]
     * @param  [type]                   $margin       [控制生成二维码的空白区域大小]
     * @param  [type]                   $saveandprint [保存二维码图片并显示出来，$outfile必须传递图片路径]
     * @return []                                     [description]
	 */
	public function Qr($text,$outfile=false,$level='L',$size=4,$margin=1,$saveandprint=0)
	{
		return $this->creatQr($text,$outfile,$level,$size,$margin,$saveandprint);
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