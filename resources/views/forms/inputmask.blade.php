@extends('layouts.vertical', ['title' => 'Inputmask '])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Forms', 'title' => 'Inputmask '])
    <div class="card">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title mb-0"> Form Inputmask</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">A Java-Script Plugin to make masks on form fields and HTML elements.</p>

            <!-- Input masks -->
            <div class="row">
                <div class="col-md-6">
                    <form action="#">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="00/00/0000">
                            <span class="fs-13 text-muted">e.g "DD/MM/YYYY"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="00/00/0000"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hour</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="00:00:00">
                            <span class="fs-13 text-muted">e.g "HH:MM:SS"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="00:00:00"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date & Hour</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="00/00/0000 00:00:00">
                            <span class="fs-13 text-muted">e.g "DD/MM/YYYY HH:MM:SS"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="00/00/0000 00:00:00"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="00000-000">
                            <span class="fs-13 text-muted">e.g "xxxxx-xxx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="00000-000"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Crazy Zip Code</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="0-00-00-00">
                            <span class="fs-13 text-muted">e.g "x-xx-xx-xx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="0-00-00-00"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Money</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="000.000.000.000.000,00" data-reverse="true">
                            <span class="fs-13 text-muted">e.g "Your money"</span>
                            <p class="mt-1">Add attribute <code>data-mask-format="000.000.000.000.000,00" data-reverse="true"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Money 2</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="#.##0,00" data-reverse="true">
                            <span class="fs-13 text-muted">e.g "#.##0,00"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="#.##0,00" data-reverse="true"</code></p>
                        </div>

                    </form>
                </div> <!-- end col -->

                <div class="col-md-6">
                    <form action="#">
                        <div class="mb-3">
                            <label class="form-label">Telephone</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="0000-0000">
                            <span class="fs-13 text-muted">e.g "xxxx-xxxx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="0000-0000"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telephone with Code Area</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="(00) 0000-0000">
                            <span class="fs-13 text-muted">e.g "(xx) xxxx-xxxx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="(00) 0000-0000"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">US Telephone</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="(000) 000-0000">
                            <span class="fs-13 text-muted">e.g "(xxx) xxx-xxxx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="(000) 000-0000"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">São Paulo Celphones</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="(00) 00000-0000">
                            <span class="fs-13 text-muted">e.g "(xx) xxxxx-xxxx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="(00) 00000-0000"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="000.000.000-00" data-reverse="true">
                            <span class="fs-13 text-muted">e.g "xxx.xxx.xxxx-xx"</span>
                            <p class="mt-1">Add attribute <code>data-mask-format="000.000.000-00" data-reverse="true"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="00.000.000/0000-00" data-reverse="true">
                            <span class="fs-13 text-muted">e.g "xx.xxx.xxx/xxxx-xx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="00.000.000/0000-00" data-reverse="true"</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">IP Address</label>
                            <input type="text" class="form-control" data-toggle="input-mask" data-mask-format="099.099.099.099" data-reverse="true">
                            <span class="fs-13 text-muted">e.g "xxx.xxx.xxx.xxx"</span>
                            <p class="mt-1">Add attribute <code>data-toggle="input-mask" data-mask-format="099.099.099.099" data-reverse="true"</code></p>
                        </div>
                    </form>
                </div> <!-- end col -->
            </div>


        </div> <!-- end card-body -->
    </div> <!-- end card-->
@endsection
@section('scripts')
    @vite(['resources/js/pages/form-inputmask.js'])
@endsection
