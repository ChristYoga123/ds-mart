@extends('layouts.guest')

@section('content')
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex overflow-hidden">
        <!-- Kiri: Form Login -->
        <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">
            <div class="flex items-center justify-center mb-6">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="200">
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 text-center">Masuk ke akun kasir</h2>
            <p class="text-gray-500 mb-6 text-sm text-center">Silakan login untuk mengakses sistem kasir DS Mart.</p>
            <form class="space-y-4" action="{{ route('authenticate') }}" method="POST">
                @csrf
                @TextInput([
                    'name' => 'name',
                    'label' => 'Username',
                    'placeholder' => 'Username',
                ])
                @TextInput([
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password',
                    'placeholder' => '********',
                ])

                @Button([
                    'type' => 'submit',
                    'text' => 'Masuk',
                ])
            </form>
            {{-- <p class="mt-6 text-center text-sm text-gray-500">Belum punya akun? <a href="#"
                    class="text-gray-900 font-semibold hover:underline">Daftar sekarang, gratis!</a></p> --}}
        </div>
        <!-- Kanan: Info & Ikon -->
        <div
            class="hidden md:flex w-1/2 bg-gradient-to-br from-gray-800 to-black flex-col items-center justify-center text-white relative">
            <div class="flex justify-center mb-8">
                <!-- SVG Keranjang Belanja -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-28 h-28 text-white opacity-90" fill="none"
                    viewBox="0 0 48 48" stroke="currentColor" stroke-width="2">
                    <rect x="8" y="14" width="32" height="20" rx="3" fill="#222" stroke="#fff"
                        stroke-width="2" />
                    <path d="M16 34v2a4 4 0 0 0 4 4h8a4 4 0 0 0 4-4v-2" stroke="#fff" stroke-width="2" />
                    <circle cx="16" cy="38" r="2.5" fill="#fff" />
                    <circle cx="32" cy="38" r="2.5" fill="#fff" />
                    <path d="M12 18h24" stroke="#fff" stroke-width="2" />
                </svg>
            </div>
            <div class="px-8 text-center">
                <div class="font-bold tracking-widest text-sm mb-2">DSMART KASIR</div>
                <div class="text-lg font-semibold leading-relaxed">Kelola transaksi toko Anda dengan mudah, cepat, dan
                    aman. Sistem kasir modern untuk kemudahan operasional harian.</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (session('error'))
        <script>
            toastr.error('{{ session('error') }}');
        </script>
    @endif
@endpush
