<x-layouts.auth title="Log in" badge="Dashboard access" heading="Welcome back" accent="to LightSense"
    blurb="Log in to check room status, review energy use and manage schedules from one dashboard.">
    <h2 class="text-xl font-bold">Log in</h2>
    <p class="mt-1 text-xs text-neutral-500">Welcome back. Enter your details to continue.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus class="field">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <x-password-input name="password" label="Password" :link="['href' => '#', 'text' => 'Forgot password?']" />

        <label class="flex items-center gap-2 text-xs text-neutral-600">
            <input type="checkbox" name="remember" class="size-3.5 rounded border-neutral-300 accent-amber"> Keep me signed in
        </label>

        <button class="btn btn-amber w-full">Log in</button>
    </form>

    <p class="mt-5 text-center text-xs text-neutral-500">Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-amber-dark">Register</a></p>
</x-layouts.auth>
