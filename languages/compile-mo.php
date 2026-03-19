<?php
function parse_po(string $content): array
{
    $entries = [];
    $content = str_replace("\r\n", "\n", $content);
    $content = str_replace("\r", "\n", $content);
    $blocks  = preg_split('/\n\n+/', $content);
    foreach ($blocks as $block) {
        $block = trim($block);
        if (empty($block)) continue;
        $msgctxt = null;
        $msgid = null;
        $msgid_plural = null;
        $msgstr = null;
        $msgstr_plural = [];
        $state = null;
        $pluralIndex = null;
        foreach (explode("\n", $block) as $line) {
            $line = trim($line);
            if (preg_match('/^#/', $line)) {
                $state = null;
                $pluralIndex = null;
                continue;
            }
            if (preg_match('/^msgctxt\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $state = 'msgctxt';
                $pluralIndex = null;
                $msgctxt = $m[1];
            } elseif (preg_match('/^msgid_plural\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $state = 'msgid_plural';
                $pluralIndex = null;
                $msgid_plural = $m[1];
            } elseif (preg_match('/^msgid\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $state = 'msgid';
                $pluralIndex = null;
                $msgid = $m[1];
            } elseif (preg_match('/^msgstr\[(\d+)\]\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $state = 'msgstr_plural';
                $pluralIndex = (int) $m[1];
                $msgstr_plural[$pluralIndex] = $m[2];
            } elseif (preg_match('/^msgstr\s+"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                $state = 'msgstr';
                $pluralIndex = null;
                $msgstr = $m[1];
            } elseif (preg_match('/^"((?:[^"\\\\]|\\\\.)*)"$/', $line, $m)) {
                if ($state === 'msgctxt') {
                    $msgctxt .= $m[1];
                } elseif ($state === 'msgid') {
                    $msgid .= $m[1];
                } elseif ($state === 'msgid_plural') {
                    $msgid_plural .= $m[1];
                } elseif ($state === 'msgstr') {
                    $msgstr .= $m[1];
                } elseif ($state === 'msgstr_plural' && $pluralIndex !== null) {
                    $msgstr_plural[$pluralIndex] = ($msgstr_plural[$pluralIndex] ?? '') . $m[1];
                }
            }
        }
        if ($msgid === null) {
            continue;
        }

        $d_ctx = $msgctxt !== null ? stripcslashes($msgctxt) : null;
        $d_id = stripcslashes($msgid);

        $keyBase = $d_ctx !== null ? ($d_ctx . "\x04" . $d_id) : $d_id;

        if ($msgid_plural !== null) {
            $d_plural = stripcslashes($msgid_plural);
            $key = $keyBase . "\0" . $d_plural;

            if (!empty($msgstr_plural)) {
                ksort($msgstr_plural);
            }
            $parts = [];
            $hasAny = false;
            foreach ($msgstr_plural as $idx => $raw) {
                $decoded = stripcslashes((string) $raw);
                $parts[] = $decoded;
                if ($decoded !== '') {
                    $hasAny = true;
                }
            }
            $value = implode("\0", $parts);

            if ($d_id === '' || $hasAny) {
                $entries[$key] = $value;
            }
            continue;
        }

        if ($msgstr === null) {
            continue;
        }

        $d_str = stripcslashes($msgstr);
        if ($d_id === '' || $d_str !== '') {
            $entries[$keyBase] = $d_str;
        }
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
    // Standard GNU MO header. We don't emit a hash table.
    $mo  = pack('V', 0x950412de) . pack('V', 0) . pack('V', $n);
    $mo .= pack('V', $ids_off) . pack('V', $strs_off) . pack('V', 0) . pack('V', 0);
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
