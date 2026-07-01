<?php
declare(strict_types=1);

function get_setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $db_path = __DIR__ . '/../db/portfolio.db';
        if (file_exists($db_path)) {
            $db = new SQLite3($db_path);
            $res = $db->query('SELECT key, value FROM settings');
            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                $cache[$row['key']] = $row['value'];
            }
            $db->close();
        }
    }
    return $cache[$key] ?? $default;
}

function site_base_url(): string
{
    return rtrim(get_setting('site_url', 'https://www.pranuuxui.com'), '/');
}

function ga_measurement_id(): string
{
    return get_setting('ga_measurement_id', 'G-XXXXXXXXXX');
}

function google_meet_link(): string
{
    return get_setting('google_meet_link', 'https://meet.google.com/new');
}
