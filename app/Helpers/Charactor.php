<?php

namespace App\Helpers;

use GoogleTranslate;

class Charactor {

    public static function randomString($length = 10){
        $arr    = array_merge(range(0,9),range('A','Z'));
        $str    = implode('', $arr);
        $str    = str_shuffle($str);
        $result = mb_substr($str, 0, $length);
        return $result;
    }

    public static function convertStrToUrl($str=null, $word='-') {
        $result = '';
        if ($str != null) {
            // Chuyển đổi ký tự tiếng Việt có dấu sang không dấu
            $str = preg_replace("/(&amp;)/", '', $str);
            $str = str_replace(
                ['à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ'],
                'a',
                $str
            );
            $str = str_replace(
                ['è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ'],
                'e',
                $str
            );
            $str = str_replace(
                ['ì', 'í', 'ị', 'ỉ', 'ĩ'],
                'i',
                $str
            );
            $str = str_replace(
                ['ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ'],
                'o',
                $str
            );
            $str = str_replace(
                ['ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ'],
                'u',
                $str
            );
            $str = str_replace(
                ['ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ'],
                'y',
                $str
            );
            $str = str_replace('đ', 'd', $str);
            $str = str_replace(
                ['À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ'],
                'A',
                $str
            );
            $str = str_replace(
                ['È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ'],
                'E',
                $str
            );
            $str = str_replace(
                ['Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ'],
                'I',
                $str
            );
            $str = str_replace(
                ['Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ'],
                'O',
                $str
            );
            $str = str_replace(
                ['Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ'],
                'U',
                $str
            );
            $str = str_replace(
                ['Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ'],
                'Y',
                $str
            );
            $str = str_replace('Đ', 'D', $str);
    
            // Loại bỏ các ký tự không mong muốn và thay thế dấu cách bằng dấu gạch ngang
            $str = strtolower($str);
            // $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
            $str = preg_replace('/[\s-]+/', ' ', $str);
            $str = preg_replace('/[\s_]/', $word, $str);
    
            $result = trim($str, $word);
        }
        return $result;
    }

    public static function convertStringSearch($string){
        $output     = null;
        if(!empty($string)){
            $output = explode(' ', $string);
            $output = implode('%', $output);
        }
        return $output;
    }

    public static function translateViToEn($strVi){
        $response       = null;
        if(!empty($strVi)){
            $ggTrans    = GoogleTranslate::translate($strVi);
            $response   = $ggTrans['translated_text'] ?? null;
        }
        return $response;
    }

    public static function splitString($html, $limitWord = 2000) {
        // Tạo mảng để lưu trữ các phần của chuỗi
        $parts = [];

        // Tạo một biến tạm để lưu trữ một phần của chuỗi
        $currentPart = '';

        // Phân tách chuỗi thành các câu
        $sentences = preg_split('/\n/i', $html);
        $newSentences = [];
        for($i=0;$i<count($sentences);++$i){
            if(!empty(trim($sentences[$i]))){
                $newSentences[] = trim($sentences[$i]);
            }
        }
        // Lặp qua từng câu
        foreach ($newSentences as $sentence) {
            // Nếu độ dài của phần hiện tại cộng với độ dài của câu vượt quá 2000 ký tự
            if (strlen($currentPart) + strlen($sentence) > $limitWord && strlen($sentence) > 10) { /* lớn hơn 10 để tránh tách những thẻ <html> */
                // Thêm phần hiện tại vào mảng
                $parts[] = $currentPart;
                // Reset phần hiện tại để bắt đầu một phần mới
                $currentPart = '';
            }
            // Thêm câu vào phần hiện tại
            $currentPart .= $sentence;
            // Nếu câu không phải là câu cuối cùng, thêm dấu xuống dòng vào phần hiện tại
            if ($sentence !== end($newSentences)) {
                $currentPart .= "\n";
            }
        }

        // Thêm phần hiện tại cuối cùng vào mảng
        $parts[] = $currentPart;

        return $parts;
    }

    public static function generateChatgptDataAndEvent($itemSeo, $prompt, $language, $key, $idContent = 0) {
        $dataChatgpt = null;
        $eventChatgpt = null;
        $dataChatgpt = 'data-id=' . $itemSeo->id . ' data-language=' . $language . ' data-id_prompt=' . $prompt->id.' data-type='.$prompt->type.' data-id_content='.$idContent;
        $eventChatgpt = "chatGpt($('#".$key."'), " . $itemSeo->id . ", '" . $language . "', " . $prompt->id . ", ".$idContent.")";
        return compact('dataChatgpt', 'eventChatgpt');
    }

}