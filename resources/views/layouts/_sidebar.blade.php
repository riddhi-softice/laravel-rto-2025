<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('blogs.*') || request()->routeIs('tags.*') ? '' : 'collapsed' }}" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#components-nav">
                <i class="bi bi-menu-button-wide"></i><span>Blog</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse {{ request()->routeIs('blogs.*') || request()->routeIs('tags.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('tags.index') }}" class="{{ request()->routeIs('tags.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Category</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('blogs.index') }}" class="{{ request()->routeIs('blogs.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Blog</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('url_configs.*') || request()->routeIs('url_brand.*') ? '' : 'collapsed' }}" data-bs-target="#url-nav" data-bs-toggle="collapse" href="#url-nav">
                <i class="bi bi-menu-button-wide"></i><span>Track URL</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <ul id="url-nav" class="nav-content collapse {{ request()->routeIs('url_configs.*') || request()->routeIs('url_brand.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('url_brand.index') }}" class="{{ request()->routeIs('url_brand.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Url Brand</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('url_configs.index') }}" class="{{ request()->routeIs('url_configs.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Url Config</span>
                    </a>
                </li>
                 <li>
                    <a href="{{ route('url_track.index') }}" class="{{ request()->routeIs('url_track.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Url Track</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reels.*') ? '' : 'collapsed' }}" href="{{ route('reels.index') }}">
                <i class="bi bi-menu-button-wide"></i>
                <span>Reels</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('get_setting') ? '' : 'collapsed' }}" href="{{ route('get_setting') }}">
                <i class="bi bi-gear"></i>
                <span>Common Setting</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('privacy_policy') ? '' : 'collapsed' }}" href="{{ route('privacy_policy') }}">
                <i class="bi bi-gear"></i>
                <span>Privacy Policy</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('app_notification.index') ? '' : 'collapsed' }}" href="{{ route('app_notification.index') }}">
                <i class="bi bi-bell"></i>
                <span>Notification</span>
            </a>
        </li>

    </ul>
</aside>
