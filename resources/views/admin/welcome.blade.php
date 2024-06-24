@auth('admin')
    Welcome, {{ Auth::guard('admin')->user()->name }}

    <form method="post" action="{{route("admin.logout")}}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endauth

<a href="{{route('admin.manager_admin.create')}}">Add new admin</a>

@if(session('success'))
    <p style="color: green">{{session('success')}}</p>
@endif
