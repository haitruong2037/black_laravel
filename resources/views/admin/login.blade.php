<form action="{{route('admin.login')}}" method="POST" >
    @csrf
    <div class="fv-row mb-8">
        <!--begin::Email-->
        <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" value=""/>
        <!--end::Email-->
    </div>

    <!--end::Input group--->
    <div class="fv-row mb-3">
        <!--begin::Password-->
        <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" value=""/>
        <!--end::Password-->
    </div>

    <div class="fv-row mb-3">
        <!--begin::Password-->
        <button type="submit">Login</button>
        <!--end::Password-->
    </div>
</form>