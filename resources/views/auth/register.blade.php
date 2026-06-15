<x-layouts.guest :title="'Daftar'">
    <h2 class="mb-6 text-xl font-bold text-white">Buat Akun Baru</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                   placeholder="Nama lengkap Anda">
            @error('name')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                   placeholder="nama@email.com">
            @error('email')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                   placeholder="Minimal 8 karakter">
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-300">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                   placeholder="Ulangi password">
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-200 hover:from-indigo-500 hover:to-purple-500 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-[0.98]">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-400">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-indigo-400 transition hover:text-indigo-300">Masuk</a>
    </p>
</x-layouts.guest>
