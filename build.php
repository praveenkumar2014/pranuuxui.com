#!/usr/bin/env php
<?php
/**
 * build.php — Static Site Builder for Praveen Kumar K Portfolio
 * Converts PHP pages to static HTML with clean URLs (no .php/.html in links)
 * Run: php build.php
 */

$root = __DIR__;
$host = 'pranuuxui.com';

// Suppress header/session warnings during CLI build
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

$pages = [
    'index.php'           => 'index.html',
    'about.php'           => 'about.html',
    'services.php'        => 'services.html',
    'skills.php'          => 'skills.html',
    'portfolio.php'       => 'portfolio.html',
    'contact.php'         => 'contact.html',
    'blog.php'            => 'blog.html',
    'article.php'         => 'article.html',
    'gpay.php'            => 'gpay.html',
    'gmobile.php'         => 'gmobile.html',
    'gtresearch.php'      => 'gtresearch.html',
    'kd.php'              => 'kd.html',
    'portfolio-details.php' => 'portfolio-details.html',
    'thank-you.php'         => 'thank-you.html',
    'login.php'             => 'login.html',
    'admin.php'             => 'admin.html',
    'terms.php'             => 'terms.html',
    'privacy.php'           => 'privacy.html',
    'careers.php'           => 'careers.html',
];

$built  = 0;
$failed = 0;

echo "🚀 Building static HTML from PHP pages...\n";
echo str_repeat('─', 60) . "\n";

foreach ($pages as $php => $html) {
    $phpFile = $root . '/' . $php;

    if (!file_exists($phpFile)) {
        echo "  ⚠  SKIP   $php  (file not found)\n";
        continue;
    }

    $_SERVER['PHP_SELF']       = '/' . $php;
    $_SERVER['SCRIPT_NAME']    = '/' . $php;
    $_SERVER['REQUEST_URI']    = '/' . str_replace('.php', '', $php);
    $_SERVER['HTTP_HOST']      = $host;
    $_SERVER['SERVER_NAME']    = $host;
    $_SERVER['SERVER_PORT']    = '443';
    $_SERVER['HTTPS']          = 'on';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    ob_start();
    try {
        include $phpFile;
        $htmlContent = ob_get_clean();
    } catch (Throwable $e) {
        ob_end_clean();
        echo "  ✗ FAIL   $php  — " . $e->getMessage() . "\n";
        $failed++;
        continue;
    }

    // ── Clean URLs: strip .php and .html from all href/action/src links ──
    $htmlContent = preg_replace('/href="([^"#?]+)\.php([#?][^"]*)?"/', 'href="$1$2"', $htmlContent);
    $htmlContent = preg_replace('/href="([^"#?]+)\.html([#?][^"]*)?"/', 'href="$1$2"', $htmlContent);
    $htmlContent = preg_replace("/href='([^'#?]+)\.php([#?][^']*)?'/", "href='$1$2'", $htmlContent);
    $htmlContent = preg_replace("/href='([^'#?]+)\.html([#?][^']*)?'/", "href='$1$2'", $htmlContent);
    // Fix form action (keep mail.php — that's a server-side script path)
    // Don't strip assets/mail.php
    $htmlContent = str_replace('action="assets/mail"', 'action="assets/mail.php"', $htmlContent);

    $outPath = $root . '/' . $html;
    file_put_contents($outPath, $htmlContent);
    $kb = round(strlen($htmlContent) / 1024, 1);
    echo "  ✓ Built  $php → $html  ({$kb}KB)\n";
    $built++;
}

echo str_repeat('─', 60) . "\n";
echo "✅ Built: $built  |  ❌ Failed/Skipped: $failed\n";

if ($failed === 0) {
    echo "\n🎉 All pages built successfully! Clean URLs applied.\n";
} else {
    echo "\n⚠  Some pages failed — check errors above.\n";
    exit(1);
}
