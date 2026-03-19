<?php

/**
 * Sync PO files to include all msgids from a POT file.
 * - Keeps existing PO entries intact.
 * - Appends missing entries using blocks from POT (with references/comments).
 *
 * Usage:
 *   php sync-po-with-pot.php <pot> <po1> [po2 ...]
 *
 * Example:
 *   php sync-po-with-pot.php buildpro.pot buildpro-vi.po buildpro-vi_VN.po
 */

declare(strict_types=1);

function read_blocks(string $path): array
{
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Failed to read: {$path}");
    }
    $content = str_replace("\r\n", "\n", $content);
    $content = str_replace("\r", "\n", $content);

    $blocks = preg_split("/\n\n+/", $content) ?: [];
    $out = [];
    foreach ($blocks as $b) {
        $b = trim($b);
        if ($b === '') {
            continue;
        }
        $out[] = $b;
    }
    return $out;
}

function extract_msgid(string $block): ?string
{
    if (!preg_match('/^msgid\s+"((?:[^"\\\\]|\\\\.)*)"$/m', $block, $m)) {
        return null;
    }
    return stripcslashes($m[1]);
}

function pot_block_map(string $potPath): array
{
    $map = [];
    foreach (read_blocks($potPath) as $block) {
        $msgid = extract_msgid($block);
        if ($msgid === null || $msgid === '') {
            continue; // skip header and malformed
        }
        // Ensure POT blocks end with msgstr ""
        if (!preg_match('/^msgstr\s+""$/m', $block)) {
            $block .= "\nmsgstr \"\"";
        }
        $map[$msgid] = $block;
    }
    return $map;
}

function po_msgid_set(string $poPath): array
{
    $set = [];
    foreach (read_blocks($poPath) as $block) {
        $msgid = extract_msgid($block);
        if ($msgid === null) {
            continue;
        }
        $set[$msgid] = true;
    }
    return $set;
}

function sync_one_po(string $potPath, string $poPath): int
{
    $potBlocks = pot_block_map($potPath);
    $existing = po_msgid_set($poPath);

    $missing = [];
    foreach ($potBlocks as $msgid => $block) {
        if (!isset($existing[$msgid])) {
            $missing[$msgid] = $block;
        }
    }

    if (empty($missing)) {
        echo "OK: {$poPath} already contains all POT msgids\n";
        return 0;
    }

    // Append missing blocks to the end of the PO.
    $original = file_get_contents($poPath);
    if ($original === false) {
        throw new RuntimeException("Failed to read: {$poPath}");
    }
    $original = rtrim(str_replace("\r\n", "\n", $original));

    $append = "\n\n";
    foreach ($missing as $block) {
        $append .= $block . "\n\n";
    }

    file_put_contents($poPath, $original . $append);
    echo "UPDATED: {$poPath} (+" . count($missing) . " entries)\n";
    return count($missing);
}

if ($argc < 3) {
    fwrite(STDERR, "Usage: php sync-po-with-pot.php <pot> <po1> [po2 ...]\n");
    exit(1);
}

$pot = (string) $argv[1];
if (!is_file($pot)) {
    // allow running from languages/ directory
    $potAlt = __DIR__ . DIRECTORY_SEPARATOR . $pot;
    if (is_file($potAlt)) {
        $pot = $potAlt;
    }
}

$totalAdded = 0;
for ($i = 2; $i < $argc; $i++) {
    $po = (string) $argv[$i];
    if (!is_file($po)) {
        $poAlt = __DIR__ . DIRECTORY_SEPARATOR . $po;
        if (is_file($poAlt)) {
            $po = $poAlt;
        }
    }
    if (!is_file($po)) {
        fwrite(STDERR, "ERR: PO not found: {$argv[$i]}\n");
        continue;
    }
    $totalAdded += sync_one_po($pot, $po);
}

echo "DONE: total added entries = {$totalAdded}\n";
