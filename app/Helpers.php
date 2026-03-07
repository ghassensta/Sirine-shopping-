<?php

if (!function_exists('clean_html_for_display')) {
    function clean_html_for_display($html) {
        // Remove contenteditable attributes
        $html = preg_replace('/\s*contenteditable\s*=\s*["\'][^"\']*["\']/', '', $html);
        // Remove data attributes from editors
        $html = preg_replace('/\s*data-[^=]*\s*=\s*["\'][^"\']*["\']/', '', $html);
        // Remove ql- classes
        $html = preg_replace('/\s*class\s*=\s*["\'][^"\']*ql-[^"\']*["\']/', '', $html);
        return $html;
    }
}
