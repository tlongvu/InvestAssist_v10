<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Sửa Tài khoản: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Tên -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Họ tên</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vai trò -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700">Vai trò</label>
                            <select id="role" name="role"
                                    class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Đổi mật khẩu (optional) -->
                        <div class="border-t pt-4">
                            <p class="text-sm text-slate-500 mb-3">Để trống nếu không muốn đổi mật khẩu</p>

                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-slate-700">Mật khẩu mới</label>
                                    <input type="password" id="password" name="password"
                                           class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Xác nhận mật khẩu mới</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                                Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                               class="px-4 py-2 bg-slate-100 text-slate-700 rounded-md text-sm font-semibold hover:bg-slate-200 transition">
                                Hủy
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
