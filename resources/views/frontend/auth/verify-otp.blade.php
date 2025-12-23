<x-frontend::layout>
    <section class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-slate-900">
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8">
            <div class="flex items-center space-x-2 mb-8">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-base font-bold text-gray-800 dark:text-white">Back To Home</h1>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Verify Your Phone</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Please enter the 4-digit OTP we sent to your phone number.
            </p>

            @if (session('status'))
                <div class="text-sm text-green-600 dark:text-green-400 text-center mb-4">
                    {{ session('status') }}
                </div>
            @endif

            @error('otp')
                <p class="mt-2 text-sm text-red-500 text-center">{{ $message }}</p>
            @enderror

            <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="phone" value="{{ $phone }}">
                
                <div class="flex justify-center space-x-2" id="otp-input-group">
                    @for ($i = 0; $i < 4; $i++)
                        <input type="text" name="otp[]" maxlength="1" inputmode="numeric"
                            class="w-12 h-12 text-center text-3xl font-bold text-gray-900 dark:text-white rounded-md bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                            autocomplete="one-time-code" @if ($i == 0) autofocus @endif
                            data-index="{{ $i }}">
                    @endfor
                </div>

                <div class="flex items-center justify-between mt-4">
                    <button type="button" id="resend-button"
                        class="text-sm font-medium text-indigo-500 disabled:text-gray-400" disabled>
                        Resend Code (<span id="countdown">60</span>s)
                    </button>

                    <button type="submit"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Verify
                    </button>
                </div>
            </form>
        </div>
    </section>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputs = document.querySelectorAll('#otp-input-group input');
                const resendButton = document.getElementById('resend-button');
                const countdownSpan = document.getElementById('countdown');
                const resendUrl = '{{ route('otp.resend') }}';
                const phone = '{{ $phone }}';
                let timeLeft = 60;
                let countdownInterval;

                // OTP input logic
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
                    });

                    input.addEventListener('paste', (e) => {
                        e.preventDefault();
                        const pasteData = e.clipboardData.getData('text').trim().slice(0, 4);
                        if (/\d{4}/.test(pasteData)) {
                            pasteData.split('').forEach((char, i) => {
                                if (inputs[i]) inputs[i].value = char;
                            });
                            inputs[inputs.length - 1].focus();
                        }
                    });
                });

                // Countdown logic
                const startCountdown = () => {
                    resendButton.disabled = true;
                    resendButton.classList.add('cursor-not-allowed');
                    countdownInterval = setInterval(() => {
                        timeLeft--;
                        countdownSpan.textContent = timeLeft;
                        if (timeLeft <= 0) {
                            clearInterval(countdownInterval);
                            resendButton.disabled = false;
                            resendButton.classList.remove('cursor-not-allowed');
                            resendButton.textContent = 'Resend Code';
                        }
                    }, 1000);
                };

                // Resend OTP via POST
                resendButton.addEventListener('click', async () => {
                    resendButton.disabled = true;
                    resendButton.textContent = 'Sending...';

                    try {
                        const response = await fetch(resendUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ phone }),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            timeLeft = 60;
                            resendButton.textContent = `Resend Code (${timeLeft}s)`;
                            startCountdown();
                            console.log('OTP resent successfully.');
                        } else {
                            console.error('Failed to resend OTP:', data.message);
                            resendButton.textContent = 'Resend Code';
                            resendButton.disabled = false;
                        }
                    } catch (error) {
                        console.error('Resend error:', error);
                        resendButton.textContent = 'Resend Code';
                        resendButton.disabled = false;
                    }
                });

                // Start initial countdown
                startCountdown();
            });
        </script>
    @endpush
</x-frontend::layout>
