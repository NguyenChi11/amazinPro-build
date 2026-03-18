<?php
function parse_po(string $content): array
{
    $entries = [];
    $content = str_replace("\r\n", "\n", $content);
    $blocks  = preg_split('/\n\n+/', $content);
    foreach ($blocks as $block) {
        $block = trim($block);
        if (empty($block)) continue;
        $msgid = null;
        $msgstr = null;
        $in_msgid = false;
        $in_msgstr = false;
        foreach (explode("\n", $block) as $line) {
            $line = trim($line);
            if (preg_match('/^#/', $line)) {
                $in_msgid = $in_msgstr = false;
                continue;
            }
            if (preg_match('/^msgid\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $in_msgid = true;
                $in_msgstr = false;
                $msgid = $m[1];
            } elseif (preg_match('/^msgstr\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $in_msgstr = true;
                $in_msgid = false;
                $msgstr = $m[1];
            } elseif (preg_match('/^"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                if ($in_msgid) $msgid .= $m[1];
                if ($in_msgstr) $msgstr .= $m[1];
            }
        }
        if ($msgid === null || $msgstr === null) continue;
        $d_id = stripcslashes($msgid);
        $d_str = stripcslashes($msgstr);
        if ($d_id === '' || $d_str !== '') $entries[$d_id] = $d_str;
    }
    return $entries;
}

function compile_mo(array $entries, string $mo_file): int
{
    ksort($entries);
    $ids = array_keys($entries);
    $strs = array_values($entries);
    $n = count($ids);
    $ids_off = 28;
    $strs_off = 28 + $n * 8;
    $data_start = 28 + $n * 16;
    $ids_meta = [];
    $strs_meta = [];
    $offset = $data_start;
    foreach ($ids  as $s) {
        $ids_meta[]  = [strlen($s), $offset];
        $offset += strlen($s) + 1;
    }
    foreach ($strs as $s) {
        $strs_meta[] = [strlen($s), $offset];
        $offset += strlen($s) + 1;
    }
    $mo  = pack('V', 0x950412de) . pack('V', 0) . pack('V', $n);
    $mo .= pack('V', $ids_off) . pack('V', $strs_off) . pack('V', 0) . pack('V', $data_start);
    foreach ($ids_meta  as [$l, $o]) {
        $mo .= pack('VV', $l, $o);
    }
    foreach ($strs_meta as [$l, $o]) {
        $mo .= pack('VV', $l, $o);
    }
    foreach ($ids  as $s) {
        $mo .= $s . "\0";
    }
    foreach ($strs as $s) {
        $mo .= $s . "\0";
    }
    file_put_contents($mo_file, $mo);
    return $n;
}

function compile_po_to_mo(string $po, string $mo): void
{
    $n = compile_mo(parse_po(file_get_contents($po)), $mo);
    echo "OK: $n strings -> $mo\n";
}

// Usage:
// - php compile-mo.php <input.po> <output.mo>
// - php compile-mo.php            (compile all buildpro-*.po in this folder)
if ($argc >= 3) {
    $po = (string) $argv[1];
    $mo = (string) $argv[2];
    if ($po === '' || $mo === '') {
        fwrite(STDERR, "ERR: Missing input/output path.\n");
        exit(1);
    }
    compile_po_to_mo($po, $mo);
    exit(0);
}

$dir = __DIR__;
$poFiles = glob($dir . DIRECTORY_SEPARATOR . 'buildpro-*.po') ?: [];
$poFiles = array_values(array_filter($poFiles, static function ($path) {
    return is_string($path) && $path !== '' && stripos(basename($path), '.po') !== false;
}));

if (empty($poFiles)) {
    fwrite(STDERR, "ERR: No buildpro-*.po files found in $dir\n");
    exit(1);
}

foreach ($poFiles as $po) {
    $mo = preg_replace('/\.po$/i', '.mo', $po);
    if (!is_string($mo) || $mo === '') {
        continue;
    }
    compile_po_to_mo($po, $mo);
}
