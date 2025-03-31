<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:10
 * @fileName: TextGraphicsEngine.php
 */

namespace Kkokk\Poster\Image\Graphics;

class TextGraphicsEngine
{
    protected $canvas = null;

    protected $content;
    /** @var string 设置字体 */
    protected $font = POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'simkai.ttf';
    /** @var string 字体系列 例如 Microsoft YaHei */
    protected $fontFamily = '';
    /** @var int 字体大小 */
    protected $fontSize = 16;
    /** @var int[] 字体颜色 */
    protected $fontColor = [52, 52, 52];
    /** @var int 字体间距 */
    protected $fontSpace = 0;
    /** @var int 字体粗细 */
    protected $fontWeight = 1;
    /** @var int 行高 */
    protected $lineHeight = 21;
    /** @var string 文本对齐方式 left center right */
    protected $textAlign = 'left';
    /** @var int 字体旋转角度 */
    protected $fontAngle = 0;
    /** @var int 字体最大换行宽度 */
    protected $fontMaxWidth = 0;

    public function config($configs = [])
    {
        !empty($configs['font_family']) && $this->setFontFamily($configs['font_family']);
        !empty($configs['font_size']) && $this->setFontSize($configs['font_size']);
        !empty($configs['font_rgba']) && $this->setFontColor($configs['font_rgba']);
        !empty($configs['font_color']) && $this->setFontColor($configs['font_color']);
        !empty($configs['font_space']) && $this->setFontSpace($configs['font_space']);
        !empty($configs['font_weight']) && $this->setFontWeight($configs['font_weight']);
        !empty($configs['font_angle']) && $this->setFontAngle($configs['font_angle']);
        !empty($configs['font_max_w']) && $this->setMaxWidth($configs['font_max_w']);

        if (!empty($configs['font'])) {
            $this->setFont($configs['font']);
        }
        return $this;
    }

    protected function getCanvas()
    {
        return $this->canvas;
    }

    protected function setCanvas($canvas)
    {
        $this->canvas = $canvas;
        return $this;
    }

    public function resolveFontSize($fontSize = null)
    {
        return $this->fontSize;
    }

    public function setText($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setFont($font)
    {
        $this->font = get_real_path($font);
        return $this;
    }

    public function setFontFamily($fontFamily)
    {
        $this->fontFamily = $fontFamily;
        return $this;
    }

    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
        $this->lineHeight = round($this->resolveFontSize($fontSize) * 1.5);
        return $this;
    }

    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
        return $this;
    }

    public function setFontSpace($fontSpace)
    {
        $this->fontSpace = $fontSpace;
        return $this;
    }

    public function setFontWeight($fontWeight)
    {
        $this->fontWeight = $fontWeight;
        return $this;
    }

    public function setFontAngle($fontAngle)
    {
        $this->fontAngle = $fontAngle;
        return $this;
    }


    public function setLineHeight($lineHeight)
    {
        $this->lineHeight = $lineHeight;
        return $this;
    }

    public function setFontAlign($textAlign)
    {
        $this->textAlign = $textAlign;
        return $this;
    }

    public function setMaxWidth($fontMaxWidth)
    {
        $this->fontMaxWidth = $fontMaxWidth;
        return $this;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function getFontColor()
    {
        return $this->fontColor;
    }

    public function getFontSpace()
    {
        return $this->fontSpace;
    }

    public function getFontWeight()
    {
        return $this->fontWeight;
    }

    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    public function getFontAlign()
    {
        return $this->textAlign;
    }

    public function getFontAngle()
    {
        return $this->fontAngle;
    }

    public function getMaxWidth()
    {
        return $this->fontMaxWidth;
    }

    public function getText()
    {
        return $this->content;
    }
}