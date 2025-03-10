@extends('layouts.vertical', ['title' => 'Ratings'])

@section('css')
    @vite(['node_modules/sweetalert2/dist/sweetalert2.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Extended UI', 'title' => 'Ratings'])
    <div class="row row-cols-lg-2">
        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">Basic 5 star rater</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div id="rater" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">5 star rater with step</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div id="rater-step" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">Custom Messages Example</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div id="rater2" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">Unlimited Number</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">You can have unlimited number of stars. Example with 16 stars. readOnly option is
                        set to true.</p>
                    <div>
                        <div id="rater3" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">Random Number</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div id="rater4" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">On Hover Event Example</h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="rater5" class="align-middle"></div>
                        <span class="live-rating badge bg-info align-middle ms-2"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">Clear/Reset Rater Example</h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="rater6" class="align-middle"></div>
                        <span class="clear-rating"></span>
                        <button id="rater6-button" class="btn btn-light btn-sm ms-2">Reset</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="col">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">Right to left support</h4>
                </div>
                <div class="card-body">
                    <div dir="rtl">
                        <span id="rater7"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/extended-rating.js'])
@endsection
