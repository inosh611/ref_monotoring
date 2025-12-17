<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-modern">
        <!-- Brand Logo -->
        <a
            :href="route('admin.dashboard.login')"
            class="brand-link brand-link-modern"
        >
            <div class="brand-logo-wrapper">
                <img
                    src="/images/Admin-panel/panel-logo - 2.png"
                    alt="ARG Logo"
                    class="brand-image img-circle elevation-3"
                />
            </div>
            <div class="brand-text-wrapper">
                <span class="brand-text-main">ARG PVT LTD</span>
                <span class="brand-text-sub">Sales Force {{ userRole }}</span>
            </div>
        </a>

        <!-- Sidebar -->
        <div class="sidebar sidebar-inner-modern">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav nav-pills nav-sidebar flex-column nav-modern"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="false"
                >
                    <!-- MAIN NAVIGATION -->
                    <li class="nav-header nav-header-modern">MAIN</li>

                    <li class="nav-item" v-if="userRole == 'Super Admin'">
                        <a
                            :href="route('admin.dashboard.login')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('admin.dashboard.login') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2Zm0 2a8 8 0 1 0 0 16a8 8 0 0 0 0-16Zm0 1c1.018 0 1.985.217 2.858.608L13.295 7.17a5 5 0 0 0-4.83 8.366L7.05 16.95l-.156-.161A7 7 0 0 1 12 5Zm6.392 4.143c.39.872.608 1.84.608 2.857a6.978 6.978 0 0 1-2.05 4.95l-1.414-1.414a5.008 5.008 0 0 0 1.295-4.83l1.561-1.563Zm-2.15-2.8l1.415 1.414l-3.725 3.726A2.003 2.003 0 0 1 12 14a2 2 0 1 1 .517-3.932l3.726-3.725Z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">
                                Dashboard
                                <span class="nav-pill">Overview</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item" v-if="userRole != 'Super Admin'">
                        <a
                            :href="route('employee.dashboard')"
                            class="nav-link nav-link-modern active"
                            :class="{ active: isActive('employee.dashboard') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2Zm0 2a8 8 0 1 0 0 16a8 8 0 0 0 0-16Zm0 1c1.018 0 1.985.217 2.858.608L13.295 7.17a5 5 0 0 0-4.83 8.366L7.05 16.95l-.156-.161A7 7 0 0 1 12 5Zm6.392 4.143c.39.872.608 1.84.608 2.857a6.978 6.978 0 0 1-2.05 4.95l-1.414-1.414a5.008 5.008 0 0 0 1.295-4.83l1.561-1.563Zm-2.15-2.8l1.415 1.414l-3.725 3.726A2.003 2.003 0 0 1 12 14a2 2 0 1 1 .517-3.932l3.726-3.725Z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">
                                Employee Dashboard
                                <span class="nav-pill">Overview</span>
                            </p>
                        </a>
                    </li>

                    <!-- SALES / OPERATIONS -->
                    <li class="nav-header nav-header-modern mt-2">
                        SALES & OPERATIONS
                    </li>

                    <li class="nav-item" v-if="can('product.management')">
                        <a
                            :href="route('product.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('product.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 2048 2048"
                                >
                                    <path
                                        fill="currentColor"
                                        d="m960 120l832 416v1040l-832 415l-832-415V536l832-416zm625 456L960 264L719 384l621 314l245-122zM960 888l238-118l-622-314l-241 120l625 312zM256 680v816l640 320v-816L256 680zm768 1136l640-320V680l-640 320v816z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Products</p>
                        </a>
                    </li>

                    <li class="nav-item" v-if="can('product.management')">
                        <a
                            :href="route('unit.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('unit.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 2048 2048"
                                >
                                    <path
                                        fill="currentColor"
                                        d="m960 120l832 416v1040l-832 415l-832-415V536l832-416zm625 456L960 264L719 384l621 314l245-122zM960 888l238-118l-622-314l-241 120l625 312zM256 680v816l640 320v-816L256 680zm768 1136l640-320V680l-640 320v816z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Units</p>
                        </a>
                    </li>

                    <li class="nav-item" v-if="can('order.management')">
                        <a
                            :href="route('order.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('order.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 2048 2048"
                                >
                                    <path
                                        fill="currentColor"
                                        d="m960 120l832 416v1040l-832 415l-832-415V536l832-416zm625 456L960 264L719 384l621 314l245-122zM960 888l238-118l-622-314l-241 120l625 312zM256 680v816l640 320v-816L256 680zm768 1136l640-320V680l-640 320v816z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">
                                Orders
                                <span class="nav-pill pill-soft">Manage</span>
                            </p>
                        </a>
                    </li>

                    <li class="nav-item" v-if="can('dealer.management')">
                        <a
                            :href="route('dealer.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('dealer.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 32 32"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M21.066 20.667c1.227-.682 1.068-3.31-.354-5.874c-.61-1.104-1.36-1.998-2.11-2.623a5.229 5.229 0 0 1-3.1 1.03a5.23 5.23 0 0 1-3.105-1.03c-.75.625-1.498 1.52-2.11 2.623c-1.423 2.563-1.58 5.192-.35 5.874c.548.312 1.126.078 1.722-.496a10.51 10.51 0 0 0-.167 1.874c0 2.938 1.14 5.312 2.543 5.312c.846 0 1.265-.865 1.466-2.188c.2 1.314.62 2.188 1.46 2.188c1.397 0 2.546-2.375 2.546-5.312c0-.66-.062-1.29-.168-1.873c.6.575 1.176.813 1.726.497zM15.5 12.2a4.279 4.279 0 1 0-.003-8.557A4.279 4.279 0 0 0 15.5 12.2zm8.594 2.714a3.514 3.514 0 0 0 0-7.025a3.513 3.513 0 1 0 .001 7.027zm4.28 2.13c-.502-.908-1.116-1.642-1.732-2.155a4.3 4.3 0 0 1-2.546.845c-.756 0-1.46-.207-2.076-.55c.496 1.093.803 2.2.86 3.19c.094 1.516-.38 2.64-1.328 3.165a2.017 2.017 0 0 1-.653.224c-.057.392-.096.8-.096 1.23c0 2.413.935 4.362 2.088 4.362c.694 0 1.04-.71 1.204-1.796c.163 1.08.508 1.796 1.2 1.796c1.145 0 2.09-1.95 2.09-4.36c0-.543-.053-1.06-.14-1.54c.492.473.966.668 1.418.408c1.007-.56.877-2.718-.29-4.82zm-21.468-2.13a3.512 3.512 0 1 0-3.514-3.512a3.515 3.515 0 0 0 3.514 3.514zm2.535 6.622c-1.592-.885-1.738-3.524-.456-6.354a4.242 4.242 0 0 1-2.078.553c-.956 0-1.832-.32-2.55-.846c-.615.512-1.228 1.246-1.732 2.153c-1.167 2.104-1.295 4.262-.287 4.82c.45.258.925.065 1.414-.406a8.83 8.83 0 0 0-.135 1.538c0 2.412.935 4.36 2.088 4.36c.694 0 1.04-.71 1.204-1.795c.165 1.08.51 1.796 1.2 1.796c1.147 0 2.09-1.95 2.09-4.36c0-.433-.04-.842-.097-1.234a2.02 2.02 0 0 1-.66-.226z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Dealers</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            :href="route('chat.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('chat.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 64 64"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M14.551 11.92a4.837 4.837 0 0 0 4.837-4.839a4.837 4.837 0 1 0-9.674 0a4.84 4.84 0 0 0 4.837 4.839zm36.302-.068a4.916 4.916 0 0 0 4.915-4.914a4.913 4.913 0 1 0-9.827 0a4.912 4.912 0 0 0 4.912 4.914zM12.764 39.458c-6.36 0-11.516 5.155-11.516 11.515s5.156 11.516 11.516 11.516S24.28 57.333 24.28 50.973c0-6.359-5.156-11.515-11.516-11.515zm1.365 18.825v1.84h-2.753v-1.84c-2.936-.336-4.809-3.131-4.809-4.958h2.935c0 .821.633 1.912 1.901 2.173v-3.45c-.633-.122-.987-.222-1.666-.408c-1.735-.479-2.936-1.94-2.936-3.802c0-1.845 1.801-3.915 4.576-4.282v-1.719h2.753v1.719c2.943.379 4.753 2.553 4.753 4.726l-2.875-.001c-.002-.95-.792-1.813-1.878-1.919v3.236c.799.176.799.156 1.533.356c2.738.745 3.355 2.537 3.355 4.04c-.001 2.308-1.846 3.892-4.889 4.29z"
                                    />
                                    <path
                                        fill="currentColor"
                                        d="M11.376 49.092c-1.23-.219-1.571-.745-1.571-1.313c0-.663.834-1.302 1.571-1.388v2.701zm2.786 6.393v-2.916c1.215.174 1.985.697 1.985 1.448c0 .664-.717 1.42-1.985 1.468zM8.238 20.3h1.907l-2.204 8.203H21.16L18.956 20.3h1.907l2.259 8.203h4.253l-2.729-9.95c-.728-2.406-3.109-4.959-6.229-4.959h-7.736c-3.123 0-5.503 2.552-6.232 4.959l-2.726 9.95h4.253L8.237 20.3z"
                                    />
                                    <path
                                        fill="currentColor"
                                        d="M56.104 13.545a4.372 4.372 0 0 1 4.373 4.369l-.007 13.414h2.815v5.499l-5.696.003v22.774c0 1.592-1.306 2.883-2.899 2.883a2.884 2.884 0 0 1-2.881-2.883V36.832h-1.837v22.772a2.885 2.885 0 0 1-2.886 2.883a2.894 2.894 0 0 1-2.895-2.883v-22.77l-42.945.013v-5.519h42.945V18.413l-9.174 10.862c-1.93 2.229-5.367-.555-3.43-2.939l9.506-11.421c.615-.612 1.166-1.37 3.436-1.37h11.573z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">
                                Chat
                                <span class="nav-pill pill-soft">Internal</span>
                            </p>
                        </a>
                    </li>

                    <!-- EMPLOYEE & TARGETS -->
                    <li
                        class="nav-header nav-header-modern mt-2"
                        v-if="can('employee.management')"
                    >
                        EMPLOYEES & TARGETS
                    </li>

                    <li class="nav-item" v-if="can('employee.management')">
                        <a
                            :href="route('employee.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('employee.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 32 32"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M21.066 20.667c1.227-.682 1.068-3.31-.354-5.874c-.61-1.104-1.36-1.998-2.11-2.623a5.229 5.229 0 0 1-3.1 1.03a5.23 5.23 0 0 1-3.105-1.03c-.75.625-1.498 1.52-2.11 2.623c-1.423 2.563-1.58 5.192-.35 5.874c.548.312 1.126.078 1.722-.496a10.51 10.51 0 0 0-.167 1.874c0 2.938 1.14 5.312 2.543 5.312c.846 0 1.265-.865 1.466-2.188c.2 1.314.62 2.188 1.46 2.188c1.397 0 2.546-2.375 2.546-5.312c0-.66-.062-1.29-.168-1.873c.6.575 1.176.813 1.726.497zM15.5 12.2a4.279 4.279 0 1 0-.003-8.557A4.279 4.279 0 0 0 15.5 12.2zm8.594 2.714a3.514 3.514 0 0 0 0-7.025a3.513 3.513 0 1 0 .001 7.027zm4.28 2.13c-.502-.908-1.116-1.642-1.732-2.155a4.3 4.3 0 0 1-2.546.845c-.756 0-1.46-.207-2.076-.55c.496 1.093.803 2.2.86 3.19c.094 1.516-.38 2.64-1.328 3.165a2.017 2.017 0 0 1-.653.224c-.057.392-.096.8-.096 1.23c0 2.413.935 4.362 2.088 4.362c.694 0 1.04-.71 1.204-1.796c.163 1.08.508 1.796 1.2 1.796c1.145 0 2.09-1.95 2.09-4.36c0-.543-.053-1.06-.14-1.54c.492.473.966.668 1.418.408c1.007-.56.877-2.718-.29-4.82zm-21.468-2.13a3.512 3.512 0 1 0-3.514-3.512a3.515 3.515 0 0 0 3.514 3.514zm2.535 6.622c-1.592-.885-1.738-3.524-.456-6.354a4.242 4.242 0 0 1-2.078.553c-.956 0-1.832-.32-2.55-.846c-.615.512-1.228 1.246-1.732 2.153c-1.167 2.104-1.295 4.262-.287 4.82c.45.258.925.065 1.414-.406a8.83 8.83 0 0 0-.135 1.538c0 2.412.935 4.36 2.088 4.36c.694 0 1.04-.71 1.204-1.795c.165 1.08.51 1.796 1.2 1.796c1.147 0 2.09-1.95 2.09-4.36c0-.433-.04-.842-.097-1.234a2.02 2.02 0 0 1-.66-.226z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Employee</p>
                        </a>
                    </li>

                    <li class="nav-item" v-if="can('employee.management')">
                        <a
                            :href="route('target.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('target.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 32 32"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M21.066 20.667c1.227-.682 1.068-3.31-.354-5.874c-.61-1.104-1.36-1.998-2.11-2.623a5.229 5.229 0 0 1-3.1 1.03a5.23 5.23 0 0 1-3.105-1.03c-.75.625-1.498 1.52-2.11 2.623c-1.423 2.563-1.58 5.192-.35 5.874c.548.312 1.126.078 1.722-.496a10.51 10.51 0 0 0-.167 1.874c0 2.938 1.14 5.312 2.543 5.312c.846 0 1.265-.865 1.466-2.188c.2 1.314.62 2.188 1.46 2.188c1.397 0 2.546-2.375 2.546-5.312c0-.66-.062-1.29-.168-1.873c.6.575 1.176.813 1.726.497zM15.5 12.2a4.279 4.279 0 1 0-.003-8.557A4.279 4.279 0 0 0 15.5 12.2zm8.594 2.714a3.514 3.514 0 0 0 0-7.025a3.513 3.513 0 1 0 .001 7.027zm4.28 2.13c-.502-.908-1.116-1.642-1.732-2.155a4.3 4.3 0 0 1-2.546.845c-.756 0-1.46-.207-2.076-.55c.496 1.093.803 2.2.86 3.19c.094 1.516-.38 2.64-1.328 3.165a2.017 2.017 0 0 1-.653.224c-.057.392-.096.8-.096 1.23c0 2.413.935 4.362 2.088 4.362c.694 0 1.04-.71 1.204-1.796c.163 1.08.508 1.796 1.2 1.796c1.145 0 2.09-1.95 2.09-4.36c0-.543-.053-1.06-.14-1.54c.492.473.966.668 1.418.408c1.007-.56.877-2.718-.29-4.82zm-21.468-2.13a3.512 3.512 0 1 0-3.514-3.512a3.515 3.515 0 0 0 3.514 3.514zm2.535 6.622c-1.592-.885-1.738-3.524-.456-6.354a4.242 4.242 0 0 1-2.078.553c-.956 0-1.832-.32-2.55-.846c-.615.512-1.228 1.246-1.732 2.153c-1.167 2.104-1.295 4.262-.287 4.82c.45.258.925.065 1.414-.406a8.83 8.83 0 0 0-.135 1.538c0 2.412.935 4.36 2.088 4.36c.694 0 1.04-.71 1.204-1.795c.165 1.08.51 1.796 1.2 1.796c1.147 0 2.09-1.95 2.09-4.36c0-.433-.04-.842-.097-1.234a2.02 2.02 0 0 1-.66-.226z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Employee Target</p>
                        </a>
                    </li>

                    <!-- REPORTS & MY STUFF -->
                    <li class="nav-header nav-header-modern mt-2">
                        REPORTS & MY ACTIVITY
                    </li>

                    <li class="nav-item" v-if="can('report.management')">
                        <a
                            :href="route('reports.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('reports.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M5 13.2a3 3 0 0 0 0 5.6V20a1 1 0 1 0 2 0v-1.2a3 3 0 0 0 0-5.6V4a1 1 0 0 0-2 0zm6 6.8v-9.2a3 3 0 0 1 0-5.6V4a1 1 0 1 1 2 0v1.2a3 3 0 0 1 0 5.6V20a1 1 0 1 1-2 0m6-1.2V20a1 1 0 1 0 2 0v-1.2a3 3 0 0 0 0-5.6V4a1 1 0 1 0-2 0v9.2a3 3 0 0 0 0 5.6"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Reports</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            :href="route('submit.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('submit.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    height="22"
                                    viewBox="0 -960 960 960"
                                    width="22"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M720-160q33 0 56.5-23.5T800-240q0-33-23.5-56.5T720-320q-33 0-56.5 23.5T640-240q0 33 23.5 56.5T720-160Zm0 80q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113T720-80ZM600-620q-29 0-49.5-20.5T530-690q0-29 20.5-49.5T600-760q29 0 49.5 20.5T670-690q0 29-20.5 49.5T600-620ZM440-200v-147l-93-77q-26-23-27.5-58t24.5-59l80-77q23-23 56-22.5t56 23.5l63 56q26 20 57 30.5t64 10.5v80q-60 0-113-24t-93-67l-74 71 80 80v180h-80Zm-200 40q33 0 56.5-23.5T320-240q0-33-23.5-56.5T240-320q-33 0-56.5 23.5T160-240q0 33 23.5 56.5T240-160Zm-69-470 138-138-41-42-96 96-40-40-42 43 81 81Zm69 550q-66 0-113-47T80-240q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113T240-80Zm-40-440q-69-17-114.5-79.5T40-738v-120l160-62 160 62v120q0 76-45.5 138.5T200-520Z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">My Visiting</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            :href="route('my.collection.index')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('my.collection.index') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    height="22"
                                    viewBox="0 -960 960 960"
                                    width="22"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M480-40q-112 0-206-51T120-227v107H40v-240h240v80h-99q48 72 126.5 116T480-120q75 0 140.5-28.5t114-77q48.5-48.5 77-114T840-480h80q0 91-34.5 171T791-169q-60 60-140 94.5T480-40Zm-36-160v-52q-47-11-76.5-40.5T324-370l66-26q12 41 37.5 61.5T486-314q33 0 56.5-15.5T566-378q0-29-24.5-47T454-466q-59-21-86.5-50T340-592q0-41 28.5-74.5T446-710v-50h70v50q36 3 65.5 29t40.5 61l-64 26q-8-23-26-38.5T482-648q-35 0-53.5 15T410-592q0 26 23 41t83 35q72 26 96 61t24 77q0 29-10 51t-26.5 37.5Q583-274 561-264.5T514-250v50h-70ZM40-480q0-91 34.5-171T169-791q60-60 140-94.5T480-920q112 0 206 51t154 136v-107h80v240H680v-80h99q-48-72-126.5-116T480-840q-75 0-140.5 28.5t-114 77q-48.5 48.5-77 114T120-480H40Z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">My Collections</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            :href="route('stock.odit.create')"
                            class="nav-link nav-link-modern"
                            :class="{ active: isActive('stock.odit.create') }"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    height="22"
                                    viewBox="0 -960 960 960"
                                    width="22"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M480-40q-112 0-206-51T120-227v107H40v-240h240v80h-99q48 72 126.5 116T480-120q75 0 140.5-28.5t114-77q48.5-48.5 77-114T840-480h80q0 91-34.5 171T791-169q-60 60-140 94.5T480-40Zm-36-160v-52q-47-11-76.5-40.5T324-370l66-26q12 41 37.5 61.5T486-314q33 0 56.5-15.5T566-378q0-29-24.5-47T454-466q-59-21-86.5-50T340-592q0-41 28.5-74.5T446-710v-50h70v50q36 3 65.5 29t40.5 61l-64 26q-8-23-26-38.5T482-648q-35 0-53.5 15T410-592q0 26 23 41t83 35q72 26 96 61t24 77q0 29-10 51t-26.5 37.5Q583-274 561-264.5T514-250v50h-70ZM40-480q0-91 34.5-171T169-791q60-60 140-94.5T480-920q112 0 206 51t154 136v-107h80v240H680v-80h99q-48-72-126.5-116T480-840q-75 0-140.5 28.5t-114 77q-48.5 48.5-77 114T120-480H40Z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Audit Stock</p>
                        </a>
                    </li>

                    <!-- SYSTEM SETTINGS -->
                    <li
                        class="nav-header nav-header-modern mt-2"
                        v-if="userRole == 'Super Admin'"
                    >
                        SYSTEM & ACCESS
                    </li>

                    <li
                        class="nav-item has-treeview"
                        v-if="userRole == 'Super Admin'"
                    >
                        <a href="#" class="nav-link nav-link-modern">
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 32 32"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M21.066 20.667c1.227-.682 1.068-3.31-.354-5.874c-.61-1.104-1.36-1.998-2.11-2.623a5.229 5.229 0 0 1-3.1 1.03a5.23 5.23 0 0 1-3.105-1.03c-.75.625-1.498 1.52-2.11 2.623c-1.423 2.563-1.58 5.192-.35 5.874c.548.312 1.126.078 1.722-.496a10.51 10.51 0 0 0-.167 1.874c0 2.938 1.14 5.312 2.543 5.312c.846 0 1.265-.865 1.466-2.188c.2 1.314.62 2.188 1.46 2.188c1.397 0 2.546-2.375 2.546-5.312c0-.66-.062-1.29-.168-1.873c.6.575 1.176.813 1.726.497zM15.5 12.2a4.279 4.279 0 1 0-.003-8.557A4.279 4.279 0 0 0 15.5 12.2zm8.594 2.714a3.514 3.514 0 0 0 0-7.025a3.513 3.513 0 1 0 .001 7.027zm4.28 2.13c-.502-.908-1.116-1.642-1.732-2.155a4.3 4.3 0 0 1-2.546.845c-.756 0-1.46-.207-2.076-.55c.496 1.093.803 2.2.86 3.19c.094 1.516-.38 2.64-1.328 3.165a2.017 2.017 0 0 1-.653.224c-.057.392-.096.8-.096 1.23c0 2.413.935 4.362 2.088 4.362c.694 0 1.04-.71 1.204-1.796c.163 1.08.508 1.796 1.2 1.796c1.145 0 2.09-1.95 2.09-4.36c0-.543-.053-1.06-.14-1.54c.492.473.966.668 1.418.408c1.007-.56.877-2.718-.29-4.82zm-21.468-2.13a3.512 3.512 0 1 0-3.514-3.512a3.515 3.515 0 0 0 3.514 3.514zm2.535 6.622c-1.592-.885-1.738-3.524-.456-6.354a4.242 4.242 0 0 1-2.078.553c-.956 0-1.832-.32-2.55-.846c-.615.512-1.228 1.246-1.732 2.153c-1.167 2.104-1.295 4.262-.287 4.82c.45.258.925.065 1.414-.406a8.83 8.83 0 0 0-.135 1.538c0 2.412.935 4.36 2.088 4.36c.694 0 1.04-.71 1.204-1.795c.165 1.08.51 1.796 1.2 1.796c1.147 0 2.09-1.95 2.09-4.36c0-.433-.04-.842-.097-1.234a2.02 2.02 0 0 1-.66-.226z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">
                                Permission
                                <i
                                    class="fas fa-angle-left right nav-arrow"
                                ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a
                                    :href="route('role.management')"
                                    class="nav-link nav-link-sub"
                                    :class="{ active: isActive('role.management') }"
                                >
                                    <p class="ml-4">Role</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    :href="route('admin.permission')"
                                    class="nav-link nav-link-sub"
                                    :class="{ active: isActive('admin.permission') }"
                                >
                                    <p class="ml-4">Permission</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- LOG OUT -->
                    <li class="nav-item mt-3" style="margin-bottom: 100px">
                        <a
                            :href="route('logout')"
                            method="post"
                            class="nav-link nav-link-modern nav-link-logout"
                        >
                            <span class="nav-icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 512 512"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32v224c0 17.7 14.3 32 32 32s32-14.3 32-32V32zm-144.5 88.6c13.6-11.3 15.4-31.5 4.1-45.1s-31.5-15.4-45.1-4.1C49.7 115.4 16 181.8 16 256c0 132.5 107.5 240 240 240s240-107.5 240-240c0-74.2-33.8-140.6-86.6-184.6c-13.6-11.3-33.8-9.4-45.1 4.1s-9.4 33.8 4.1 45.1c38.9 32.3 63.5 81 63.5 135.4c0 97.2-78.8 176-176 176s-176-78.8-176-176c0-54.4 24.7-103.1 63.5-135.4z"
                                    />
                                </svg>
                            </span>
                            <p class="nav-text">Log out</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
export default {
    name: "MainSideBar",
    props: {
        userPermissions: {
            type: Array,
            required: true,
        },
        userRoles: {
            type: Array,
            required: true,
        },
    },
    watch: {
        userPermissions: {
            immediate: true,
            handler(newVal) {
                console.log("Updated userPermissions:", newVal);
            },
        },
        userRoles: {
            immediate: true,
            handler(newVal) {
                console.log("Updated userRoll:", newVal[0]);
            },
        },
    },
    computed: {
        userRole() {
            return this.userRoles?.[0] ?? "";
        },
    },
    methods: {
        can(permission) {
            return this.userPermissions.includes(permission);
        },
        canAny(permissions) {
            return permissions.some((p) => this.userPermissions.includes(p));
        },
        isActive(routeName) {
            return route().current(routeName);
        },
    },
};
</script>

<style>
/* Overall sidebar look */
.sidebar-modern {
    background: radial-gradient(
        circle at top left,
        #202b3b 0,
        #111827 60%,
        #020617 100%
    );
    border-right: 1px solid rgba(148, 163, 184, 0.12);
}

/* Brand */
.brand-link-modern {
    display: flex;
    align-items: center;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.18);
}

.brand-logo-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    overflow: hidden;
    margin-right: 0.6rem;
    background: rgba(15, 23, 42, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-text-wrapper {
    display: flex;
    flex-direction: column;
}

.brand-text-main {
    font-weight: 700;
    letter-spacing: 0.06em;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.brand-text-sub {
    font-size: 0.7rem;
    color: #9ca3af;
}

/* Sidebar inner */
.sidebar-inner-modern {
    padding-top: 0.6rem;
}

/* Section headers */
.nav-header-modern {
    font-size: 0.7rem;
    letter-spacing: 0.12em;
    color: #6b7280;
    padding: 0.7rem 1.25rem 0.3rem;
    text-transform: uppercase;
}

/* Nav links */
.nav-modern .nav-item .nav-link-modern {
    border-radius: 999px;
    margin: 3px 0.8rem;
    padding: 0.55rem 0.9rem;
    display: flex;
    align-items: center;
    color: #e5e7eb;
    background: transparent;
    transition: all 0.18s ease-in-out;
}

.nav-modern .nav-item .nav-link-modern .nav-icon-wrapper {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.65rem;
}

.nav-modern .nav-item .nav-link-modern .nav-text {
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

/* Pills */
.nav-pill {
    background: rgba(15, 23, 42, 0.9);
    border-radius: 999px;
    padding: 2px 8px;
    font-size: 0.65rem;
    margin-left: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.pill-soft {
    background: rgba(148, 163, 184, 0.25);
}

/* Hover & active */
.nav-link-modern:hover {
    background: linear-gradient(90deg, #0f172a, #111827);
    transform: translateX(2px);
}

.nav-link-modern.active {
    background: linear-gradient(120deg, #6366f1, #14b8a6);
    box-shadow: 0 12px 20px rgba(79, 70, 229, 0.3);
    color: #f9fafb !important;
}

.nav-link-modern.active .nav-icon-wrapper {
    background: rgba(15, 23, 42, 0.2);
}

/* Sub menu */
.nav-link-sub {
    padding: 0.4rem 1.8rem;
    font-size: 0.8rem;
}

.nav-link-sub:hover {
    background: rgba(15, 23, 42, 0.7);
}

/* Arrow */
.nav-arrow {
    font-size: 0.7rem;
    margin-left: auto;
}

/* Logout */
.nav-link-logout {
    margin-top: 0.6rem;
    border-top: 1px solid rgba(148, 163, 184, 0.18);
    padding-top: 0.8rem;
    color: #fecaca;
}

.nav-link-logout:hover {
    background: linear-gradient(120deg, #b91c1c, #ef4444);
    color: #fef2f2 !important;
}

/* Scrollbar small tweak */
.main-sidebar .sidebar {
    scrollbar-width: thin;
    scrollbar-color: #4b5563 transparent;
}
.main-sidebar .sidebar::-webkit-scrollbar {
    width: 6px;
}
.main-sidebar .sidebar::-webkit-scrollbar-thumb {
    background-color: #4b5563;
    border-radius: 999px;
}
</style>
