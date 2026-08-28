<?php

declare(strict_types=1);
/**
 * microfyPHP
 * MicrofyClass.php
 * v0.1.8
 * Author: SirCode
 */
class Microfy
{

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   Arrays
     * ──────────────────────────────────────────────────────────────────────────────
     */

    // Safe array accessor
    public static function val(array $array, $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    // GET/POST/REQUEST
    public static function getVar($key, $default = '')
    {
        return self::val($_GET, $key, $default);
    }
    public static function postVar($key, $default = '')
    {
        return self::val($_POST, $key, $default);
    }
    public static function requestVar($key, $default = '')
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    // Extract specific keys
    public static function extractKeys(array $keys, array $source, string $prefix = ''): array
    {
        $out = [];
        foreach ($keys as $key) {
            $out["{$prefix}{$key}"] = $source[$key] ?? '';
        }
        return $out;
    }

    public static function getVars(array $keys, string $prefix = '')
    {
        return self::extractKeys($keys, $_GET, $prefix);
    }
    public static function postVars(array $keys, string $prefix = '')
    {
        return self::extractKeys($keys, $_POST, $prefix);
    }
    public static function reqVars(array $keys, string $prefix = '')
    {
        return self::extractKeys($keys, $_REQUEST, $prefix);
    }
    public static function getVarsWithPrefix(array $keys): array
    {
        return self::getVars($keys, 'get_');
    }

    // Map & load key=>label or key=>[label,default]
    public static function loadInputs(array $map, array $source): array
    {
        $out = [];
        foreach ($map as $var => $rule) {
            [$key, $default] = is_array($rule) ? $rule + ['', ''] : [$rule, ''];
            $out[$var]       = $source[$key] ?? $default;
        }
        return $out;
    }

    public static function getAll(array $map)
    {
        return self::loadInputs($map, $_GET);
    }
    public static function postAll(array $map)
    {
        return self::loadInputs($map, $_POST);
    }
    public static function reqAll(array $map)
    {
        return self::loadInputs($map, $_REQUEST);
    }

    // Export to $GLOBALS
    public static function injectGlobals(array $source, array $allow, string $prefix = ''): void
    {
        foreach ($allow as $key) {
            $GLOBALS[$prefix . $key] = $source[$key] ?? '';
        }
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   Database
     * ──────────────────────────────────────────────────────────────────────────────
     */

    // --- PDO connection ---
    public static function dbPdo($host, $dbname, $user, $pass, $charset = 'utf8mb4', $driver = 'mysql'): ?PDO
    {
        if ($driver === 'pgsql') {
            $dsn = "pgsql:host=$host;dbname=$dbname";
        } else {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            self::dd("PDO Connection failed: " . $e->getMessage());
            return null;
        }
    }

    // --- DB helpers ---
    public static function dbAll(PDO $pdo, string $sql, array $params = []): array
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // <-- fix here
    }

    public static function dbInsert(PDO $pdo, string $table, array $data): string
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($data), '?');
        $sql = "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $pdo->lastInsertId();
    }

    public static function dbUpdate(PDO $pdo, string $table, array $data, string $where, array $params = []): bool
    {
        $set = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $sql = "UPDATE `$table` SET $set WHERE $where";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array_merge(array_values($data), $params));
    }

    public static function dbDelete(PDO $pdo, string $table, string $where, array $params = []): bool
    {
        $sql = "DELETE FROM `$table` WHERE $where";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }




    public static function dbOne(PDO $pdo, string $sql, array $params = []): mixed
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public static function dbVal(PDO $pdo, string $sql, array $params = []): mixed
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function dbCount(PDO $pdo, string $sql, array $params = []): int
    {
        return (int) self::dbVal($pdo, $sql, $params);
    }

    public static function dbExec(PDO $pdo, string $sql, array $params = []): bool
    {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function dbInsertId(PDO $pdo): string
    {
        return $pdo->lastInsertId();
    }

    public static function dbExists(PDO $pdo, string $table, string $column, $value): bool
    {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false; // invalid table name
        }
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            return false; // invalid column name
        }

        $sql  = "SELECT 1 FROM `$table` WHERE `$column` = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchColumn() !== false;
    }

    public static function dbError(PDOException $e): void
    {
        self::dd("DB Error: " . $e->getMessage());
    }

    // --- MySQLi connection ---
    public static function dbMysqli($host, $user, $pass, $dbname, $port = 3306): mysqli
    {
        $mysqli = new mysqli($host, $user, $pass, $dbname, $port);

        if ($mysqli->connect_error) {
            self::dd("MySQLi Connection failed: " . $mysqli->connect_error);
        }

        return $mysqli;
    }

    /* 
    Usage Examples
    $pdo = Microfy::dbPdo('localhost', 'mydb', 'user', 'pass');
    $rows = Microfy::dbAll($pdo, "SELECT * FROM users");
    Microfy::pp($rows);
    */

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   Debug
     * ──────────────────────────────────────────────────────────────────────────────
     */

    // --- Pretty Print (print_r) ---
    public static function pp($data, $limit = null): void
    {
        echo self::ppr($data, $limit);
    }

    public static function ppd($data, $limit = null): void
    {
        self::pp($data, $limit);
        die();
    }

    public static function mpp(...$args): void
    {
        foreach ($args as $arg) {
            echo self::ppr($arg);
        }
    }

    public static function mppd(...$args): void
    {
        self::mpp(...$args);
        die();
    }

    public static function ppr($data, $limit = null): string
    {
        $output = print_r($data, true);
        if ($limit !== null) {
            $lines  = explode("\n", $output);
            $output = implode("\n", array_slice($lines, 0, $limit));
        }
        return "<pre>$output</pre>";
    }

    public static function pper($data, $limit = null): string
    {
        $output = self::ppr($data, $limit);
        echo $output;
        return $output;
    }

    // --- Var Dump (dumps) ---
    public static function pd($var, $label = null): void
    {
        echo self::pdr($var, $label);
    }

    public static function pdd($var, $label = null): void
    {
        self::pd($var, $label);
        die();
    }

    public static function pdr($var, $label = null): string
    {
        ob_start();
        echo "<pre>";
        if ($label) {
            echo "$label:\n";
        }

        var_dump($var);
        echo "</pre>";
        return ob_get_clean();
    }

    // --- Simple dump shortcuts ---
    public static function d(...$args): void
    {
        foreach ($args as $arg) {
            echo self::pdr($arg);
        }
    }

    public static function dd(...$args): void
    {
        self::d(...$args);
        die();
    }

    // --- Logging ---
    public static function mlog($text, $label = null, $file = 'debug.log'): void
    {
        $entry = ($label ? "$label:\n" : "") . $text . "\n";
        file_put_contents($file, $entry, FILE_APPEND);
    }

    public static function logPr($var, $label = null, $file = 'debug_pr.log'): void
    {
        self::mlog(print_r($var, true), $label, $file);
    }

    public static function logVd($var, $label = null, $file = 'debug_vd.log'): void
    {
        self::mlog(self::pdr($var, $label), null, $file);
    }

    public static function debugSession(): void
    {
        echo "<div style='font-family: monospace; color: black; background: #f8f8f8; border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Session Name:</strong> " . session_name() . "<br>";
        echo "<strong>Session ID:</strong> " . session_id() . "<br>";
        echo "<strong>\$_SESSION:</strong><br>";
        echo "<pre>" . htmlspecialchars(print_r($_SESSION, true)) . "</pre>";
        echo "</div>";
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   ENV
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    // --- FILES ---

    public static function jsonf(string $file, bool $assoc = true)
    {
        if (! file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        return json_decode($content, $assoc);
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   HTML HELPERS
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function a(string $href, ?string $text = null, string $target = '', string $class = ''): string
    {
        if (! preg_match('#^https?://#', $href)) {
            $href = "https://$href";
        }

        $text       = $text ?? $href;
        $targetAttr = $target ? " target=\"$target\"" : '';
        $classAttr  = $class ? " class=\"$class\"" : '';

        return "<a href=\"$href\"$targetAttr$classAttr>$text</a>";
    }

    public static function htmlTableSafe(
        array $rows,
        string $cssClass = '',
        string $id = ''
    ): string {
        if (empty($rows)) {
            return "<p><em>No data.</em></p>";
        }

        if (!isset($rows[0]) || !is_array($rows[0]) || empty($rows[0])) {
            return "<p><em>No columns to display.</em></p>";
        }

        $idAttr = $id !== ''
            ? " id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8', false) . "'"
            : '';

        $classAttr = $cssClass !== ''
            ? " class='" . htmlspecialchars($cssClass, ENT_QUOTES, 'UTF-8', false) . "'"
            : " border='1' cellpadding='6' cellspacing='0'";

        $html = "<table{$idAttr}{$classAttr}>";

        // thead
        $html .= "<thead><tr>";
        foreach (array_keys($rows[0]) as $field) {
            $html .= '<th>' . htmlspecialchars((string) $field, ENT_QUOTES, 'UTF-8', false) . '</th>';
        }
        $html .= "</tr></thead>";

        // tbody
        $html .= "<tbody>";
        foreach ($rows as $row) {
            if (!is_array($row)) continue;
            $html .= "<tr>";
            foreach ($row as $value) {
                $cellStr = is_scalar($value) && !is_bool($value)
                    ? (string) $value
                    : '';
                $html .= '<td>' . htmlspecialchars($cellStr, ENT_QUOTES, 'UTF-8', false) . '</td>';
            }
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";

        return $html;
    }


    /**
     * Renders an HTML table, escaping every cell by default—
     * but allowing raw HTML in any column you whitelist.
     *
     * @param array       $rows         Row-data, as an array of associative arrays
     * @param string[]    $allowRawCols Column-names whose values are already-safe HTML
     * @param string      $cssClass     Optional CSS class
     * @param string      $id           Optional element ID
     * @return string                   HTML string of the table (or a “no data” message)
     */
    public static function htmlTable(
        array $rows,
        array $allowRawCols = [],
        string $id = '',
        string $cssClass = ''
    ): string {
        if (empty($rows)) {
            return "<p><em>No data.</em></p>";
        }

        if (!isset($rows[0]) || !is_array($rows[0]) || empty($rows[0])) {
            return "<p><em>No columns to display.</em></p>";
        }

        // id and class (or fallback border attributes)
        $idAttr = $id !== ''
            ? " id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8', false) . "'"
            : '';

        $classAttr = $cssClass !== ''
            ? " class='" . htmlspecialchars($cssClass, ENT_QUOTES, 'UTF-8', false) . "'"
            : " border='1' cellpadding='6' cellspacing='0'";

        $html = "<table{$idAttr}{$classAttr}>";

        // thead
        $html .= "<thead><tr>";
        foreach (array_keys($rows[0]) as $field) {
            $html .= '<th>' . htmlspecialchars((string) $field, ENT_QUOTES, 'UTF-8', false) . '</th>';
        }
        $html .= "</tr></thead>";

        // tbody
        $html .= "<tbody>";
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $html .= "<tr>";
            foreach ($row as $field => $value) {
                $cellStr = is_scalar($value) && !is_bool($value)
                    ? (string) $value
                    : '';

                if (in_array($field, $allowRawCols, true)) {
                    $html .= "<td>{$cellStr}</td>";
                } else {
                    $html .= '<td>' . htmlspecialchars($cellStr, ENT_QUOTES, 'UTF-8', false) . '</td>';
                }
            }
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";

        return $html;
    }


    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   MISC OUTPUT
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function cList(array $items, bool $reset = false): string
    {
        static $counter = 1;
        if ($reset) {
            $counter = 1;
        }

        $output = '';
        foreach ($items as $item) {
            $output .= $counter++ . '. ' . $item . '<br>';
        }
        return $output;
    }

    public static function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }

    /* 
    Usage Examples
    echo Microfy::now();
    $data = Microfy::jsonf('users.json');
    echo Microfy::a('example.com', 'Example');
    echo Microfy::htmlTable($data);
    Microfy::cList(['apple', 'banana', 'cherry']);

    */

    // --- LOADER / CONST ---

    public static function load(string $file): void
    {
        include_once __DIR__ . "/$file.php";
    }

    public static function def(string $name, $value): void
    {
        if (! defined($name)) {
            define($name, $value);
        }
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   RESPONSE / OUTPUT
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function hsc(string $str): string
    {
        return htmlspecialchars($str);
    }

    public static function sendJson($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function ok(string $msg = 'OK'): void
    {
        self::sendJson(['status' => 'ok', 'msg' => $msg]);
    }

    public static function fail(string $msg = 'Error'): void
    {
        self::sendJson(['status' => 'fail', 'msg' => $msg]);
    }

    public static function jsonArray(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function jsonString(string $msg, bool $ok = true): string
    {
        return self::jsonArray([
            'status' => $ok ? 'ok' : 'fail',
            'msg'    => $msg,
        ]);
    }

    // --- STRING ---

    public static function slugify(string $string): string
    {
        // Transliterate accented characters to ASCII
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $string = preg_replace('/[^A-Za-z0-9]+/', '-', $string);
        return strtolower(trim($string, '-'));
    }


    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   STYLE / HTML OUTPUT
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function hx(int $level, string $text, string $class = ''): string
    {
        $level     = max(1, min(6, $level));
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<h$level$classAttr>$text</h$level>";
    }

    public static function b(string $text = '', string $class = ''): string
    {
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<b$classAttr>$text</b>";
    }

    public static function i(string $text = '', string $class = ''): string
    {
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<i$classAttr>$text</i>";
    }

    public static function bi(string $text = '', string $class = ''): string
    {
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<b$classAttr><i>$text</i></b>";
    }

    public static function small(string $text = '', string $class = ''): string
    {
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<small$classAttr>$text</small>";
    }

    public static function mark(string $text = '', string $class = ''): string
    {
        $classAttr = $class ? " class=\"$class\"" : '';
        return "<mark$classAttr>$text</mark>";
    }

    /* 
    Example Usage
    Microfy::def('APP_NAME', 'microfy');
    Microfy::load('config');

    Microfy::hx(2, 'Welcome');
    echo Microfy::b('Bold text');
    echo Microfy::slugify('This is a title!');
    Microfy::ok(); // JSON response

    */

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   BLOCK ELEMENTS
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function p(string $text = '', string $class = ''): string
    {
        return "<p" . self::classAttr($class) . ">$text</p>";
    }

    public static function span(string $text = '', string $class = ''): string
    {
        return "<span" . self::classAttr($class) . ">$text</span>";
    }
    public static function div(string $text, array $attrs = []): string
    {
        $attrStr = self::Attr($attrs);
        return "<div$attrStr>$text</div>";
    }
    public static function section(string $text = '', string $class = ''): string
    {
        return "<section" . self::classAttr($class) . ">$text</section>";
    }

    public static function Attr(array $attrs): string
    {
        $parts = [];
        foreach ($attrs as $k => $v) {
            $parts[] = "$k=\"" . htmlspecialchars($v) . "\"";
        }

        return $parts ? ' ' . implode(' ', $parts) : '';
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *   CODE BLOCKS
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function code(string $content, string $lang = ''): string
    {
        $class = $lang ? " class=\"language-$lang\"" : '';
        return "<pre><code$class>" . htmlspecialchars($content) . "</code></pre>";
    }

    public static function codejs(string $text): string
    {
        return self::code($text, 'js');
    }

    public static function codephp(string $text): string
    {
        return self::code($text, 'php');
    }

    public static function codejson(string $text): string
    {
        return self::code($text, 'json');
    }

    public static function codehtml(string $text): string
    {
        return self::code($text, 'html');
    }

    public static function codesql(string $text): string
    {
        return self::code($text, 'sql');
    }

    public static function codebash(string $text): string
    {
        return self::code($text, 'bash');
    }

    public static function codec(string $text): string
    {
        return self::code($text, 'c');
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *  LISTS
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function ul(array $items, string $class = ''): string
    {
        $html = "<ul" . self::classAttr($class) . ">";
        foreach ($items as $item) {
            $html .= "<li>$item</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    public static function ulOpen(string $class = ''): string
    {
        return "<ul" . self::classAttr($class) . ">";
    }

    public static function ulClose(): string
    {
        return "</ul>";
    }

    public static function li(string $text): string
    {
        return "<li>$text</li>";
    }

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *  LINE BREAKS & HR
     * ──────────────────────────────────────────────────────────────────────────────
     */

    public static function br(...$args): string
    {
        if (empty($args)) {
            return '<br>';
        }

        $output = '';
        foreach ($args as $arg) {
            $output .= '<br>' . $arg;
        }
        return $output;
    }

    public static function bra(...$args): string
    {
        if (empty($args)) {
            return '<br>';
        }

        $output = '';
        foreach ($args as $arg) {
            $output .= $arg . '<br>';
        }
        return $output;
    }

    public static function hr(...$args): string
    {
        if (empty($args)) {
            return '<hr>';
        }

        $output = '';
        foreach ($args as $arg) {
            $output .= '<hr>' . $arg;
        }
        return $output;
    }

    public static function hra(...$args): string
    {
        if (empty($args)) {
            return '<hr>';
        }

        $output = '';
        foreach ($args as $arg) {
            $output .= $arg . '<hr>';
        }
        return $output;
    }

    // --- COUNTER ---

    public static function c(string $text = ''): string
    {
        static $counter = 1;
        return $counter++ . '. ' . $text;
    }

    public static function cStr(string $text = ''): string
    {
        static $counter = 1;
        return $counter++ . '. ' . $text;
    }

    // --- SHARED UTILITY ---

    protected static function classAttr(string $class): string
    {
        return $class ? " class=\"$class\"" : '';
    }

    /*
    Example Usage
    Microfy::p('Paragraph text', 'highlight');
    Microfy::codephp('<?php echo "Hello"; ?>');
    Microfy::ul(['Item 1', 'Item 2']);
    Microfy::br();
    Microfy::c('Step A'); */

    /**
     * ──────────────────────────────────────────────────────────────────────────────
     *  HTML HELPERS 2
     * ──────────────────────────────────────────────────────────────────────────────
     */

    // Core tag builder
    public static function tag(string $tag, $content = '', array $attrs = [], bool $selfClose = false): string
    {
        $attrStrings = [];
        foreach ($attrs as $k => $v) {
            $attrStrings[] = sprintf('%s="%s"', $k, htmlspecialchars((string) $v, ENT_QUOTES));
        }
        $attrString = $attrStrings ? ' ' . implode(' ', $attrStrings) : '';

        if ($selfClose) {
            return "<{$tag}{$attrString} />";
        }

        if (is_array($content)) {
            $content = implode('', $content);
        }

        return "<{$tag}{$attrString}>{$content}</{$tag}>";
    }

    // Alias for tag()
    public static function html_tag(string $tag, $content = '', array $attrs = [], bool $selfClose = false): string
    {
        return self::tag($tag, $content, $attrs, $selfClose);
    }

    // Specific paired element for <html>
    public static function html_html($content = '', array $attrs = []): string
    {
        return self::html_tag('html', $content, $attrs);
    }

    public static function html_head($content = '', array $attrs = []): string
    {
        return self::html_tag('head', $content, $attrs);
    }

    public static function html_body($content = '', array $attrs = []): string
    {
        return self::html_tag('body', $content, $attrs);
    }

    public static function html_header($content = '', array $attrs = []): string
    {
        return self::html_tag('header', $content, $attrs);
    }

    public static function html_footer($content = '', array $attrs = []): string
    {
        return self::html_tag('footer', $content, $attrs);
    }

    public static function html_section($content = '', array $attrs = []): string
    {
        return self::html_tag('section', $content, $attrs);
    }

    public static function html_article($content = '', array $attrs = []): string
    {
        return self::html_tag('article', $content, $attrs);
    }

    public static function html_nav($content = '', array $attrs = []): string
    {
        return self::html_tag('nav', $content, $attrs);
    }

    public static function html_aside($content = '', array $attrs = []): string
    {
        return self::html_tag('aside', $content, $attrs);
    }

    public static function html_div($content = '', array $attrs = []): string
    {
        return self::html_tag('div', $content, $attrs);
    }

    public static function html_span($content = '', array $attrs = []): string
    {
        return self::html_tag('span', $content, $attrs);
    }

    public static function html_h1($content = '', array $attrs = []): string
    {
        return self::html_tag('h1', $content, $attrs);
    }

    public static function html_h2($content = '', array $attrs = []): string
    {
        return self::html_tag('h2', $content, $attrs);
    }

    public static function html_h3($content = '', array $attrs = []): string
    {
        return self::html_tag('h3', $content, $attrs);
    }

    public static function html_h4($content = '', array $attrs = []): string
    {
        return self::html_tag('h4', $content, $attrs);
    }

    public static function html_h5($content = '', array $attrs = []): string
    {
        return self::html_tag('h5', $content, $attrs);
    }

    public static function html_h6($content = '', array $attrs = []): string
    {
        return self::html_tag('h6', $content, $attrs);
    }

    public static function html_p($content = '', array $attrs = []): string
    {
        return self::html_tag('p', htmlspecialchars((string) $content), $attrs);
    }

    public static function html_blockquote($content = '', array $attrs = []): string
    {
        return self::html_tag('blockquote', $content, $attrs);
    }

    public static function html_pre($content = '', array $attrs = []): string
    {
        return self::html_tag('pre', htmlspecialchars((string) $content), $attrs);
    }

    public static function html_code($content = '', array $attrs = []): string
    {
        return self::html_tag('code', htmlspecialchars((string) $content), $attrs);
    }

    public static function html_ul(array $items, array $attrs = []): string
    {
        $lis = array_map(fn($i) => self::tag('li', $i), $items);
        return self::html_tag('ul', $lis, $attrs);
    }

    public static function html_ol(array $items, array $attrs = []): string
    {
        $lis = array_map(fn($i) => self::tag('li', $i), $items);
        return self::html_tag('ol', $lis, $attrs);
    }

    public static function html_li($content = '', array $attrs = []): string
    {
        return self::html_tag('li', $content, $attrs);
    }

    public static function html_dl(array $terms, array $attrs = []): string
    {
        $children = [];
        foreach ($terms as $t) {
            $children[] = self::tag('dt', htmlspecialchars((string) $t['term']));
            $children[] = self::tag('dd', htmlspecialchars((string) $t['desc']));
        }
        return self::html_tag('dl', $children, $attrs);
    }

    /*  Sample usage

    echo Microfy::html_ul(['One', 'Two', 'Three'], ['class' => 'my-list']);
    echo Microfy::html_p('This is a paragraph.');
    echo Microfy::html_code('echo "Hello World";'); */

    /* Tables */

    public static function html_table($content = '', array $attrs = []): string
    {
        return self::html_tag('table', $content, $attrs);
    }

    public static function html_thead($content = '', array $attrs = []): string
    {
        return self::html_tag('thead', $content, $attrs);
    }

    public static function html_tbody($content = '', array $attrs = []): string
    {
        return self::html_tag('tbody', $content, $attrs);
    }

    public static function html_tr($content = '', array $attrs = []): string
    {
        return self::html_tag('tr', $content, $attrs);
    }

    public static function html_th($content = '', array $attrs = []): string
    {
        return self::html_tag('th', $content, $attrs);
    }

    public static function html_td($content = '', array $attrs = []): string
    {
        return self::html_tag('td', $content, $attrs);
    }

    /* Form element methods */

    public static function html_form($content = '', array $attrs = []): string
    {
        return self::html_tag('form', $content, $attrs);
    }

    public static function html_label($content = '', array $attrs = []): string
    {
        return self::html_tag('label', htmlspecialchars((string) $content), $attrs);
    }

    public static function html_input(array $attrs = []): string
    {
        return self::html_tag('input', '', $attrs, true);
    }

    public static function html_textarea($content = '', array $attrs = []): string
    {
        return self::html_tag('textarea', htmlspecialchars((string) $content), $attrs);
    }

    public static function html_select(array $options, array $attrs = []): string
    {
        $opts = [];
        foreach ($options as $value => $text) {
            $opts[] = self::tag('option', htmlspecialchars((string) $text), ['value' => (string) $value]);
        }
        return self::html_tag('select', $opts, $attrs);
    }

    public static function html_button($content = '', array $attrs = []): string
    {
        return self::html_tag('button', $content, $attrs);
    }
    /* echo Microfy::html_form(
            Microfy::html_label('Email:', ['for' => 'email']) .
            Microfy::html_input(['type' => 'email', 'id' => 'email', 'name' => 'email']) .
            Microfy::html_button('Send', ['type' => 'submit']),
            ['method' => 'post']
        ); */

    /* Self-closing and embedded content methods */

    public static function html_br(array $attrs = []): string
    {
        return self::html_tag('br', '', $attrs, true);
    }

    public static function html_hr(array $attrs = []): string
    {
        return self::html_tag('hr', '', $attrs, true);
    }

    public static function html_img(array $attrs = []): string
    {
        return self::html_tag('img', '', $attrs, true);
    }

    public static function html_meta(array $attrs = []): string
    {
        return self::html_tag('meta', '', $attrs, true);
    }

    public static function html_link(array $attrs = []): string
    {
        return self::html_tag('link', '', $attrs, true);
    }

    public static function html_script($content = '', array $attrs = []): string
    {
        return self::html_tag('script', $content, $attrs);
    }

    public static function html_style($content = '', array $attrs = []): string
    {
        return self::html_tag('style', $content, $attrs);
    }

    /**
     * Pretty‑print a chunk of HTML.
     *
     * @param string $html Unformatted HTML fragment.
     * @return string Formatted HTML with line breaks and indentation.
     */
    public static function pretty_html(string $html): string
    {
        $dom                     = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;

        @$dom->loadHTML(
            '<?xml encoding="utf-8"?>' . $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $out = $dom->saveHTML();
        return preg_replace('/^<\?xml.*?\?>\s*/', '', $out);
    }

    /*  Example
     
        $html = Microfy::html_div([
            Microfy::html_h1('Hello'),
            Microfy::html_br(),
            Microfy::html_img(['src' => 'pic.jpg', 'alt' => 'image'])
        ]);

        echo Microfy::pretty_html($html); */

    /* ---------END---------- */
}

