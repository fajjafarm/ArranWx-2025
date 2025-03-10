@extends('layouts.vertical',['title' => 'Chat'])



@section('content')

<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">Chat</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Osen</a></li>

            <li class="breadcrumb-item active">Chat</li>
        </ol>
    </div>
</div>




<div class="card">
    <div class="chat d-flex">
        <div class="offcanvas-xxl offcanvas-start" tabindex="-1" id="chatUserList" aria-labelledby="chatUserListLabel">
            <div id="chat-user-list" class="collapse collapse-horizontal show">
                <div class="chat-user-list border-end">
                    <div class="card-body py-2 px-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 py-1">
                            <div class="chat-users">
                                <div class="avatar-lg chat-avatar-online">
                                    <img src="/images/users/avatar-1.jpg" class="img-fluid rounded-circle" alt="Chris Keller">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">
                                    <a href="#!" class="text-reset lh-base">Dhanoo K.</a>
                                </h5>
                                <p class="mb-0 text-muted">Admin</p>
                            </div>
                            <div class="dropdown lh-1">
                                <a href="#" class="dropdown-toggle drop-arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="solar:settings-outline" class="align-middle"></iconify-icon>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="ti ti-user-plus me-1 fs-17 align-middle"></i>
                                        <span class="align-middle">New Contact</span>
                                    </a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="ti ti-users-plus me-1 fs-17 align-middle"></i>
                                        <span class="align-middle">New Group</span>
                                    </a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="ti ti-star me-1 fs-17 align-middle"></i>
                                        <span class="align-middle">Favorites</span>
                                    </a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="ti ti-archive me-1 fs-17 align-middle"></i>
                                        <span class="align-middle">Archive Contacts</span>
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="flex-grow-0 btn btn-sm btn-icon btn-soft-danger d-xl-none" data-bs-dismiss="offcanvas" data-bs-target="#chatUserList" aria-label="Close">
                                <i class="ti ti-x fs-20"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Contact list -->
                    <div class="d-flex flex-column">
                        <div class="users-list position-relative list-scroll" data-simplebar>

                            <div class="px-3 py-2">
                                <div class="app-search py-1">
                                    <input type="text" class="form-control border-light bg-light bg-opacity-50 rounded-2" placeholder="Search something here...">
                                    <i class="app-search-icon ti ti-search text-muted fs-16"></i>
                                </div>
                            </div>

                            <div class="d-flex align-items-center px-3 py-2 bg-body-secondary position-sticky top-0 z-1">
                                <iconify-icon icon="solar:pin-bold-duotone" class="fs-18 text-muted"></iconify-icon>
                                <h5 class="mb-0 ms-1 fw-semibold fs-14">Pinned</h5>
                            </div><!-- end chat-title -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <img src="/images/users/avatar-2.jpg" class="img-fluid rounded-circle" alt="Brandon Smith" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">5:45am</span>
                                            Brandon Smith
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 float-end text-end"><span class="badge bg-danger-subtle text-danger">3</span></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">How are you today?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users active">
                                    <div class="avatar-md chat-avatar-online">
                                        <img src="/images/users/avatar-5.jpg" class="img-fluid rounded-circle" alt="James Zavel" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">4:30am</span>
                                            James Zavel
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-success"><i class="ti ti-checks"></i></span>
                                            <span class="w-75 d-inline-block text-primary fs-12 fw-semibold">typing...</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-online">
                                        <img src="/images/users/avatar-8.jpg" class="img-fluid rounded-circle" alt="Maria Lopez" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">6:12pm</span>
                                            Maria Lopez
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 float-end text-end"><span class="badge bg-danger-subtle text-danger">1</span></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">How are you today?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <div class="h-100 w-100 rounded-circle bg-info text-white d-flex align-items-center justify-content-center">
                                            <span class="fw-semibold">OD</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">6:12pm</span>
                                            Osen Discussion
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">JS Developer's Come in office?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <div class="d-flex align-items-center px-3 py-2 bg-body-secondary position-sticky top-0 z-1">
                                <iconify-icon icon="solar:chat-line-bold-duotone" class="fs-18 text-muted"></iconify-icon>
                                <h5 class="mb-0 ms-1 fw-semibold fs-14">All Messages</h5>
                            </div><!-- end chat-title -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-online">
                                        <img src="/images/users/avatar-3.jpg" class="img-fluid rounded-circle" alt="Brandon Smith" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">3:40am</span>
                                            Eunice Bennett
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">Please check these design </span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <div class="h-100 w-100 rounded-circle bg-warning text-white d-flex align-items-center justify-content-center">
                                            <i class="ti ti-brand-javascript fs-20"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">3:30am</span>
                                            Javascript Team
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-muted"><i class="ti ti-check"></i></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">New Project?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <div class="h-100 w-100 rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center">
                                            <i class="ti ti-brand-figma fs-20"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">3:30am</span>
                                            UI Team
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-muted"><i class="ti ti-checks"></i></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">Project Completed</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <img src="/images/users/avatar-4.jpg" class="img-fluid rounded-circle" alt="Brandon Smith" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">2:33am</span>
                                            Hoyt Bahe
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-success"><i class="ti ti-checks"></i></span>
                                            <span class="w-75 d-inline-block text-primary fs-12 fw-semibold"><i class="ti ti-microphone"></i> Voice Message</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users active">
                                    <div class="avatar-md chat-avatar-online">
                                        <img src="/images/users/avatar-9.jpg" class="img-fluid rounded-circle" alt="James Zavel" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">4:35am</span>
                                            John Otta
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-success"><i class="ti ti-checks"></i></span>
                                            <span class="w-75 d-inline-block fs-12">What next plan ?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-online">
                                        <img src="/images/users/avatar-6.jpg" class="img-fluid rounded-circle" alt="Brandon Smith" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">Tue</span>
                                            Louis Moller
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 float-end text-end"><span class="badge bg-danger-subtle text-danger">1</span></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">Are you free for 15 min?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <img src="/images/users/avatar-7.jpg" class="img-fluid rounded-circle" alt="Brandon Smith" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">Tue</span>
                                            David Callan
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 float-end text-end"><span class="badge bg-danger-subtle text-danger">3</span></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">Are you interested in learning?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-online">
                                        <img src="/images/users/avatar-9.jpg" class="img-fluid rounded-circle" alt="Brandon Smith" />
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">Fri</span>
                                            Sean Lee
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-muted"><i class="ti ti-checks"></i></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">Howdy?</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->

                            <a href="javascript:void(0);" class="text-body d-block">
                                <div class="chat-users">
                                    <div class="avatar-md chat-avatar-offline">
                                        <div class="h-100 w-100 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                            <i class="ti ti-brand-react fs-20"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="mt-0 mb-0 fs-13">
                                            <span class="float-end text-muted fs-12">Sat</span>
                                            React Team
                                        </h5>
                                        <p class="mt-1 mb-0 text-muted lh-1">
                                            <span class="w-25 text-end float-end text-success"><i class="ti ti-checks"></i></span>
                                            <span class="w-75 d-inline-block text-truncate overflow-hidden">@jamesZavel Is new React employee</span>
                                        </p>
                                    </div>
                                </div>
                            </a><!-- end chat-user -->
                        </div>
                    </div>
                    <!-- End Contact list -->
                </div>
            </div>
        </div>

        <div class="chat-content card rounded-0 shadow-none mb-0">
            <div class="card-header py-2 px-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <div class="d-flex align-items-center gap-2">

                        <a href="#" class="btn btn-sm btn-icon btn-soft-primary d-none d-xl-flex me-2" data-bs-toggle="collapse" data-bs-target="#chat-user-list" aria-expanded="true">
                            <i class="ti ti-chevrons-left fs-20"></i>
                        </a>

                        <button class="btn btn-sm btn-icon btn-ghost-light text-dark d-xl-none d-flex" type="button" data-bs-toggle="offcanvas" data-bs-target="#chatUserList" aria-controls="chatUserList">
                            <i class="ti ti-menu-2 fs-20"></i>
                        </button>

                        <img src="/images/users/avatar-5.jpg" class="avatar-lg rounded-circle" alt="">

                        <div>
                            <h5 class="my-0 lh-base">
                                <a href="#" class="text-reset">James Zavel</a>
                            </h5>
                            <p class="mb-0 text-muted">
                                <small class="ti ti-circle-filled text-success"></small> Active
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="javascript: void(0);" class="btn btn-sm btn-icon btn-ghost-light d-none d-xl-flex" data-bs-toggle="modal" data-bs-target="#userCall" data-bs-toggle="tooltip" data-bs-placement="top" title="Voice Call">
                            <i class="ti ti-phone-call fs-20"></i>
                        </a>
                        <a href="javascript: void(0);" class="btn btn-sm btn-icon btn-ghost-light d-none d-xl-flex" data-bs-toggle="modal" data-bs-target="#userVideoCall" data-bs-toggle="tooltip" data-bs-placement="top" title="Video Call">
                            <i class="ti ti-video fs-20"></i>
                        </a>

                        <a href="javascript: void(0);" class="btn btn-sm btn-icon btn-ghost-light d-xl-flex">
                            <i class="ti ti-info-circle fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <div class="chat-scroll p-3" data-simplebar>
                    <ul class="chat-list" data-apps-chat="messages-list">
                        <li class="chat-group" id="even-1">
                            <img src="/images/users/avatar-5.jpg" class="avatar-sm rounded-circle" alt="avatar-5" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">James.</h6>
                                    <h6 class="d-inline-flex text-muted">10:04pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>Hello! 👋</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link link-reset" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#even-1"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group odd" id="odd-1">
                            <img src="/images/users/avatar-1.jpg" class="avatar-sm rounded-circle" alt="avatar-1" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">You.</h6>
                                    <h6 class="d-inline-flex text-muted">10:05pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>Hey there, how are you doing? Any plans for our upcoming meeting?</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#odd-1"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group" id="even-2">
                            <img src="/images/users/avatar-5.jpg" class="avatar-sm rounded-circle" alt="avatar-5" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">James.</h6>
                                    <h6 class="d-inline-flex text-muted">10:08pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>Sure, everything's good.</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link link-reset" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#even-2"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group odd" id="odd-2">
                            <img src="/images/users/avatar-1.jpg" class="avatar-sm rounded-circle" alt="avatar-1" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">You.</h6>
                                    <h6 class="d-inline-flex text-muted">10:10pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>Fantastic! 👍</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#odd-2"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group" id="even-3">
                            <img src="/images/users/avatar-5.jpg" class="avatar-sm rounded-circle" alt="avatar-5" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">James.</h6>
                                    <h6 class="d-inline-flex text-muted">10:15pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>If you're available, let's schedule it for today.</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link link-reset" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#even-3"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group odd" id="odd-3">
                            <img src="/images/users/avatar-1.jpg" class="avatar-sm rounded-circle" alt="avatar-1" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">You.</h6>
                                    <h6 class="d-inline-flex text-muted">10:16pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>Absolutely! Just give me a heads up if 2pm suits you.</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#odd-3"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group" id="even-4">
                            <img src="/images/users/avatar-5.jpg" class="avatar-sm rounded-circle" alt="avatar-5" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">James.</h6>
                                    <h6 class="d-inline-flex text-muted">10:18pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>Apologies 😔, I've got another meeting at 2pm. Could we possibly shift it to 3pm?</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link link-reset" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#even-4"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-message" id="even-4-1">
                                    <p>If you have a few extra minutes, we could also go over the presentation talk format.</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link link-reset" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#even-4-1"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="chat-group odd" id="odd-4">
                            <img src="/images/users/avatar-1.jpg" class="avatar-sm rounded-circle" alt="avatar-1" />

                            <div class="chat-body">
                                <div>
                                    <h6 class="d-inline-flex">You.</h6>
                                    <h6 class="d-inline-flex text-muted">10:19pm</h6>
                                </div>

                                <div class="chat-message">
                                    <p>3pm works for me 👍. Absolutely, let's dive into the presentation format. It'd be fantastic to wrap that up today. I'm attaching last year's format and  here for reference.</p>

                                    <div class="chat-actions dropdown">
                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i> Copy Message</a>
                                            <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-dismissible="#odd-4"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="p-3 border-top position-sticky bottom-0 w-100 mb-0">
                    <form class="row align-items-center g-2" name="chat-form" id="chat-form">

                        <div class="col-auto">
                            <button type="button" class="btn btn-icon btn-soft-warning">
                                <iconify-icon icon="solar:smile-circle-outline" class="fs-20"></iconify-icon>
                            </button>
                        </div>

                        <div class="col">
                            <input type="text" name="message" class="form-control" placeholder="Type Message..." required>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex align-items-center gap-1">
                                <button type="submit" class="btn btn-icon btn-success"><i class='ti ti-send'></i></button>
                                <a href="#" class="btn btn-icon btn-soft-primary"><i class="ti ti-microphone"></i> </a>
                                <a href="#" class="btn btn-icon btn-soft-primary"><i class="ti ti-paperclip"></i></a>
                            </div>
                        </div> <!-- end col -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection



