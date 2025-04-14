<?php
require_once('config.php');
require_once('includes/functions.php');

// Ensure user is logged in
requireLogin();

// Default settings
$settings = [
    'sla' => [
        'target_percentage' => 80,
        'wait_time_threshold' => 20
    ],
    'wait_times' => [
        'excellent' => 20,
        'good' => 40,
        'fair' => 60,
        'poor' => 120
    ],
    'display' => [
        'refresh_interval' => 30,
        'date_format' => 'M j, Y',
        'time_format' => 'H:i:s'
    ],
    'notifications' => [
        'enable_email' => false,
        'enable_slack' => false,
        'alert_threshold' => 90
    ]
];

require_once('includes/header.php');
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="pb-5 border-b border-gray-200">
        <h3 class="text-2xl leading-6 font-medium text-gray-900">
            System Settings
        </h3>
        <p class="mt-2 text-sm text-gray-500">
            Configure system-wide settings and thresholds
        </p>
    </div>

    <!-- Settings Form -->
    <div class="mt-8 max-w-3xl">
        <form method="post" action="settings.php" class="space-y-8">
            <!-- SLA Settings -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Service Level Agreement (SLA)</h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Percentage</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="sla_target" 
                                       value="<?php echo $settings['sla']['target_percentage']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Percentage of calls to be answered within threshold</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Wait Time Threshold (seconds)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="sla_threshold" 
                                       value="<?php echo $settings['sla']['wait_time_threshold']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Maximum acceptable wait time in seconds</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wait Time Thresholds -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Wait Time Thresholds</h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Excellent (seconds)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="wait_excellent" 
                                       value="<?php echo $settings['wait_times']['excellent']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Good (seconds)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="wait_good" 
                                       value="<?php echo $settings['wait_times']['good']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fair (seconds)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="wait_fair" 
                                       value="<?php echo $settings['wait_times']['fair']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Poor (seconds)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="wait_poor" 
                                       value="<?php echo $settings['wait_times']['poor']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Display Settings</h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Refresh Interval (seconds)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="refresh_interval" 
                                       value="<?php echo $settings['display']['refresh_interval']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date Format</label>
                            <div class="mt-1">
                                <select name="date_format" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="M j, Y" <?php echo $settings['display']['date_format'] === 'M j, Y' ? 'selected' : ''; ?>>Jan 1, 2025</option>
                                    <option value="Y-m-d" <?php echo $settings['display']['date_format'] === 'Y-m-d' ? 'selected' : ''; ?>>2025-01-01</option>
                                    <option value="d/m/Y" <?php echo $settings['display']['date_format'] === 'd/m/Y' ? 'selected' : ''; ?>>01/01/2025</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Notifications</h3>
                    <div class="mt-6 space-y-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="enable_email" 
                                       <?php echo $settings['notifications']['enable_email'] ? 'checked' : ''; ?>
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">Enable Email Notifications</label>
                                <p class="text-gray-500">Receive email alerts for important events</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="enable_slack" 
                                       <?php echo $settings['notifications']['enable_slack'] ? 'checked' : ''; ?>
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">Enable Slack Notifications</label>
                                <p class="text-gray-500">Receive notifications in Slack</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alert Threshold (%)</label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="alert_threshold" 
                                       value="<?php echo $settings['notifications']['alert_threshold']; ?>" 
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Send alerts when metrics exceed this threshold</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
