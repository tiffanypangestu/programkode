@extends('auth.layouts.app')
@section('title', 'Register')

@section('content')
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-gray-900">Buat Akun</h1>
        <p class="mt-2 text-sm leading-6 text-gray-500">
            Daftarkan akun untuk menggunakan sistem BRC Gold Cosmetics.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="nama_lengkap" class="mb-2 block text-sm font-semibold text-gray-700">
                Nama Lengkap
            </label>

            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                placeholder="Masukkan Nama Lengkap" autocomplete="name" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
                Nama Pengguna
            </label>

            <input type="text" name="name" id="name" value="{{ old('name') }}"
                placeholder="Masukkan nama pengguna" autocomplete="username" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">

        </div>

        <div>
            <label for="jabatan" class="mb-2 block text-sm font-semibold text-gray-700">
                Jabatan
            </label>

            <select name="jabatan" id="jabatan" required
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
                <option value="">Pilih jabatan</option>

                <option value="Admin" {{ old('jabatan') === 'Admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="Supervisor" {{ old('jabatan') === 'Supervisor' ? 'selected' : '' }}>
                    Supervisor
                </option>

                <option value="Beauty Consultant" {{ in_array(old('jabatan'), ['Beauty Consultant', 'BC']) ? 'selected' : '' }}>
                    BC (Beauty Consultant)
                </option>

                <option value="Sales Promotion Girl" {{ in_array(old('jabatan'), ['Sales Promotion Girl', 'SPG']) ? 'selected' : '' }}>
                    SPG (Sales Promotion Girl)
                </option>
            </select>

            @error('jabatan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Input Khusus SPG: Pilih Outlet Tempat Bertugas --}}
        <div id="container-outlet-spg" class="hidden">
            <label for="outlet_id" class="mb-2 block text-sm font-semibold text-gray-700">
                Pilih Outlet Tempat Bertugas <span class="text-red-600">*</span>
            </label>

            <select name="outlet_id" id="outlet_id"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
                <option value="">-- Pilih Outlet Tempat Bertugas --</option>
                @foreach ($outlets ?? [] as $out)
                    <option value="{{ $out->outlet_id }}" {{ old('outlet_id') == $out->outlet_id ? 'selected' : '' }}>
                        {{ $out->nama_outlet }} ({{ $out->wilayah->nama_wilayah ?? 'Wilayah ' . $out->wilayah_id }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Khusus SPG: Tentukan outlet tempat Anda bertugas setiap hari.</p>

            @error('outlet_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Input Khusus BC: Pilih Wilayah (Dalam Kota / Luar Kota / Luar Pulau) --}}
        <div id="container-wilayah-bc" class="hidden">
            <label for="wilayah_id" class="mb-2 block text-sm font-semibold text-gray-700">
                Pilih Wilayah Penugasan <span class="text-red-600">*</span>
            </label>

            <select name="wilayah_id" id="wilayah_id"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
                <option value="">-- Pilih Wilayah (Dalam Kota / Luar Kota / Luar Pulau) --</option>
                @foreach ($wilayahs ?? [] as $w)
                    <option value="{{ $w->wilayah_id }}" {{ old('wilayah_id') == $w->wilayah_id ? 'selected' : '' }}>
                        {{ $w->nama_wilayah }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Khusus Beauty Consultant: Pilih wilayah penugasan counter Anda.</p>

            @error('wilayah_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>


        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">
                Email
            </label>

            <input type="email" name="email" id="email" value="{{ old('email') }}"
                placeholder="nama@perusahaan.com" autocomplete="email" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">
                Password
            </label>

            <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                autocomplete="new-password" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">

        </div>

        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">
                Konfirmasi Password
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password"
                autocomplete="new-password" required
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-[#b18a32] focus:ring-2 focus:ring-[#b18a32]/20">
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-[#a78024] px-4 py-3 font-semibold text-white shadow-md shadow-[#a78024]/20 transition hover:bg-[#89691d] focus:outline-none focus:ring-2 focus:ring-[#a78024] focus:ring-offset-2">
            Daftar Akun
        </button>
    </form>

    <script>
        function sesuaikanFormJabatan() {
            const selectJabatan = document.getElementById('jabatan');
            const containerSpg = document.getElementById('container-outlet-spg');
            const containerBc = document.getElementById('container-wilayah-bc');
            const selectOutlet = document.getElementById('outlet_id');
            const selectWilayah = document.getElementById('wilayah_id');

            if (!selectJabatan || !containerSpg || !containerBc) return;

            const val = selectJabatan.value;

            if (val === 'Sales Promotion Girl' || val === 'SPG') {
                containerSpg.classList.remove('hidden');
                selectOutlet.required = true;

                containerBc.classList.add('hidden');
                selectWilayah.required = false;
            } else if (val === 'Beauty Consultant' || val === 'BC') {
                containerBc.classList.remove('hidden');
                selectWilayah.required = true;

                containerSpg.classList.add('hidden');
                selectOutlet.required = false;
            } else {
                containerSpg.classList.add('hidden');
                containerBc.classList.add('hidden');
                selectOutlet.required = false;
                selectWilayah.required = false;
            }
        }

        document.getElementById('jabatan').addEventListener('change', sesuaikanFormJabatan);
        document.addEventListener('DOMContentLoaded', sesuaikanFormJabatan);
    </script>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-[#9b7624] hover:underline">
            Masuk di sini
        </a>
    </p>
@endsection
