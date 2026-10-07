<?php

/**
 * microfyPHP
 * microfy_echo_aliases.php
 * v0.1.8 
 * Author: SirCode
 */


if (! function_exists('e_cList')) {
    function e_cList(array $items, bool $reset = false): void
    {
        echo Microfy::cList($items, $reset);
    }
}

if (! function_exists('e_hsc')) {
    function e_hsc(string $str): void
    {
        echo Microfy::hsc($str);
    }
}

if (! function_exists('e_sendJson')) {
    function e_sendJson($data): void
    {
        echo Microfy::sendJson($data);
        exit;
    }
}

if (! function_exists('e_p')) {
    function e_p(string $text = '', string $class = ''): void
    {
        echo Microfy::p($text, $class);
    }
}

if (! function_exists('e_span')) {
    function e_span(string $text = '', string $class = ''): void
    {
        echo Microfy::span($text, $class);
    }
}

if (! function_exists('e_section')) {
    function e_section(string $text = '', string $class = ''): void
    {
        echo Microfy::section($text, $class);
    }
}

/**
 * ──────────────────────────────────────────────────────────────────────────────
 *  CODE
 * ──────────────────────────────────────────────────────────────────────────────
 */

if (! function_exists('e_code')) {
    function e_code(string $content, string $lang = ''): void
    {
        echo Microfy::code($content, $lang);
    }
}

if (! function_exists('e_codejs')) {
    function e_codejs(string $text): void
    {
        echo Microfy::codejs($text);
    }
}

if (! function_exists('e_codephp')) {
    function e_codephp(string $text): void
    {
        echo Microfy::codephp($text);
    }
}

if (! function_exists('e_codejson')) {
    function e_codejson(string $text): void
    {
        echo Microfy::codejson($text);
    }
}

if (! function_exists('e_codehtml')) {
    function e_codehtml(string $text): void
    {
        echo Microfy::codehtml($text);
    }
}

if (! function_exists('e_codesql')) {
    function e_codesql(string $text): void
    {
        echo Microfy::codesql($text);
    }
}

if (! function_exists('e_codebash')) {
    function e_codebash(string $text): void
    {
        echo Microfy::codebash($text);
    }
}

if (! function_exists('e_codec')) {
    function e_codec(string $text): void
    {
        echo Microfy::codec($text);
    }
}

/**
 * ──────────────────────────────────────────────────────────────────────────────
 *  LISTS
 * ──────────────────────────────────────────────────────────────────────────────
 */

if (! function_exists('e_ul')) {
    function e_ul(array $items, string $class = ''): void
    {
        echo Microfy::ul($items, $class);
    }
}

if (! function_exists('e_ulOpen')) {
    function e_ulOpen(string $class = ''): void
    {
        echo Microfy::ulOpen($class);
    }
}

if (! function_exists('e_ulClose')) {
    function e_ulClose(): void
    {
        echo Microfy::ulClose();
    }
}

if (! function_exists('e_li')) {
    function e_li(string $text): void
    {
        echo Microfy::li($text);
    }
}

/**
 * ──────────────────────────────────────────────────────────────────────────────
 *  LINE BREAKS & HR
 * ──────────────────────────────────────────────────────────────────────────────
 */

if (! function_exists('e_br')) {
    function e_br(...$args): void
    {
        echo Microfy::br(...$args);
    }
}

if (! function_exists('e_bra')) {
    function e_bra(...$args): void
    {
        echo Microfy::bra(...$args);
    }
}

if (! function_exists('e_hr')) {
    function e_hr(...$args): void
    {
        echo Microfy::hr(...$args);
    }
}

if (! function_exists('e_hra')) {
    function e_hra(...$args): void
    {
        echo Microfy::hra(...$args);
    }
}

/**
 * ──────────────────────────────────────────────────────────────────────────────
 *    Counter
 * ──────────────────────────────────────────────────────────────────────────────
 */

if (! function_exists('e_c')) {
    function e_c(string $text = ''): void
    {
        echo Microfy::c($text);
    }
}

if (! function_exists('e_cStr')) {
    function e_cStr(string $text = ''): void
    {
        echo Microfy::cStr($text);
    }
}

/**
 * ──────────────────────────────────────────────────────────────────────────────
 *    HTMLTABLES
 * ──────────────────────────────────────────────────────────────────────────────
 */

if (! function_exists('e_htmlTableSafe')) {
    function e_htmlTableSafe(array $rows, string $cssClass = '', string $id = ''): void
    {
        echo Microfy::htmlTableSafe($rows, $cssClass, $id);
    }
}

if (! function_exists('e_htmlTable')) {
    function e_htmlTable(
        array $rows,
        array $allowRawCols = [],
        string $cssClass = '',
        string $id = ''
    ): void {
        echo Microfy::htmlTable($rows, $allowRawCols, $cssClass, $id);
    }
}

/**
 * ──────────────────────────────────────────────────────────────────────────────
 *   STYLE / HTML OUTPUT
 * ──────────────────────────────────────────────────────────────────────────────
 */

if (! function_exists('e_h')) {
    function e_hx(int $level, string $text, string $class = ''): void
    {
        echo Microfy::hx($level, $text, $class);
    }
}

if (! function_exists('e_b')) {
    function e_b(string $text = '', string $class = ''): void
    {
        echo Microfy::b($text, $class);
    }
}

if (! function_exists('e_i')) {
    function e_i(string $text = '', string $class = ''): void
    {
        echo Microfy::i($text, $class);
    }
}

if (! function_exists('e_bi')) {
    function e_bi(string $text = '', string $class = ''): void
    {
        echo Microfy::bi($text, $class);
    }
}

if (! function_exists('e_small')) {
    function e_small(string $text = '', string $class = ''): void
    {
        echo Microfy::small($text, $class);
    }
}

if (! function_exists('e_mark')) {
    function e_mark(string $text = '', string $class = ''): void
    {
        echo Microfy::mark($text, $class);
    }
}



    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *  HTML HELPERS 2
     * ──────────────────────────────────────────────────────────────────────────────
     */



if (!function_exists('e_html_tag')) {
    function e_html_tag(string $tag, $content = '', array $attrs = [], bool $selfClose = false): void {
        echo Microfy::html_tag($tag, $content, $attrs, $selfClose);
    }
}

if (!function_exists('e_html_html')) {
    function e_html_html($content = '', array $attrs = []): void {
        echo Microfy::html_html($content, $attrs);
    }
}

if (!function_exists('e_html_head')) {
    function e_html_head($content = '', array $attrs = []): void {
        echo Microfy::html_head($content, $attrs);
    }
}

if (!function_exists('e_html_body')) {
    function e_html_body($content = '', array $attrs = []): void {
        echo Microfy::html_body($content, $attrs);
    }
}

if (!function_exists('e_html_header')) {
    function e_html_header(...$args): void {
        echo Microfy::html_header(...$args);
    }
}
if (!function_exists('e_html_footer')) {
    function e_html_footer(...$args): void {
        echo Microfy::html_footer(...$args);
    }
}
if (!function_exists('e_html_section')) {
    function e_html_section(...$args): void {
        echo Microfy::html_section(...$args);
    }
}
if (!function_exists('e_html_article')) {
    function e_html_article(...$args): void {
        echo Microfy::html_article(...$args);
    }
}
if (!function_exists('e_html_nav')) {
    function e_html_nav(...$args): void {
        echo Microfy::html_nav(...$args);
    }
}
if (!function_exists('e_html_aside')) {
    function e_html_aside(...$args): void {
        echo Microfy::html_aside(...$args);
    }
}
if (!function_exists('e_html_div')) {
    function e_html_div(...$args): void {
        echo Microfy::html_div(...$args);
    }
}
if (!function_exists('e_html_span')) {
    function e_html_span(...$args): void {
        echo Microfy::html_span(...$args);
    }
}
if (!function_exists('e_html_h1')) {
    function e_html_h1(...$args): void {
        echo Microfy::html_h1(...$args);
    }
}
if (!function_exists('e_html_h2')) {
    function e_html_h2(...$args): void {
        echo Microfy::html_h2(...$args);
    }
}
if (!function_exists('e_html_h3')) {
    function e_html_h3(...$args): void {
        echo Microfy::html_h3(...$args);
    }
}
if (!function_exists('e_html_h4')) {
    function e_html_h4(...$args): void {
        echo Microfy::html_h4(...$args);
    }
}
if (!function_exists('e_html_h5')) {
    function e_html_h5(...$args): void {
        echo Microfy::html_h5(...$args);
    }
}
if (!function_exists('e_html_h6')) {
    function e_html_h6(...$args): void {
        echo Microfy::html_h6(...$args);
    }
}
if (!function_exists('e_html_p')) {
    function e_html_p(...$args): void {
        echo Microfy::html_p(...$args);
    }
}
if (!function_exists('e_html_blockquote')) {
    function e_html_blockquote(...$args): void {
        echo Microfy::html_blockquote(...$args);
    }
}
if (!function_exists('e_html_pre')) {
    function e_html_pre(...$args): void {
        echo Microfy::html_pre(...$args);
    }
}
if (!function_exists('e_html_code')) {
    function e_html_code(...$args): void {
        echo Microfy::html_code(...$args);
    }
}
if (!function_exists('e_html_ul')) {
    function e_html_ul(...$args): void {
        echo Microfy::html_ul(...$args);
    }
}
if (!function_exists('e_html_ol')) {
    function e_html_ol(...$args): void {
        echo Microfy::html_ol(...$args);
    }
}
if (!function_exists('e_html_li')) {
    function e_html_li(...$args): void {
        echo Microfy::html_li(...$args);
    }
}
if (!function_exists('e_html_dl')) {
    function e_html_dl(...$args): void {
        echo Microfy::html_dl(...$args);
    }
}
if (!function_exists('e_html_table')) {
    function e_html_table(...$args): void {
        echo Microfy::html_table(...$args);
    }
}
if (!function_exists('e_html_thead')) {
    function e_html_thead(...$args): void {
        echo Microfy::html_thead(...$args);
    }
}
if (!function_exists('e_html_tbody')) {
    function e_html_tbody(...$args): void {
        echo Microfy::html_tbody(...$args);
    }
}
if (!function_exists('e_html_tr')) {
    function e_html_tr(...$args): void {
        echo Microfy::html_tr(...$args);
    }
}
if (!function_exists('e_html_th')) {
    function e_html_th(...$args): void {
        echo Microfy::html_th(...$args);
    }
}
if (!function_exists('e_html_td')) {
    function e_html_td(...$args): void {
        echo Microfy::html_td(...$args);
    }
}
if (!function_exists('e_html_form')) {
    function e_html_form(...$args): void {
        echo Microfy::html_form(...$args);
    }
}
if (!function_exists('e_html_label')) {
    function e_html_label(...$args): void {
        echo Microfy::html_label(...$args);
    }
}
if (!function_exists('e_html_input')) {
    function e_html_input(...$args): void {
        echo Microfy::html_input(...$args);
    }
}
if (!function_exists('e_html_textarea')) {
    function e_html_textarea(...$args): void {
        echo Microfy::html_textarea(...$args);
    }
}
if (!function_exists('e_html_select')) {
    function e_html_select(...$args): void {
        echo Microfy::html_select(...$args);
    }
}
if (!function_exists('e_html_button')) {
    function e_html_button(...$args): void {
        echo Microfy::html_button(...$args);
    }
}
if (!function_exists('e_html_br')) {
    function e_html_br(...$args): void {
        echo Microfy::html_br(...$args);
    }
}
if (!function_exists('e_html_hr')) {
    function e_html_hr(...$args): void {
        echo Microfy::html_hr(...$args);
    }
}
if (!function_exists('e_html_img')) {
    function e_html_img(...$args): void {
        echo Microfy::html_img(...$args);
    }
}
if (!function_exists('e_html_meta')) {
    function e_html_meta(...$args): void {
        echo Microfy::html_meta(...$args);
    }
}
if (!function_exists('e_html_link')) {
    function e_html_link(...$args): void {
        echo Microfy::html_link(...$args);
    }
}
if (!function_exists('e_html_script')) {
    function e_html_script(...$args): void {
        echo Microfy::html_script(...$args);
    }
}
if (!function_exists('e_html_style')) {
    function e_html_style(...$args): void {
        echo Microfy::html_style(...$args);
    }
}

