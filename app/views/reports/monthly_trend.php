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
                <a class="reports-nav-link active" href="<?php echo Config::APP_URL; ?>/reports/monthly-trend">
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
            <h1 class="h2">Monthly Trend Report</h1>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCharts()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportData()">
                    <i class="fas fa-download"></i> Export
                </button>
                <div class="custom-dropdown">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleDropdown()">
                        <i class="fas fa-calendar"></i> 
                        <?php 
                        $months = isset($_GET['months']) ? (int)$_GET['months'] : 12;
                        echo "Last {$months} Months";
                        ?> 
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="timePeriodDropdown">
                        <a href="#" onclick="changeTimePeriod(6)" <?php echo $months == 6 ? 'class="active"' : ''; ?>>Last 6 Months</a>
                        <a href="#" onclick="changeTimePeriod(12)" <?php echo $months == 12 ? 'class="active"' : ''; ?>>Last 12 Months</a>
                        <a href="#" onclick="changeTimePeriod(24)" <?php echo $months == 24 ? 'class="active"' : ''; ?>>Last 24 Months</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Overview Cards -->
        <div class="reports-stats">
            <div class="stat-card border-left-primary shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-primary">
                            Total Submissions
                        </div>
                        <div class="stat-value" id="totalSubmissions">
                            0
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
                            Average per Month
                        </div>
                        <div class="stat-value" id="averagePerMonth">
                            0
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-info shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-info">
                            Peak Month
                        </div>
                        <div class="stat-value" id="peakMonth">
                            N/A
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card border-left-warning shadow">
                <div class="stat-content">
                    <div class="stat-text">
                        <div class="stat-label text-warning">
                            Growth Rate
                        </div>
                        <div class="stat-value" id="growthRate">
                            0%
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <!-- Monthly Trend Line Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6 class="chart-title">Monthly Submission Trend</h6>
                </div>
                <div class="chart-body">
                    <div class="chart-container">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Distribution Pie Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6 class="chart-title">Recent Distribution</h6>
                </div>
                <div class="chart-body">
                    <div class="chart-container">
                        <canvas id="monthlyDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="card-title">Trend Analysis</h6>
            </div>
            <div class="card-body">
                <h6>Monthly Statistics:</h6>
                <ul class="list-unstyled" id="monthlyStats">
                    <li>No statistics available</li>
                </ul>
            </div>
        </div>

        <!-- Monthly Data Table -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="card-title">Monthly Data</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="monthlyTable">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Submissions</th>
                                <th>Change from Previous</th>
                                <th>Percentage Change</th>
                            </tr>
                        </thead>
                        <tbody id="monthlyTableBody">
                            <tr>
                                <td colspan="4" class="text-center">Loading data...</td>
                            </tr>
                        </tbody>
                    </table>
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

let monthlyTrendChart, monthlyDistributionChart;
let monthlyData = {};
let currentTimePeriod = 12;

// Toggle dropdown function
function toggleDropdown() {
    const dropdown = document.getElementById('timePeriodDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('timePeriodDropdown');
    const dropdownContainer = event.target.closest('.custom-dropdown');
    
    // Only close if clicking outside the dropdown container
    if (!dropdownContainer) {
        dropdown.classList.remove('show');
    }
});

// Change time period function
function changeTimePeriod(months) {
    console.log('Changing time period to:', months);
    currentTimePeriod = months;
    
    // Close dropdown
    document.getElementById('timePeriodDropdown').classList.remove('show');
    
    // Simple approach: reload page with new time period
    window.location.href = '?months=' + months;
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load data directly from PHP instead of AJAX
    monthlyData = <?php echo json_encode($monthlyStats); ?>;
    console.log('Monthly data loaded:', monthlyData);
    console.log('Data type:', typeof monthlyData);
    console.log('Data keys:', Object.keys(monthlyData || {}));
    
    if (monthlyData && monthlyData.data) {
        console.log('Data array length:', monthlyData.data.length);
        console.log('Labels array length:', monthlyData.labels ? monthlyData.labels.length : 'undefined');
    }
    
    try {
        updateMonthlyCards();
        console.log('Monthly cards updated');
        
        createMonthlyCharts();
        console.log('Monthly charts created');
        
        updateMonthlyAnalysis();
        console.log('Monthly analysis updated');
        
        updateMonthlyTable();
        console.log('Monthly table updated');
    } catch (error) {
        console.error('Error updating monthly data:', error);
        console.error('Error stack:', error.stack);
    }
});

// Load monthly data from server (removed - using direct PHP data loading instead)

// Update monthly cards
function updateMonthlyCards() {
    if (!monthlyData || !monthlyData.data || !monthlyData.labels) {
        console.error('Monthly data not available:', monthlyData);
        return;
    }
    
    const data = monthlyData.data;
    const labels = monthlyData.labels;
    
    // Calculate statistics
    const total = data.reduce((sum, value) => sum + value, 0);
    const average = data.length > 0 ? (total / data.length).toFixed(1) : 0;
    const maxIndex = data.indexOf(Math.max(...data));
    const peakMonth = labels[maxIndex] || 'N/A';
    
    // Calculate growth rate (comparing first and last months)
    const firstMonth = data[0] || 0;
    const lastMonth = data[data.length - 1] || 0;
    const growthRate = firstMonth > 0 ? (((lastMonth - firstMonth) / firstMonth) * 100).toFixed(1) : '0.0';
    
    // Update card values
    document.getElementById('totalSubmissions').textContent = total.toLocaleString();
    document.getElementById('averagePerMonth').textContent = average;
    document.getElementById('peakMonth').textContent = peakMonth;
    document.getElementById('growthRate').textContent = growthRate + '%';
}

// Create monthly charts
function createMonthlyCharts() {
    if (!monthlyData || !monthlyData.data || !monthlyData.labels) {
        console.error('Monthly data not available for charts:', monthlyData);
        return;
    }
    
    const data = monthlyData.data;
    const labels = monthlyData.labels;
    
    // Line Chart
    const lineCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    monthlyTrendChart = new Chart(lineCtx, {
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
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#4e73df',
                pointHoverBackgroundColor: '#2e59d9',
                pointHoverBorderColor: '#2e59d9',
                pointRadius: 4,
                pointHoverRadius: 6
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
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        },
    });

    // Pie Chart (showing last 6 months)
    const pieData = data.slice(-6);
    const pieLabels = labels.slice(-6);
    
    const pieCtx = document.getElementById('monthlyDistributionChart').getContext('2d');
    monthlyDistributionChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
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

// Update monthly analysis
function updateMonthlyAnalysis() {
    console.log('updateMonthlyAnalysis called with data:', monthlyData);
    if (!monthlyData || !monthlyData.data || !monthlyData.labels) {
        console.error('Monthly data not available for analysis:', monthlyData);
        return;
    }
    
    const data = monthlyData.data;
    const labels = monthlyData.labels;
    
    // Calculate monthly statistics
    const total = data.reduce((sum, value) => sum + value, 0);
    const average = (total / data.length).toFixed(1);
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const maxIndex = data.indexOf(maxValue);
    const minIndex = data.indexOf(minValue);
    
    // Calculate changes
    const changes = [];
    for (let i = 1; i < data.length; i++) {
        const change = data[i] - data[i-1];
        const percentage = data[i-1] > 0 ? ((change / data[i-1]) * 100).toFixed(1) : '0.0';
        changes.push({ change, percentage });
    }
    
    // Update monthly statistics
    const statsHtml = `
        <li><strong>Total Submissions:</strong> ${total.toLocaleString()}</li>
        <li><strong>Average per Month:</strong> ${average}</li>
        <li><strong>Highest Month:</strong> ${labels[maxIndex]} (${maxValue})</li>
        <li><strong>Lowest Month:</strong> ${labels[minIndex]} (${minValue})</li>
        <li><strong>Range:</strong> ${maxValue - minValue} submissions</li>
    `;
    
    document.getElementById('monthlyStats').innerHTML = statsHtml;
}

// Update monthly table
function updateMonthlyTable() {
    console.log('updateMonthlyTable called with data:', monthlyData);
    
    if (!monthlyData) {
        console.error('Monthly data is null or undefined');
        document.getElementById('monthlyTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger">No data available</td></tr>';
        return;
    }
    
    if (!monthlyData.data || !monthlyData.labels) {
        console.error('Monthly data structure is invalid:', monthlyData);
        document.getElementById('monthlyTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Invalid data structure</td></tr>';
        return;
    }
    
    const data = monthlyData.data;
    const labels = monthlyData.labels;
    
    console.log('Data array:', data);
    console.log('Labels array:', labels);
    
    if (data.length === 0) {
        document.getElementById('monthlyTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-warning">No monthly data found</td></tr>';
        return;
    }
    
    let tableHtml = '';
    
    for (let i = 0; i < data.length; i++) {
        const month = labels[i];
        const submissions = data[i];
        const change = i > 0 ? data[i] - data[i-1] : 0;
        const percentage = i > 0 && data[i-1] > 0 ? ((change / data[i-1]) * 100).toFixed(1) : '0.0';
        
        const changeClass = change > 0 ? 'text-success' : change < 0 ? 'text-danger' : 'text-muted';
        const changeIcon = change > 0 ? 'fa-arrow-up' : change < 0 ? 'fa-arrow-down' : 'fa-minus';
        
        tableHtml += `
            <tr>
                <td>${month}</td>
                <td>${submissions.toLocaleString()}</td>
                <td class="${changeClass}">
                    <i class="fas ${changeIcon}"></i> ${change > 0 ? '+' : ''}${change}
                </td>
                <td class="${changeClass}">${percentage}%</td>
            </tr>
        `;
    }
    
    console.log('Generated table HTML:', tableHtml);
    document.getElementById('monthlyTableBody').innerHTML = tableHtml;
}


// Refresh charts
function refreshCharts() {
    // Simply reload the page to get fresh data
    window.location.reload();
}

// Export data
function exportData() {
    // Create CSV content
    const data = monthlyData.data;
    const labels = monthlyData.labels;
    
    let csvContent = "Month,Submissions,Change,Percentage Change\n";
    
    for (let i = 0; i < data.length; i++) {
        const month = labels[i];
        const submissions = data[i];
        const change = i > 0 ? data[i] - data[i-1] : 0;
        const percentage = i > 0 && data[i-1] > 0 ? ((change / data[i-1]) * 100).toFixed(1) : '0.0';
        
        csvContent += `"${month}",${submissions},${change},${percentage}%\n`;
    }
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'monthly_trend_report.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    alert('Error: ' + message);
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>
