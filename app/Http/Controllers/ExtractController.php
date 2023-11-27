<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Owenoj\LaravelGetId3\GetId3;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\Track;
use Illuminate\Support\Facades\Hash;
use File;

class ExtractController extends Controller
{

    private $playlist;
    private $song;
    private $track;
    private const MUSIC_DISK = 'lab_music';

    public function __construct(Playlist $playlist, Song $song, Track $track)
    {
        $this->playlist = $playlist;
        $this->song = $song;
        $this->track = $track;
    }

    /**
     * Prepara os dados do diretório com as mp3
     */

    private function processFolder()
    {
        $data = [];
        $directories = Storage::disk(self::MUSIC_DISK)->directories();

        foreach ($directories as $directory) {
            $data[] = $this->processDirectory($directory);
        }

        return $data;
    }

    private function processDirectory($directory)
    {
        $data = [
            'name' => basename($directory),
            'tracks' => [],
        ];

        $files = Storage::disk(self::MUSIC_DISK)->files($directory);

        foreach ($files as $file) {
            $this->processFile($file, $data['tracks']);
        }

        return $data;
    }

    private function processFile($filePath, &$tracks)
    {
        $fileInfo = pathinfo($filePath);

        if ($fileInfo['extension'] === 'mp3') {
            $track = GetId3::fromDiskAndPath(self::MUSIC_DISK, $filePath);
            $systemName = hash_file('md5', $filePath);

            $fileNameParts = explode(' - ', $fileInfo['filename']);
            [, $songName] = $fileNameParts[0] ? explode('] ', $fileNameParts[0]) : ['', ''];

            $tracks[] = [
                'path' => $filePath,
                'system_name' => "{$systemName}.mp3",
                'song_name' => $songName ?? '',
                'track_name' => $fileNameParts[1] ?? '',
                'track_number' => count($tracks) + 1,
                'track_year' => explode(' ', basename(dirname($filePath)))[1] ?? '',
                'song_length' => $track->getPlaytime(),
            ];
        }
    }

    /**
     * Salva os nomes das mp3 e sua estrutura de playlists da timeline no banco de dados
     */
    private function saveData()
    {
        $data = $this->processFolder();

        foreach ($data as $datum) {
            $playlist = $this->saveOrUpdatePlaylist($datum['name']);

            foreach ($datum['tracks'] as $datumRawTrack) {
                $song = $this->saveOrUpdateSong($datumRawTrack);
                $this->saveOrUpdateTrack($datumRawTrack, $song, $playlist);
            }
        }

        return true;
    }

    private function saveOrUpdatePlaylist($playlistName)
    {
        return Playlist::firstOrCreate(['name' => $playlistName]);
    }

    private function saveOrUpdateSong($datumRawTrack)
    {
        $params = [
            'name' => $datumRawTrack['song_name'],
            'length' => $datumRawTrack['song_length'],
            'path' => $datumRawTrack['path'],
            'system_name' => $datumRawTrack['system_name'],
        ];

        return Song::firstOrCreate($params);
    }

    private function saveOrUpdateTrack($datumRawTrack, $song, $playlist)
    {
        $params = [
            'song_id' => $song->id,
            'playlist_id' => $playlist->id,
            'name' => $datumRawTrack['track_name'],
            'track_number' => $datumRawTrack['track_number'],
            'year' => $datumRawTrack['track_year'],
        ];

        Track::firstOrCreate($params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function extract()
    {
        $this->saveData();
        // $songs = $this->song->all()->toArray();

        // foreach ($songs as $song) {
        //     $destinationPath = Storage::disk('raw_music').'/'.$song['system_name'];
        //     if (!file_exists($destinationPath)) {
        //         $sourcePath = Storage::disk('lab_music').'/'.$song['path'];
        //         File::copy($sourcePath, $destinationPath);
        //     }
        // }
    }

    /**
     * Lets play :)
     */
    public function test() 
    {

        // $dirRoot = Storage::disk('lab_music');
        
        // foreach ($dirRoot->listContents() as $dirSub) {
        //     $track = GetId3::fromDiskAndPath('lab_music', $file['path']);
        // }

        return 'pronto';

    }

}
