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

    public function __construct(Playlist $playlist, Song $song, Track $track)
    {
        $this->playlist = $playlist;
        $this->song = $song;
        $this->track = $track;
    }

    /**
     * Prepara os dados do diretório com as mp3
     *
     * @return array
     */
    private function processFolder()
    {
        
        $dirRoot = Storage::disk('lab_music');
        
        $nameRoot = '';
        $dada = []; $n = 0;
        foreach ($dirRoot->listContents() as $dirSub) {

            if($nameRoot <> $dirSub['basename']) {
                $nameRoot = $dirSub['basename'];
                $data[$n]['name'] = $dirSub['basename'];
            }
            $files = $dirRoot->listContents($dirSub['basename']);
            $o = 0;
            foreach ($files as $file) {
                if ($file['extension'] == 'mp3') {

                    $arr1 = explode(' - ', $file['filename']);
                    list($q1,$q2) = ($arr1[0]) ? explode('] ', $arr1[0]) : ['',''];
                    list($r1,$r2) = explode(' ', $dirSub['basename']);

                    $system_name = hash_file('md5', "D:/Timeline Musics/{$file['path']}");
                    $track = GetId3::fromDiskAndPath('lab_music', $file['path']);

                    $data[$n]['tracks'][$o]['path'] = $file['path'];
                    $data[$n]['tracks'][$o]['system_name'] = "{$system_name}.mp3";
                    $data[$n]['tracks'][$o]['song_name'] = ($arr1[1]) ?? '';
                    $data[$n]['tracks'][$o]['track_name'] = ($q2) ?? '';
                    $data[$n]['tracks'][$o]['track_number'] = $n+1;
                    $data[$n]['tracks'][$o]['track_year'] = ($r2) ?? '';
                    $data[$n]['tracks'][$o]['song_length'] = $track->getPlaytime();
                }
                $o++;
            }
            $n++;

        }

        return $data;

    }

    /**
     * Salva os nomes das mp3 e sua estrutura de playlists da timeline no banco de dados
     *
     * @return bool
     */
    private function saveData()
    {
        
        $data = $this->processFolder();
        $playlist = $this->playlist;
        $song = $this->song;
        $track = $this->track;

        foreach ($data as $datum) {
            
            $datumPlaylist = $playlist->firstOrCreate(['name' => $datum['name']]);
            
            foreach ($datum['tracks'] as $datumRawTrack) {

                $param = [
                    'name' => $datumRawTrack['song_name'],
                    'length' => $datumRawTrack['song_length'],
                    'path' => $datumRawTrack['path'],
                    'system_name' => $datumRawTrack['system_name'],
                ];
                $datumSong = $song->firstOrCreate($param);
                
                $param = [
                    'song_id' => $datumSong['id'],
                    'playlist_id' => $datumPlaylist['id'],
                    'name' => $datumRawTrack['track_name'],
                    'track_number' => $datumRawTrack['track_number'],
                    'year' => $datumRawTrack['track_year'],
                ];
                $datumTrack = $track->firstOrCreate($param);

            }
            
        }

        return true;

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
        $song = $this->song;

        $arrSongs = $song->all()->toArray();
        foreach ($arrSongs as $theSong) {
            if (!file_exists("C:/xampp/htdocs/shaggy-music/storage/app/public/mp3_timeline/{$theSong['system_name']}")) {
                File::copy("D:/Timeline Musics/{$theSong['path']}","C:/xampp/htdocs/shaggy-music/storage/app/public/mp3_timeline/{$theSong['system_name']}");
            }
        }

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



        return 'ola';

    }

}
