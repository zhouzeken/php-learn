<?php
namespace app\common\controller;
use PhpOffice\PhpWord\IOFactory;
class PhpWord
{
    /**
     * 导入
     */
    public function import($file){
        $phpWord = IOFactory::load($file);
        $html = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $ele1) {
                $paragraphStyle = $ele1->getParagraphStyle();
                if ($paragraphStyle) {
                    $html .= '<p style="text-align:'. $paragraphStyle->getAlignment() .';text-indent:20px;">';
                } else {
                    $html .= '<p>';
                }
                if ($ele1 instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($ele1->getElements() as $ele2) {
                        if ($ele2 instanceof \PhpOffice\PhpWord\Element\Text) {
                            /* start 样式*/
                            /*
                            $style = $ele2->getFontStyle();
                            $fontFamily = mb_convert_encoding($style->getName(), 'GBK', 'UTF-8');
                            $fontSize = $style->getSize();
                            $isBold = $style->isBold();
                            $fontColor = $style->getColor();
                            $styleString = '';
                            $fontFamily && $styleString .= "font-family:{$fontFamily};";
                            $fontSize && $styleString .= "font-size:{$fontSize}px;";
                            $isBold && $styleString .= "font-weight:bold;";
                            $fontColor && $styleString .= "color:#{$fontColor};";
                            $html .= sprintf('<span style="%s">%s</span>',
                                $styleString,
                                mb_convert_encoding($ele2->getText(), 'GBK', 'UTF-8')
                            );
                            */
                            /* end 样式*/

                            //纯文本
                            $html .= mb_convert_encoding($ele2->getText(), 'GBK', 'UTF-8');
                            //dump($ele2->getText());
                            //echo '***';
                        } elseif ($ele2 instanceof \PhpOffice\PhpWord\Element\Image) {
                            $fileName = md5($ele2->getSource()).'.'.$ele2->getImageExtension();
                            $imageSrc = './public/uploads/' . $fileName;
                            $imageData = $ele2->getImageStringData(true);
                            // $imageData = 'data:' . $ele2->getImageType() . ';base64,' . $imageData;
                            file_put_contents($imageSrc, base64_decode($imageData));
                            $imagesUrl = '/api/public/uploads/'.$fileName;
                            $html .= '<img src="'. $imagesUrl .'">';
                        }
                    }
                }
                $html .= '</p>';
            }
        }

        return mb_convert_encoding($html, 'UTF-8', 'GBK');
    }
}