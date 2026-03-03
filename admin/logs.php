<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Log Aktivitas</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Simple pagination could be added here, currently just limiting to last 100
                    $query = "SELECT l.*, u.username, u.role 
                              FROM log_aktivitas l 
                              JOIN users u ON l.id_user = u.id_user 
                              ORDER BY l.waktu DESC LIMIT 100";
                    $logs = query($query);
                    foreach ($logs as $log) : ?>
                    <tr>
                        <td><?= $log['waktu'] ?></td>
                        <td class="fw-bold"><?= $log['username'] ?></td>
                        <td><small class="text-muted"><?= $log['role'] ?></small></td>
                        <td><?= $log['aktivitas'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
