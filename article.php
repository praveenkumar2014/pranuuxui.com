<?php
include 'header.php';
require_once 'includes/db_fetch.php';

$id = (int) ($_GET['id'] ?? 0);
$blog = $id ? fetch_blog_by_id($id) : null;
if (!$blog) {
    http_response_code(404);
    $page_title_override = 'Article not found';
}
?>
<main>
<section class="content-box-area mt-4">
<div class="container">
<div class="row g-4">
<div class="col-xl-4"><?php include 'includes/profile_card.php'; ?></div>
<div class="col-xl-8">
<div class="card content-box-card">
<div class="card-body">
<?php if (!$blog): ?>
    <h1 class="main-title">Article not found</h1>
    <p class="text-secondary">That post may have moved. <a href="blog">Browse all articles</a>.</p>
<?php else: ?>
    <div class="meta mb-3 d-flex gap-3 small text-secondary">
        <span><i class="far fa-calendar-alt me-1"></i> <?php echo htmlspecialchars($blog['date'] ?? '', ENT_QUOTES); ?></span>
        <span><i class="far fa-folder me-1"></i> <?php echo htmlspecialchars($blog['category'] ?? '', ENT_QUOTES); ?></span>
    </div>
    <h1 class="main-title mb-4"><?php echo htmlspecialchars($blog['title'], ENT_QUOTES); ?></h1>
    <?php if (!empty($blog['image'])): ?>
    <img loading="lazy" src="<?php echo htmlspecialchars($blog['image'], ENT_QUOTES); ?>" alt="" class="img-fluid rounded-4 mb-4 w-100">
    <?php endif; ?>
    <div class="article-content text-secondary" style="line-height:1.8;">
        <?php echo $blog['content'] ?? '<p>' . htmlspecialchars($blog['excerpt'] ?? '', ENT_QUOTES) . '</p>'; ?>
    </div>
    <div class="mt-5 pt-4 border-top border-secondary border-opacity-25">
        <a href="blog" class="text-primary text-decoration-none"><i class="fas fa-arrow-left me-2"></i>Back to blog</a>
        <span class="mx-2 text-secondary">·</span>
        <a href="contact" class="text-primary text-decoration-none">Discuss a similar project</a>
    </div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</section>
</main>
<?php include 'footer.php'; ?>
