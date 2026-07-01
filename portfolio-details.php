<?php
include 'header.php';
require_once 'includes/db_fetch.php';

$id = (int) ($_GET['id'] ?? 0);
$project = $id ? fetch_project_by_id($id) : null;
?>
<main>
<section class="content-box-area mt-4">
<div class="container">
<div class="row g-4">
<div class="col-xl-4"><?php include 'includes/profile_card.php'; ?></div>
<div class="col-xl-8">
<div class="card content-box-card">
<div class="card-body portUXUI-card">
<?php if (!$project): ?>
    <h1 class="main-title">Case study not found</h1>
    <p class="text-secondary"><a href="portfolio">Return to portfolio</a></p>
<?php else: ?>
    <div class="portUXUI-details-area">
        <div class="main-image mb-4">
            <img loading="lazy" src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES); ?>" class="img-fluid rounded-4 w-100">
        </div>
        <span class="badge bg-primary bg-opacity-25 text-primary mb-3"><?php echo htmlspecialchars($project['category'] ?? 'UX Project', ENT_QUOTES); ?></span>
        <h1 class="main-title mb-3"><?php echo htmlspecialchars($project['title'], ENT_QUOTES); ?></h1>
        <div class="portUXUI-details-text text-secondary" style="line-height:1.8;">
            <p><?php echo htmlspecialchars($project['description'] ?? 'End-to-end UX engagement covering research, IA, UI, and developer handoff.', ENT_QUOTES); ?></p>
            <h3 class="h5 text-white mt-4">What I did</h3>
            <ul>
                <li>Stakeholder workshops and journey mapping</li>
                <li>Wireframes → high-fidelity UI in Figma</li>
                <li>Usability validation before development lock-in</li>
                <li>Design specs aligned with WCAG 2.2</li>
            </ul>
        </div>
        <div class="mt-4 d-flex flex-wrap gap-3">
            <a href="contact" class="btn btn-call">Start a similar project</a>
            <a href="portfolio" class="btn btn-outline-secondary">More work</a>
        </div>
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
