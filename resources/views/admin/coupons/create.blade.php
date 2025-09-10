@extends('layouts.app2')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ __('Create Coupon') }}</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">{{ __('Create a new coupon for a user.') }}</p>
        </div>

        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.coupons.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('Back to Coupons') }}</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <form action="{{ route('admin.coupons.store') }}" method="POST" onsubmit="event.preventDefault(); submitForm(this, '{{ __('Coupon created successfully') }}', 'admin.coupons.store')">
                @csrf
                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Select User') }}</label>
                    <select name="user_id" id="user_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                        <option value="" disabled selected>{{ __('Select a user') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Discount Percentage') }}</label>
                    <input type="number" name="discount_percentage" id="discount_percentage" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required min="1" max="100" placeholder="{{ __('Enter discount percentage (1-100)') }}">
                </div>
                <div class="mb-4">
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Expiration Date') }}</label>
                    <input type="date" name="expires_at" id="expires_at" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.coupons.index') }}" class="bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white px-4 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">{{ __('Cancel') }}</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function submitForm(form, successMessage, routeName) {
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
                        window.location.href = data.redirect || '{{ route('admin.coupons.index') }}';
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
    </script>
@endsection