<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="reports-layout">
    <!-- Sidebar Navigation -->
    <div class="reports-sidebar">
        <h6 class="sidebar-heading">
            <span>Reports</span>
        </h6>
        <ul class="reports-nav">
            <li class="reports-nav-item">
                <a class="reports-nav-link active" href="<?php echo Config::APP_URL; ?>/reports">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li class="reports-nav-item">
                <a class="reports-nav-link" href="<?php echo Config::APP_URL; ?>/reports/submission-status">
                    <i class="fas fa-tasks"></i> Submission Status
                </a>
            </li>
            <?php if (in_array($_SESSION['user']['role'], ['admin', 'official'])): ?>
            <li class="reports-nav-item">
                <a class="reports-nav-link" href="<?php echo Config::APP_URL; ?>/reports/department-wise">
                    <i class="fas fa-building"></i> Department Wise
                </a>
            </li>
            <?php endif; ?>
            <li class="reports-nav-item">
                <a class="reports-nav-link" href="<?php echo Config::APP_URL; ?>/reports/monthly-trend">
                    <i class="fas fa-chart-line"></i> Monthly Trend
                </a>
            </li>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <li class="reports-nav-item">
                <a class="reports-nav-link" href="<?php echo Config::APP_URL; ?>/reports/comprehensive">
                    <i class="fas fa-chart-bar"></i> Comprehensive
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="reports-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Reports Dashboard</h1>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCharts()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="reports-stats">
            <div class="stat-card border-left-primary shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-primary">
                            Total Submissions
                        </div>
                        <div class="stat-value">
                            <?php echo number_format($stats['total_submissions']); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-success shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-success">
                            Completed
                        </div>
                        <div class="stat-value">
                            <?php echo number_format($stats['completed']); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-warning shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-warning">
                            Pending
                        </div>
                        <div class="stat-value">
                            <?php echo number_format($stats['pending']); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-info shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-info">
                            Under Review
                        </div>
                        <div class="stat-value">
                            <?php echo number_format($stats['under_review']); ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <!-- Status Distribution Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6 class="chart-title">Submission Status Distribution</h6>
                </div>
                <div class="chart-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Distribution Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6 class="chart-title">Department Distribution</h6>
                </div>
                <div class="chart-body">
                    <div class="chart-container">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <?php if (in_array($_SESSION['user']['role'], ['admin', 'official']) && !empty($departmentStats)): ?>
        <!-- Department Statistics Table -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="card-title">Department Statistics</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="departmentTable">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Total</th>
                                <th>Pending</th>
                                <th>Under Review</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                                <th>Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departmentStats as $dept): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                                <td><?php echo number_format($dept['total_submissions']); ?></td>
                                <td><?php echo number_format($dept['pending']); ?></td>
                                <td><?php echo number_format($dept['under_review']); ?></td>
                                <td><?php echo number_format($dept['approved']); ?></td>
                                <td><?php echo number_format($dept['rejected']); ?></td>
                                <td><?php echo number_format($dept['completed']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($_SESSION['user']['role'] === 'citizen' && !empty($userStats)): ?>
        <!-- User Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="card-title">Your Submission Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h6>Status Breakdown:</h6>
                        <ul class="list-unstyled">
                            <li><span class="badge bg-warning text-dark">Pending:</span> <?php echo $userStats['pending']; ?></li>
                            <li><span class="badge bg-info">Under Review:</span> <?php echo $userStats['under_review']; ?></li>
                            <li><span class="badge bg-success">Approved:</span> <?php echo $userStats['approved']; ?></li>
                            <li><span class="badge bg-danger">Rejected:</span> <?php echo $userStats['rejected']; ?></li>
                            <li><span class="badge bg-primary">Completed:</span> <?php echo $userStats['completed']; ?></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <h6>Total Submissions:</h6>
                        <div class="h4 mb-0 font-weight-bold text-primary">
                            <?php echo $userStats['total_submissions']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart.js configuration
Chart.defaults.font.family = 'Nunito', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = '#858796';

// Status Chart
const statusCanvas = document.getElementById('statusChart');
if (statusCanvas) {
    const statusCtx = statusCanvas.getContext('2d');
    const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: [
            'Pending',
            'Under Review', 
            'Approved',
            'Rejected',
            'Completed'
        ],
        datasets: [{
            data: [
                <?php echo $stats['pending'] ?? 0; ?>,
                <?php echo $stats['under_review'] ?? 0; ?>,
                <?php echo $stats['approved'] ?? 0; ?>,
                <?php echo $stats['rejected'] ?? 0; ?>,
                <?php echo $stats['completed'] ?? 0; ?>
            ],
            backgroundColor: [
                '#ffc107',
                '#17a2b8', 
                '#28a745',
                '#dc3545',
                '#6f42c1'
            ],
            hoverBackgroundColor: [
                '#e0a800',
                '#138496',
                '#1e7e34',
                '#c82333',
                '#5a32a3'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: true,
            position: 'bottom'
        },
        cutoutPercentage: 80,
    },
    });
}

// Department Chart
const departmentCanvas = document.getElementById('departmentChart');
if (departmentCanvas) {
    const departmentCtx = departmentCanvas.getContext('2d');
    const departmentChart = new Chart(departmentCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php 
            $deptLabels = [];
            $deptData = [];
            $deptColors = ['#dc3545', '#ffc107', '#28a745', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'];
            $colorIndex = 0;
            foreach ($departmentStats as $dept) {
                if ($dept['total_submissions'] > 0) {
                    $deptLabels[] = "'" . addslashes($dept['department_name']) . "'";
                    $deptData[] = $dept['total_submissions'];
                    $colorIndex++;
                }
            }
            echo implode(',', $deptLabels);
            ?>
        ],
        datasets: [{
            data: [
                <?php echo implode(',', $deptData); ?>
            ],
            backgroundColor: [
                <?php 
                for ($i = 0; $i < count($deptData); $i++) {
                    echo "'" . $deptColors[$i % count($deptColors)] . "'";
                    if ($i < count($deptData) - 1) echo ',';
                }
                ?>
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: true,
            position: 'bottom'
        },
        cutoutPercentage: 80,
    },
    });
}

// Refresh charts function
function refreshCharts() {
    location.reload();
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>
