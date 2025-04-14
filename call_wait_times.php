<?php
require_once('config.php');
require_once('includes/functions.php');

// Ensure user is logged in
requireLogin();

// Get date range parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get daily statistics for the date range
$stats = getCallStats('daily', $start_date, $end_date);

// Define wait time thresholds (in seconds)
$thresholds = [
    'excellent' => 20,
    'good' => 40,
    'fair' => 60,
    'poor' => 120
];

require_once('includes/header.php');
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-2xl leading-6 font-medium text-gray-900">
            Call Wait Times Analysis
        </h3>
        <div class="mt-3 sm:mt-0 sm:ml-4">
            <form class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-700">From:</label>
                    <input type="date" 
                           name="start_date" 
                           value="<?php echo htmlspecialchars($start_date); ?>"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-700">To:</label>
                    <input type="date" 
                           name="end_date" 
                           value="<?php echo htmlspecialchars($end_date); ?>"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Apply
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Average Wait Time -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Average Wait Time</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    <?php 
                    $avg_wait = array_sum(array_column($stats, 'avg_duration')) / count($stats);
                    $wait_color = 'text-gray-900';
                    if ($avg_wait <= $thresholds['excellent']) $wait_color = 'text-green-600';
                    elseif ($avg_wait <= $thresholds['good']) $wait_color = 'text-blue-600';
                    elseif ($avg_wait <= $thresholds['fair']) $wait_color = 'text-yellow-600';
                    else $wait_color = 'text-red-600';
                    echo "<span class='{$wait_color}'>" . formatDuration($avg_wait) . '</span>';
                    ?>
                </dd>
            </div>
        </div>

        <!-- Longest Wait Time -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Peak Wait Time</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    <?php 
                    $peak_wait = max(array_column($stats, 'avg_duration'));
                    echo formatDuration($peak_wait);
                    ?>
                </dd>
            </div>
        </div>

        <!-- Best Performance Day -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Best Performance Day</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    <?php 
                    $best_day = array_reduce($stats, function($carry, $item) {
                        if (!$carry || $item['avg_duration'] < $carry['avg_duration']) {
                            return $item;
                        }
                        return $carry;
                    });
                    echo date('M j', strtotime($best_day['date']));
                    ?>
                </dd>
            </div>
        </div>

        <!-- Days Within Target -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Days Within Target</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    <?php 
                    $days_within_target = array_reduce($stats, function($carry, $item) use ($thresholds) {
                        return $carry + ($item['avg_duration'] <= $thresholds['good'] ? 1 : 0);
                    }, 0);
                    $total_days = count($stats);
                    echo $days_within_target . '/' . $total_days;
                    ?>
                </dd>
            </div>
        </div>
    </div>

    <!-- Wait Time Distribution -->
    <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Wait Time Distribution</h4>
            <div class="space-y-4">
                <?php
                $categories = [
                    'excellent' => ['label' => 'Excellent (0-20s)', 'color' => 'bg-green-600'],
                    'good' => ['label' => 'Good (21-40s)', 'color' => 'bg-blue-600'],
                    'fair' => ['label' => 'Fair (41-60s)', 'color' => 'bg-yellow-600'],
                    'poor' => ['label' => 'Poor (>60s)', 'color' => 'bg-red-600']
                ];

                foreach ($stats as $stat) {
                    $wait_time = $stat['avg_duration'];
                    if ($wait_time <= $thresholds['excellent']) $stat['category'] = 'excellent';
                    elseif ($wait_time <= $thresholds['good']) $stat['category'] = 'good';
                    elseif ($wait_time <= $thresholds['fair']) $stat['category'] = 'fair';
                    else $stat['category'] = 'poor';
                }

                $distribution = array_count_values(array_column($stats, 'category'));
                foreach ($categories as $key => $category) {
                    $count = isset($distribution[$key]) ? $distribution[$key] : 0;
                    $percentage = ($total_days > 0) ? ($count / $total_days * 100) : 0;
                    ?>
                    <div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-900"><?php echo $category['label']; ?></div>
                            <div class="text-sm font-medium text-gray-900"><?php echo $count; ?> days (<?php echo number_format($percentage, 1); ?>%)</div>
                        </div>
                        <div class="mt-1 relative">
                            <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                <div class="<?php echo $category['color']; ?> rounded" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Stats Table -->
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Calls</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Avg Wait Time</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Performance</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Active Agents</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php foreach ($stats as $daily_stat): 
                                $wait_time = $daily_stat['avg_duration'];
                                if ($wait_time <= $thresholds['excellent']) {
                                    $status_color = 'bg-green-100 text-green-800';
                                    $status_text = 'Excellent';
                                } elseif ($wait_time <= $thresholds['good']) {
                                    $status_color = 'bg-blue-100 text-blue-800';
                                    $status_text = 'Good';
                                } elseif ($wait_time <= $thresholds['fair']) {
                                    $status_color = 'bg-yellow-100 text-yellow-800';
                                    $status_text = 'Fair';
                                } else {
                                    $status_color = 'bg-red-100 text-red-800';
                                    $status_text = 'Poor';
                                }
                            ?>
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                    <?php echo date('M j, Y', strtotime($daily_stat['date'])); ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?php echo number_format($daily_stat['total_calls']); ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?php echo formatDuration($daily_stat['avg_duration']); ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 <?php echo $status_color; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?php echo number_format($daily_stat['total_agents']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="mt-8">
        <canvas id="waitTimeChart" class="w-full h-64"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('waitTimeChart').getContext('2d');
    const stats = <?php echo json_encode($stats); ?>;
    const thresholds = <?php echo json_encode($thresholds); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: stats.map(stat => new Date(stat.date).toLocaleDateString()),
            datasets: [{
                label: 'Average Wait Time',
                data: stats.map(stat => stat.avg_duration),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.1
            }, {
                label: 'Excellent Threshold',
                data: stats.map(() => thresholds.excellent),
                borderColor: 'rgb(34, 197, 94)',
                borderDash: [5, 5],
                fill: false
            }, {
                label: 'Poor Threshold',
                data: stats.map(() => thresholds.poor),
                borderColor: 'rgb(239, 68, 68)',
                borderDash: [5, 5],
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Wait Time (seconds)'
                    }
                }
            }
        }
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
