<?php

namespace Lidmo\WP\Foundation\Support;


use Lidmo\WP\Foundation\Util;

class Str
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    protected static $plural = [
        '/(quiz)$/i' => "$1zes",
        '/^(ox)$/i' => "$1en",
        '/([m|l])ouse$/i' => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i' => "$1es",
        '/([^aeiouy]|qu)y$/i' => "$1ies",
        '/(hive)$/i' => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i' => "ses",
        '/([ti])um$/i' => "$1a",
        '/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
        '/(bu)s$/i' => "$1ses",
        '/(alias)$/i' => "$1es",
        '/(octop)us$/i' => "$1i",
        '/(ax|test)is$/i' => "$1es",
        '/(us)$/i' => "$1es",
        '/s$/i' => "s",
        '/$/' => "s"
    ];

    protected static $singular = [
        '/(quiz)zes$/i' => "$1",
        '/(matr)ices$/i' => "$1ix",
        '/(vert|ind)ices$/i' => "$1ex",
        '/^(ox)en$/i' => "$1",
        '/(alias)es$/i' => "$1",
        '/(octop|vir)i$/i' => "$1us",
        '/(cris|ax|test)es$/i' => "$1is",
        '/(shoe)s$/i' => "$1",
        '/(o)es$/i' => "$1",
        '/(bus)es$/i' => "$1",
        '/([m|l])ice$/i' => "$1ouse",
        '/(x|ch|ss|sh)es$/i' => "$1",
        '/(m)ovies$/i' => "$1ovie",
        '/(s)eries$/i' => "$1eries",
        '/([^aeiouy]|qu)ies$/i' => "$1y",
        '/([lr])ves$/i' => "$1f",
        '/(tive)s$/i' => "$1",
        '/(hive)s$/i' => "$1",
        '/(li|wi|kni)ves$/i' => "$1fe",
        '/(shea|loa|lea|thie)ves$/i' => "$1f",
        '/(^analy)ses$/i' => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
        '/([ti])a$/i' => "$1um",
        '/(n)ews$/i' => "$1ews",
        '/(h|bl)ouses$/i' => "$1ouse",
        '/(corpse)s$/i' => "$1",
        '/(us)es$/i' => "$1",
        '/s$/i' => ""
    ];

    protected static $irregular = [
        'move' => 'moves',
        'foot' => 'feet',
        'goose' => 'geese',
        'sex' => 'sexes',
        'child' => 'children',
        'man' => 'men',
        'tooth' => 'teeth',
        'person' => 'people',
        'valve' => 'valves'
    ];

    protected static $uncountable = [
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    ];

    public static function plural($string)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable))
            return $string;


        // check for irregular singular forms
        foreach (self::$irregular as $pattern => $result) {
            $pattern = '/' . $pattern . '$/i';

            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach (self::$plural as $pattern => $result) {
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        return $string;
    }

    public static function singular($string)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable))
            return $string;

        // check for irregular plural forms
        foreach (self::$irregular as $result => $pattern) {
            $pattern = '/' . $pattern . '$/i';

            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach (self::$singular as $pattern => $result) {
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        return $string;
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    public static function after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    public static function afterLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, (string)$search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    public static function before($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, (string)$search, true);

        return $result === false ? $subject : $result;
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    public static function beforeLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return static::substr($subject, 0, $pos);
    }

    /**
     * Get the portion of a string between two given values.
     *
     * @param string $subject
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function between($subject, $from, $to)
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::beforeLast(static::after($subject, $from), $to);
    }

    /**
     * Convert a value to camel case.
     *
     * @param string $value
     * @return string
     */
    public static function camel($value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string contains all array values.
     *
     * @param string $haystack
     * @param string[] $needles
     * @return bool
     */
    public static function containsAll($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if (!static::contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (
                $needle !== '' && $needle !== null
                && substr($haystack, -strlen($needle)) === (string)$needle
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string $value
     * @param string $cap
     * @return string
     */
    public static function finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|array $pattern
     * @param string $value
     * @return bool
     */
    public static function is($pattern, $value)
    {
        $patterns = Util::arrayWrap($pattern);

        $value = (string)$value;

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            $pattern = (string)$pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern == $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string is a valid UUID.
     *
     * @param string $value
     * @return bool
     */
    public static function isUuid($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
    }

    /**
     * Convert a string to kebab case.
     *
     * @param string $value
     * @return string
     */
    public static function kebab($value)
    {
        return static::snake($value, '-');
    }

    /**
     * Return the length of the given string.
     *
     * @param string $value
     * @param string|null $encoding
     * @return int
     */
    public static function length($value, $encoding = null)
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Limit the number of words in a string.
     *
     * @param string $value
     * @param int $words
     * @param string $end
     * @return string
     */
    public static function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (!isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Masks a portion of a string with a repeated character.
     *
     * @param string $string
     * @param string $character
     * @param int $index
     * @param int|null $length
     * @param string $encoding
     * @return string
     */
    public static function mask($string, $character, $index, $length = null, $encoding = 'UTF-8')
    {
        if ($character === '') {
            return $string;
        }

        if (is_null($length) && PHP_MAJOR_VERSION < 8) {
            $length = mb_strlen($string, $encoding);
        }

        $segment = mb_substr($string, $index, $length, $encoding);

        if ($segment === '') {
            return $string;
        }

        $strlen = mb_strlen($string, $encoding);
        $startIndex = $index;

        if ($index < 0) {
            $startIndex = $index < -$strlen ? 0 : $strlen + $index;
        }

        $start = mb_substr($string, 0, $startIndex, $encoding);
        $segmentLen = mb_strlen($segment, $encoding);
        $end = mb_substr($string, $startIndex + $segmentLen);

        return $start . str_repeat(mb_substr($character, 0, 1, $encoding), $segmentLen) . $end;
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     * @param string $subject
     * @return string
     */
    public static function match($pattern, $subject)
    {
        preg_match($pattern, $subject, $matches);

        if (!$matches) {
            return '';
        }

        return $matches[1] ?? $matches[0];
    }

    /**
     * Pad both sides of a string with another.
     *
     * @param string $value
     * @param int $length
     * @param string $pad
     * @return string
     */
    public static function padBoth($value, $length, $pad = ' ')
    {
        return str_pad($value, strlen($value) - mb_strlen($value) + $length, $pad, STR_PAD_BOTH);
    }

    /**
     * Pad the left side of a string with another.
     *
     * @param string $value
     * @param int $length
     * @param string $pad
     * @return string
     */
    public static function padLeft($value, $length, $pad = ' ')
    {
        return str_pad($value, strlen($value) - mb_strlen($value) + $length, $pad, STR_PAD_LEFT);
    }

    /**
     * Pad the right side of a string with another.
     *
     * @param string $value
     * @param int $length
     * @param string $pad
     * @return string
     */
    public static function padRight($value, $length, $pad = ' ')
    {
        return str_pad($value, strlen($value) - mb_strlen($value) + $length, $pad, STR_PAD_RIGHT);
    }

    /**
     * Parse a Class[@]method style callback into class and method.
     *
     * @param string $callback
     * @param string|null $default
     * @return array<int, string|null>
     */
    public static function parseCallback($callback, $default = null)
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     * @return string
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Repeat the given string.
     *
     * @param string $string
     * @param int $times
     * @return string
     */
    public static function repeat(string $string, int $times)
    {
        return str_repeat($string, $times);
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param string $search
     * @param array<int|string, string> $replace
     * @param string $subject
     * @return string
     */
    public static function replaceArray($search, array $replace, $subject)
    {
        $segments = explode($search, $subject);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= (array_shift($replace) ?? $search) . $segment;
        }

        return $result;
    }

    /**
     * Replace the given value in the given string.
     *
     * @param string|string[] $search
     * @param string|string[] $replace
     * @param string|string[] $subject
     * @return string
     */
    public static function replace($search, $replace, $subject)
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Remove any occurrence of the given string in the subject.
     *
     * @param string|array<string> $search
     * @param string $subject
     * @param bool $caseSensitive
     * @return string
     */
    public static function remove($search, $subject, $caseSensitive = true)
    {
        $subject = $caseSensitive
            ? str_replace($search, '', $subject)
            : str_ireplace($search, '', $subject);

        return $subject;
    }

    /**
     * Reverse the given string.
     *
     * @param string $value
     * @return string
     */
    public static function reverse(string $value)
    {
        return implode(array_reverse(mb_str_split($value)));
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param string $value
     * @param string $prefix
     * @return string
     */
    public static function start($value, $prefix)
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $value);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to title case.
     *
     * @param string $value
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Convert the given string to title case for each word.
     *
     * @param string $value
     * @return string
     */
    public static function headline($value)
    {
        $parts = explode(' ', $value);

        $parts = count($parts) > 1
            ? $parts = array_map([static::class, 'title'], $parts)
            : $parts = array_map([static::class, 'title'], static::ucsplit(implode('_', $parts)));

        $collapsed = static::replace(['-', '_', ' '], '_', implode('_', $parts));

        return implode(' ', array_filter(explode('_', $collapsed)));
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     * @param string|null $language
     * @return string
     */
    public static function slug($value)
    {
        $table = [
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => '-'
        ];

        // -- Remove duplicated spaces
        $value = preg_replace(['/\s{2,}/', '/[\t\n]/'], ' ', $value);

        // -- Returns the slug
        return strtolower(strtr($value, $table));
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $words = explode(' ', static::replace(['-', '_'], ' ', $value));

        $studlyWords = array_map(function ($word) {
            return static::ucfirst($word);
        }, $words);

        return static::$studlyCache[$key] = implode($studlyWords);
    }

    /**
     * Returns the portion of the string specified by the start and length parameters.
     *
     * @param string $string
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Returns the number of substring occurrences.
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @param int|null $length
     * @return int
     */
    public static function substrCount($haystack, $needle, $offset = 0, $length = null)
    {
        if (!is_null($length)) {
            return substr_count($haystack, $needle, $offset, $length);
        } else {
            return substr_count($haystack, $needle, $offset);
        }
    }

    /**
     * Replace text within a portion of a string.
     *
     * @param string|array $string
     * @param string|array $replace
     * @param array|int $offset
     * @param array|int|null $length
     * @return string|array
     */
    public static function substrReplace($string, $replace, $offset = 0, $length = null)
    {
        if ($length === null) {
            $length = strlen($string);
        }

        return substr_replace($string, $replace, $offset, $length);
    }

    /**
     * Swap multiple keywords in a string with other keywords.
     *
     * @param array $map
     * @param string $subject
     * @return string
     */
    public static function swap(array $map, $subject)
    {
        return strtr($subject, $map);
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param string $string
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    /**
     * Split a string into pieces by uppercase characters.
     *
     * @param string $string
     * @return array
     */
    public static function ucsplit($string)
    {
        return preg_split('/(?=\p{Lu})/u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Get the number of words a string contains.
     *
     * @param string $string
     * @return int
     */
    public static function wordCount($string)
    {
        return str_word_count($string);
    }

    /**
     * Remove all strings from the casing caches.
     *
     * @return void
     */
    public static function flushCache()
    {
        static::$snakeCache = [];
        static::$camelCache = [];
        static::$studlyCache = [];
    }
}
