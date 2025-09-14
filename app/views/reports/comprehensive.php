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
                        <a class="nav-link" href="<?php echo Config::APP_URL; ?>/reports/department-wise">
                            <i class="fas fa-building"></i> Department Wise
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::APP_URL; ?>/reports/monthly-trend">
                            <i class="fas fa-chart-line"></i> Monthly Trend
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo Config::APP_URL; ?>/reports/comprehensive">
                            <i class="fas fa-chart-bar"></i> Comprehensive
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Comprehensive Report</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCharts()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportData()">
                            <i class="fas fa-download"></i> Export All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="printReport()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>

            <!-- Executive Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
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

                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Completed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedSubmissions">
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

                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingSubmissions">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Departments</div>
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

                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        High Priority</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="highPrioritySubmissions">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Completion Rate</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="completionRate">
                                        Loading...
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row">
                <!-- Status Distribution -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Status Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Priority Distribution -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Priority Distribution</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="priorityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Distribution -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Departments</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row">
                <!-- Monthly Trend -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Monthly Trend</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area pt-4 pb-2">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Performance Metrics</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Completion Rate</span>
                                    <span id="completionRateValue">0%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" id="completionRateBar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Response Rate</span>
                                    <span id="responseRateValue">0%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" id="responseRateBar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Efficiency</span>
                                    <span id="efficiencyValue">0%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" id="efficiencyBar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Performance Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Department Performance</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="departmentTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Total</th>
                                            <th>Completed</th>
                                            <th>Pending</th>
                                            <th>Completion Rate</th>
                                            <th>Avg. Response Time</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody id="departmentTableBody">
                                        <tr>
                                            <td colspan="7" class="text-center">Loading data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Report -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Executive Summary</h6>
                        </div>
                        <div class="card-body">
                            <div id="executiveSummary">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Loading executive summary...
                                </div>
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

let statusChart, priorityChart, departmentChart, monthlyTrendChart;
let comprehensiveData = {};

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadComprehensiveData();
});

// Load comprehensive data from server
async function loadComprehensiveData() {
    try {
        // Load all data types
        const [statusResponse, priorityResponse, departmentResponse, monthlyResponse] = await Promise.all([
            fetch('<?php echo Config::APP_URL; ?>/reports/export-data?type=status'),
            fetch('<?php echo Config::APP_URL; ?>/reports/export-data?type=priority'),
            fetch('<?php echo Config::APP_URL; ?>/reports/export-data?type=department'),
            fetch('<?php echo Config::APP_URL; ?>/reports/export-data?type=monthly')
        ]);

        const [statusResult, priorityResult, departmentResult, monthlyResult] = await Promise.all([
            statusResponse.json(),
            priorityResponse.json(),
            priorityResponse.json(),
            monthlyResponse.json()
        ]);

        if (statusResult.success && priorityResult.success && departmentResult.success && monthlyResult.success) {
            comprehensiveData = {
                status: statusResult.data,
                priority: priorityResult.data,
                department: departmentResult.data,
                monthly: monthlyResult.data
            };
            
            updateSummaryCards();
            createAllCharts();
            updatePerformanceMetrics();
            updateDepartmentTable();
            updateExecutiveSummary();
        } else {
            console.error('Error loading data');
            showError('Failed to load comprehensive data');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Failed to load comprehensive data');
    }
}

// Update summary cards
function updateSummaryCards() {
    const statusData = comprehensiveData.status.data;
    const total = statusData.reduce((sum, value) => sum + value, 0);
    const completed = statusData[2] || 0; // Approved + Completed
    const pending = statusData[0] || 0;
    const highPriority = comprehensiveData.priority.data[0] || 0;
    
    // Calculate completion rate
    const completionRate = total > 0 ? ((completed / total) * 100).toFixed(1) : '0.0';
    
    // Update card values
    document.getElementById('totalSubmissions').textContent = total.toLocaleString();
    document.getElementById('completedSubmissions').textContent = completed.toLocaleString();
    document.getElementById('pendingSubmissions').textContent = pending.toLocaleString();
    document.getElementById('highPrioritySubmissions').textContent = highPriority.toLocaleString();
    document.getElementById('completionRate').textContent = completionRate + '%';
    
    // Department count
    const departmentCount = comprehensiveData.department.labels.length;
    document.getElementById('totalDepartments').textContent = departmentCount;
}

// Create all charts
function createAllCharts() {
    createStatusChart();
    createPriorityChart();
    createDepartmentChart();
    createMonthlyTrendChart();
}

// Create status chart
function createStatusChart() {
    const data = comprehensiveData.status.data;
    const labels = comprehensiveData.status.labels;
    
    const ctx = document.getElementById('statusChart').getContext('2d');
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545', '#6f42c1'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: true, position: 'bottom' },
            cutoutPercentage: 80,
        },
    });
}

// Create priority chart
function createPriorityChart() {
    const data = comprehensiveData.priority.data;
    const labels = comprehensiveData.priority.labels;
    
    const ctx = document.getElementById('priorityChart').getContext('2d');
    priorityChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: true, position: 'bottom' },
            cutoutPercentage: 80,
        },
    });
}

// Create department chart
function createDepartmentChart() {
    const data = comprehensiveData.department.data.slice(0, 5); // Top 5 departments
    const labels = comprehensiveData.department.labels.slice(0, 5);
    
    const ctx = document.getElementById('departmentChart').getContext('2d');
    departmentChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: true, position: 'bottom' },
            cutoutPercentage: 80,
        },
    });
}

// Create monthly trend chart
function createMonthlyTrendChart() {
    const data = comprehensiveData.monthly.data;
    const labels = comprehensiveData.monthly.labels;
    
    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
    monthlyTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Submissions',
                data: data,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { maxRotation: 45, minRotation: 45 } }
            },
            plugins: { legend: { display: false } },
        },
    });
}

// Update performance metrics
function updatePerformanceMetrics() {
    const statusData = comprehensiveData.status.data;
    const total = statusData.reduce((sum, value) => sum + value, 0);
    const completed = statusData[2] || 0;
    const underReview = statusData[1] || 0;
    
    const completionRate = total > 0 ? ((completed / total) * 100).toFixed(1) : '0.0';
    const responseRate = total > 0 ? (((completed + underReview) / total) * 100).toFixed(1) : '0.0';
    const efficiency = total > 0 ? ((completed / (completed + statusData[0])) * 100).toFixed(1) : '0.0';
    
    // Update values
    document.getElementById('completionRateValue').textContent = completionRate + '%';
    document.getElementById('responseRateValue').textContent = responseRate + '%';
    document.getElementById('efficiencyValue').textContent = efficiency + '%';
    
    // Update progress bars
    document.getElementById('completionRateBar').style.width = completionRate + '%';
    document.getElementById('responseRateBar').style.width = responseRate + '%';
    document.getElementById('efficiencyBar').style.width = efficiency + '%';
}

// Update department table
function updateDepartmentTable() {
    // This would be populated with detailed department performance data
    const tableBody = document.getElementById('departmentTableBody');
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Detailed department performance data will be loaded here</td></tr>';
}

// Update executive summary
function updateExecutiveSummary() {
    const statusData = comprehensiveData.status.data;
    const total = statusData.reduce((sum, value) => sum + value, 0);
    const completed = statusData[2] || 0;
    const pending = statusData[0] || 0;
    const completionRate = total > 0 ? ((completed / total) * 100).toFixed(1) : '0.0';
    
    const summaryHtml = `
        <div class="row">
            <div class="col-md-6">
                <h6>Key Metrics:</h6>
                <ul class="list-unstyled">
                    <li><strong>Total Submissions:</strong> ${total.toLocaleString()}</li>
                    <li><strong>Completion Rate:</strong> ${completionRate}%</li>
                    <li><strong>Pending Submissions:</strong> ${pending.toLocaleString()}</li>
                    <li><strong>Active Departments:</strong> ${comprehensiveData.department.labels.length}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Recommendations:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Focus on reducing pending submissions</li>
                    <li><i class="fas fa-check text-success"></i> Improve response times for high-priority items</li>
                    <li><i class="fas fa-check text-success"></i> Monitor department performance regularly</li>
                    <li><i class="fas fa-check text-success"></i> Implement automated status updates</li>
                </ul>
            </div>
        </div>
    `;
    
    document.getElementById('executiveSummary').innerHTML = summaryHtml;
}

// Refresh charts
function refreshCharts() {
    loadComprehensiveData();
}

// Export data
function exportData() {
    // Create comprehensive CSV
    let csvContent = "Report Type,Data\n";
    csvContent += "Status Distribution," + comprehensiveData.status.data.join(',') + "\n";
    csvContent += "Priority Distribution," + comprehensiveData.priority.data.join(',') + "\n";
    csvContent += "Department Distribution," + comprehensiveData.department.data.join(',') + "\n";
    csvContent += "Monthly Trend," + comprehensiveData.monthly.data.join(',') + "\n";
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'comprehensive_report.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Print report
function printReport() {
    window.print();
}

// Show error message
function showError(message) {
    alert('Error: ' + message);
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>
