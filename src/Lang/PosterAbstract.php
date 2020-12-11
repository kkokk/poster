<?php
namespace Kkokk\Poster\Lang;
require_once(__DIR__."/../PHPQrcode/phpqrcode.php");
use Kkokk\Poster\Exception\PosterException;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2020-12-11 18:46:32
 */

/**
 * 执行
 */
abstract class PosterAbstract
{   

    protected $im;
    protected $im_w;
    protected $im_h;
    protected $pathname = 'poster';
    protected $filename;
    protected $type     = '';
    protected $path;
    protected $source;
    protected $font_family = __DIR__.'/../style/simkai.ttf';
    protected $poster_type = [
        'gif'  => 'imagegif',
        'jpeg' => 'imagejpeg',
        'jpg'  => 'imagejpeg',
        'png'  => 'imagepng',
        'wbmp' => 'imagewbmp'
    ];
    protected $options = [];
    abstract public function buildIm($w,$h,$rgba=[],$alpha=false);
    abstract public function buildImDst($src,$w=0,$h=0);
    abstract public function buildImage($src,$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0,$alpha=false,$type='normal');
    abstract public function buildText($content,$dst_x=0,$dst_y=0,$font=16,$rgba=[],$max_w=0,$font_family='');
    abstract public function buildQr($text,$dst_x=0,$dst_y=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0,$size=4,$margin=1);
    abstract public function Qr($text,$outfile=false,$level='L',$size=4,$margin=1,$saveandprint=0);
    abstract public function getPoster();
    abstract public function setPoster();

    public function __construct($params = [])
    {   
        if (!empty($params)) $params = [$params];
        if (isset($params) && isset($params[0])) {
            $params[0] = str_replace(['\\','/'], "\\", $params[0]);
            
            if (strripos($params[0],"\\")!==false) {

                $this->pathname = substr($params[0], 0,strripos($params[0],"\\"));

                $this->filename = substr($params[0], strripos($params[0],"\\")+1);

            }else{
                $this->filename = $params[0];
            }


            if (strripos($this->filename,".")!==false) {

                $this->type = substr($this->filename, strripos($this->filename,".")+1);

                if (!in_array($this->type, ['jpeg','jpg','png','gif','wbmp'])) {

                    throw new PosterException('The file naming format is incorrect');
                }
            }else{
                $this->filename = $this->filename;
            }

            

        }else{
            if (empty($this->filename)) {
                $this->filename = time();
            }
        }

        $this->path = iconv("UTF-8", "GBK", $_SERVER['DOCUMENT_ROOT']);
        $this->path = $this->path?$this->path.'/':__DIR__.'/../../tests/';

    }

    /**
     * @Author lang
     * @Date   2020-08-14T14:06:27+0800
     * @return [type]
     */
    protected function getData()
    {   
        if (empty($this->type)) $this->type='png';
        return $this->returnImage($this->type);
    }

    /**
     * [setData description]
     * @Author   lang
     * @DateTime 2020-08-16T12:34:34+0800
     */
    protected function setData()
    {   
        if (empty($this->type)) $this->type='png';
        return $this->setImage($this->type);
    }
    /**
     * 返回图片流或者图片
     * @Author lang
     * @Date   2020-08-14T14:29:57+0800
     * @return [type]
     */
    protected function returnImage($type){

        if (!isset($this->im)||empty($this->im)) throw new PosterException('没有创建任何资源');

        $this->dirExists($this->pathname);
        if (strripos($this->filename,".")===false) {
            $this->filename = $this->filename.'.'.$this->type;
        }
        $this->poster_type[$type]($this->im,$this->path.$this->pathname.'\\'.$this->filename);

        # 释放资源
        // $this->destroyImage($this->im);

        return ['url'=>$this->pathname.'/'.$this->filename];
        
    }

    /**
     * [setImage description]
     * @Author   lang
     * @DateTime 2020-08-16T12:35:17+0800
     * @param    [type]                   $type [description]
     */
    protected function setImage($type){
        if (isset($this->source) && !empty($this->source)) {

            return $this->poster_type[$type]($this->im,$this->source); 
        }

        throw new PosterException('没有找到源文件');
    }
    /**
     * @Author lang
     * @Date   2020-08-14T15:32:04+0800
     * @param  [type]
     * @return [type]
     */
    protected function dirExists($pathname){

        if (!file_exists($this->path.$pathname)) {

            return mkdir($this->path.$pathname,0777,true);
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
     * 创建指定图片为画布 宽高，颜色，透明的画布
     */
    protected function ImDst($source,$w,$h){


        if (!is_file($source)) {
            throw new PosterException('水印图像不存在');
        }
        $this->source = $source;
        //获取水印图像信息
        $info = @getimagesize($source);
        list($bgWidth, $bgHight, $bgType) = @getimagesize($source);

        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            throw new PosterException('非法水印文件');
        }

        $this->type = image_type_to_extension($bgType, false);
        //创建水印图像资源
        $fun   = 'imagecreatefrom' . image_type_to_extension($bgType, false);
        $cut   = $fun($source);
        
        //设定水印图像的混色模式
        imagealphablending($cut, true);

        if (!empty($w)&& !empty($h)) {
            $this->im_w = $w;
            $this->im_h = $h;
            $circle_new       = $this->createIm($w, $h,[255, 255, 255, 127],$alpha=true);
            imagecopyresized($circle_new, $cut,0, 0,0, 0,$w, $h, $bgWidth, $bgHight);
            $cut = $circle_new;
            // $this->destroyImage($circle_new);
        }else{
            $this->im_w = $bgWidth;
            $this->im_h = $bgHight;
        }

        
        $this->im   = $cut;
    }

    /**
     * 创建画布
     */
    protected function createIm($w,$h,$rgba,$alpha=false){
        $cut = imagecreatetruecolor($w,$h);

        $white = $alpha?$this->createColor($cut,$rgba):$this->createColorText($cut,$rgba);
        if ($alpha) {
            imagecolortransparent($cut,$white);
            imagesavealpha($cut, true);
        }
        imagefill($cut, 0, 0, $white);

        return $cut;
    }

    /**
     * 创建画布颜色
     */
    protected function createColor($cut,$rgba=[255,255,255,127]){

        if (empty($rgba)) $rgba=[255,255,255,127];
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

        return imagecolorallocatealpha($cut, $rgba[0], $rgba[1], $rgba[2], $rgba[3]);
    }

    /**
     * 创建文字颜色
     */
    protected function createColorText($cut,$rgba=[255,255,255,1]){

        if (empty($rgba)) $rgba=[255,255,255,1];
        if (count($rgba)<3) throw new PosterException('The text rgba length is 3');
        foreach ($rgba as $k => $value) {
            if(!is_int($rgba[$k])){
                throw new PosterException('The text value must be an integer');
            }elseif ($k<3 && ($rgba[$k]>255||$rgba[$k]<0)) {
                throw new PosterException('The text color value is between 0-255');
            }
        }

        return imagecolorallocate($cut, $rgba[0], $rgba[1], $rgba[2]);
    }

    /**
     * 创建图片，合并到画布，释放内存
     */
    protected function CopyImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha=false,$type='normal'){

        if (strpos($src,"http")===false) {
            $path = $this->path;
        }else{
            $path = "";
        }

        list($Width, $Hight, $bgType) = @getimagesize($path.$src);
        $bgType = image_type_to_extension($bgType, false);

        if ($bgType=='gif') {
            $pic = imagecreatefromstring(file_get_contents($path.$src));
        }else{

            $fun = "imagecreatefrom".$bgType;
            $pic = @$fun($path.$src);
        }
        
        $bgWidth = !empty($src_w)?$src_w:$Width;
        $bgHight = !empty($src_h)?$src_h:$Hight;

        switch ($type) {
            case 'normal':

                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    $circle_new       = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
                    // $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
                    // imagecolortransparent($circle_new,$circle_new_white);
                    // imagefill($circle_new, 0, 0, $circle_new_white);
                    $w_circle_new = $bgWidth;
                    $h_circle_new = $bgHight;
                    # 按比例缩放
                    imagecopyresized($circle_new, $pic,0, 0,0, 0,$w_circle_new, $h_circle_new, $Width, $Hight);
                    $pic = $circle_new;
                }
                
                break;
            case 'circle':

                $circle        = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
                $circle_new    = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
                
                $w_circle = $bgWidth;
                $h_circle = $bgHight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic,0, 0,0, 0,$w_circle, $h_circle, $Width, $Hight);
               
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

        # 处理目标 x 轴
        if ($dst_x === 'center') {

            $dst_x = ceil(($this->im_w - $bgWidth) / 2);

        }elseif (is_numeric($dst_x)&&$dst_x<0) {

            $dst_x = ceil($this->im_w+$dst_x);

        }elseif (strpos($dst_x, "%")!==false) {

            if (substr($dst_x, 0,strpos($dst_x, "%"))<0) {

                $dst_x = ceil($this->im_w+($this->im_w * substr($dst_x, 0,strpos($dst_x, "%"))/100));

            }else{

                $dst_x = ceil($this->im_w * substr($dst_x, 0,strpos($dst_x, "%"))/100);

            }
            

        }

        # 处理目标 y 轴
        if ($dst_y === 'center') {
            $dst_y = ceil(($this->im_h - $bgHight) / 2);
        }elseif (is_numeric($dst_y)&&$dst_y<0) {

            $dst_y = ceil($this->im_h+$dst_y);

        }elseif (strpos($dst_y, "%")!==false) {

            if (substr($dst_y, 0,strpos($dst_y, "%"))<0) {

                $dst_y = ceil($this->im_h+(($this->im_h * substr($dst_y, 0,strpos($dst_y, "%")))/100));

            }else{
                $dst_y = ceil($this->im_h * substr($dst_y, 0,strpos($dst_y, "%"))/100);
            }
            
        }

        //整合海报
        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHight);

        if (isset($circle)&&is_resource($circle)) $this->destroyImage($circle);       
        if (isset($circle_new)&&is_resource($circle_new)) $this->destroyImage($circle_new);       
    }


    protected function CopyMergeImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha=false,$type='normal'){
        if (strpos($src,"http")===false) {
            $path = $this->path;
        }else{
            $path = "";
        }
        

        list($Width, $Hight, $bgType) = @getimagesize($path.$src);
        $bgType = image_type_to_extension($bgType, false);

        if ($bgType=='gif') {
            $pic = imagecreatefromstring(file_get_contents($path.$src));
        }else{

            $fun = "imagecreatefrom".$bgType;
            $pic = @$fun($path.$src);
        }
        

        $bgWidth = !empty($src_w)?$src_w:$Width;
        $bgHight = !empty($src_h)?$src_h:$Hight;

        switch ($type) {
            case 'normal':

                $circle_new  = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
                //整合水印
                imagecopy($circle_new, $pic, 0, 0, 0, 0, $bgWidth, $bgWidth);
                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    // $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
                    // imagecolortransparent($circle_new,$circle_new_white);
                    // imagefill($circle_new, 0, 0, $circle_new_white);
                    $w_circle_new = $bgWidth;
                    $h_circle_new = $bgHight;
                    # 按比例缩放
                    imagecopyresized($circle_new, $pic,0, 0,0, 0,$w_circle_new, $h_circle_new, $Width, $Hight);
                    $pic = $circle_new;
                }
                
                break;
            case 'circle':

                $circle        = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
                $circle_new    = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
                
                $w_circle = $bgWidth;
                $h_circle = $bgHight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic,0, 0,0, 0,$w_circle, $h_circle, $Width, $Hight);
               
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

        //整合水印
        imagecopymerge($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHight,100);

        if (isset($circle)&&is_resource($circle)) $this->destroyImage($circle);       
        if (isset($circle_new)&&is_resource($circle_new)) $this->destroyImage($circle_new); 
    }

    protected function CopyText($content,$dst_x,$dst_y,$font,$rgba,$max_w=0,$font_family=''){
        if(empty($content)) return true;
        $font_family = !empty($font_family)?$this->path.$font_family:$this->font_family;

        $color    = $this->createColorText($this->im,$rgba);
        mb_internal_encoding("UTF-8"); // 设置编码

        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $contents = "";
        $letter  = [];

        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i=0;$i<mb_strlen($content);$i++) {
            $letter[] = mb_substr($content, $i, 1);
        }

        foreach ($letter as $l) {
            $teststr = $contents." ".$l;
            $fontBox = imagettfbbox($font, 0, $font_family, $teststr);
            // $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            $max_ws = $this->im_w;
            if (isset($max_w)&&!empty($max_w)) {
                $max_ws = $max_w;
            }
        
            if (($fontBox[2] > $max_ws) && ($contents !== "")) {
                $contents .= "\n";
            }
            $contents .= $l;
        }

        if ($dst_x=='center') {
            $dst_x = ceil(($this->im_w - $fontBox[2]) / 2);
        }

        imagettftext ($this->im, $font, 0, $dst_x, $dst_y+$font, $color, $font_family, $contents);
    }

    /**
     * [CopyQr description]
     * @Author lang
     * @Date   2020-10-14T14:40:51+0800
     * @param  [type]                   $text   [description]
     * @param  [type]                   $size   [description]
     * @param  [type]                   $margin [description]
     * @param  [type]                   $dst_x  [description]
     * @param  [type]                   $dst_y  [description]
     * @param  [type]                   $src_x  [description]
     * @param  [type]                   $src_y  [description]
     */
    protected function CopyQr($text,$size,$margin,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h){
        
        $result = \QRcode::re_png($text,$size,$margin);
        if ($src_w > 0) {

            $bgWidth = $src_w;
            $Width   = imagesx($result);
        }else{

            $bgWidth = imagesx($result);

        }

        if ($src_h > 0) {

            $bgHight = $src_h;
            $Hight   = imagesy($result);
        }else{

            $bgHight = imagesy($result);

        }
        # 处理目标 x 轴
        if ($dst_x === 'center') {

            $dst_x = ceil(($this->im_w - $bgWidth) / 2);

        }elseif (is_numeric($dst_x)&&$dst_x<0) {

            $dst_x = ceil($this->im_w+$dst_x);

        }elseif (strpos($dst_x, "%")!==false) {

            if (substr($dst_x, 0,strpos($dst_x, "%"))<0) {

                $dst_x = ceil($this->im_w+($this->im_w * substr($dst_x, 0,strpos($dst_x, "%"))/100));

            }else{

                $dst_x = ceil($this->im_w * substr($dst_x, 0,strpos($dst_x, "%"))/100);

            }
            

        }

        # 处理目标 y 轴
        if ( $dst_y === 'center') {

            $dst_y = ceil(($this->im_h - $bgHight) / 2);
        }elseif (is_numeric($dst_y)&&$dst_y<0) {
            t;

            $dst_y = ceil($this->im_h+$dst_y);

        }elseif (strpos($dst_y, "%")!==false) {
            
            if (substr($dst_y, 0,strpos($dst_y, "%"))<0) {

                $dst_y = ceil($this->im_h+(($this->im_h * substr($dst_y, 0,strpos($dst_y, "%")))/100));

            }else{
                $dst_y = ceil($this->im_h * substr($dst_y, 0,strpos($dst_y, "%"))/100);
            }
            
        }

        # 自定义宽高的时候
        if (!empty($src_w) && !empty($src_h)) {
            $circle_new       = $this->createIm($bgWidth, $bgHight,[255, 255, 255, 127],$alpha=true);
            // $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
            // imagecolortransparent($circle_new,$circle_new_white);
            // imagefill($circle_new, 0, 0, $circle_new_white);
            $w_circle_new = $bgWidth;
            $h_circle_new = $bgHight;
            # 按比例缩放
            imagecopyresized($circle_new, $result,0, 0,0, 0,$w_circle_new, $h_circle_new, $Width, $Hight);
            $result = $circle_new;
        }

         //整合海报
        imagecopy($this->im, $result, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHight);
    }

    /**
     * [creatQr description]
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
    protected function creatQr($text,$outfile,$level,$size,$margin,$saveandprint){
        if ($outfile) {
            $outfile = $this->path.$outfile;
        }
        return \QRcode::png($text,$outfile,$level,$size,$margin,$saveandprint);
    }

    /**
     * 释放资源
     * @Author lang
     * @Date   2020-08-14T14:29:46+0800
     * @param  Resource
     * @return [type]
     */
    protected function destroyImage($Resource){

        imagedestroy($Resource);
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || imagedestroy($this->im);
    }
}