<?php
include 'header.php';
require_once 'includes/db_fetch.php';

function skill_icon_fallback_url(string $name): string
{
    $map = [
        'Next.js' => 'nextdotjs',
        'TypeScript' => 'typescript',
        'OpenAI GPT' => 'openai',
        'Tailwind CSS' => 'tailwindcss',
        'LangChain (Working Knowledge)' => 'langchain',
        'LlamaIndex (Working Knowledge)' => 'llamaindex',
    ];
    $slug = $map[$name] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '', $name));
    return "https://cdn.simpleicons.org/{$slug}/4770FF";
}

$all_skills = fetch_skills();
$category_intros = [
    'AI-Driven Design' => 'Plugins and workflows that speed up exploration without skipping critique.',
    'AI-Assisted UX & Design' => 'Designing products where models assist — but humans stay accountable.',
    'Generative AI & LLM Ecosystem' => 'Hands-on with the stacks teams actually ship: agents, RAG, tool calling, and evals.',
    'UX Strategy & Product Design' => 'Eighteen years turning messy requirements into calm, scalable experiences.',
    'UX Research' => 'Evidence before pixels — interviews, tests, and analytics that stakeholders trust.',
    'Frontend Technologies' => 'Enough code literacy to partner with engineers and protect craft in production.',
];
?>
<main>
<section class="content-box-area mt-4">
<div class="container">
<div class="row g-4">
<div class="col-xl-4"><?php include 'includes/profile_card.php'; ?></div>
<div class="col-xl-8">
<div class="card content-box-card">
<div class="card-body">
<div class="top-info">
    <div class="text">
        <h1 class="main-title">Tools I <span>Actually Use</span></h1>
        <p class="text-secondary mb-0">Organized the way recruiters and hiring managers scan a senior UX + AI portfolio — not a buzzword cloud.</p>
    </div>
    <div class="available-btn"><span><i class="fas fa-circle text-success"></i> Open to senior UX / AI design roles</span></div>
</div>

<div class="skills-page mt-4" id="skills-page">
<?php
$current_cat = '';
foreach ($all_skills as $skill):
    if ($skill['category'] !== $current_cat):
        if ($current_cat !== '') echo '</div></div>';
        $current_cat = $skill['category'];
        $intro = $category_intros[$current_cat] ?? 'Practical depth across discovery, design, and delivery.';
        echo '<div class="skill-category mb-5 reveal-up">';
        echo '<h2 class="main-common-title skill-cat-title mb-2">' . htmlspecialchars($current_cat, ENT_QUOTES) . '</h2>';
        echo '<p class="skill-cat-desc text-secondary small mb-4">' . htmlspecialchars($intro, ENT_QUOTES) . '</p>';
        echo '<div class="row g-3 skills-grid">';
    endif;
?>
    <div class="col-xl-2 col-md-3 col-sm-4 col-4">
        <div class="skill-card reveal-up text-center">
            <div class="skill-icon">
                <img loading="lazy" decoding="async"
                    src="<?php echo htmlspecialchars($skill['icon_url'] ?? '', ENT_QUOTES); ?>"
                    data-fallback="<?php echo htmlspecialchars(skill_icon_fallback_url($skill['name'] ?? ''), ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($skill['name'] ?? '', ENT_QUOTES); ?>"
                    onerror="this.onerror=null; this.src=this.dataset.fallback;">
            </div>
            <span class="small fw-bold text-secondary"><?php echo htmlspecialchars($skill['name'] ?? '', ENT_QUOTES); ?></span>
        </div>
    </div>
<?php endforeach; if ($current_cat !== '') echo '</div></div>'; ?>

<div class="work-together-slider mt-5">
    <div class="slider-main d-flex gap-4 align-items-center">
        <div class="slider-item"><a href="contact">Let's talk about your product</a><a href="contact">Let's talk about your product</a></div>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
</main>
<style>
.skill-card:hover { background: rgba(71, 112, 255, 0.05) !important; border-color: rgba(71, 112, 255, 0.2) !important; transform: translateY(-5px); }
.skill-cat-title { font-size: 18px; font-weight: 700; color: #fff; border-left: 3px solid #4770FF; padding-left: 15px; }
.skill-icon { width: 28px; height: 28px; margin: 0 auto 8px; }
.skill-icon img { width: 100%; height: 100%; object-fit: contain; }
.skill-card { height: 100%; padding: 1rem 0.5rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.06); transition: 0.25s ease; }
.skill-card span { font-size: 11px; line-height: 1.3; display: block; }
</style>
<?php include 'footer.php'; ?>
