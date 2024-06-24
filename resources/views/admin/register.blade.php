<form action="{{route('admin.manager_admin.store')}}" method="POST" >
    @csrf
    <div class="fv-row mb-8">
        <!--begin::Email-->
        <input type="text" placeholder="Name" name="name" class="form-control bg-transparent" value=""/>
        <!--end::Email-->
        @error('name')
            <span class="text-danger">{{ $message }}</span> 
        @enderror
    </div>
    <div class="fv-row mb-8">
        <!--begin::Email-->
        <input type="email" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" value=""/>
        <!--end::Email-->
        @error('email')
            <span class="text-danger">{{ $message }}</span> 
        @enderror
    </div>

    <!--end::Input group--->
    <div class="fv-row mb-3">
        <!--begin::Password-->
        <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" value=""/>
        <!--end::Password-->
        @error('password')
            <span class="text-danger">{{ $message }}</span> 
        @enderror
    </div>

    <div class="fv-row mb-3">
        <!--begin::Password-->
        <input type="password" placeholder="Confirm Password" name="password_confirmation" class="form-control bg-transparent" value=""/>
        <!--end::Password-->
        @error('password_confirmation')
            <span class="text-danger">{{ $message }}</span> 
        @enderror
    </div>

    <div class="fv-row mb-3">
        <!--begin::Password-->
        <button type="submit">Create</button>
        <!--end::Password-->
    </div>
</form>