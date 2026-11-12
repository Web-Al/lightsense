<x-layouts.auth title="Create account" badge="New account" heading="Start automating" accent="your lights"
    blurb="Register to connect your devices and manage your lighting from any browser.">
    <h2 class="text-xl font-bold">Create your account</h2>
    <p class="mt-1 text-xs text-neutral-500">Fill in your details to get started.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="name" class="label">Full name</label>
            <input id="name" name="name" value="{{ old('name') }}" placeholder="Your full name" required autofocus class="field">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required class="field">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <x-password-input name="password" label="Password" hint="Use at least 8 characters and a number." />
        <x-password-input name="password_confirmation" label="Confirm password" />

        <div>
            <label class="flex items-start gap-2 text-xs text-neutral-600">
                <input type="checkbox" name="terms" value="1" class="mt-0.5 size-3.5 rounded accent-amber">
                <span>I agree to the <a href="#" class="font-semibold text-amber-dark underline">Terms of Service</a> and <a href="#" class="font-semibold text-amber-dark underline">Privacy Policy</a>.</span>
            </label>
            @error('terms') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <button class="btn btn-amber w-full">Create account</button>
    </form>

    <p class="mt-5 text-center text-xs text-neutral-500">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-amber-dark">Log in</a></p>
</x-layouts.auth>
