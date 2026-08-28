<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Gudang & Kasir') - PT Sinar Abadi</title>
    @vite('resources/css/app.css')

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Flowbite CSS & JS CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 antialiased font-sans">

    <!-- Flowbite Top Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <!-- Mobile Hamburger Button -->
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Buka Sidebar</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <!-- Logo & Brand Name -->
                    <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24 items-center">
                        <svg class="w-7 h-7 me-2 text-blue-600 dark:text-blue-500" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span
                            class="self-center text-lg font-bold sm:text-xl whitespace-nowrap text-gray-900 dark:text-white">PT
                            Sinar Abadi</span>
                    </a>
                </div>

                <!-- User Profile & Dropdown -->
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Buka User Menu</span>
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </div>
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                            id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" role="none">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs font-medium text-gray-500 truncate dark:text-gray-300" role="none">
                                    {{ auth()->user()->email }} ({{ strtoupper(auth()->user()->role) }})
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                            role="menuitem">
                                            Keluar (Logout)
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flowbite Sidebar -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium text-sm">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center p-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                        <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>

                <!-- Transaksi Header -->
                <li class="pt-4 mt-4 space-y-2 border-t border-gray-200 dark:border-gray-700">
                    <span
                        class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Transaksi</span>
                </li>
                <li>
                    <a href="{{ route('transaksi.index') }}"
                        class="flex items-center p-2 rounded-lg {{ request()->routeIs('transaksi.index') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span class="ms-3">Riwayat Transaksi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaksi.jual') }}"
                        class="flex items-center p-2 rounded-lg {{ request()->routeIs('transaksi.jual') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="ms-3">Kasir / Penjualan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaksi.masuk') }}"
                        class="flex items-center p-2 rounded-lg {{ request()->routeIs('transaksi.masuk') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                        <span class="ms-3">Barang Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaksi.transfer') }}"
                        class="flex items-center p-2 rounded-lg {{ request()->routeIs('transaksi.transfer') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span class="ms-3">Transfer Gudang</span>
                    </a>
                </li>

                <!-- Master Data (Khusus Admin) -->
                @if (auth()->user()->role === 'admin')
                    <li class="pt-4 mt-4 space-y-2 border-t border-gray-200 dark:border-gray-700">
                        <span
                            class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Master
                            Data</span>
                    </li>
                    <li>
                        <a href="{{ route('barang.index') }}"
                            class="flex items-center p-2 rounded-lg {{ request()->routeIs('barang.*') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="ms-3">Master Barang</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gudang.index') }}"
                            class="flex items-center p-2 rounded-lg {{ request()->routeIs('gudang.*') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <span class="ms-3">Master Gudang</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelanggan.index') }}"
                            class="flex items-center p-2 rounded-lg {{ request()->routeIs('pelanggan.*') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span class="ms-3">Master Pelanggan</span>
                        </a>
                    </li>

                    <!-- Laporan (Khusus Admin) -->
                    <li class="pt-4 mt-4 space-y-2 border-t border-gray-200 dark:border-gray-700">
                        <span
                            class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Laporan</span>
                    </li>
                    <li>
                        <a href="{{ route('laporan.index') }}"
                            class="flex items-center p-2 rounded-lg {{ request()->routeIs('laporan.*') ? 'bg-blue-50 text-blue-600 dark:bg-gray-700 dark:text-blue-400 font-semibold' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }} group">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="ms-3">Kartu & Mutasi Stok</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </aside>

    <!-- Main Content Body -->
    <div class="p-4 sm:ml-64 pt-20">
        <!-- Header Page Title -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@yield('header', 'Halaman')</h1>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ date('d F Y') }}</span>
        </div>

        <!-- Flowbite Dismissible Alert: Success -->
        @if (session('success'))
            <div id="alert-success"
                class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-800"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"
                    data-dismiss-target="#alert-success" aria-label="Close">
                    <span class="sr-only">Tutup</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Flowbite Dismissible Alert: Error / Validation -->
        @if ($errors->any() || session('error'))
            <div id="alert-danger"
                class="p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-800"
                role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="font-bold">Terjadi Kesalahan:</span>
                    <button type="button"
                        class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
                        data-dismiss-target="#alert-danger" aria-label="Close">
                        <span class="sr-only">Tutup</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="mt-2 text-sm">
                    @if (session('error'))
                        <p>{{ session('error') }}</p>
                    @endif
                    @if ($errors->any())
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif

        <!-- Slot Isi Konten -->
        @yield('content')
    </div>

</body>

</html>
