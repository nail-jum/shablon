<?php

/**
 * Класс для работы с изображениями
 * Методы:
 * watermarkSimple  - Добавление водяных знаков на изображение (только на латинице!)
 * watermark        - Добавление водяных знаков на изображение
 */

class MyFuncImage
{

    /**
     * Функция для вывода отладочной информации с прерыванием программы
     */
    static function debugInfo($message)
    {
        $result = [
            'status' => 'debug',
            'data' => $message,
            'info' => ['Отладочная информация'],
        ];
        echo json_encode($result);
        exit();
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Получение информации об изображении, полученном через веб-форму
     * возвращает массив $_FILES[$inputName] или false
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function getImageInfoFromPost($inputName) : array | bool
    {
        // Типы файлов изображений
        $file_mimes = [
            'image/gif',            // GIF (RFC 2045 и RFC 2046)
            'image/jpeg',           // JPEG (RFC 2045 и RFC 2046)
            'image/pjpeg',          // JPEG[9]
            'image/png',            // Portable Network Graphics[10](RFC 2083)
            'image/svg+xml',        // SVG[11]
            'image/tiff',           // TIFF (RFC 3302)
            'image/vnd.microsoft.icon', // ICO[12]
            'image/vnd.wap.wbmp',   // WBMP
            'image/webp',           // WebP
        ];
        if (
            !isset($_FILES[$inputName]['name']) ||
            !in_array($_FILES[$inputName]['type'], $file_mimes)
        ) {
            return false;
        }
        return $_FILES[$inputName];
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Получение содержимого изображения, полученного через веб-форму
     * возвращает массив [mime тип, содержимое изображения]
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function getImageContentFromPost($inputName) : array | bool
    {
        $imageInfo = MyFuncImage::getImageInfoFromPost($inputName);
        if ($imageInfo == false) {
            return false;
        }
        $image = [];
        $image['mime_type'] = mime_content_type($imageInfo['tmp_name']);
        $image['content']   = file_get_contents($imageInfo['tmp_name']);
        if ($image['content'] == false) {
            // ошибка чтения файла
            return false;
        }
        return $image;
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Чтение файла изображения, полученного через веб-форму
     * возвращает объект изображения GdImage
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function readImageFromPost($inputName) : array | bool
    {
        $imageInfo = MyFuncImage::getImageInfoFromPost($inputName);
        if ($imageInfo == false) {
            return false;
        }
        //MyFuncImage::debugInfo(['number' => 2, 'files' => $_FILES, 'inputName' => $inputName]);
        $imageContent = file_get_contents($imageInfo['tmp_name']);
        if ($imageContent == false) {
            // ошибка чтения файла
            return false;
        }
        $mimeType = mime_content_type($imageInfo['tmp_name']);
        $gdImage  = imagecreatefromstring($imageContent);       // создание объекта GdImage
        return [
            'mime_type' => $mimeType,
            'GdImage' => $gdImage,
        ];
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Добавление текстовых водяных знаков на изображение (только на латинице!)
     * Кириллица не поддерживается, ограничение размера шрифта
     * размер 5 примерно соответствует шрифту "Arial 12pt"
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function watermarkSimple($img, $text)
    {
        // Внимание: y-координата - это позиция верхнего левого угла блока текста
        $height = imagesy($img);    // высота исходного изображения
        $alpha = 100;               // от 0 (непрозрачный) до 127 (полностью прозрачный)
        $size = 5;                  // размер встроенного шрифта (от 1 до 5)
        $c = imagecolorallocatealpha($img, 128, 128, 128, $alpha);

        //записываем строку на изображение
        imagestring($img, $size, 10, 10, $text, $c);            // левый верхний угол
        imagestring($img, $size, 10, $height - 20, $text, $c);  // левый нижний угол
        return $img;
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Добавление текстовых водяных знаков на изображение
     * поддерживается кириллица, любой размер шрифта
     * imagettftext() требует примерно в 20 раз больше ресурсов по сравнению с imagestring()
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function watermark($img, $text)
    {
        // Внимание: y-координата - это позиция базовой линии шрифта, может не совпадать с низшей точкой символа
        $height = imagesy($img);    // высота исходного изображения
        $alpha = 100;               // от 0 (непрозрачный) до 127 (полностью прозрачный)
        $angle = 0;                 // угол поворота текста
        $size = 14;                 // размер шрифта в типографских пунктах
        $c = imagecolorallocatealpha($img, 128, 128, 128, $alpha);
        $font = $_SERVER['DOCUMENT_ROOT'] . '/nail-custom/ArialCyrBold.ttf';

        //записываем строку на изображение
        imagettftext($img, $size, $angle, 10, $height - 10, $c, $font, $text);  // левый нижний угол
        return $img;
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Наложение одного изображения на другое с масштабированием и эффектом прозрачности
     * $scale - масштаб второго изображения относительно первого
     * $transparency - прозрачность от 0 (прозрачный) до 100 (непрозрачный)
     * если $transparency == false, то прозрачность не применяется
     * $x0, $y0 - отступы второго изображения от верхнего левого угла
     *     если отрицательные - то от нижнего правого угла
     *     если не указаны (false), то по центру
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function addImage($img1, $img2, $scale = false, $transparency = false, $x0 = false, $y0 = false)
    {
        // Размеры изображений
        $width1  = imagesx($img1);
        $height1 = imagesy($img1);
        $width2  = imagesx($img2);
        $height2 = imagesy($img2);

        // допустимые значения масштаба
        if ($scale !== false) {
            $scale = (int)$scale;
            if ($scale <= 0) {
                $scale = false;
            }
            if ($scale > 100) {
                $scale = 100;
            }
        }
        // допустимые значения прозрачности
        if ($transparency !== false) {
            $transparency = (int)$transparency;
            if ($transparency <= 0) {
                $transparency = false;
            }
            if ($transparency > 100) {
                $transparency = 100;
            }
        }

        if ($scale == false) {
            // Если масштаб не указан или равен 0, сохраняем размеры второго изображения
            $newWidth2  = $width2;
            $newHeight2 = $height2;
        } else {
            // Если указан масштаб, вычисляем новые размеры второго изображения
            // Допустимые размеры водяного знака
            $maxWidth  = $width1 * $scale / 100;
            $maxHeight = $height1 * $scale / 100;
            // Коэффициенты сжатия
            $ratioWidth = $width2 / $maxWidth;
            $ratioHeight = $height2 / $maxHeight;
            // Определение новых размеров при максимальном сжатии
            if ($ratioWidth > $ratioHeight) {
                $newWidth2  = $maxWidth;
                $newHeight2 = $height2 / $ratioWidth;
            } else {
                $newWidth2  = $width2 / $ratioHeight;
                $newHeight2 = $maxHeight;
            }
        }
        // Координаты второго изображения относительно первого
        if ($x0 === false ) {
            $x0 = ($width1 - $newWidth2) / 2;       // по центру
        }
        if ($x0 < 0) {
            $x0 = $width1 - $width2 + $x0;          // отступ от правого края
        }
        if ($y0 === false ) {
            $y0 = ($height1 - $newHeight2) / 2;     // по центру
        }
        if ($y0 < 0) {
            $y0 = $height1 - $newHeight2 + $y0;     // отступ от нижнего края
        }

        if ($transparency == false) {
            // Наложение изображения с масштабированием без эффекта прозрачности
            // берет прямоугольный участок из src_image с шириной src_width и высотой src_height
            // на координатах src_x, src_y и помещает его в прямоугольный участок изображения dst_image
            // шириной dst_width и высотой dst_height на координатах dst_x, dst_y.
            imagecopyresampled(
                $img1,          // $dst_image   - основное изображение
                $img2,          // $src_image   - накладываемое изображение
                $x0,            // $dst_x       - координаты начала позиционирования
                $y0,            // $dst_y       - координаты начала позиционирования
                0,              // $src_x       - координаты начала вырезания
                0,              // $src_y       - координаты начала вырезания
                $newWidth2,     // $dst_width   - ширина позиционирования
                $newHeight2,    // $dst_height  - высота позиционирования
                $width2,        // $src_width   - ширина вырезания
                $height2        // $src_height  - высота вырезания
            );
        } else {
            // - - - - - - - - - - - - - - - - - - - - - - -
            // Наложение водяного знака на изображение с эффектом прозрачности
            // Функция imagecopymerge_alpha() из руководства:
            // https://www.php.net/manual/en/function.imagecopymerge.php
            // - - - - - - - - - - - - - - - - - - - - - - -
            // создаем новый водяной знак с новыми размерами
            $newWatermark = imagecreatetruecolor($newWidth2, $newHeight2);
            // копируем участок исходного изображения, который будет под водяным знаком
            imagecopy($newWatermark, $img1, 0, 0, $x0, $y0, $newWidth2, $newHeight2);
            // накладываем исходный водяной знак с масштабированием к новым размерам
            imagecopyresampled($newWatermark, $img2, 0, 0, 0, 0, $newWidth2, $newHeight2, $width2, $height2);
            // накладываем новый водяной знак на исходное изображение с применением прозрачности
            imagecopymerge($img1, $newWatermark, $x0, $y0, 0, 0, $newWidth2, $newHeight2, $transparency);
            // - - - - - - - - - - - - - - - - - - - - - - -
        }

        // Получение содержимого изображения
        ob_start();
        //imagepng($img1);
        imagejpeg($img1);
        $newImageContent = ob_get_clean();

        return $newImageContent;
    }

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * Наложение водяного знака на изображение
     * максимально упрощенная функция для использования на сайте
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     */
    static function addImageSimple()
    {
        function readImage($inputName)
        {
            // Типы файлов изображений
            $file_mimes = [
                'image/gif',            // GIF (RFC 2045 и RFC 2046)
                'image/jpeg',           // JPEG (RFC 2045 и RFC 2046)
                'image/pjpeg',          // JPEG[9]
                'image/png',            // Portable Network Graphics[10](RFC 2083)
                'image/svg+xml',        // SVG[11]
                'image/tiff',           // TIFF (RFC 3302)
                'image/vnd.microsoft.icon', // ICO[12]
                'image/vnd.wap.wbmp',   // WBMP
                'image/webp',           // WebP
            ];
            if (
                !isset($_FILES[$inputName]['name']) ||
                !in_array($_FILES[$inputName]['type'], $file_mimes)
            ) {
                return false;
            }
            // информация о файле
            $imageInfo = $_FILES[$inputName];
            if ($imageInfo == false) {
                return false;
            }
            // содержимое файла
            $imageContent = file_get_contents($imageInfo['tmp_name']);
            if ($imageContent == false) {
                // ошибка чтения файла
                return false;
            }
            // тип файла
            $mimeType = mime_content_type($imageInfo['tmp_name']);
            // создание объекта GdImage
            $gdImage  = imagecreatefromstring($imageContent);
            return [
                'mime_type' => $mimeType,
                'GdImage' => $gdImage,
            ];
        }

        // Получение файлов из запроса
        $imgArray1  = readImage('image-source');
        $imgArray2 = readImage('image-watermark');
        if (($imgArray1 == false) || ($imgArray2 == false)) {
            return false;
        }
        $img1  = $imgArray1['GdImage'];
        $img2  = $imgArray2['GdImage'];

        // Размеры изображений
        $width1  = imagesx($img1);
        $height1 = imagesy($img1);
        $width2  = imagesx($img2);
        $height2 = imagesy($img2);

        // фиксированное значение масштаба
        $scale = 40;
        // фиксированное значение прозрачности
        $transparency =28;
        // новые размеры водяного знака (масштаб по значению ширины)
        $newWidth2  = $width1 * $scale / 100;
        $newHeight2 = $height2 * $newWidth2 / $width2;
        // координаты водяного знака (по центру)
        $x0 = ($width1 - $newWidth2) / 2;
        $y0 = ($height1 - $newHeight2) / 2;

        // - - - - - - - - - - - - - - - - - - - - - - -
        // Наложение водяного знака на изображение с эффектом прозрачности
        // Функция imagecopymerge_alpha() из руководства:
        // https://www.php.net/manual/en/function.imagecopymerge.php
        // - - - - - - - - - - - - - - - - - - - - - - -
        // создаем новый водяной знак с новыми размерами
        $newWatermark = imagecreatetruecolor($newWidth2, $newHeight2);
        // копируем участок исходного изображения, который будет под водяным знаком
        imagecopy($newWatermark, $img1, 0, 0, $x0, $y0, $newWidth2, $newHeight2);
        // накладываем исходный водяной знак с масштабированием к новым размерам
        imagecopyresampled($newWatermark, $img2, 0, 0, 0, 0, $newWidth2, $newHeight2, $width2, $height2);
        // накладываем новый водяной знак на исходное изображение с применением прозрачности
        imagecopymerge($img1, $newWatermark, $x0, $y0, 0, 0, $newWidth2, $newHeight2, $transparency);
        // - - - - - - - - - - - - - - - - - - - - - - -

        // Получение содержимого изображения
        ob_start();
        //imagepng($img1);
        imagejpeg($img1);
        $newImageContent = ob_get_clean();

        return 'data:' . $imgArray1['mime_type'] . ';base64,' . base64_encode($newImageContent);
    }

}
