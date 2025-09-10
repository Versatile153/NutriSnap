@extends('layouts.app2')

@section('title', __('Email Marketing'))

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Top Navigation Bar -->
    <nav class="bg-white dark:bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-xl sm:text-2xl font-bold text-nutri-blue dark:text-blue-400">{{ __('Email Marketing') }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-nutri-blue dark:hover:text-blue-400">{{ __('Dashboard') }}</a>
                    <button x-data @click="$dispatch('open-help-modal')" class="text-nutri-blue dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300" title="{{ __('Help') }}">
                        <i data-feather="help-circle" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded-lg flex items-center gap-2">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-lg flex items-center gap-2">
                <i data-feather="alert-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Email Sending Form -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-lg mb-6 animate-slide-up">
            <h2 class="text-xl sm:text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4">{{ __('Send Email') }}</h2>
            <form action="{{ route('admin.emails.send') }}" method="POST" x-data="{
                recipientType: 'individual',
                isLoading: false,
                previewContent: { subject: '', message: '' }
            }" x-ref="form" @submit.prevent="showConfirmation($event)">
                @csrf
                <div class="space-y-4">
                    <!-- Recipient Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="recipient_type" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Recipient Type') }}</label>
                            <select id="recipient_type" name="recipient_type" x-model="recipientType" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-nutri-blue focus:border-nutri-blue transition duration-200 shadow-sm text-base">
                                <option value="individual">{{ __('Individual User') }}</option>
                                <option value="selected">{{ __('Selected Users') }}</option>
                                <option value="non_subscribed">{{ __('Users Without Subscription') }}</option>
                                <option value="inactive">{{ __('Inactive Users (1 Week)') }}</option>
                                <option value="new_users">{{ __('New Users (Last Week)') }}</option>
                                <option value="no_meal">{{ __('Users Without Meal Analysis') }}</option>
                            </select>
                        </div>
                        <div x-show="recipientType === 'individual'" x-transition>
                            <label for="recipient_email" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Recipient Email or ID') }}</label>
                            <select id="recipient_email" name="recipient_email" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-nutri-blue focus:border-nutri-blue tom-select shadow-sm text-base">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('recipient_email')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i data-feather="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div x-show="recipientType === 'selected'" x-transition>
                            <label for="recipients" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Select Users') }}</label>
                            <select id="recipients" name="recipients[]" multiple class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-nutri-blue focus:border-nutri-blue tom-select shadow-sm text-base">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('recipients')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i data-feather="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Subject') }}</label>
                        <input type="text" id="subject" name="subject" required x-model="previewContent.subject" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-nutri-blue focus:border-nutri-blue shadow-sm text-base" placeholder="{{ __('Enter email subject') }}">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i data-feather="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message with WYSIWYG -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Message') }}</label>
                        <textarea id="message" name="message" rows="8" required x-model="previewContent.message" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-nutri-blue focus:border-nutri-blue shadow-sm text-base" placeholder="{{ __('Enter email message') }}"></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1"><i data-feather="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Schedule Toggle (Commented Out Until Backend Implemented) -->
                    <!--
                    <div class="flex items-center gap-4">
                        <label for="schedule" class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Schedule Email') }}</label>
                        <input type="checkbox" id="schedule" x-model="schedule" class="rounded border-gray-300 dark:border-gray-600 text-nutri-blue focus:ring-nutri-blue">
                        <div x-show="schedule" x-transition class="flex-1">
                            <label for="schedule_date" class="block text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Schedule Date') }}</label>
                            <input type="datetime-local" id="schedule_date" name="schedule_date" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-nutri-blue focus:border-nutri-blue shadow-sm">
                        </div>
                    </div>
                    -->

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-end gap-4">
                        <button type="button" @click="$refs.form.reset(); previewContent.subject = ''; previewContent.message = ''; tinymce.get('message')?.setContent('')" class="bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg">{{ __('Clear Form') }}</button>
                        <button type="button" @click="$dispatch('preview-email')" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg">{{ __('Preview Email') }}</button>
                        <button type="submit" class="bg-nutri-blue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2" :disabled="isLoading">
                            <span x-show="isLoading" class="animate-spin"><i data-feather="loader" class="w-5 h-5"></i></span>
                            <span>{{ __('Send Email') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- User Segment Analytics -->
        <div x-data="{ open: true }" class="mb-6">
            <h2 class="text-xl sm:text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
                {{ __('Email Audience Analytics') }}
                <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>
            </h2>
            <div x-show="open" x-transition x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-all animate-slide-up" style="animation-delay: 0.1s">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-feather="user-x" class="w-5 h-5 text-nutri-blue dark:text-blue-400"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Users Without Subscription') }}</h3>
                    </div>
                    <p class="text-2xl font-bold text-nutri-blue dark:text-blue-400">{{ $nonSubscribedUsers }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-all animate-slide-up" style="animation-delay: 0.2s">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-feather="clock" class="w-5 h-5 text-yellow-500"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Inactive Users (1 Week)') }}</h3>
                    </div>
                    <p class="text-2xl font-bold text-yellow-500">{{ $inactiveUsers }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-all animate-slide-up" style="animation-delay: 0.3s">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-feather="user-plus" class="w-5 h-5 text-green-500"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('New Users (Last Week)') }}</h3>
                    </div>
                    <p class="text-2xl font-bold text-green-500">{{ $newUsers }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-all animate-slide-up" style="animation-delay: 0.4s">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-feather="utensils" class="w-5 h-5 text-red-500"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Users Without Meal Analysis') }}</h3>
                    </div>
                    <p class="text-2xl font-bold text-red-500">{{ $noMealUsers }}</p>
                </div>
            </div>
        </div>

        <!-- Help Modal -->
        <div x-data="{ open: false }" x-show="open" @open-help-modal.window="open = true" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full">
                <h3 class="text-xl font-semibold text-nutri-blue dark:text-blue-400 mb-4">{{ __('Email Marketing Help') }}</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4">{{ __('Use this tool to send emails to users. Select a recipient type, enter a subject and message, and click Send Email. Use the preview button to see how the email will look.') }}</p>
                <button @click="open = false" class="bg-nutri-blue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">{{ __('Close') }}</button>
            </div>
        </div>

        <!-- Preview Modal -->
        <div x-data="{ open: false, content: '' }" x-show="open" @preview-email.window="open = true; content = previewContent" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full max-h-[80vh] overflow-auto">
                <h3 class="text-xl font-semibold text-nutri-blue dark:text-blue-400 mb-4">{{ __('Email Preview') }}</h3>
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900">
                    <div class="bg-nutri-blue text-white p-4 text-center rounded-t-lg">
                        <h1 class="text-xl font-bold">{{ __('NutriSnap') }}</h1>
                    </div>
                    <div class="p-4 text-gray-900 dark:text-gray-100">
                        <p class="mb-4">{{ __('Hello') }} {{ __('User') }},</p>
                        <div x-html="content.message || '{{ __('Enter email message') }}'"></div>
                        <a href="{{ url('/') }}" class="inline-block bg-nutri-blue text-white px-6 py-2 rounded-lg mt-4 hover:bg-blue-700">{{ __('Visit NutriSnap') }}</a>
                    </div>
                    <div class="bg-nutri-dark text-white p-4 text-center rounded-b-lg text-sm">
                        <p>{{ __('NutriSnap - AI-powered calorie tracking for a healthier you.') }}</p>
                        <p>
                            <a href="{{ url('/unsubscribe') }}" class="text-nutri-blue hover:underline">{{ __('Unsubscribe') }}</a> | 
                            <a href="{{ url('/privacy') }}" class="text-nutri-blue hover:underline">{{ __('Privacy Policy') }}</a>
                        </p>
                        <p>&copy; 2025 {{ __('NutriSnap') }}. {{ __('All rights reserved.') }}</p>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="open = false" class="bg-nutri-blue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.4/tinymce.min.js" onerror="console.error('TinyMCE failed to load')"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js" onerror="console.error('TomSelect failed to load')"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
    .animate-slide-up {
        animation: slideUp 0.5s ease-out forwards;
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .tom-select .ts-input {
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    .tom-select .ts-input:focus {
        outline: none;
        box-shadow: 0 0 0 2px #3B82F6;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize TinyMCE
        try {
            tinymce.init({
                selector: '#message',
                plugins: 'advlist autolink lists link charmap preview anchor',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link',
                menubar: false,
                height: 300,
                skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
                content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                setup: (editor) => {
                    editor.on('change', () => {
                        Alpine.store('formData').previewContent.message = editor.getContent();
                    });
                }
            });
        } catch (e) {
            console.error('TinyMCE initialization failed:', e);
        }

        // Initialize TomSelect
        function initTomSelect() {
            try {
                document.querySelectorAll('.tom-select').forEach(select => {
                    if (!select.tomselect) {
                        new TomSelect(select, {
                            maxOptions: 50,
                            placeholder: select.id === 'recipient_email' ? '{{ __('Enter email or ID') }}' : '{{ __('Select users') }}',
                            searchField: ['text'],
                            render: {
                                option: (data, escape) => `<div>${escape(data.text)}</div>`,
                                item: (data, escape) => `<div>${escape(data.text)}</div>`
                            }
                        });
                    }
                });
            } catch (e) {
                console.error('TomSelect initialization failed:', e);
            }
        }

        // Alpine.js store for form data
        Alpine.store('formData', {
            previewContent: {
                subject: '',
                message: ''
            }
        });

        // Listen for TomSelect initialization
        document.addEventListener('init-tom-select', initTomSelect);

        // Initial TomSelect setup
        initTomSelect();

        // SweetAlert2 Confirmation
        function showConfirmation(event) {
            event.preventDefault();
            Swal.fire({
                title: '{{ __('Confirm Email Send') }}',
                text: '{{ __('Are you sure you want to send this email?') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3B82F6',
                cancelButtonColor: '#EF4444',
                confirmButtonText: '{{ __('Yes, Send') }}',
                cancelButtonText: '{{ __('Cancel') }}',
                showLoaderOnConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = event.target;
                    form.querySelector('button[type=submit]').setAttribute('x-data', '{ isLoading: true }');
                    form.submit();
                }
            }).catch(err => {
                console.error('SweetAlert2 error:', err);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to show confirmation dialog.',
                    icon: 'error'
                });
            });
        }

        // Feather Icons
        try {
            feather.replace();
        } catch (e) {
            console.error('Feather Icons initialization failed:', e);
        }
    });
</script>
@endsection