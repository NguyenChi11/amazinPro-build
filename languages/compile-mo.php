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

$po = $argv[1];
$mo = $argv[2];
$n = compile_mo(parse_po(file_get_contents($po)), $mo);
echo "OK: $n strings -> $mo\n";
