<?php

namespace App\Helpers;

class TextHelper
{
    public static function linkify(string $text): string
    {
        // URLを正規表現で検出
        $pattern = '/(https?:\/\/[^\s]+)/i';
        $replacement = '<a href="$1" class="text-blue-600 underline" target="_blank" rel="noopener noreferrer">$1</a>';

        // 改行も <br> に変換
        $text = preg_replace($pattern, $replacement, e($text));
        return nl2br($text);
    }
}
