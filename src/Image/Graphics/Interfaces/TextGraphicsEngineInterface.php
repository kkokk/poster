<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:30
 * @fileName: TextGraphicsEngineInterface.php
 */

namespace Kkokk\Poster\Image\Graphics\Interfaces;

interface TextGraphicsEngineInterface
{
    public function setFont($font);

    public function setFontFamily($fontFamily);

    public function setFontSize($fontSize);

    public function setFontColor($fontColor);

    public function setFontSpace($fontSpace);

    public function setFontWeight($fontWeight);

    public function setLineHeight($lineHeight);

    public function setFontAlign($fontAlign);

    public function setFontAngle($fontAngle);

    public function setMaxWidth($fontMaxWidth);

    public function setText($content);

    public function getFont();

    public function getFontFamily();

    public function getFontSize();

    public function getFontColor();

    public function getFontSpace();

    public function getFontWeight();

    public function getLineHeight();

    public function getFontAlign();

    public function getFontAngle();

    public function getMaxWidth();

    public function getText();
}