<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials.title-meta', ['title' => 'Log Out'])

    @include('layouts.partials.head-css')
</head>

<body class="h-100">

    <div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
        <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
            <div class="col-xl-4 col-lg-5 col-md-6">
                <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">
                    <a href="{{ route ('second' ,['dashboards','index']) }}" class="auth-brand mb-3">
                        <img src="/images/logo-dark.png" alt="dark logo" height="24" class="logo-dark">
                        <img src="/images/logo.png" alt="logo light" height="24" class="logo-light">
                    </a>

                    <h3 class="fw-semibold mb-4">You are Logged Out</h3>

                    <div class="d-flex align-items-center gap-2 mb-3 text-start">
                        <img src="/images/users/avatar-1.jpg" alt="" class="avatar-xl rounded img-thumbnail">
                        <div>
                            <h3 class="fw-semibold text-dark">Hi ! Dhanoo K.</h3>
                            <p class="mb-0">Thank you for using Ocen Admin</p>
                        </div>
                    </div>

                    <div class="mb-3 text-start">
                        <div class="bg-success-subtle p-2 rounded fw-medium mb-0" role="alert">
                            <p class="mb-0 text-success">You have been successfully logged out of your account. To continue using our services, please log in again with your credentials. If you encounter any issues, feel free to contact our support team for assistance.</p>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit">Support Center</button>
                    </div>
                    <p class="text-danger fs-14 my-3">Back to <a href="{{ route ('second' , ['auth','login']) }}" class="text-dark fw-semibold ms-1">Login !</a>

                    <p class="mt-auto mb-0">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © Osen - By <span class="fw-bold text-decoration-underline text-uppercase text-reset fs-12">Coderthemes</span>
                    </p>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.partials.footer-scripts')

</body>

</html>