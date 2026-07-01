<?php
include 'header.php';
require_once 'includes/db_fetch.php';

$per_page = 24;
$page = max(1, (int) ($_GET['page'] ?? 1));
$category = isset($_GET['category']) ? trim((string) $_GET['category']) : null;
if ($category === '') {
    $category = null;
}
$offset = ($page - 1) * $per_page;
$total = count_projects($category);
$all_projects = fetch_projects($per_page, $offset, $category);
$categories = fetch_project_categories();
$base_url = 'portfolio' . ($category ? '?category=' . urlencode($category) : '');
?>
<main>
<section class="content-box-area mt-4">
<div class="container">
<div class="row g-4">
<div class="col-xl-4"><?php include 'includes/profile_card.php'; ?></div>
<div class="col-xl-8">
<div class="card content-box-card">
<div class="card-body portUXUI-card">
<div class="top-info mb-4">
    <div class="text">
        <h1 class="main-title">Selected <span>Work</span> <small class="text-secondary fs-6">(<?php echo number_format($total); ?> case studies)</small></h1>
        <p>Enterprise UX, AI copilots, design systems, and product strategy — informed by Behance craft and ThemeForest-level polish, grounded in real delivery constraints.</p>
    </div>
</div>

<?php if ($categories): ?>
<div class="portfolio-filters mb-4 d-flex flex-wrap gap-2">
    <a href="portfolio" class="btn btn-sm <?php echo $category ? 'btn-outline-secondary' : 'btn-primary'; ?>">All</a>
    <?php foreach (array_slice($categories, 0, 10) as $cat): ?>
    <a href="portfolio?category=<?php echo urlencode($cat); ?>" class="btn btn-sm <?php echo $category === $cat ? 'btn-primary' : 'btn-outline-secondary'; ?>"><?php echo htmlspecialchars($cat, ENT_QUOTES); ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="portUXUI-area">
    <div class="row g-4 parent-container">
    <?php if (empty($all_projects)): ?>
        <div class="col-12 text-center py-5"><p class="text-secondary">No projects in this category yet.</p></div>
    <?php else: ?>
        <?php foreach ($all_projects as $p): ?>
        <div class="col-lg-6 portfolio-item-wrapper">
            <div class="portUXUI-item">
                <div class="image">
                    <img loading="lazy" src="<?php echo htmlspecialchars($p['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($p['title'], ENT_QUOTES); ?>" class="img-fluid w-100">
                    <a href="<?php echo htmlspecialchars($p['image'], ENT_QUOTES); ?>" class="gallery-popup full-image-preview parent-container" aria-label="Preview image">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 4.167v11.666M4.167 10h11.666"></path></svg>
                    </a>
                </div>
                <div class="text">
                    <div class="info">
                        <a href="portfolio-details?id=<?php echo (int) $p['id']; ?>" class="title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES); ?></a>
                        <p class="subtitle"><?php echo htmlspecialchars($p['category'] ?? '', ENT_QUOTES); ?></p>
                    </div>
                    <div class="visite-btn">
                        <a href="portfolio-details?id=<?php echo (int) $p['id']; ?>">View case study
                            <svg class="arrow-up" width="14" height="15" viewBox="0 0 14 15" fill="none"><path d="M9.91634 4.5835L4.08301 10.4168" stroke-linecap="round" stroke-linejoin="round"></path><path d="M4.66699 4.5835H9.91699V9.8335" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>

<?php echo render_pagination($total, $per_page, $page, $base_url); ?>

</div>
</div>
</div>
</div>
</div>
</section>
</main>
<?php include 'footer.php'; ?>
