<?php
require_once 'vendor/autoload.php';

/*

名称  html转换为pdf图片

功能  将html页面转换为pdf图片(部分css样式无法识别)

参数数量 2个

1.必须 html代码 可以用file_get_contenth获取

2.必须 生成pdf存放位置路径

3.非必须 pdf宽

4.非必须 pdf高

返回值 图片名称

实例  code($html,'img/1.pdf');

 * */

function html2pdf($html, $PATH, $w=414 ,$h=736){

    //设置中文字体(很重要 它会影响到第二步中 图片生成)

    $mpdf=new mPDF('utf-8');

    $mpdf->autoScriptToLang = true;

    $mpdf->autoLangToFont = true;

    //设置pdf的尺寸

    $mpdf->WriteHTML('');

    //设置pdf显示方式

    $mpdf->SetDisplayMode('fullpage');

   //删除pdf第一页(由于设置pdf尺寸导致多出了一页)

    $mpdf->DeletePages(1,1);

    $mpdf->WriteHTML($html);
    $pdf_name = md5(time()).'.pdf';
    $mpdf->Output($PATH.$pdf_name);

    return $pdf_name;

}




function pdf2png($PDF, $PNG, $w=50, $h=50){

    if(!extension_loaded('imagick')){

        return false;

    }

    if(!file_exists($PDF)){

        return false;

    }



    $im = new Imagick();



    $im->setResolution($w,$h); //设置分辨率

    $im->setCompressionQuality(15);//设置图片压缩的质量



    $im->readImage($PDF);

    $im -> resetIterator();

    $imgs = $im->appendImages(true);

    $imgs->setImageFormat( "png" );

    $img_name = $PNG;

    $imgs->writeImage($img_name);

    $imgs->clear();

    $imgs->destroy();

    $im->clear();

    $im->destroy();



    return $img_name;

}