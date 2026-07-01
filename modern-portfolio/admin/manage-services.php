<?php include 'includes/config.php';

$db->exec("CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    icon TEXT,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

if (isset($_GET['delete'])) {
    $stmt = $db->prepare('DELETE FROM services WHERE id = :id');
    $stmt->bindValue(':id', $_GET['delete'], SQLITE3_INTEGER);
    $stmt->execute();
    header('Location: manage-services.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $stmt = $db->prepare('UPDATE services SET title = :title, icon = :icon, description = :desc WHERE id = :id');
        $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    } else {
        $stmt = $db->prepare('INSERT INTO services (title, icon, description) VALUES (:title, :icon, :desc)');
    }
    $stmt->bindValue(':title', $_POST['title'], SQLITE3_TEXT);
    $stmt->bindValue(':icon', $_POST['icon'], SQLITE3_TEXT);
    $stmt->bindValue(':desc', $_POST['description'], SQLITE3_TEXT);
    $stmt->execute();
    header('Location: manage-services.php');
    exit;
}

$count = (int) $db->querySingle('SELECT COUNT(*) FROM services');
if ($count === 0) {
    $defaults = [
        ['UX Strategy & Research', 'fas fa-search', 'Discovery workshops, user interviews, and journey mapping for AI products.'],
        ['AI Agentic UX Design', 'fas fa-robot', 'Conversational flows, tool-calling UX, and human-in-the-loop patterns.'],
        ['Design Systems', 'fas fa-layer-group', 'Scalable component libraries with accessibility baked in.'],
        ['Prototyping & Validation', 'fas fa-flask', 'High-fidelity Figma prototypes tested before engineering sprints.'],
        ['Product Design Leadership', 'fas fa-users', 'Cross-functional alignment for fintech, health, and SaaS teams.'],
        ['Design Ops & Handoff', 'fas fa-code-branch', 'Dev-ready specs, tokens, and QA for shipped quality.'],
    ];
    foreach ($defaults as $row) {
        $stmt = $db->prepare('INSERT INTO services (title, icon, description) VALUES (?, ?, ?)');
        $stmt->bindValue(1, $row[0], SQLITE3_TEXT);
        $stmt->bindValue(2, $row[1], SQLITE3_TEXT);
        $stmt->bindValue(3, $row[2], SQLITE3_TEXT);
        $stmt->execute();
    }
}

$services = $db->query('SELECT * FROM services ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services | Madmin</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/glassy.css">
    <link rel="stylesheet" href="../assets/fontawsome/css/all.min.css">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: #080808; color: #fff; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); position: fixed; height: 100vh; background: rgba(255, 255, 255, 0.02); border-right: 1px solid rgba(255, 255, 255, 0.05); padding: 30px 20px; backdrop-filter: blur(20px); }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; }
        .nav-link { color: #888; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background: rgba(71, 112, 255, 0.1); color: #4770FF; }
        .glass-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .glass-table tr { background: rgba(255,255,255,0.02); }
        .glass-table td, .glass-table th { padding: 15px; border: 1px solid rgba(255,255,255,0.05); border-width: 1px 0; }
        .glass-table td:first-child, .glass-table th:first-child { border-left-width: 1px; border-radius: 12px 0 0 12px; }
        .glass-table td:last-child, .glass-table th:last-child { border-right-width: 1px; border-radius: 0 12px 12px 0; }
        .form-control, .form-control:focus { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; border-radius: 10px; padding: 10px; box-shadow: none; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="logo-text mb-5" style="font-size: 24px; font-weight: 800;">M<span>admin</span></div>
        <nav class="flex-grow-1">
            <a href="dashboard" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="manage-projects" class="nav-link"><i class="fas fa-briefcase"></i> Projects</a>
            <a href="manage-blogs" class="nav-link"><i class="fas fa-blog"></i> Blogs</a>
            <a href="manage-skills" class="nav-link"><i class="fas fa-tools"></i> Skills & Icons</a>
            <a href="manage-services" class="nav-link active"><i class="fas fa-magic"></i> Services</a>
            <a href="settings" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <a href="logout" class="nav-link btn-logout text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-size: 28px; font-weight: 700;">Manage Services</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="resetForm()">+ Add Service</button>
        </div>

        <table class="glass-table">
            <thead><tr><th>Service</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($row = $services->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><i class="<?php echo htmlspecialchars($row['icon']); ?> me-2 text-primary"></i><?php echo htmlspecialchars($row['title']); ?></td>
                    <td class="text-secondary"><?php echo htmlspecialchars(mb_strimwidth($row['description'] ?? '', 0, 80, '…')); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-info me-2" onclick='editService(<?php echo json_encode($row); ?>)'><i class="fas fa-edit"></i></button>
                        <a href="?delete=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this service?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: #111; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
                <form method="POST">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="modalTitle">Add Service</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="serviceId">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="serviceTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon class (Font Awesome)</label>
                            <input type="text" name="icon" id="serviceIcon" class="form-control" placeholder="fas fa-magic">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="serviceDesc" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100">Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('modalTitle').textContent = 'Add Service';
            ['serviceId','serviceTitle','serviceIcon','serviceDesc'].forEach(id => document.getElementById(id).value = '');
        }
        function editService(row) {
            document.getElementById('modalTitle').textContent = 'Edit Service';
            document.getElementById('serviceId').value = row.id;
            document.getElementById('serviceTitle').value = row.title;
            document.getElementById('serviceIcon').value = row.icon || '';
            document.getElementById('serviceDesc').value = row.description || '';
            new bootstrap.Modal(document.getElementById('serviceModal')).show();
        }
    </script>
</body>
</html>
