
@extends('layouts.app2')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ __('Manage Foods') }}</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">{{ __('Add, view, and manage food items and their nutritional information.') }}</p>
        </div>

        <!-- Navigation Links -->
        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('Back to Dashboard') }}</a>
            <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('Manage Partners') }}</a>
            <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('View Statistics') }}</a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 dark:border-green-400 text-green-700 dark:text-green-200 p-4 mb-6 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add Food Form -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Add New Food') }}</h3>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <form action="{{ route('admin.foods.store') }}" method="POST" onsubmit="event.preventDefault(); submitFoodForm(this, '{{ __('Food added successfully') }}')">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Food Name') }}</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                            @error('name')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="calories_per_100g" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Calories per 100g') }}</label>
                            <input type="number" name="calories_per_100g" id="calories_per_100g" min="0" step="0.1" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                            @error('calories_per_100g')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="nutrients" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Nutrients (JSON)') }}</label>
                            <textarea name="nutrients" id="nutrients" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 font-mono" rows="4" required>{{"{\"sodium\": 0, \"sugar\": 0, \"protein\": 0}"}}</textarea>
                            @error('nutrients')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">{{ __('Add Food') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Nutrition Stats Section -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Nutrition Statistics') }}</h3>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                @if ($foods->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300 text-center">{{ __('No data available to display chart.') }}</p>
                @else
                    <canvas id="nutritionChart" style="max-height: 300px;"></canvas>
                    <button onclick="downloadChart('nutritionChart', 'nutrition_stats.png')" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500">{{ __('Download') }}</button>
                @endif
            </div>
        </div>

        <!-- Foods Table -->
        <div x-data="{ search: '' }" class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Food List') }}</h3>
            <div class="mb-6">
                <input type="text" x-model="search" placeholder="{{ __('Search foods by name...') }}" class="w-full sm:w-1/2 border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
            @if ($foods->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <p class="text-gray-600 dark:text-gray-300">{{ __('No foods available.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                    <table class="min-w-full">
                        <thead class="bg-gray-200 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('ID') }}</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Calories per 100g') }}</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Nutrients') }}</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($foods as $food)
                                <tr x-show="search === '' || '{{ $food->name }}'.toLowerCase().includes(search.toLowerCase())" class="border-b dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">{{ $food->id }}</td>
                                    <td class="px-6 py-4">{{ $food->name }}</td>
                                    <td class="px-6 py-4">{{ $food->calories_per_100g }}</td>
                                    <td class="px-6 py-4 font-mono text-sm">{{ json_encode($food->nutrients) }}</td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <button onclick="openFoodModal({{ $food->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ __('View') }}</button>
                                        <form action="{{ route('admin.foods.destroy', $food) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this, '{{ __('Are you sure you want to delete this food?') }}', '{{ __('Food deleted successfully') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4">
                        {{ $foods->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Food Details Modal -->
        @foreach($foods as $food)
            <div id="foodModal{{ $food->id }}" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl max-w-lg w-full transform transition-all">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Food Details:') }} {{ $food->name }}</h3>
                    <div class="space-y-3">
                        <p><strong class="text-gray-700 dark:text-gray-200">{{ __('Calories per 100g') }}:</strong> {{ $food->calories_per_100g }}</p>
                        <p><strong class="text-gray-700 dark:text-gray-200">{{ __('Nutrients') }}:</strong></p>
                        <pre class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg text-sm font-mono text-gray-900 dark:text-gray-200">{{ json_encode($food->nutrients, JSON_PRETTY_PRINT) }}</pre>
                        <p><strong class="text-gray-700 dark:text-gray-200">{{ __('Created At:') }}:</strong> {{ $food->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <button onclick="closeFoodModal({{ $food->id }})" class="mt-6 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">{{ __('Close') }}</button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        let chartInstance = null;

        function initializeChart() {
            const canvas = document.getElementById('nutritionChart');
            if (!canvas) {
                console.error('Canvas element not found for nutritionChart');
                return;
            }
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Failed to get 2D context for nutritionChart');
                return;
            }

            // Destroy existing chart if any
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
                console.log('Previous chart instance destroyed');
            }

            const foods = @json($foods->items());
            console.log('Foods data:', foods);

            if (!foods || !foods.length) {
                console.warn('No food data available for chart');
                return;
            }

            const labels = foods.map(food => food.name || 'Unknown');
            const data = foods.map(food => food.calories_per_100g || 0);

            try {
                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '{{ __('Calories per 100g') }}',
                            data: data,
                            backgroundColor: '#3B82F6',
                            borderColor: '#1D4ED8',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: '{{ __('Calories (kcal)') }}' },
                                ticks: { color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#1F2937' }
                            },
                            x: {
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#1F2937',
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: { display: true, position: 'top' }
                        }
                    }
                });
                console.log('Chart initialized successfully');
            } catch (error) {
                console.error('Error initializing chart:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Chart Error') }}',
                    text: '{{ __('Failed to load chart. Please try again or contact support.') }}'
                });
            }
        }

        // Initialize chart only once
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM loaded, initializing chart');
            initializeChart();
        });

        function openFoodModal(foodId) {
            const modal = document.getElementById('foodModal' + foodId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                console.error('Modal not found for foodId:', foodId);
            }
        }

        function closeFoodModal(foodId) {
            const modal = document.getElementById('foodModal' + foodId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function submitFoodForm(form, successMessage) {
            const nutrientsInput = form.querySelector('#nutrients');
            try {
                JSON.parse(nutrientsInput.value);
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Invalid JSON') }}',
                    text: '{{ __('Please enter valid JSON for nutrients.') }}'
                });
                return;
            }

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
                    text: '{{ __('Failed to add food:') }} ' + error.message
                });
            });
        }

        function confirmDelete(form, confirmMessage, successMessage) {
            Swal.fire({
                title: '{{ __('Confirm') }}',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'DELETE',
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
                            text: '{{ __('Failed to delete food:') }} ' + error.message
                        });
                    });
                }
            });
        }

        function downloadChart(canvasId, filename) {
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = filename;
                link.click();
            } else {
                console.error('Canvas not found for download:', canvasId);
            }
        }
    </script>

    <style>
        .modal { display: none; }
        .modal:not(.hidden) { display: flex; }
        #nutritionChart { max-height: 300px; width: 100%; }
    </style>
@endsection
