<x-frontend::layout>

    <section class="min-h-screen flex items-center justify-center bg-white dark:bg-gray-900 px-4">

        <div class="w-full max-w-md bg-gray-100 dark:bg-gray-800 rounded-xl shadow-lg p-8 space-y-6">

            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white tracking-wide">
                    OTP Verification
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                    Please enter the 4-digit OTP sent to your phone.
                </p>
            </div>

            <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="phone" value="{{ old('phone', $phone ?? '') }}">

                <div>
                    <label for="otp" class="sr-only">One-Time Password (OTP)</label>
                    <div class="flex justify-center gap-2" id="otp-input-group">
                        @for ($i = 0; $i < 4; $i++)
                            <input type="text" name="otp[]" maxlength="1" inputmode="numeric"
                                class="w-12 h-12 text-center text-xl rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all caret-indigo-500"
                                autocomplete="one-time-code" @if ($i == 0) autofocus @endif
                                data-index="{{ $i }}">
                        @endfor
                    </div>

                    @error('otp')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800">
                    Verify OTP
                </button>
            </form>

            <div class="text-center">
                <form method="POST" action="{{ route('otp.resend') }}" id="resend-form" class="hidden">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    <button type="submit"
                        class="w-full py-3 px-4 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 rounded-lg text-gray-800 dark:text-gray-200 font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800">
                        Resend OTP
                    </button>
                </form>

                <p id="countdown-text" class="text-xs text-gray-600 dark:text-gray-400 mt-4">
                    You can resend OTP in <span id="countdown">60</span>s
                </p>
            </div>

            @if (session('status'))
                <div class="text-sm text-green-600 dark:text-green-400 text-center">
                    {{ session('status') }}
                </div>
            @endif
        </div>

        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const inputs = document.querySelectorAll('#otp-input-group input');

                    inputs.forEach((input, index) => {
                        input.addEventListener('input', (e) => {
                            if (e.target.value.length === 1 && index < inputs.length - 1) {
                                inputs[index + 1].focus();
                            }
                        });

                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Backspace' && e.target.value === '') {
                                if (index > 0) {
                                    inputs[index - 1].focus();
                                }
                            }

                            // Block non-numeric input
                            if (e.key.length === 1 && !/\d/.test(e.key)) {
                                e.preventDefault();
                            }
                        });
                    });

                    const resendForm = document.getElementById('resend-form');
                    const countdownText = document.getElementById('countdown-text');
                    const countdownSpan = document.getElementById('countdown');
                    let timeLeft = 60;

                    const countdown = setInterval(() => {
                        timeLeft--;
                        countdownSpan.textContent = timeLeft;

                        if (timeLeft <= 0) {
                            clearInterval(countdown);
                            countdownText.classList.add('hidden');
                            resendForm.classList.remove('hidden');
                        }
                    }, 1000);
                });
            </script>
        @endpush

    </section>

</x-frontend::layout>
