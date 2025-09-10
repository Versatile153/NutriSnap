@extends('layouts.app2')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-900 min-h-screen">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-white">{{ __('Coupon Management') }}</h2>
            <p class="mt-2 text-gray-300">{{ __('View and manage all coupons issued to users.') }}</p>
        </div>

        <!-- Navigation Links -->
        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Back to Dashboard') }}</a>
            <a href="{{ route('admin.coupons.create') }}" class="text-nutri-blue hover:text-blue-500 font-medium">{{ __('Create Coupon') }}</a>
        </div>

        <!-- Coupons Table -->
        <div class="overflow-x-auto bg-gray-800 rounded-xl shadow-lg">
            <table class="min-w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Code') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('User') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Discount') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Expires') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-white">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                        <tr class="border-b border-gray-600 hover:bg-gray-700">
                            <td class="px-6 py-4">{{ $coupon->code }}</td>
                            <td class="px-6 py-4">{{ $coupon->user->name }}</td>
                            <td class="px-6 py-4">{{ $coupon->discount_percentage }}%</td>
                            <td class="px-6 py-4">{{ $coupon->expires_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="{{ $coupon->is_used ? 'text-red-400' : 'text-green-400' }}">
                                    {{ $coupon->is_used ? __('Used') : __('Active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-nutri-blue hover:text-blue-500">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.coupons.toggle', $coupon) }}" method="POST" class="inline" onsubmit="event.preventDefault(); submitForm(this, '{{ __('Coupon status updated successfully') }}', 'admin.coupons.toggle')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_used" value="{{ $coupon->is_used ? 0 : 1 }}">
                                    <button type="submit" class="text-yellow-400 hover:text-yellow-500">
                                        {{ $coupon->is_used ? __('Mark Active') : __('Mark Used') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function submitForm(form, successMessage, routeName) {
            const method = (routeName === 'users.destroy') ? 'DELETE' : 'PATCH';
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
    </script>
@endsection