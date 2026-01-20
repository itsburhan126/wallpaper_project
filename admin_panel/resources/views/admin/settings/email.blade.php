@extends('layouts.admin')

@section('header', 'Email Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.email.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- SMTP Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">SMTP Configuration</h3>
                        <p class="text-sm text-gray-500">Configure your email sending service (SMTP)</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <!-- Mailer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mailer</label>
                    <select name="mail_mailer" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                        <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="log" {{ ($settings['mail_mailer'] ?? '') == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Host -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                            placeholder="smtp.gmail.com">
                    </div>

                    <!-- Port -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                            placeholder="587">
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                        <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                            placeholder="email@example.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                        <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                            placeholder="App Password or SMTP Password">
                    </div>

                    <!-- Encryption -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- From Address Configuration -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fas fa-at"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">From Address</h3>
                        <p class="text-sm text-gray-500">Default sender information for system emails</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- From Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                        <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                            placeholder="noreply@example.com">
                    </div>

                    <!-- From Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? config('app.name') }}" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all placeholder-gray-400"
                            placeholder="My App">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Save Email Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection