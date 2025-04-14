<?php
require_once('config.php');
require_once('includes/functions.php');

// Ensure user is logged in
requireLogin();

// Get date range parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get demo data for agents and queues
$agents = [
    ['id' => 1, 'name' => 'John Smith'],
    ['id' => 2, 'name' => 'Sarah Johnson'],
    ['id' => 3, 'name' => 'Michael Brown'],
    ['id' => 4, 'name' => 'Emily Davis'],
    ['id' => 5, 'name' => 'David Wilson']
];

$queues = [
    ['id' => 1, 'name' => 'Sales'],
    ['id' => 2, 'name' => 'Support'],
    ['id' => 3, 'name' => 'Technical'],
    ['id' => 4, 'name' => 'Billing']
];

// Get selected agents and queues from POST
$selected_agents = isset($_POST['agents']) ? $_POST['agents'] : [];
$selected_queues = isset($_POST['queues']) ? $_POST['queues'] : [];
$matrix_type = isset($_POST['matrix_type']) ? $_POST['matrix_type'] : 'many_to_many';

require_once('includes/header.php');
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="pb-5 border-b border-gray-200">
        <h3 class="text-2xl leading-6 font-medium text-gray-900">
            Agent-Queue Selection Matrix
        </h3>
        <p class="mt-2 text-sm text-gray-500">
            Configure agent and queue relationships for reporting
        </p>
    </div>

    <!-- Matrix Configuration Form -->
    <form method="post" class="mt-8 space-y-8">
        <!-- Date Range Selection -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Date Range</h3>
                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From</label>
                        <div class="mt-1">
                            <input type="date" 
                                   name="start_date" 
                                   value="<?php echo htmlspecialchars($start_date); ?>"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">To</label>
                        <div class="mt-1">
                            <input type="date" 
                                   name="end_date" 
                                   value="<?php echo htmlspecialchars($end_date); ?>"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matrix Type Selection -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Matrix Type</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center">
                        <input type="radio" 
                               name="matrix_type" 
                               value="one_to_one"
                               <?php echo $matrix_type === 'one_to_one' ? 'checked' : ''; ?>
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label class="ml-3 block text-sm font-medium text-gray-700">
                            One-to-One (Single agent to single queue)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" 
                               name="matrix_type" 
                               value="one_to_many"
                               <?php echo $matrix_type === 'one_to_many' ? 'checked' : ''; ?>
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label class="ml-3 block text-sm font-medium text-gray-700">
                            One-to-Many (Single agent to multiple queues)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" 
                               name="matrix_type" 
                               value="many_to_one"
                               <?php echo $matrix_type === 'many_to_one' ? 'checked' : ''; ?>
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label class="ml-3 block text-sm font-medium text-gray-700">
                            Many-to-One (Multiple agents to single queue)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" 
                               name="matrix_type" 
                               value="many_to_many"
                               <?php echo $matrix_type === 'many_to_many' ? 'checked' : ''; ?>
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                        <label class="ml-3 block text-sm font-medium text-gray-700">
                            Many-to-Many (Multiple agents to multiple queues)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent Selection -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Select Agents</h3>
                <div class="mt-4 grid grid-cols-1 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($agents as $agent): ?>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   name="agents[]" 
                                   value="<?php echo $agent['id']; ?>"
                                   <?php echo in_array($agent['id'], $selected_agents) ? 'checked' : ''; ?>
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label class="font-medium text-gray-700"><?php echo htmlspecialchars($agent['name']); ?></label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Queue Selection -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Select Queues</h3>
                <div class="mt-4 grid grid-cols-1 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($queues as $queue): ?>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   name="queues[]" 
                                   value="<?php echo $queue['id']; ?>"
                                   <?php echo in_array($queue['id'], $selected_queues) ? 'checked' : ''; ?>
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label class="font-medium text-gray-700"><?php echo htmlspecialchars($queue['name']); ?></label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Matrix View -->
        <?php if (!empty($selected_agents) && !empty($selected_queues)): ?>
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Matrix View</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Agent</th>
                                <?php foreach ($queues as $queue): 
                                    if (in_array($queue['id'], $selected_queues)):
                                ?>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($queue['name']); ?>
                                </th>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php foreach ($agents as $agent):
                                if (in_array($agent['id'], $selected_agents)):
                            ?>
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($agent['name']); ?>
                                </td>
                                <?php foreach ($queues as $queue):
                                    if (in_array($queue['id'], $selected_queues)):
                                    // Generate demo statistics
                                    $calls = rand(50, 200);
                                    $duration = rand(120, 360);
                                ?>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?php echo number_format($calls) . ' calls<br>' . 
                                              floor($duration/60) . 'm ' . ($duration%60) . 's avg'; ?>
                                </td>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Generate Report
            </button>
        </div>
    </form>

    <?php if (!empty($selected_agents) && !empty($selected_queues)): ?>
    <!-- Performance Charts -->
    <div class="mt-8 space-y-8">
        <!-- Calls Distribution Chart -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Calls Distribution</h3>
                <canvas id="callsChart" class="w-full h-64"></canvas>
            </div>
        </div>

        <!-- Average Handle Time Chart -->
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Average Handle Time</h3>
                <canvas id="handleTimeChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Demo data for charts
        const agents = <?php echo json_encode(array_filter($agents, function($agent) use ($selected_agents) {
            return in_array($agent['id'], $selected_agents);
        })); ?>;
        const queues = <?php echo json_encode(array_filter($queues, function($queue) use ($selected_queues) {
            return in_array($queue['id'], $selected_queues);
        })); ?>;

        // Calls Distribution Chart
        new Chart(document.getElementById('callsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: agents.map(agent => agent.name),
                datasets: queues.map(queue => ({
                    label: queue.name,
                    data: agents.map(() => Math.floor(Math.random() * 150) + 50),
                    backgroundColor: `hsla(${Math.random() * 360}, 70%, 50%, 0.6)`
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Calls'
                        }
                    }
                }
            }
        });

        // Average Handle Time Chart
        new Chart(document.getElementById('handleTimeChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: agents.map(agent => agent.name),
                datasets: queues.map(queue => ({
                    label: queue.name,
                    data: agents.map(() => Math.floor(Math.random() * 240) + 120),
                    borderColor: `hsla(${Math.random() * 360}, 70%, 50%, 1)`,
                    fill: false,
                    tension: 0.1
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Average Handle Time (seconds)'
                        }
                    }
                }
            }
        });
    });
    </script>
    <?php endif; ?>
</div>

<?php require_once('includes/footer.php'); ?>
