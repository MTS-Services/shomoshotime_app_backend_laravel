<h1>{{ 'This is user dashboard' }}</h1>

<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">{{ __('Logout') }}</button>
</form>
