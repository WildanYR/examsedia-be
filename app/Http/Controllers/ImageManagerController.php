<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageManagerController extends ApiController
{
    public function readDir(Request $request) {
        $imgPath = 'images';
        if (gettype($request->query('path')) != "NULL") {
            $imgPath = $imgPath.'/'.$request->query('path');
        }
        if (!is_dir(public_path($imgPath))) {
            return $this->ErrorResponse('Folder tidak ditemukan', 404);
        }
        $fileScan = scandir(public_path($imgPath));
        $files = array();
        $directories = array();
        foreach ($fileScan as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir(public_path($imgPath.'/'.$file))) {
                array_push($directories, $file);
            } else {
                array_push($files, $file);
            }
        }
        return $this->successResponse([
            "files" => $files,
            "directories" => $directories
        ]);
    }

    public function uploadImage(Request $request) {
        $validator = Validator::make($request->all(), [
            "image" => "required|image|max:5120",
            "path" => 'string'
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();
        $imgPath = 'images';
        if (isset($fields['path'])) {
            $imgPath = $imgPath.'/'.$fields['path'];
        }
        $file = $request->file('image');
        $fileName = $file->getClientOriginalName();
        $file->move(public_path($imgPath), $fileName);
        return $this->successResponse(null, 'Upload gambar berhasil');
    }

    public function rmImage(Request $request) {
        $path = $request->query('path');
        if (!$path) {
            return $this->DataInvalidresponse(['path' => 'harus diisi']);
        }
        if (!is_file(public_path('images/'.$path))) {
            return $this->ErrorResponse('File tidak ditemukan', 404);
        }
        unlink(public_path('images/'.$path));
        return $this->successResponse(null, 'File berhasil dihapus');
    }

    public function makeDir(Request $request) {
        $validator = Validator::make($request->all(), [
            "path" => "required|string"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();
        mkdir(public_path('images/'.$fields['path']));
        return $this->successResponse(null, 'Folder berhasil dibuat');
    }
    
    /**
     * Recursively delete a file or directory.  Use with care!
     *
     * @param string $path
     */
    private function recursiveRemove(string $path) {
        if (is_dir($path)) {
            foreach (scandir($path) as $entry) {
                if (!in_array($entry, ['.', '..'], true)) {
                    $this->recursiveRemove($path . DIRECTORY_SEPARATOR . $entry);
                }
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }
    public function rmDir(Request $request) {
        $path = $request->query('path');
        if (!$path) {
            return $this->DataInvalidresponse(['path' => 'harus diisi']);
        }
        if (!is_dir(public_path('images/'.$path))) {
            return $this->ErrorResponse('Folder tidak ditemukan', 404);
        }
        $this->recursiveRemove(public_path('images/'.$path));
        return $this->successResponse(null, 'Folder berhasil dihapus');
    }
}
