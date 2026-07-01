<?php
include 'header.php';
require_once 'includes/db_fetch.php';

$per_page = 12;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;
$total = count_blogs();
$all_blogs = fetch_blogs($per_page, $offset);
?>
<main>
<section class="content-box-area mt-4">
<div class="container">
<div class="row g-4">
<div class="col-xl-4"><?php include 'includes/profile_card.php'; ?></div>
<div class="col-xl-8">
<div class="card content-box-card">
<div class="card-body portfolio-card">
<div class="top-info">
    <div class="text">
        <h1 class="main-title">Writing on <span>UX, AI &amp; delivery</span></h1>
        <p class="text-secondary">Field notes — not AI filler. <?php echo number_format($total); ?> articles on research, agent UX, design systems, and shipping under pressure.</p>
    </div>
</div>
<div class="blog-area mt-4">
<div class="row g-4">
<?php if (empty($all_blogs)): ?>
    <div class="col-lg-12 text-center py-5"><p class="text-secondary">Articles loading soon.</p></div>
<?php else: ?>
    <?php foreach ($all_blogs as $blog): ?>
    <div class="col-lg-6">
        <article class="blog-item card h-100 bg-transparent border-0">
            <div class="image mb-3">
                <img loading="lazy" src="<?php echo htmlspecialchars($blog['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($blog['title'], ENT_QUOTES); ?>" class="img-fluid rounded-4">
            </div>
            <div class="text">
                <div class="meta mb-2 d-flex gap-3 small text-secondary">
                    <span><i class="far fa-calendar-alt me-1"></i> <?php echo htmlspecialchars($blog['date'] ?? '', ENT_QUOTES); ?></span>
                    <span><i class="far fa-folder me-1"></i> <?php echo htmlspecialchars($blog['category'] ?? '', ENT_QUOTES); ?></span>
                </div>
                <h2 class="title h5 mb-2"><a href="article?id=<?php echo (int) $blog['id']; ?>" class="text-white text-decoration-none"><?php echo htmlspecialchars($blog['title'], ENT_QUOTES); ?></a></h2>
                <p class="small text-secondary"><?php echo htmlspecialchars($blog['excerpt'] ?? '', ENT_QUOTES); ?></p>
                <a href="article?id=<?php echo (int) $blog['id']; ?>" class="link-btn small text-primary text-decoration-none">Read article <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </article>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<?php echo render_pagination($total, $per_page, $page, 'blog'); ?>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
</main>
<?php include 'footer.php'; ?>
