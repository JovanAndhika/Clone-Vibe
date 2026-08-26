<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Discovery;
use App\Models\History;
use App\Models\Music;
use App\Models\Music_playlist;
use App\Models\Newgenre;
use App\Models\Playlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return view('user.home', [
            'title' => 'home',
            'active' => 'home',
            'musics' => Music::orderBy('release_date', 'desc')->paginate(10),
        ]);
    }

    public function search()
    {
        // BUAT DISCOVERY
        $music_discoveries = Music::all();
        $discovers = Discovery::all();

        $songs = collect([]);
        foreach ($music_discoveries as $m) {
            $songs->put($m->id, ['id' => $m->id, 'title' => $m->title, 'artist' => $m->artist, 'genre' => $m->genre, 'file_path' => $m->file_path, 'release_date' => $m->release_date, 'category_id' => $m->category_id]);
        }

        $collect_all_music = collect([]);
        foreach ($discovers as $d) {
            $temp = $songs->where('category_id', $d->id);
            $collect_all_music->put($d->id, $temp);
        }

        $allNewGenres = Newgenre::all();

        return view('user.search', [
            'title' => 'search',
            'active' => 'search',
            'musics' => Music::filter(request('keyword'), request('searchBy'))->orderBy('release_date', 'desc')->paginate(10)->withQueryString(),
            'discovers' => $discovers,
            'music_discoveries' => $music_discoveries,
            'collect_all_music' => $collect_all_music,
            'newgenres' => $allNewGenres,
        ]);
    }

    public function nowPlaying()
    {
        // variable untuk dikirim ke views
        $variables = [
            'title' => 'nowPlaying',
            'active' => 'nowPlaying',
        ];

        // cek request
        if (request()->filled('playlist_id')) {
            // jika request adalah playlist
            $playlist = Playlist::where('id', request('playlist_id'))->where('user_id', auth()->user()->id)->first();

            // jika playlist tidak ditemukan, fetch musik terakhir kali dimainkan
            if (! $playlist) {
                $this->lastPlayed($variables);
            } else {
                // fetch semua music di playlist
                $musics = $playlist->musics;

                // fetch music yang diplay sekarang + simpan ke history
                $index = request('index');
                if ($index < 0) {
                    $index = 0;
                    request()->merge(['index' => $index]);
                }
                if ($index >= $musics->count()) {
                    $index = $musics->count() - 1;
                    request()->merge(['index' => $index]);
                }
                $music = $musics->skip($index)->first();
                $this->storeHistory($music->id);

                // cek apakah musik sudah di like atau belum
                $this->isLiked($music, $variables);

                // simpan variable
                $variables['playlist'] = $playlist;
                $variables['musics'] = $musics;
                $variables['music'] = $music;
            }

        } elseif (request()->filled('music_id')) {
            // jika request adalah single music
            $music = Music::find(request('music_id'));

            if ($music) {
                // simpan ke history jika ada music
                $this->storeHistory($music->id);

                // cek apakah musik sudah di like atau belum
                $this->isLiked($music, $variables);

                // simpan variable
                $variables['music'] = $music;
            } else {
                // jika id music invalid, ambil music terakhir yang di play
                $this->lastPlayed($variables);
            }

        } else {
            // jika tidak ada request, ambil music terakhir yang di play
            $this->lastPlayed($variables);
        }

        return view('user.nowPlaying', $variables);
    }

    // function untuk mengambil musik terakhir yang di play
    public function lastPlayed(&$variables)
    {
        // mengambil music paling terakhir history
        $last_music = History::where('user_id', auth()->user()->id)->latest()->first();
        if ($last_music) {
            $variables['music'] = $last_music->music;
            $this->isLiked($last_music->music, $variables);
        }
    }

    // function untuk cek apakah lagu sudah di like atau belum
    private function isLiked($music, &$variables)
    {
        // mengambil playlist liked songs milik user
        $getIdPlaylist = Playlist::where('user_id', auth()->user()->id)->where('name', 'liked songs')->value('id');
        if (Music_playlist::where('playlist_id', $getIdPlaylist)->where('music_id', $music->id)->first()) {
            // jika ditemukan di playlist like
            $variables['isLiked'] = true;
        } else {
            // jika tidak ditemukan di playlist like
            $variables['isLiked'] = false;
        }
    }

    public function discoverPlaylist()
    {
        return view('user.discoverPlaylist', [
            'title' => 'discoverPlaylist',
            'active' => 'discoverPlaylist',
        ]);
    }

    public function history()
    {
        // fetch semua history yang dimiliki user
        $history = History::with('music')->where('user_id', auth()->user()->id)->latest()->get();
        $hasil = [];

        // cek semua history
        foreach ($history as $isi) {
            // mengakses tanggal dan waktu
            $playedDate = Carbon::parse($isi->created_at)->format('l, d M Y');
            $playedTime = Carbon::parse($isi->created_at)->format('H:i:s');

            // menyimpan di array berdasarkan tanggal dan waktu
            $hasil[$playedDate][$playedTime] = $isi;
        }

        return view('user.history', [
            'title' => 'history',
            'active' => 'history',
            'histories' => $hasil,
        ]);
    }

    // disarankan mengakses pakai ajax/UserController saja
    public function storeHistory($music_id)
    {
        // cari music
        $music = Music::find($music_id);

        // jika music ditemukan
        if ($music) {
            // disimpan ke history
            History::create([
                'music_id' => $music->id,
                'user_id' => auth()->user()->id,
            ]);
        }
    }

    // Menghapus 1 riwayat lagu
    public function destroyHistory(History $history)
    {
        // Pastikan history tersebut milik user yang sedang login
        if ($history->user_id === auth()->user()->id) {
            $history->delete();
        }

        return back()->with('success', 'History song deleted successfully!');
    }

    // Menghapus seluruh riwayat lagu user
    public function clearHistory()
    {
        History::where('user_id', auth()->user()->id)->delete();

        return back()->with('success', 'All history cleared successfully!');
    }

    // genre search
    public function jazz()
    {
        $jazz = DB::table('music')->where('genre', 'Jazz')->get();

        return view('user.searchResult.jazzResult', ['jazz' => $jazz])
            ->with('genreJazz', 'genreJazz searched');
    }

    public function pop()
    {
        $pop = DB::table('music')->where('genre', 'Pop')->get();

        return view('user.searchResult.popResult', ['pop' => $pop])
            ->with('genrePop', 'genrePop searched');
    }

    public function dangdut()
    {
        $song = DB::table('music')->where('genre', 'Dangdut')->get();

        return view('user.searchResult.dangdutResult', ['dangdut' => $song])
            ->with('genreDangdut', 'genreDangdut searched');
    }

    public function kpop()
    {
        $song = DB::table('music')->where('genre', 'Kpop')->get();

        return view('user.searchResult.kpopResult', ['kpop' => $song])
            ->with('genreKpop', 'genreKpop searched');
    }

    public function rock()
    {
        $song = DB::table('music')->where('genre', 'Rock')->get();

        return view('user.searchResult.rockResult', ['rock' => $song])
            ->with('genreRock', 'genreRock searched');
    }

    public function classical()
    {
        $song = DB::table('music')->where('genre', 'Classical')->get();

        return view('user.searchResult.classicalResult', ['classical' => $song])
            ->with('genreClassical', 'genreClassical searched');
    }

    public function dance()
    {
        $song = DB::table('music')->where('genre', 'Dance')->get();

        return view('user.searchResult.danceResult', ['dance' => $song])
            ->with('genreDance', 'genreDance searched');
    }

    public function ponk()
    {
        $song = DB::table('music')->where('genre', 'Ponk')->get();

        return view('user.searchResult.ponkResult', ['ponk' => $song])
            ->with('genrePonk', 'genrePonk searched');
    }

    public function newgenre(Newgenre $newgenre)
    {

        $value_newgenre = $newgenre->new_genre;

        $data = DB::table('music')
            ->join('newgenres', 'music.genre', '=', 'newgenres.new_genre')
            ->where('music.genre', $value_newgenre)
            ->get();

        return view('user.searchResult.newgenreResult', ['newgenre' => $newgenre, 'songs' => $data]);
    }

    // LIKED SONGS BY USER
    public function like()
    {
        $getIdPlaylist = Playlist::where('user_id', auth()->user()->id)->where('name', 'liked songs')->value('id');
        $music_playlist = Music_playlist::where('playlist_id', $getIdPlaylist)->get();

        $songs_raw = collect([]);
        foreach ($music_playlist as $m) {
            $music = Music::where('id', $m->music_id)->first();
            $songs_raw->put($m->id, ['id_music' => $music->id, 'title' => $music->title, 'artist' => $music->artist, 'genre' => $music->genre, 'file_path' => $music->file_path, 'release_date' => $music->release_date, 'category_id' => $music->category_id]);
        }

        return view('user.getLikedSongs', [
            'title' => 'like',
            'active' => 'like',
            'musics_raw' => $songs_raw,
        ]);

    }

    public function addlike(Music $music)
    {
        $playlistId = Playlist::where('user_id', auth()->user()->id)->where('name', 'liked songs')->value('id');
        $musicId = $music->id;
        $data = [
            'playlist_id' => $playlistId,
            'music_id' => $musicId,
        ];

        Music_playlist::create($data);

        return back();
    }

    public function deletelike(Music_playlist $music_playlist)
    {

        Music_playlist::destroy($music_playlist->id);

        return back();
    }
}
