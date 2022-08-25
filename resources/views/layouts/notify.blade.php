@if (Session::has('success-message') || count($errors) > 0||Session::has('warning-message') || Session::has('success'))
@if (Session::has('success-message'))
    displayMessage('{{ session('success-message') }}', 'success')
@endif

@if (Session::has('success'))
    displayMessage('{{ session('success') }}', 'success')
@endif

@if (Session::has('warning-message'))
    displayMessage('{{ session('warning-message') }}', 'warning')
@endif

@if (count($errors) > 0)
    @foreach ($errors->all() as $error)
        displayMessage('{{ $error }}', 'danger')
    @endforeach
@endif
@endif
