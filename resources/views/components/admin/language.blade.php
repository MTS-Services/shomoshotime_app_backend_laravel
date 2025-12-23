
<form action="{{ route('lang.change') }}" method="POST">
    @csrf
    <select name="lang"
        class="select focus:outline-0 focus-within:outline-0 focus:ring-0 focus:border-border-primary bg-bg-light dark:bg-bg-dark border-border-tertiary shadow-none w-fit min-w-30 px-auto"
        onchange="this.form.submit()">

        <option value="en" {{session()->get('locale') == 'en' ? 'selected' : '' }}>
            {{'English' }}
        </option>
        <option value="ar" {{ session()->get('locale') == 'ar' ? 'selected' : '' }}>
            {{ 'Arabic' }}
        </option>

    </select>
</form>
