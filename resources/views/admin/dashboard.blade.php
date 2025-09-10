@extends('layouts.app2')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-900 min-h-screen">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-white">{{ __('Admin Dashboard') }}</h2>
            <p class="mt-2 text-gray-300">{{ __('Manage users, partners, coupons, statistics, and foods with ease.') }}</p>
        </div>

        <!-- Navigation Links -->
        <div class="mb-8 flex flex-wrap gap-4">
            <a href="#" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Manage Partners') }}</a>
            <a href="{{ route('admin.coupons.index') }}" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Manage Coupons') }}</a>
            <a href="{{ route('admin.coupons.create') }}" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Create Coupon') }}</a>
            <a href="{{ route('admin.shares.index') }}" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Review Shares') }}</a>
            <a href="#" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('View Statistics') }}</a>
            <a href="{{ route('admin.foods.index') }}" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Manage Foods') }}</a>
        </div>

        <!-- Send Email to All Users -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-white mb-6">{{ __('Send Email to All Users') }}</h3>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                <form action="{{ route('users.email-all') }}" method="POST" onsubmit="event.preventDefault(); submitEmailForm(this, '{{ __('Emails queued successfully') }}', 'users.email-all')">
                    @csrf
                    <div class="mb-4">
                        <label for="mass-email-subject" class="block text-sm font-medium text-gray-200">{{ __('Subject') }}</label>
                        <input type="text" name="subject" id="mass-email-subject" class="w-full border border-gray-600 rounded-lg p-3 bg-gray-700 text-white focus:ring-2 focus:ring-nutri-blue" required>
                    </div>
                    <div class="mb-4">
                        <label for="mass-email-message" class="block text-sm font-medium text-gray-200">{{ __('Message') }}</label>
                        <textarea name="message" id="mass-email-message" class="w-full border border-gray-600 rounded-lg p-3 bg-gray-700 text-white focus:ring-2 focus:ring-nutri-blue" rows="4" required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">{{ __('Send to All') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Section -->
        <div x-data="{ search: '' }" class="mb-12">
            <h3 class="text-2xl font-bold text-white mb-6">{{ __('Statistics') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- User Stats Cards -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <p class="text-lg font-semibold text-gray-200">{{ __('Total Users') }}</p>
                    <p class="text-3xl font-bold text-nutri-blue">{{ $totalUsers }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <p class="text-lg font-semibold text-gray-200">{{ __('Suspended Users') }}</p>
                    <p class="text-3xl font-bold text-red-400">{{ $suspendedUsers }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl shadow-lg p-6 relative">
                    <canvas id="usersByRoleChart" class="w-full h-48"></canvas>
                    <button onclick="downloadChart('usersByRoleChart', 'users_by_role.png')" class="mt-2 bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 absolute bottom-4 right-4">{{ __('Download') }}</button>
                </div>
                <!-- Coupon Stats Cards -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <p class="text-lg font-semibold text-gray-200">{{ __('Total Coupons') }}</p>
                    <p class="text-3xl font-bold text-nutri-blue">{{ $totalCoupons }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <p class="text-lg font-semibold text-gray-200">{{ __('Active Coupons') }}</p>
                    <p class="text-3xl font-bold text-green-400">{{ $activeCoupons }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl shadow-lg p-6">
                    <p class="text-lg font-semibold text-gray-200">{{ __('Used Coupons') }}</p>
                    <p class="text-3xl font-bold text-red-400">{{ $usedCoupons }}</p>
                </div>
            </div>
            <div class="mt-8 bg-gray-800 rounded-xl shadow-lg p-6 relative">
                <canvas id="registrationTrendsChart" class="w-full h-64"></canvas>
                <button onclick="downloadChart('registrationTrendsChart', 'registration_trends.png')" class="mt-2 bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 absolute bottom-4 right-4">{{ __('Download') }}</button>
            </div>
            <div class="mt-8 bg-gray-800 rounded-xl shadow-lg p-6 relative">
                <canvas id="activeVsSuspendedChart" class="w-full h-64"></canvas>
                <button onclick="downloadChart('activeVsSuspendedChart', 'active_vs_suspended.png')" class="mt-2 bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 absolute bottom-4 right-4">{{ __('Download') }}</button>
            </div>
            <div class="mt-8 bg-gray-800 rounded-xl shadow-lg p-6 relative">
                <canvas id="couponTrendsChart" class="w-full h-64"></canvas>
                <button onclick="downloadChart('couponTrendsChart', 'coupon_trends.png')" class="mt-2 bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 absolute bottom-4 right-4">{{ __('Download') }}</button>
            </div>
            <div class="mt-8 bg-gray-800 rounded-xl shadow-lg p-6 relative">
                <canvas id="couponsByPlatformChart" class="w-full h-64"></canvas>
                <button onclick="downloadChart('couponsByPlatformChart', 'coupons_by_platform.png')" class="mt-2 bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 absolute bottom-4 right-4">{{ __('Download') }}</button>
            </div>
        </div>

        <!-- User Management Section -->
        <div x-data="{ search: '' }" class="mb-12">
            <h3 class="text-2xl font-bold text-white mb-6">{{ __('User Management') }}</h3>
            <div class="mb-6">
                <input type="text" x-model="search" placeholder="{{ __('Search users by name or email...') }}" class="w-full sm:w-1/2 border border-gray-600 rounded-lg p-3 bg-gray-800 text-white focus:ring-2 focus:ring-nutri-blue">
            </div>
            <div class="overflow-x-auto bg-gray-800 rounded-xl shadow-lg">
                <table class="min-w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Role') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr x-show="search === '' || '{{ $user->name }}'.toLowerCase().includes(search.toLowerCase()) || '{{ $user->email }}'.toLowerCase().includes(search.toLowerCase())" class="border-b border-gray-600 hover:bg-gray-700">
                                <td class="px-6 py-4">{{ $user->id }}</td>
                                <td class="px-6 py-4">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ $user->role }}</td>
                                <td class="px-6 py-4">
                                    <span class="{{ $user->is_suspended ? 'text-red-400' : 'text-green-400' }}">
                                        {{ $user->is_suspended ? __('Suspended') : __('Active') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 flex gap-2">
                                    <button onclick="openUserModal({{ $user->id }})" class="text-nutri-blue hover:text-blue-500">{{ __('View') }}</button>
                                    @if($user->is_suspended)
                                        <form action="{{ route('users.unsuspend', $user) }}" method="POST" class="inline" onsubmit="event.preventDefault(); submitForm(this, '{{ __('User unsuspended successfully') }}', 'users.unsuspend')">
                                            @csrf
                                            <button type="submit" class="text-green-400 hover:text-green-500">{{ __('Unsuspend') }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('users.suspend', $user) }}" method="POST" class="inline" onsubmit="event.preventDefault(); submitForm(this, '{{ __('User suspended successfully') }}', 'users.suspend')">
                                            @csrf
                                            <button type="submit" class="text-yellow-400 hover:text-yellow-500">{{ __('Suspend') }}</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this, '{{ __('Are you sure you want to delete this user?') }}', '{{ __('User deleted successfully') }}', 'users.destroy')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-500">{{ __('Delete') }}</button>
                                    </form>
                                    <button onclick="openEmailModal({{ $user->id }})" class="text-nutri-blue hover:text-blue-500">{{ __('Email') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- User Details Modal -->
        @foreach($users as $user)
            <div id="userModal{{ $user->id }}" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 p-8 rounded-xl shadow-2xl max-w-lg w-full transform transition-all">
                    <h3 class="text-2xl font-bold text-white mb-4">{{ __('User Details') }}: {{ $user->name }}</h3>
                    <div class="space-y-3">
                        <p><strong class="text-gray-200">{{ __('Email') }}:</strong> {{ $user->email }}</p>
                        <p><strong class="text-gray-200">{{ __('Role') }}:</strong> {{ $user->role }}</p>
                        <p><strong class="text-gray-200">{{ __('Status') }}:</strong> {{ $user->is_suspended ? __('Suspended') : __('Active') }}</p>
                        <p><strong class="text-gray-200">{{ __('Created At') }}:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                        @if($user->profile)
                            <p><strong class="text-gray-200">{{ __('Bio') }}:</strong> {{ $user->profile->bio ?? __('N/A') }}</p>
                        @endif
                    </div>
                    <button onclick="closeUserModal({{ $user->id }})" class="mt-6 bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">{{ __('Close') }}</button>
                </div>
            </div>
        @endforeach

        <!-- Email Modal -->
        @foreach($users as $user)
            <div id="emailModal{{ $user->id }}" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 p-8 rounded-xl shadow-2xl max-w-lg w-full transform transition-all">
                    <h3 class="text-2xl font-bold text-white mb-4">{{ __('Send Email to') }} {{ $user->name }}</h3>
                    <form action="{{ route('users.email', $user) }}" method="POST" onsubmit="event.preventDefault(); submitEmailForm(this, '{{ __('Email sent successfully') }}', 'users.email')">
                        @csrf
                        <div class="mb-4">
                            <label for="subject{{ $user->id }}" class="block text-sm font-medium text-gray-200">{{ __('Subject') }}</label>
                            <input type="text" name="subject" id="subject{{ $user->id }}" class="w-full border border-gray-600 rounded-lg p-3 bg-gray-700 text-white focus:ring-2 focus:ring-nutri-blue" required>
                        </div>
                        <div class="mb-4">
                            <label for="message{{ $user->id }}" class="block text-sm font-medium text-gray-200">{{ __('Message') }}</label>
                            <textarea name="message" id="message{{ $user->id }}" class="w-full border border-gray-600 rounded-lg p-3 bg-gray-700 text-white focus:ring-2 focus:ring-nutri-blue" rows="4" required></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeEmailModal({{ $user->id }})" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition">{{ __('Cancel') }}</button>
                            <button type="submit" class="bg-nutri-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">{{ __('Send') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        // Modal Control Functions
        function openUserModal(userId) {
            const modal = document.getElementById('userModal' + userId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeUserModal(userId) {
            const modal = document.getElementById('userModal' + userId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openEmailModal(userId) {
            const modal = document.getElementById('emailModal' + userId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeEmailModal(userId) {
            const modal = document.getElementById('emailModal' + userId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Form Submission with SweetAlert2
        function submitForm(form, successMessage, routeName) {
            const method = (routeName === 'users.destroy') ? 'DELETE' : 'POST';
            fetch(form.action, {
                method: method,
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('Success') }}',
                        text: successMessage,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: data.message || '{{ __('Something went wrong!') }}'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Error') }}',
                    text: '{{ __('Failed to process request:') }} ' + error.message
                });
            });
        }

        function submitEmailForm(form, successMessage, routeName) {
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('Success') }}',
                        text: successMessage,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        if (routeName === 'users.email') {
                            closeEmailModal(form.action.split('/').pop());
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: data.message || '{{ __('Something went wrong!') }}'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Error') }}',
                    text: '{{ __('Failed to send email:') }} ' + error.message
                });
            });
        }

        function confirmDelete(form, confirmMessage, successMessage, routeName) {
            Swal.fire({
                title: '{{ __('Confirm') }}',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm(form, successMessage, routeName);
                }
            });
        }

        // Utility to destroy existing chart instance
        function destroyChart(chartInstance) {
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
        }

        // Global chart instances
        let usersByRoleChart = null;
        let registrationTrendsChart = null;
        let activeVsSuspendedChart = null;
        let couponTrendsChart = null;
        let couponsByPlatformChart = null;

        // Initialize charts only once
        function initializeCharts() {
            // Destroy existing instances if they exist
            destroyChart(usersByRoleChart);
            destroyChart(registrationTrendsChart);
            destroyChart(activeVsSuspendedChart);
            destroyChart(couponTrendsChart);
            destroyChart(couponsByPlatformChart);

            // Users by Role Pie Chart
            const ctxRole = document.getElementById('usersByRoleChart')?.getContext('2d');
            if (ctxRole) {
                try {
                    usersByRoleChart = new Chart(ctxRole, {
                        type: 'pie',
                        data: {
                            labels: {!! $usersByRole->pluck('role')->toJson() !!},
                            datasets: [{
                                data: {!! $usersByRole->pluck('count')->toJson() !!},
                                backgroundColor: ['#3B82F6', '#EF4444', '#F59E0B', '#10B981', '#8B5CF6'],
                                hoverBackgroundColor: ['#1D4ED8', '#DC2626', '#D97706', '#059669', '#7C3AED'],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { size: 14, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 14, family: 'Inter, sans-serif' },
                                    bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                    padding: 10,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            let label = tooltipItem.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += tooltipItem.raw;
                                            return label + ' {{ __('users') }}';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: { size: 12, weight: 'bold' },
                                    formatter: (value, ctx) => {
                                        let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        return sum ? ((value / sum) * 100).toFixed(1) + '%' : '0%';
                                    },
                                    textAlign: 'center'
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            },
                            onHover: (event, elements) => {
                                if (elements.length > 0) {
                                    event.native.target.style.cursor = 'pointer';
                                } else {
                                    event.native.target.style.cursor = 'default';
                                }
                            },
                            onClick: (e, elements) => {
                                if (elements.length) {
                                    const role = e.chart.data.labels[elements[0].index];
                                    filterUsersByRole(role);
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                } catch (error) {
                    console.error('Error initializing usersByRoleChart:', error);
                }
            }

            // Registration Trends Line Chart
            const ctxTrends = document.getElementById('registrationTrendsChart')?.getContext('2d');
            if (ctxTrends) {
                try {
                    const labels = {!! $registrationTrends->pluck('date')->toJson() !!};
                    const data = {!! $registrationTrends->pluck('count')->toJson() !!};
                    let annotations = {};
                    if (data.length > 0) {
                        const maxValue = Math.max(...data);
                        const maxIndex = data.indexOf(maxValue);
                        annotations = {
                            type: 'line',
                            label: {
                                content: '{{ __('Peak Registrations') }}',
                                enabled: true,
                                position: 'top'
                            },
                            scaleID: 'y',
                            value: maxValue,
                            borderColor: '#EF4444',
                            borderWidth: 2,
                            borderDash: [6, 3]
                        };
                    }
                    registrationTrendsChart = new Chart(ctxTrends, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: '{{ __('New Users') }}',
                                data: data,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#10B981',
                                pointHoverBackgroundColor: '#059669'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { size: 14, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 14, family: 'Inter, sans-serif' },
                                    bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                    padding: 10,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.raw + ' {{ __('new users on') }} ' + tooltipItem.label;
                                        }
                                    }
                                },
                                zoom: {
                                    zoom: {
                                        wheel: { enabled: true },
                                        pinch: { enabled: true },
                                        mode: 'x'
                                    },
                                    pan: {
                                        enabled: true,
                                        mode: 'x'
                                    }
                                },
                                annotation: {
                                    annotations: {
                                        peakLine: annotations
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 12, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#374151' },
                                    ticks: {
                                        font: { size: 12, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        },
                        plugins: [ChartDataLabels, ChartZoom]
                    });
                } catch (error) {
                    console.error('Error initializing registrationTrendsChart:', error);
                }
            }

            // Active vs Suspended Bar Chart
            const ctxStatus = document.getElementById('activeVsSuspendedChart')?.getContext('2d');
            if (ctxStatus) {
                try {
                    activeVsSuspendedChart = new Chart(ctxStatus, {
                        type: 'bar',
                        data: {
                            labels: ['{{ __('Active') }}', '{{ __('Suspended') }}'],
                            datasets: [{
                                label: '{{ __('Users by Status') }}',
                                data: [{{ $totalUsers - $suspendedUsers }}, {{ $suspendedUsers }}],
                                backgroundColor: ['#10B981', '#EF4444'],
                                hoverBackgroundColor: ['#059669', '#DC2626'],
                                borderColor: '#fff',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { size: 14, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 14, family: 'Inter, sans-serif' },
                                    bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                    padding: 10,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.raw + ' {{ __('users') }}';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: { size: 12, weight: 'bold' },
                                    formatter: (value) => value,
                                    anchor: 'end',
                                    align: 'end'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#374151' },
                                    ticks: {
                                        font: { size: 12, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 12, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                } catch (error) {
                    console.error('Error initializing activeVsSuspendedChart:', error);
                }
            }

            // Coupon Trends Line Chart
            const ctxCouponTrends = document.getElementById('couponTrendsChart')?.getContext('2d');
            if (ctxCouponTrends) {
                try {
                    const couponLabels = {!! $couponTrends->pluck('date')->toJson() !!};
                    const couponData = {!! $couponTrends->pluck('count')->toJson() !!};
                    let couponAnnotations = {};
                    if (couponData.length > 0) {
                        const maxValue = Math.max(...couponData);
                        const maxIndex = couponData.indexOf(maxValue);
                        couponAnnotations = {
                            type: 'line',
                            label: {
                                content: '{{ __('Peak Coupon Issuance') }}',
                                enabled: true,
                                position: 'top'
                            },
                            scaleID: 'y',
                            value: maxValue,
                            borderColor: '#EF4444',
                            borderWidth: 2,
                            borderDash: [6, 3]
                        };
                    }
                    couponTrendsChart = new Chart(ctxCouponTrends, {
                        type: 'line',
                        data: {
                            labels: couponLabels,
                            datasets: [{
                                label: '{{ __('Coupons Issued') }}',
                                data: couponData,
                                borderColor: '#8B5CF6',
                                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#8B5CF6',
                                pointHoverBackgroundColor: '#7C3AED'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { size: 14, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 14, family: 'Inter, sans-serif' },
                                    bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                    padding: 10,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.raw + ' {{ __('coupons issued on') }} ' + tooltipItem.label;
                                        }
                                    }
                                },
                                zoom: {
                                    zoom: {
                                        wheel: { enabled: true },
                                        pinch: { enabled: true },
                                        mode: 'x'
                                    },
                                    pan: {
                                        enabled: true,
                                        mode: 'x'
                                    }
                                },
                                annotation: {
                                    annotations: {
                                        peakLine: couponAnnotations
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 12, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#374151' },
                                    ticks: {
                                        font: { size: 12, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        },
                        plugins: [ChartDataLabels, ChartZoom]
                    });
                } catch (error) {
                    console.error('Error initializing couponTrendsChart:', error);
                }
            }

            // Coupons by Platform Pie Chart
            const ctxPlatform = document.getElementById('couponsByPlatformChart')?.getContext('2d');
            if (ctxPlatform) {
                try {
                    couponsByPlatformChart = new Chart(ctxPlatform, {
                        type: 'pie',
                        data: {
                            labels: {!! $couponsByPlatform->keys()->toJson() !!},
                            datasets: [{
                                data: {!! $couponsByPlatform->values()->toJson() !!},
                                backgroundColor: ['#3B82F6', '#EF4444', '#F59E0B', '#10B981'],
                                hoverBackgroundColor: ['#1D4ED8', '#DC2626', '#D97706', '#059669'],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { size: 14, family: 'Inter, sans-serif' },
                                        color: '#D1D5DB'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 14, family: 'Inter, sans-serif' },
                                    bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                    padding: 10,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            let label = tooltipItem.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += tooltipItem.raw;
                                            return label + ' {{ __('coupons') }}';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: { size: 12, weight: 'bold' },
                                    formatter: (value, ctx) => {
                                        let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        return sum ? ((value / sum) * 100).toFixed(1) + '%' : '0%';
                                    },
                                    textAlign: 'center'
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                } catch (error) {
                    console.error('Error initializing couponsByPlatformChart:', error);
                }
            }
        }

        // Filter users by role
        let filterTimeout;
        function filterUsersByRole(role) {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                const searchInput = document.querySelector('[x-model="search"]');
                if (searchInput) {
                    searchInput.value = role;
                    searchInput.dispatchEvent(new Event('input'));
                }
            }, 300);
        }

        // Download chart as PNG
        function downloadChart(canvasId, filename) {
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = filename;
                link.click();
            }
        }

        // Initialize charts on DOM load
        document.addEventListener('DOMContentLoaded', () => {
            initializeCharts();
        });
    </script>
@endsection