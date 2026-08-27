<div class="col-3 p-0 m-0 fontMonsseratExtraBold d-flex align-items-center justify-content-center resizable">
    <ul class="nav flex-column text-white hoverable">
        <li class="nav-item d-inline-flex align-items-center my-3">
            <a class="nav-link text-white @if ($active == 'nowPlaying') active @endif"
                href="{{ route('user.nowPlaying') }}#jumphere"><i
                    class="bi bi-play-circle text-white @if ($active == 'nowPlaying') active @endif"></i></a>
            <span>
                <a class="nav-link text-white @if ($active == 'nowPlaying') active @endif"
                    href="{{ route('user.nowPlaying') }}#jumphere">Now Playing</a>
            </span>
        </li>

        <li class="nav-item d-inline-flex align-items-center my-3">
            <a class="nav-link text-white @if ($active == 'home') active @endif"
                href="{{ route('user.index') }}#jumphere"><i
                    class="bi bi bi-house text-white @if ($active == 'home') active @endif"></i></a>
            <span>
                <a class="nav-link text-white @if ($active == 'home') active @endif"
                    href="{{ route('user.index') }}#jumphere">Home</a>
            </span>
        </li>

        <li class="nav-item d-inline-flex align-items-center my-3">
            <a class="nav-link text-white @if ($active == 'search') active @endif"
                href="{{ route('user.search') }}#jumphere"><i
                    class="bi bi-search text-white @if ($active == 'search') active @endif"></i></a>
            <span>
                <a class="nav-link text-white @if ($active == 'search') active @endif"
                    href="{{ route('user.search') }}#jumphere">Discover</a>
            </span>
        </li>

        <li class="nav-item d-inline-flex align-items-center my-3">
            <a class="nav-link text-white @if ($active == 'history') active @endif"
                href="{{ route('user.history') }}#jumphere"><i
                    class="bi bi-clock-history text-white @if ($active == 'history') active @endif"></i></a>
            <span>
                <a class="nav-link text-white @if ($active == 'history') active @endif"
                    href="{{ route('user.history') }}#jumphere">History</a>
            </span>
        </li>

        <li class="nav-item d-inline-flex align-items-center my-3">
            <a class="nav-link text-white @if ($active == 'playlist') active @endif"
                href="{{ url('/user/playlists') }}#jumphere"><i
                    class="bi bi-collection-play text-white @if ($active == 'playlist') active @endif"></i></a>
            <span>
                <a class="nav-link text-white @if ($active == 'playlist') active @endif"
                    href="{{ url('/user/playlists') }}#jumphere">Playlist</a>
            </span>
        </li>

        <li class="nav-item d-inline-flex align-items-center my-3">
            <a class="nav-link text-white @if ($active == 'like') active @endif"
                href="{{ route('user.like') }}#jumphere"><i
                    class="bi bi-heart text-white @if ($active == 'like') active @endif"></i></a>
            <span>
                <a class="nav-link text-white @if ($active == 'like') active @endif"
                    href="{{ route('user.like') }}#jumphere">Likes</a>
            </span>
        </li>

    </ul>
</div>
