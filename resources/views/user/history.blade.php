@extends('user.layouts.user_main')
@section('container')
    <div class="col scrollable-div p-4" id="jumphere">
        <div class="container-fluid vh-100 d-flex flex-column">
            <div class="container-fluid flex-grow-1 p-5">

                <div class="container-fluid text-center mb-5">
                    <h1 class="fontMonsseratExtraBold" style="font-size: 50px;">Your History</h1>

                    {{-- Tombol Clear All History --}}
                    @if (count($histories) > 0)
                        <form action="{{ route('user.clearHistory') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all history?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm mt-3">
                                <i class="bi bi-trash"></i> Clear All History
                            </button>
                        </form>
                    @endif
                </div>

                <div class="container-fluid scrollable-div">
                    @forelse ($histories as $key => $history)
                        <div class="container-fluid text-left mb-5" id="2">
                            <h4 class="fontMonsseratSemiBold mb-3">{{ $key }}</h4>
                            <table class="table table-striped table-hover table-dark fontMonsseratRegular align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Song</th>
                                        <th scope="col">Artist</th>
                                        <th scope="col">Last Played</th>
                                        <th scope="col"></th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($history as $ok => $item)
                                        @if ($item->music)
                                        <tr>
                                            <td>
                                                <img src="@if ($item->music->icon) {{ asset($item->music->icon) }} @else {{ asset('img/now_playing/empty_icon.jpeg') }} @endif" alt="Artist Photo" class="img-fluid rounded-3" style="max-width: 50px; max-height: 50px;">
                                            </td>
                                            <td>{{ $item->music->title }}</td>
                                            <td>{{ $item->music->artist }}</td>
                                            <td>{{ $ok }}</td>
                                            <td>
                                                <a href="{{ route('user.nowPlaying') }}?music_id={{ $item->music->id }}#jumphere">
                                                    <i class="bi bi-play-fill text-white fs-4"></i>
                                                </a>
                                            </td>
                                            {{-- Tombol Hapus per Lagu --}}
                                            <td class="text-center">
                                                <form action="{{ route('user.destroyHistory', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this song from history?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0">
                                                        <i class="bi bi-trash-fill fs-5"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <div class="text-center text-muted mt-5">
                            <p>No listening history found.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection
