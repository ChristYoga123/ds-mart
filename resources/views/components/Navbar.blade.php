<header class="mb-8">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-8">
            <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                {{ env('APP_NAME') }}
            </h1>

            <!-- Navigation Links -->
            <div class="flex space-x-8">
                <a href="{{ route('kasir.index') }}"
                    class="{{ request()->routeIs('kasir.index') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center text-sm font-medium">
                    <i class="fas fa-cash-register mr-2"></i>
                    Kasir
                </a>
                <a href="{{ route('kasir.transaksi.index') }}"
                    class="{{ request()->routeIs('kasir.transaksi.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center text-sm font-medium">
                    <i class="fas fa-history mr-2"></i>
                    Transaksi
                </a>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <p class="text-gray-600">
                Tanggal: <span class="font-medium">{{ Carbon\Carbon::now()->locale('id')->format('l, d F Y') }}</span>
            </p>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white rounded-lg transition duration-200 cursor-pointer">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

{{-- <nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('kasir.index') }}" class="text-2xl font-bold text-indigo-600">
                        DS Mart
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('kasir.index') }}"
                        class="{{ request()->routeIs('kasir.index') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-cash-register mr-2"></i>
                        Kasir
                    </a>
                    <a href="{{ route('kasir.transaksi.index') }}"
                        class="{{ request()->routeIs('kasir.transaksi.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-history mr-2"></i>
                        Transaksi
                    </a>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center">
                <!-- Profile Dropdown -->
                <div class="ml-3 relative">
                    <div>
                        <button type="button"
                            class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <div
                                class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </div>

                    <!-- Dropdown menu -->
                    <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1"
                        id="user-menu">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem" tabindex="-1">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('kasir.index') }}"
                class="{{ request()->routeIs('kasir.index') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-cash-register mr-2"></i>
                Kasir
            </a>
            <a href="{{ route('kasir.transaksi.index') }}"
                class="{{ request()->routeIs('kasir.transaksi.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                <i class="fas fa-history mr-2"></i>
                Transaksi
            </a>
        </div>
    </div>
</nav> --}}

<script>
    // Toggle mobile menu
    document.getElementById('user-menu-button').addEventListener('click', function() {
        document.getElementById('user-menu').classList.toggle('hidden');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('user-menu');
        const userMenuButton = document.getElementById('user-menu-button');

        if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.add('hidden');
        }
    });
</script>
