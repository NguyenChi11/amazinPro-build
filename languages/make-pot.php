<?php

/**
 * Minimal POT generator for this theme.
 *
 * Usage:
 *   php make-pot.php [theme_root] [output_pot]
 *
 * Defaults:
 *   theme_root  = parent directory of this script
 *   output_pot  = <theme_root>/languages/buildpro.pot
 */

declare(strict_types=1);

const BUILDPRO_TEXTDOMAIN = 'buildpro';

/** @return array{0:string,1:int}|null */
function get_prev_non_ws_token(array $tokens, int $index): ?array
{
    for ($i = $index - 1; $i >= 0; $i--) {
        $t = $tokens[$i];
        if (is_array($t)) {
            if ($t[0] === T_WHITESPACE) {
                continue;
            }
            return [$t[1], $t[2]];
        }
        if (trim((string) $t) === '') {
            continue;
        }
        return [(string) $t, -1];
    }
    return null;
}

function pot_escape(string $s): string
{
    $s = str_replace("\\", "\\\\", $s);
    $s = str_replace("\"", "\\\"", $s);
    $s = str_replace("\r\n", "\n", $s);
    $s = str_replace("\r", "\n", $s);
    $s = str_replace("\n", "\\n", $s);
    $s = str_replace("\t", "\\t", $s);
    return $s;
}

function decode_php_string_literal(string $literal): string
{
    $literal = trim($literal);
    if ($literal === '') {
        return '';
    }
    $q = $literal[0];
    if (($q !== "'" && $q !== '"') || substr($literal, -1) !== $q) {
        return $literal;
    }
    $inner = substr($literal, 1, -1);
    return stripcslashes($inner);
}

/**
 * @param array<int, array{0:int,1:string,2:int}|string> $tokens
 * @return array{args: array<int, string>, endIndex: int}
 */
function parse_function_args(array $tokens, int $openParenIndex): array
{
    $depth = 0;
    $args = [];
    $current = '';

    for ($i = $openParenIndex; $i < count($tokens); $i++) {
        $t = $tokens[$i];
        $text = is_array($t) ? $t[1] : (string) $t;

        if ($text === '(') {
            $depth++;
            if ($depth === 1) {
                continue;
            }
        }

        if ($text === ')') {
            $depth--;
            if ($depth === 0) {
                $args[] = trim($current);
                return ['args' => $args, 'endIndex' => $i];
            }
        }

        if ($depth === 1 && $text === ',') {
            $args[] = trim($current);
            $current = '';
            continue;
        }

        if ($depth >= 1) {
            $current .= $text;
        }
    }

    return ['args' => [], 'endIndex' => $openParenIndex];
}

/**
 * @return array<string, array{refs: array<int, string>, comments: array<int, string>}>
 */
function extract_strings_from_php(string $themeRoot): array
{
    $functions = [
        '__' => ['msgid' => 0, 'domain' => 1],
        '_e' => ['msgid' => 0, 'domain' => 1],
        'esc_html__' => ['msgid' => 0, 'domain' => 1],
        'esc_html_e' => ['msgid' => 0, 'domain' => 1],
        'esc_attr__' => ['msgid' => 0, 'domain' => 1],
        'esc_attr_e' => ['msgid' => 0, 'domain' => 1],
        '_x' => ['msgid' => 0, 'domain' => 2],
        'esc_html_x' => ['msgid' => 0, 'domain' => 2],
        'esc_attr_x' => ['msgid' => 0, 'domain' => 2],
        '_n' => ['msgid' => 0, 'domain' => 3],
        '_nx' => ['msgid' => 0, 'domain' => 4],
    ];

    $results = [];

    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($themeRoot, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($rii as $fileInfo) {
        if (!$fileInfo instanceof SplFileInfo) {
            continue;
        }
        if (strtolower($fileInfo->getExtension()) !== 'php') {
            continue;
        }

        $absPath = $fileInfo->getPathname();
        $relPath = str_replace('\\', '/', substr($absPath, strlen(rtrim($themeRoot, "\\/")) + 1));

        if (str_starts_with($relPath, 'languages/')) {
            continue;
        }
        if (str_starts_with($relPath, 'import/TGM-Plugin-Activation-develop/')) {
            continue;
        }

        $code = file_get_contents($absPath);
        if ($code === false || $code === '') {
            continue;
        }

        $tokens = token_get_all($code);

        for ($i = 0; $i < count($tokens); $i++) {
            $t = $tokens[$i];
            if (!is_array($t) || $t[0] !== T_STRING) {
                continue;
            }

            $fn = $t[1];
            if (!isset($functions[$fn])) {
                continue;
            }

            // Next non-whitespace token should be "(".
            $j = $i + 1;
            while ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                $j++;
            }
            if ($j >= count($tokens) || $tokens[$j] !== '(') {
                continue;
            }

            $parsed = parse_function_args($tokens, $j);
            $args = $parsed['args'];
            $i = $parsed['endIndex']; // advance

            $msgidIndex = $functions[$fn]['msgid'];
            $domainIndex = $functions[$fn]['domain'];

            if (!isset($args[$msgidIndex])) {
                continue;
            }

            $msgidRaw = $args[$msgidIndex];
            if (!preg_match('/^([\"\"]).*\1$|^\'.*\'$/s', $msgidRaw)) {
                continue;
            }

            $domainRaw = $args[$domainIndex] ?? '';
            $domain = '';
            if ($domainRaw !== '' && preg_match('/^([\"\"]).*\1$|^\'.*\'$/s', $domainRaw)) {
                $domain = decode_php_string_literal($domainRaw);
            }

            if ($domain !== BUILDPRO_TEXTDOMAIN) {
                continue;
            }

            $msgid = decode_php_string_literal($msgidRaw);
            if ($msgid === '') {
                continue;
            }

            $line = is_array($t) ? (int) $t[2] : 1;
            $ref = $relPath . ':' . $line;

            $comment = null;
            $prev = get_prev_non_ws_token($tokens, $i);
            if ($prev && is_string($prev[0])) {
                $prevText = $prev[0];
                if (preg_match('/translators\s*:\s*(.+?)(\*\/|$)/is', $prevText, $m)) {
                    $comment = 'translators: ' . trim($m[1]);
                }
            }

            if (!isset($results[$msgid])) {
                $results[$msgid] = ['refs' => [], 'comments' => []];
            }
            if (!in_array($ref, $results[$msgid]['refs'], true)) {
                $results[$msgid]['refs'][] = $ref;
            }
            if ($comment && !in_array($comment, $results[$msgid]['comments'], true)) {
                $results[$msgid]['comments'][] = $comment;
            }
        }
    }

    return $results;
}

function build_pot_header(): string
{
    $date = gmdate('Y-m-d\\TH:i:s+00:00');
    return "# Copyright (C) " . gmdate('Y') . " AmazinPro_Team\n"
        . "# This file is distributed under the same license as the Build Pro V2 package.\n"
        . "msgid \"\"\n"
        . "msgstr \"\"\n"
        . "\"Project-Id-Version: Build Pro V2 1.1\\n\"\n"
        . "\"Report-Msgid-Bugs-To: https://amazinpro.com/\\n\"\n"
        . "\"POT-Creation-Date: {$date}\\n\"\n"
        . "\"MIME-Version: 1.0\\n\"\n"
        . "\"Content-Type: text/plain; charset=UTF-8\\n\"\n"
        . "\"Content-Transfer-Encoding: 8bit\\n\"\n"
        . "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n"
        . "\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"\n"
        . "\"Language-Team: LANGUAGE <LL@li.org>\\n\"\n"
        . "\"X-Domain: " . BUILDPRO_TEXTDOMAIN . "\\n\"\n\n";
}

function write_pot(string $outputFile, array $entries): void
{
    // Sort by first reference path+line to keep stable structure.
    uksort($entries, static function (string $a, string $b) use ($entries): int {
        $ra = $entries[$a]['refs'][0] ?? '';
        $rb = $entries[$b]['refs'][0] ?? '';
        if ($ra === $rb) {
            return strcmp($a, $b);
        }
        return strcmp($ra, $rb);
    });

    $out = build_pot_header();
    foreach ($entries as $msgid => $data) {
        foreach ($data['refs'] as $ref) {
            $out .= "#: {$ref}\n";
        }
        foreach ($data['comments'] as $c) {
            $out .= "#. {$c}\n";
        }
        $out .= 'msgid "' . pot_escape($msgid) . "\"\n";
        $out .= "msgstr \"\"\n\n";
    }

    file_put_contents($outputFile, $out);
}

$themeRoot = $argv[1] ?? dirname(__DIR__);
$output = $argv[2] ?? ($themeRoot . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'buildpro.pot');

$themeRoot = realpath($themeRoot) ?: $themeRoot;

$entries = extract_strings_from_php($themeRoot);

// Ensure header exists even if empty.
write_pot($output, $entries);

echo "OK: " . count($entries) . " strings -> {$output}\n";
