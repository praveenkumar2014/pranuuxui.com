<?php include 'includes/config.php';

$keys = [
    'site_url' => 'Site URL',
    'contact_email' => 'Contact Email',
    'ga_measurement_id' => 'Google Analytics ID',
    'google_meet_link' => 'Google Meet Link',
    'cal_username' => 'Cal.com Username',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($keys as $key => $label) {
        if (isset($_POST[$key])) {
            $stmt = $db->prepare('INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :val)');
            $stmt->bindValue(':key', $key, SQLITE3_TEXT);
            $stmt->bindValue(':val', trim($_POST[$key]), SQLITE3_TEXT);
            $stmt->execute();
        }
    }
    $saved = true;
}

$settings = [];
$res = $db->query('SELECT key, value FROM settings');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | Madmin</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/glassy.css">
    <link rel="stylesheet" href="../assets/fontawsome/css/all.min.css">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: #080808; color: #fff; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); position: fixed; height: 100vh; background: rgba(255, 255, 255, 0.02); border-right: 1px solid rgba(255, 255, 255, 0.05); padding: 30px 20px; backdrop-filter: blur(20px); }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; max-width: 900px; }
        .nav-link { color: #888; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background: rgba(71, 112, 255, 0.1); color: #4770FF; }
        .form-control, .form-control:focus { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; border-radius: 10px; padding: 12px; box-shadow: none; }
        .settings-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 32px; }
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
            <a href="manage-services" class="nav-link"><i class="fas fa-magic"></i> Services</a>
            <a href="settings" class="nav-link active"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <a href="logout" class="nav-link btn-logout text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1 style="font-size: 28px; font-weight: 700;" class="mb-4">Site Settings</h1>

        <?php if (!empty($saved)): ?>
            <div class="alert alert-success mb-4" style="background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); color: #6ee7b7;">Settings saved successfully.</div>
        <?php endif; ?>

        <div class="settings-card">
            <form method="POST">
                <?php foreach ($keys as $key => $label): ?>
                <div class="mb-4">
                    <label class="form-label text-secondary small"><?php echo htmlspecialchars($label); ?></label>
                    <input type="text" name="<?php echo htmlspecialchars($key); ?>" class="form-control"
                           value="<?php echo htmlspecialchars($settings[$key] ?? ''); ?>">
                </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius: 10px;">Save Settings</button>
            </form>
        </div>
    </div>
</body>
</html>
