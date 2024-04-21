<h1>Retrieving data from remote server API</h1>


<a href="/retrieve">Retrieve</a>

@if (session()->has('success'))
    <div>
        {{ session('success') }}
    </div>
@endif

@if (session()->has('error'))
    <div>
        {{ session('error') }}
    </div>
@endif

