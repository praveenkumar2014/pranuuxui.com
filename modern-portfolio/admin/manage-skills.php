<?php include 'includes/config.php';

if (isset($_GET['delete'])) {
    $stmt = $db->prepare('DELETE FROM skills WHERE id = :id');
    $stmt->bindValue(':id', $_GET['delete'], SQLITE3_INTEGER);
    $stmt->execute();
    header('Location: manage-skills.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $stmt = $db->prepare('UPDATE skills SET name = :name, category = :cat, icon_url = :icon WHERE id = :id');
        $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    } else {
        $stmt = $db->prepare('INSERT INTO skills (name, category, icon_url) VALUES (:name, :cat, :icon)');
    }
    $stmt->bindValue(':name', $_POST['name'], SQLITE3_TEXT);
    $stmt->bindValue(':cat', $_POST['category'], SQLITE3_TEXT);
    $stmt->bindValue(':icon', $_POST['icon_url'], SQLITE3_TEXT);
    $stmt->execute();
    header('Location: manage-skills.php');
    exit;
}

$skills = $db->query('SELECT * FROM skills ORDER BY category, name');
$total = (int) $db->querySingle('SELECT COUNT(*) FROM skills');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skills | Madmin</title>
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
        .form-control { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; border-radius: 10px; padding: 10px; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="logo-text mb-5" style="font-size: 24px; font-weight: 800;">M<span>admin</span></div>
        <nav class="flex-grow-1">
            <a href="dashboard" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="manage-projects" class="nav-link"><i class="fas fa-briefcase"></i> Projects</a>
            <a href="manage-blogs" class="nav-link"><i class="fas fa-blog"></i> Blogs</a>
            <a href="manage-skills" class="nav-link active"><i class="fas fa-tools"></i> Skills & Icons</a>
            <a href="manage-services" class="nav-link"><i class="fas fa-magic"></i> Services</a>
            <a href="settings" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <a href="logout" class="nav-link btn-logout text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700;">Skills & Icons</h1>
                <p class="text-secondary mb-0"><?php echo $total; ?> skills in database</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#skillModal" onclick="resetForm()">+ Add Skill</button>
        </div>

        <table class="glass-table">
            <thead>
                <tr><th>Skill</th><th>Category</th><th>Icon</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $skills->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['category']); ?></span></td>
                    <td><img loading="lazy" src="<?php echo htmlspecialchars($row['icon_url']); ?>" width="24" height="24" alt=""></td>
                    <td>
                        <button class="btn btn-sm btn-outline-info me-2" onclick='editSkill(<?php echo json_encode($row); ?>)'><i class="fas fa-edit"></i></button>
                        <a href="?delete=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this skill?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="skillModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #111; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
                <form method="POST">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="modalTitle">Add Skill</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="skillId">
                        <div class="mb-3">
                            <label class="form-label">Skill Name</label>
                            <input type="text" name="name" id="skillName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" id="skillCategory" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon URL</label>
                            <input type="text" name="icon_url" id="skillIcon" class="form-control" placeholder="https://cdn.simpleicons.org/react">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100">Save Skill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('modalTitle').textContent = 'Add Skill';
            document.getElementById('skillId').value = '';
            document.getElementById('skillName').value = '';
            document.getElementById('skillCategory').value = '';
            document.getElementById('skillIcon').value = '';
        }
        function editSkill(row) {
            document.getElementById('modalTitle').textContent = 'Edit Skill';
            document.getElementById('skillId').value = row.id;
            document.getElementById('skillName').value = row.name;
            document.getElementById('skillCategory').value = row.category;
            document.getElementById('skillIcon').value = row.icon_url;
            new bootstrap.Modal(document.getElementById('skillModal')).show();
        }
    </script>
</body>
</html>
