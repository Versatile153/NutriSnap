
@extends('layouts.app2')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ __('Share Submissions') }}</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">{{ __('Review user-submitted social media shares to issue coupons.') }}</p>
        </div>

        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('Back to Dashboard') }}</a>
            <a href="{{ route('admin.coupons.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('Manage Coupons') }}</a>
        </div>

        @if ($shares->isEmpty())
            <p class="text-gray-600 dark:text-gray-300">{{ __('No share submissions available.') }}</p>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-gray-700 dark:text-gray-200">
                            <th class="p-3">{{ __('User') }}</th>
                            <th class="p-3">{{ __('Meal') }}</th>
                            <th class="p-3">{{ __('Platform') }}</th>
                            <th class="p-3">{{ __('Share Link') }}</th>
                            <th class="p-3">{{ __('Status') }}</th>
                            <th class="p-3">{{ __('Submitted') }}</th>
                            <th class="p-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shares as $share)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="p-3">{{ $share->user->name }} ({{ $share->user->email }})</td>
                                <td class="p-3">Meal #{{ $share->id }} ({{ $share->meal_type }})</td>
                                <td class="p-3">{{ ucfirst($share->platform ?? ($share->share_proof['platform'] ?? 'Unknown')) }}</td>
                                <td class="p-3">
                                    <a href="{{ $share->share_link['url'] ?? $share->share_proof['url'] ?? '#' }}" 
                                       class="text-blue-600 dark:text-blue-400 underline" 
                                       target="_blank">{{ __('View Post') }}</a>
                                </td>
                                <td class="p-3">{{ $share->share_link['approved'] ?? $share->share_proof['approved'] ?? false ? __('Approved') : __('Pending') }}</td>
                                <td class="p-3">{{ $share->updated_at->format('M d, Y') }}</td>
                                <td class="p-3">
                                    @if (!($share->share_link['approved'] ?? $share->share_proof['approved'] ?? false))
                                        <form action="{{ route('admin.shares.approve', $share->id) }}" method="POST" onsubmit="event.preventDefault(); submitForm(this, '{{ __('Share approved and coupon issued successfully') }}')">
                                            @csrf
                                            <div class="flex gap-2">
                                                <input type="number" name="discount_percentage" class="w-24 p-2 bg-gray-700 text-white rounded-lg" placeholder="{{ __('Discount %') }}" required min="1" max="100">
                                                <input type="date" name="expires_at" class="p-2 bg-gray-700 text-white rounded-lg" required min="{{ now()->addDay()->format('Y-m-d') }}">
                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('Approve & Issue Coupon') }}</button>
                                            </div>
                                            @error('discount_percentage')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                            @error('expires_at')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </form>
                                    @else
                                        <span class="text-gray-400">{{ __('Approved') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function submitForm(form, successMessage) {
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
                    text: '{{ __('Failed to process:') }} ' + error.message
                });
            });
        }
    </script>
@endsection
