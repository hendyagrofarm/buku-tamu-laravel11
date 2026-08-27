<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><script src="https://cdn.tailwindcss.com"></script><title>Login Petugas</title></head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 p-5 antialiased">
<div class="mx-auto flex min-h-[calc(100vh-2.5rem)] max-w-md items-center">
    <div class="w-full rounded-[2rem] bg-white p-7 shadow-2xl sm:p-9">
        <a href="<?php echo e(route('kiosk.home')); ?>" class="text-sm font-semibold text-emerald-700">← Kembali ke mode tamu</a>
        <div class="mt-7"><p class="text-xs font-bold uppercase tracking-[.25em] text-emerald-700">Buku Tamu Digital</p><h1 class="mt-2 text-3xl font-bold text-slate-900">Login Petugas</h1><p class="mt-2 text-sm leading-6 text-slate-500">Akun petugas otomatis dibatasi sesuai lokasi yang ditetapkan Administrator.</p></div>
        <?php if(session('success')): ?><div class="mt-5 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div><?php endif; ?>
        <form method="POST" action="<?php echo e(route('login.process')); ?>" class="mt-7 space-y-5"><?php echo csrf_field(); ?>
            <div><label class="mb-2 block text-sm font-semibold">Email</label><input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
            <div><label class="mb-2 block text-sm font-semibold">Password</label><input type="password" name="password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></div>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-emerald-600"> Ingat saya</label>
            <button class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white shadow-lg shadow-emerald-900/20 hover:bg-emerald-800">Masuk ke Dashboard</button>
        </form>
        <div class="mt-6 rounded-xl bg-slate-50 p-4 text-xs leading-5 text-slate-500"><strong>Akun awal:</strong><br>admin@bukutamu.local<br>password123</div>
    </div>
</div>
</body></html>
<?php /**PATH C:\xampp\htdocs\buku-tamu-laravel11\resources\views/auth/login.blade.php ENDPATH**/ ?>