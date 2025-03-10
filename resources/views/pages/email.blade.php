@extends('layouts.vertical',['title' => 'Inbox'])

@section('css')
    @vite(['node_modules/quill/dist/quill.snow.css'])
    @vite(['node_modules/quill/dist/quill.core.css'])
@endsection

@section('content')

@include("layouts.partials.page-title", ["title" => "Inbox"])

<div class="card">
    <div class="d-flex">
        <div class="email-sidebar">
            <div class="offcanvas-xxl offcanvas-start" tabindex="-1" id="email-sidebar" aria-labelledby="email-sidebarLabel">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-2 align-items-center mb-2">
                        <button type="button" class="btn btn-danger fw-semibold w-100" data-bs-toggle="modal" data-bs-target="#email-compose-modal">Compose</button>

                        <button type="button" class="btn btn-sm btn-icon btn-soft-danger ms-auto d-xl-none" data-bs-dismiss="offcanvas" data-bs-target="#email-sidebar" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>

                    <div class="email-menu-list d-flex flex-column gap-1">
                        <a href="javascript: void(0);" class="active">
                            <iconify-icon icon="solar:inbox-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>Inbox</span>
                            <span class="badge bg-danger-subtle fs-12 text-danger ms-auto">21</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:map-arrow-right-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>Sent</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:star-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>Starred</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>Scheduled</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:clapperboard-edit-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>Draft</span>
                        </a>
                    </div>
                </div>

                <div class="card-body border-top border-light">
                    <a href="#" class="btn-link d-flex align-items-center text-muted fw-bold fs-12 text-uppercase mb-0" data-bs-toggle="collapse" data-bs-target="#other" aria-expanded="false" aria-controls="other">Other <i class="ti ti-chevron-down ms-auto"></i></a>
                    <div id="other" class="collapse show">
                        <div class="email-menu-list d-flex flex-column gap-1 mt-2">
                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:mailbox-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                                <span>All Mail</span>
                            </a>

                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                                <span>Trash</span>
                            </a>
                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:info-square-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                                <span>Spam</span>
                            </a>
                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:chat-round-line-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                                <span>Chats</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body border-top border-light">
                    <a href="#" class="btn-link d-flex align-items-center text-muted fw-bold fs-12 text-uppercase mb-0" data-bs-toggle="collapse" data-bs-target="#labels" aria-expanded="false" aria-controls="labels">Labels <i class="ti ti-chevron-down ms-auto"></i></a>
                    <div id="labels" class="collapse show">
                        <div class="email-menu-list d-flex flex-column gap-1 mt-2">
                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-success fs-16 me-2"></iconify-icon>
                                <span>Personal</span>
                            </a>

                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-danger fs-16 me-2"></iconify-icon>
                                <span>Client</span>
                            </a>

                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-info fs-16 me-2"></iconify-icon>
                                <span>Marketing</span>
                            </a>

                            <a href="javascript: void(0);">
                                <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-secondary fs-16 me-2"></iconify-icon>
                                <span>Office</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-grow-1 card rounded-0 shadow-none mb-0">
            <div class="border-start border-light h-100">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-light d-xxl-none d-flex p-1" data-bs-toggle="offcanvas" data-bs-target="#email-sidebar" aria-controls="email-sidebar">
                            <i class="ti ti-menu-2 fs-17"></i>
                        </button>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-icon btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="<span class='fs-12'>Mark as read</span>">
                                <i class="ti ti-mail-opened fs-18"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="<span class='fs-12'>Archive</span>">
                                <i class="ti ti-archive fs-18"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="<span class='fs-12'>Delete</span>">
                                <i class="ti ti-trash fs-18"></i>
                            </button>

                            <button type="button" class="btn btn-icon btn-sm btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="<span class='fs-12'>Report spam</span>">
                                <i class="ti ti-info-hexagon fs-18"></i>
                            </button>
                        </div>

                        <div class="ms-auto d-xl-flex d-none">
                            <div class="app-search">
                                <input type="text" class="form-control rounded-pill" placeholder="Search mail...">
                                <i class="ti ti-mail-search fs-18 app-search-icon text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top border-light">
                    <div class="table-responsive">
                        <table class="table table-hover mail-list mb-0">
                            <tbody>
                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-2.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">George Thomas</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Request For Information </a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> I hope you are doing well. I have a small request. Can you please...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Jan 5, 3:45 PM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-danger fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star-filled"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-3.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Robert C. Lane</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Invitation For Meeting </a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Good Morning, I hope this email finds you well. I am writing to extra...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> <i class="ti ti-paperclip"></i> 2 </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Mar 23, 7:30 AM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-success fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/brands/dribbble.svg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Dribbble</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Become a successful self-taught designer </a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> There's no one right way to learn design. In fa...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Apr 10, 1:15
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-info fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star-filled"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-5.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Darren C. Gallimore</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Holiday Notice</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Good Evening, I hope you are doing well. I have a small request.</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">May 8, 9:45 PM</p>
                                    </td>

                                    <td class="pe-3">

                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-9.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Mike A. Bell</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Offer Letter</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Thank you for applying. I hope you are doing well. I have a small.</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">Jun 16, 6:00 AM</p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-secondary fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star-filled"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-6.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Bennett C. Rice</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Apology Letter</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> I hope you are doing well. I have a small request. Can you please</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> <i class="ti ti-paperclip"></i> 4 </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Jun 16, 6:00 AM
                                        </p>
                                    </td>

                                    <td class="pe-3">

                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/brands/gitlab.svg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">John J. Bowser</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">How to get started on Gitlab</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> We know setting off on a freelancing journey can feel intim...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> <i class="ti ti-paperclip"></i> 3 </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Aug 22, 2:35 AM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                    </td>
                                </tr>


                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-8.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Jill N. Neal</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Apply For Executive Position</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> I am writing to express my keen interest in the Executive Po...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Aug 22, 2:35 AM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-success fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/brands/instagram.svg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Instagram</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">You have received 2 new followers</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> 2 new followers, 1 new collected project, and more at...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Oct 31, 8:00 AM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-info fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/brands/amazon.svg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Amazon</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Your order is shipped</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Your order is on the way with tracking...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> <i class="ti ti-paperclip"></i> 1 </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Nov 19, 10:10 PM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-success fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>


                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star-filled"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-7.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Alfredo D. Rico</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Class schedule</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Your online class will be held on Saturday at 2:30 pm Bangladesh.</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Dec 25, 12:30 PM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-secondary fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/users/avatar-4.jpg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Vernon B. Rutter</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Invitation to attend our Exclusive Webinar</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> An exclusive webinar will be held on 23 January...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Jan 30, 4:50 AM
                                        </p>
                                    </td>

                                    <td class="pe-3">

                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star-filled"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/brands/digital-ocean.svg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Digital Ocean</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">Update to Discord's Policies</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Hey! we wanted to let you know that we are updating our Te...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> &nbsp; </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            Feb 9, 9:05 PM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-danger fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>

                                <tr class="position-relative">
                                    <td class="ps-3">
                                        <input class="form-check-input position-relative z-2" type="checkbox">
                                    </td>

                                    <td>
                                        <button class="btn p-0 text-warning fs-16 flex-shrink-0"><i class="ti ti-star-filled"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/images/brands/linkedin.svg" alt="user avatar" class="avatar-md rounded-circle">

                                            <h5 class="fs-14 mb-0 fw-medium">
                                                <a href="#!" class="link-reset text-truncate">Linkedin</a>
                                            </h5>
                                        </div>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" href="#email-details-modal" role="button" aria-controls="email-details-modal" class="link-reset text-truncate fs-14 fw-semibold stretched-link">New job similar to UI/UX</a>
                                    </td>

                                    <td>
                                        <div>
                                            <span class="fs-14 text-muted text-truncate mb-0"> Jobs similar to UI/UX Designer at St Trinity Property group and s...</span>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="#!" class="link-reset text-truncate text-nowrap"> <i class="ti ti-paperclip"></i> 4 </a>
                                    </td>

                                    <td>
                                        <p class="fs-12 text-muted mb-0 text-end text-truncate">
                                            May 17, 3:45 PM
                                        </p>
                                    </td>

                                    <td class="pe-3">
                                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="text-success fs-16 ms-2 align-middle"></iconify-icon>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Mail Details modal -->
<div id="email-details-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="email-details-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header d-flex flex-wrap gap-2 align-items-start">
                <img class="me-2 rounded-circle" src="/images/users/avatar-2.jpg" alt="placeholder image" height="40">
                <div class="flex-grow-1">
                    <h6 class="fs-16">Steven Smith</h6>
                    <p class="text-muted mb-0">From: jonathan@domain.com</p>
                </div>

                <div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body">
                <h5 class="fs-18">Your elite author Graphic Optimization reward is ready!</h5>

                <hr>

                <p>Hi Coderthemes!</p>
                <p>Clicking ‘Order Service’ on the right-hand side of the above page will present you with an order page. This service has the following Briefing Guidelines that will need to be filled before placing your order:</p>
                <ol>
                    <li>Your design preferences (Color, style, shapes, Fonts, others) </li>
                    <li>Tell me, why is your item different? </li>
                    <li>Do you want to bring up a specific feature of your item? If yes, please tell me </li>
                    <li>Do you have any preference or specific thing you would like to change or improve on your item page? </li>
                    <li>Do you want to include your item's or your provider's logo on the page? if yes, please send it to me in vector format (Ai or EPS) </li>
                    <li>Please provide me with the copy or text to display</li>
                </ol>

                <p>Filling in this form with the above information will ensure that they will be able to start work quickly.</p>
                <p>You can complete your order by putting your coupon code into the Promotional code box and clicking ‘Apply Coupon’.</p>
                <p><b>Best,</b> <br> Graphic Studio</p>
                <hr>

                <h5 class="mb-3">Attachments</h5>

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-1 shadow-none border border-light">
                            <div class="p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-lg">
                                            <span class="avatar-title bg-soft-primary text-primary rounded">
                                                .ZIP
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <a href="javascript:void(0);" class="text-muted fw-bold">Osen-admin-design.zip</a>
                                        <p class="mb-0">2.3 MB</p>
                                    </div>
                                    <div class="col-auto">
                                        <!-- Button -->
                                        <a href="javascript:void(0);" class="btn btn-link btn-lg text-muted">
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card mb-1 shadow-none border border-light">
                            <div class="p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="/images/sellers/s-6.svg" class="avatar-lg rounded" alt="file-image">
                                    </div>
                                    <div class="col ps-0">
                                        <a href="javascript:void(0);" class="text-muted fw-bold">Dashboard-design.jpg</a>
                                        <p class="mb-0">3.25 MB</p>
                                    </div>
                                    <div class="col-auto">
                                        <!-- Button -->
                                        <a href="javascript:void(0);" class="btn btn-link btn-lg text-muted">
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
            </div>

            <div class="modal-footer gap-1 py-2">
                <button class="btn btn-primary" data-bs-target="#email-compose-modal" data-bs-toggle="modal"><i class="align-text-bottom me-1 ti ti-arrow-back-up"></i> Reply</button>
                <button class="btn btn-primary" data-bs-target="#email-compose-modal" data-bs-toggle="modal"><i class="align-text-bottom me-1 ti ti-arrow-big-right"></i> Forward</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Mail Compose Modal -->
<div id="email-compose-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="email-compose-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h4 class="modal-title" id="email-compose-modalLabel">New Message</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form>
                    <div class="mb-2">
                        <label for="msgto" class="form-label">To</label>
                        <input type="text" id="msgto" class="form-control" placeholder="Example@email.com">
                    </div>
                    <div class="mb-2">
                        <label for="mailsubject" class="form-label">Subject</label>
                        <input type="text" id="mailsubject" class="form-control" placeholder="Your subject">
                    </div>

                    <div>
                        <label class="form-label">Message</label>
                        <div id="mail-compose" style="height: 150px;">
                            <p>Writing something...</p>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Send Message</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@section('scripts')
    @vite(['resources/js/pages/apps-email.js'])
@endsection






