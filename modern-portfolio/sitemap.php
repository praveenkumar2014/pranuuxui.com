<?php
/**
 * Dynamic XML sitemap for SEO — projects, blogs, and core pages.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_fetch.php';

header('Content-Type: application/xml; charset=utf-8');

$base = site_base_url();
$today = date('Y-m-d');

$static = [
    ['loc' => $base . '/', 'priority' => '1.0', 'freq' => 'weekly'],
    ['loc' => $base . '/about', 'priority' => '0.9', 'freq' => 'monthly'],
    ['loc' => $base . '/services', 'priority' => '0.9', 'freq' => 'monthly'],
    ['loc' => $base . '/skills', 'priority' => '0.9', 'freq' => 'monthly'],
    ['loc' => $base . '/portfolio', 'priority' => '0.9', 'freq' => 'weekly'],
    ['loc' => $base . '/blog', 'priority' => '0.8', 'freq' => 'weekly'],
    ['loc' => $base . '/contact', 'priority' => '0.8', 'freq' => 'monthly'],
    ['loc' => $base . '/careers', 'priority' => '0.7', 'freq' => 'weekly'],
    ['loc' => $base . '/privacy', 'priority' => '0.3', 'freq' => 'yearly'],
    ['loc' => $base . '/terms', 'priority' => '0.3', 'freq' => 'yearly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($static as $u) {
    echo "  <url><loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>";
    echo "<lastmod>{$today}</lastmod><changefreq>{$u['freq']}</changefreq><priority>{$u['priority']}</priority></url>\n";
}

$db = get_db();
if ($db) {
    $proj = $db->query('SELECT id FROM projects ORDER BY id DESC LIMIT 500');
    while ($row = $proj->fetchArray(SQLITE3_ASSOC)) {
        $loc = $base . '/portfolio-details?id=' . (int) $row['id'];
        echo "  <url><loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc><lastmod>{$today}</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>\n";
    }
    $blogs = $db->query('SELECT id FROM blogs ORDER BY id DESC LIMIT 500');
    while ($row = $blogs->fetchArray(SQLITE3_ASSOC)) {
        $loc = $base . '/article?id=' . (int) $row['id'];
        echo "  <url><loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc><lastmod>{$today}</lastmod><changefreq>monthly</changefreq><priority>0.5</priority></url>\n";
    }
    $db->close();
}

echo '</urlset>';
