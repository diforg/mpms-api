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
    private const SOURCE_DISK = 'lab_music';
    private const DESTINY_DISK = 'raw_music';

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
        $directories = Storage::disk(self::SOURCE_DISK)->directories();

        foreach ($directories as $directory) {
            $data[] = $this->processDirectory($directory);
        }

        return $data;
    }

    /**
     * Processa as pastas primárias (playlists)
     */
    private function processDirectory($directory)
    {
        $data = [
            'name' => basename($directory),
            'tracks' => [],
        ];

        $files = Storage::disk(self::SOURCE_DISK)->files($directory);

        foreach ($files as $file) {
            $this->processFile($file, $data['tracks']);
        }

        return $data;
    }

    /**
     * Processa os arquivos da pasta (tracks e songs)
     */
    private function processFile($filePath, &$tracks)
    {
        $fileInfo = pathinfo($filePath);

        if ($fileInfo['extension'] === 'mp3') {
            $track = GetId3::fromDiskAndPath(self::SOURCE_DISK, $filePath);
            $systemName = hash('md5', $filePath);

            $fileNameParts = explode(' - ', $fileInfo['filename']);
            $trackName = explode(']', $fileNameParts[0]);
            $tracks[] = [
                'path' => $filePath,
                'system_name' => "{$systemName}.mp3",
                'song_name' => $fileNameParts[1] ?? '',
                'track_name' => $trackName[1] ?? '',
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

    /**
     * Salva / Atualiza Playlists
     */
    private function saveOrUpdatePlaylist($playlistName)
    {
        return Playlist::firstOrCreate(['name' => $playlistName]);
    }

    /**
     * Salva / Atualiza Songs
     */
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

    /**
     * Salva / Atualiza Tracks
     */
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
     * Extrai os dados do padrão de pastas e subpastas do diretório 'mymusic' e move os arquivo mp3 para as pastas
     */
    public function extract()
    {
        $this->saveData();
        $songs = $this->song->get();
        foreach ($songs as $song) {
            $destinationPath = Storage::disk(self::SOURCE_DISK)->path('/').$song['system_name'];
            if (!file_exists($destinationPath)) {
                $sourcePath = Storage::disk(self::DESTINY_DISK)->path('/').$song['path'];
                File::copy($sourcePath, $destinationPath);
            }
        }
    }

}
