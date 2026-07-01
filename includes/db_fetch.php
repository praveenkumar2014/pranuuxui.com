<?php
declare(strict_types=1);

function get_db(): ?SQLite3
{
    $db_path = __DIR__ . '/../db/portfolio.db';
    if (!file_exists($db_path)) {
        return null;
    }
    return new SQLite3($db_path);
}

function fetch_projects(int $limit = 12, int $offset = 0, ?string $category = null): array
{
    $db = get_db();
    if (!$db) {
        return [];
    }
    if ($category) {
        $stmt = $db->prepare('SELECT * FROM projects WHERE category = :cat ORDER BY id DESC LIMIT :lim OFFSET :off');
        $stmt->bindValue(':cat', $category, SQLITE3_TEXT);
    } else {
        $stmt = $db->prepare('SELECT * FROM projects ORDER BY id DESC LIMIT :lim OFFSET :off');
    }
    $stmt->bindValue(':lim', $limit, SQLITE3_INTEGER);
    $stmt->bindValue(':off', $offset, SQLITE3_INTEGER);
    $res = $stmt->execute();
    $data = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }
    return $data;
}

function count_projects(?string $category = null): int
{
    $db = get_db();
    if (!$db) {
        return 0;
    }
    if ($category) {
        $stmt = $db->prepare('SELECT COUNT(*) AS c FROM projects WHERE category = :cat');
        $stmt->bindValue(':cat', $category, SQLITE3_TEXT);
        $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    } else {
        $row = $db->query('SELECT COUNT(*) AS c FROM projects')->fetchArray(SQLITE3_ASSOC);
    }
    return (int) ($row['c'] ?? 0);
}

function fetch_project_categories(): array
{
    $db = get_db();
    if (!$db) {
        return [];
    }
    $res = $db->query('SELECT DISTINCT category FROM projects WHERE category IS NOT NULL ORDER BY category');
    $cats = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $cats[] = $row['category'];
    }
    return $cats;
}

function fetch_project_by_id(int $id): ?array
{
    $db = get_db();
    if (!$db) {
        return null;
    }
    $stmt = $db->prepare('SELECT * FROM projects WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    return $row ?: null;
}

function fetch_blogs(int $limit = 12, int $offset = 0, ?string $category = null): array
{
    $db = get_db();
    if (!$db) {
        return [];
    }
    if ($category) {
        $stmt = $db->prepare('SELECT * FROM blogs WHERE category = :cat ORDER BY id DESC LIMIT :lim OFFSET :off');
        $stmt->bindValue(':cat', $category, SQLITE3_TEXT);
    } else {
        $stmt = $db->prepare('SELECT * FROM blogs ORDER BY id DESC LIMIT :lim OFFSET :off');
    }
    $stmt->bindValue(':lim', $limit, SQLITE3_INTEGER);
    $stmt->bindValue(':off', $offset, SQLITE3_INTEGER);
    $res = $stmt->execute();
    $data = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }
    return $data;
}

function count_blogs(?string $category = null): int
{
    $db = get_db();
    if (!$db) {
        return 0;
    }
    if ($category) {
        $stmt = $db->prepare('SELECT COUNT(*) AS c FROM blogs WHERE category = :cat');
        $stmt->bindValue(':cat', $category, SQLITE3_TEXT);
        $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    } else {
        $row = $db->query('SELECT COUNT(*) AS c FROM blogs')->fetchArray(SQLITE3_ASSOC);
    }
    return (int) ($row['c'] ?? 0);
}

function fetch_blog_by_id(int $id): ?array
{
    $db = get_db();
    if (!$db) {
        return null;
    }
    $stmt = $db->prepare('SELECT * FROM blogs WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    return $row ?: null;
}

function fetch_skills(): array
{
    $db = get_db();
    if (!$db) {
        return [];
    }
    $res = $db->query('SELECT * FROM skills ORDER BY category, name');
    $data = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }
    return $data;
}

function render_pagination(int $total, int $per_page, int $page, string $base_url): string
{
    $pages = (int) max(1, ceil($total / $per_page));
    if ($pages <= 1) {
        return '';
    }
    $html = '<nav class="content-pagination mt-4" aria-label="Pagination"><ul class="pagination justify-content-center flex-wrap gap-1">';
    for ($i = 1; $i <= min($pages, 12); $i++) {
        $active = $i === $page ? ' active' : '';
        $sep = str_contains($base_url, '?') ? '&' : '?';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars($base_url . $sep . 'page=' . $i, ENT_QUOTES) . '">' . $i . '</a></li>';
    }
    if ($pages > 12) {
        $html .= '<li class="page-item disabled"><span class="page-link">… ' . $pages . ' pages</span></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}
