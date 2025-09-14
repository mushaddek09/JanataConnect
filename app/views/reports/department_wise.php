<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Reports</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::APP_URL; ?>/reports">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::APP_URL; ?>/reports/submission-status">
                            <i class="fas fa-tasks"></i> Submission Status
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo Config::APP_URL; ?>/reports/department-wise">
                            <i class="fas fa-building"></i> Department Wise
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::APP_URL; ?>/reports/monthly-trend">
                            <i class="fas fa-chart-line"></i> Monthly Trend
                        </a>
                    </li>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::APP_URL; ?>/reports/comprehensive">
                            <i class="fas fa-chart-bar"></i> Comprehensive
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Department-wise Report</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCharts()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportData()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Department Overview Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Departments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDepartments">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-building fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Active Departments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeDepartments">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Submissions</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSubmissions">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Top Department</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="topDepartment">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-trophy fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Department Distribution Pie Chart -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Department Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="departmentPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Comparison Bar Chart -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Department Comparison</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar pt-4 pb-2">
                                <canvas id="departmentBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Statistics Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Department Statistics</h6>
                            <div class="dropdown no-arrow">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#" onclick="filterTable('all')">All Departments</a>
                                    <a class="dropdown-item" href="#" onclick="filterTable('active')">Active Only</a>
                                    <a class="dropdown-item" href="#" onclick="filterTable('top10')">Top 10</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="departmentTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Total Submissions</th>
                                            <th>Pending</th>
                                            <th>Under Review</th>
                                            <th>Approved</th>
                                            <th>Rejected</th>
                                            <th>Completed</th>
                                            <th>Completion Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody id="departmentTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center">Loading data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

let departmentPieChart, departmentBarChart;
let departmentData = {};

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load data directly from PHP instead of AJAX
    departmentData = <?php echo json_encode($departmentBreakdown); ?>;
    updateDepartmentCards();
    createDepartmentCharts();
    updateDepartmentTable();
});

// Load department data from server (kept for compatibility)
async function loadDepartmentData() {
    try {
        const response = await fetch('<?php echo Config::APP_URL; ?>/reports/export-data?type=department');
        const result = await response.json();
        
        if (result.success) {
            departmentData = result.data;
            updateDepartmentCards();
            createDepartmentCharts();
            updateDepartmentTable();
        } else {
            console.error('Error loading data:', result.error);
            showError('Failed to load department data');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Failed to load department data');
    }
}

// Update department cards
function updateDepartmentCards() {
    const data = departmentData.data;
    const labels = departmentData.labels;
    
    // Calculate totals
    const total = data.reduce((sum, value) => sum + value, 0);
    const activeDepartments = data.filter(value => value > 0).length;
    const topDepartmentIndex = data.indexOf(Math.max(...data));
    const topDepartment = labels[topDepartmentIndex] || 'N/A';
    
    // Update card values
    document.getElementById('totalDepartments').textContent = labels.length;
    document.getElementById('activeDepartments').textContent = activeDepartments;
    document.getElementById('totalSubmissions').textContent = total.toLocaleString();
    document.getElementById('topDepartment').textContent = topDepartment;
}

// Create department charts
function createDepartmentCharts() {
    const data = departmentData.data;
    const labels = departmentData.labels;
    
    // Generate colors for departments
    const colors = generateColors(labels.length);
    
    // Pie Chart
    const pieCtx = document.getElementById('departmentPieChart').getContext('2d');
    departmentPieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
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
    const barCtx = document.getElementById('departmentBarChart').getContext('2d');
    departmentBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Submissions',
                data: data,
                backgroundColor: colors,
                borderColor: colors.map(color => color.replace('0.8', '1')),
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
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
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

// Update department table
function updateDepartmentTable() {
    // This would be populated with detailed department data
    // For now, we'll show a simple message
    const tableBody = document.getElementById('departmentTableBody');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Detailed department data will be loaded here</td></tr>';
}

// Generate colors for charts
function generateColors(count) {
    const colors = [];
    const baseColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
    ];
    
    for (let i = 0; i < count; i++) {
        colors.push(baseColors[i % baseColors.length]);
    }
    
    return colors;
}

// Filter table
function filterTable(filter) {
    // This would implement table filtering
    console.log('Filtering table by:', filter);
}

// Refresh charts
function refreshCharts() {
    loadDepartmentData();
}

// Export data
function exportData() {
    // Create CSV content
    const data = departmentData.data;
    const labels = departmentData.labels;
    
    let csvContent = "Department,Submissions,Percentage\n";
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
    a.download = 'department_wise_report.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    alert('Error: ' + message);
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>
