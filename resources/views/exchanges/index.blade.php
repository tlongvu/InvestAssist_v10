<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Công ty Chứng khoán') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Danh sách Công ty Chứng khoán</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tên Công ty / Tài khoản</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($exchanges as $exchange)
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $exchange->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-6 py-4 text-center text-sm text-slate-500">Chưa có công ty chứng khoán nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $exchanges->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
