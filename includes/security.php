<?php
// includes/security.php - Bezpečnostní pomocné funkce

/**
 * Bezpečné escapování HTML
 * @param mixed $string
 * @return string
 */
function e($string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Bezpečné vypsání HTML atributu
 * @param mixed $string
 * @return string
 */
function attr($string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitizace HTML obsahu (ponechá pouze povolené tagy)
 * @param string $html
 * @param array $allowedTags
 * @return string
 */
function sanitizeHtml(string $html, array $allowedTags = []): string {
    if (empty($allowedTags)) {
        $allowedTags = [
            'p', 'br', 'strong', 'em', 'u', 'i', 'b',
            'ul', 'ol', 'li', 'blockquote', 'pre', 'code',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'a' => ['href', 'title', 'target'],
            'img' => ['src', 'alt', 'width', 'height', 'style'],
            'table', 'thead', 'tbody', 'tr', 'td', 'th',
            'div' => ['style'], 'span' => ['style']
        ];
    }
    
    // Převod povolených tagů na formát pro strip_tags
    $allowedTagsString = '';
    foreach ($allowedTags as $tag => $attrs) {
        if (is_numeric($tag)) {
            $allowedTagsString .= "<$attrs>";
        } else {
            $allowedTagsString .= "<$tag>";
        }
    }
    
    // Odstranění nebezpečných tagů
    $html = strip_tags($html, $allowedTagsString);
    
    // Odstranění nebezpečných atributů
    $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $html);
    $html = preg_replace('/\s*on\w+\s*=\s*[^\s>]+/', '', $html);
    
    // Odstranění javascript: URL
    $html = preg_replace('/href\s*=\s*["\']?\s*javascript:[^"\']*["\']?/i', 'href="#"', $html);
    
    return $html;
}

/**
 * Bezpečné vypsání JSON
 * @param mixed $data
 * @return string
 */
function safeJson($data): string {
    return htmlspecialchars(
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * CSRF token generátor a validátor
 */
class CSRF {
    private static $tokenName = 'csrf_token';
    
    public static function generateToken(): string {
        if (!isset($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::$tokenName];
    }
    
    public static function validateToken(string $token): bool {
        if (!isset($_SESSION[self::$tokenName])) {
            return false;
        }
        return hash_equals($_SESSION[self::$tokenName], $token);
    }
    
    public static function getTokenField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
    }
}

/**
 * Validace a sanitizace vstupů
 */
class Input {
    /**
     * Získání POST hodnoty
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function post(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Získání GET hodnoty
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Validace e-mailu
     * @param string $email
     * @return string|false
     */
    public static function email(string $email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Sanitizace řetězce
     * @param string $string
     * @param int $maxLength
     * @return string
     */
    public static function string(string $string, int $maxLength = 0): string {
        $string = trim($string);
        if ($maxLength > 0) {
            $string = substr($string, 0, $maxLength);
        }
        return $string;
    }
    
    /**
     * Validace čísla
     * @param mixed $number
     * @param int $min
     * @param int $max
     * @return int|false
     */
    public static function int($number, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) {
        $number = filter_var($number, FILTER_VALIDATE_INT);
        if ($number === false) {
            return false;
        }
        if ($number < $min || $number > $max) {
            return false;
        }
        return $number;
    }
}

// Příklad použití ve vašich šablonách:
// <?= e($post['blkt_nazev']) ?>
// <?= attr($user['email']) ?>
// <?= sanitizeHtml($post['blkt_obsah']) ?>
// <?= CSRF::getTokenField() ?>