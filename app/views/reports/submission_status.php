<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="reports-layout">
    <!-- Sidebar Navigation -->
    <div class="reports-sidebar">
        <h6 class="sidebar-heading">
            <span>Reports</span>
        </h6>
        <ul class="reports-nav">
            <li class="reports-nav-item">
                <a class="reports-nav-link" href="<?php echo Config::APP_URL; ?>/reports">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li class="reports-nav-item">
                <a class="reports-nav-link active" href="<?php echo Config::APP_URL; ?>/reports/submission-status">
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
            <h1 class="h2">Submission Status Report</h1>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCharts()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportData()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- Status Overview Cards -->
        <div class="reports-stats">
            <div class="stat-card border-left-warning shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-warning">
                            Pending
                        </div>
                        <div class="stat-value" id="pendingCount">
                            Loading...
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
                        <div class="stat-value" id="underReviewCount">
                            Loading...
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-success shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-success">
                            Approved
                        </div>
                        <div class="stat-value" id="approvedCount">
                            Loading...
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-danger shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-danger">
                            Rejected
                        </div>
                        <div class="stat-value" id="rejectedCount">
                            Loading...
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-primary shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-primary">
                            Completed
                        </div>
                        <div class="stat-value" id="completedCount">
                            Loading...
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-secondary shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-secondary">
                            Total
                        </div>
                        <div class="stat-value" id="totalCount">
                            Loading...
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <!-- Status Distribution Pie Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6 class="chart-title">Status Distribution</h6>
                </div>
                <div class="chart-body">
                    <div class="chart-container">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Bar Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6 class="chart-title">Status Comparison</h6>
                </div>
                <div class="chart-body">
                    <div class="chart-container">
                        <canvas id="statusBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart.js configuration
Chart.defaults.font.family = 'Nunito', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = '#858796';

let statusPieChart, statusBarChart;
let statusData = {};

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load data directly from PHP instead of AJAX
    statusData = <?php echo json_encode($statusStats); ?>;
    updateStatusCards();
    createStatusCharts();
    updateStatusTable();
});

// Load status data from server (kept for compatibility)
async function loadStatusData() {
    try {
        const response = await fetch('<?php echo Config::APP_URL; ?>/reports/export-data?type=status');
        const result = await response.json();
        
        if (result.success) {
            statusData = result.data;
            updateStatusCards();
            createStatusCharts();
            updateStatusTable();
        } else {
            console.error('Error loading data:', result.error);
            showError('Failed to load status data');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Failed to load status data');
    }
}

// Update status cards
function updateStatusCards() {
    const data = statusData.data;
    const labels = statusData.labels;
    
    // Calculate totals
    const total = data.reduce((sum, value) => sum + value, 0);
    
    // Update card values
    document.getElementById('totalCount').textContent = total.toLocaleString();
    
    labels.forEach((label, index) => {
        const count = data[index];
        const statusId = label.toLowerCase().replace(' ', '') + 'Count';
        const element = document.getElementById(statusId);
        if (element) {
            element.textContent = count.toLocaleString();
        }
    });
}

// Create status charts
function createStatusCharts() {
    const data = statusData.data;
    const labels = statusData.labels;
    const colors = statusData.colors;
    
    // Pie Chart
    const pieCtx = document.getElementById('statusPieChart').getContext('2d');
    statusPieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#ffc107', // Pending
                    '#17a2b8', // Under Review
                    '#28a745', // Approved
                    '#dc3545', // Rejected
                    '#6f42c1'  // Completed
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
                callbacks: {
                    label: function(tooltipItem, data) {
                        const label = data.labels[tooltipItem.index];
                        const value = data.datasets[0].data[tooltipItem.index];
                        const total = data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 80,
        },
    });

    // Bar Chart
    const barCtx = document.getElementById('statusBarChart').getContext('2d');
    statusBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Submissions',
                data: data,
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#28a745',
                    '#dc3545',
                    '#6f42c1'
                ],
                borderColor: [
                    '#e0a800',
                    '#138496',
                    '#1e7e34',
                    '#c82333',
                    '#5a32a3'
                ],
                borderWidth: 1
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        },
    });
}

// Update status table
function updateStatusTable() {
    // This would be populated with detailed breakdown data
    // For now, we'll show a simple message
    const tableBody = document.getElementById('statusTableBody');
    tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Detailed breakdown data will be loaded here</td></tr>';
}

// Refresh charts
function refreshCharts() {
    loadStatusData();
}

// Export data
function exportData() {
    // Create CSV content
    const data = statusData.data;
    const labels = statusData.labels;
    
    let csvContent = "Status,Count,Percentage\n";
    const total = data.reduce((sum, value) => sum + value, 0);
    
    labels.forEach((label, index) => {
        const count = data[index];
        const percentage = ((count / total) * 100).toFixed(2);
        csvContent += `"${label}",${count},${percentage}%\n`;
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'submission_status_report.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    alert('Error: ' + message);
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>
