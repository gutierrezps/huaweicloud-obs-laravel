<?php

namespace App\Http\Controllers;

use \Throwable;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ObsController extends Controller
{
    public function list(Request $request): View
    {
        $response = view('obs-console');

        try {
            $files = Storage::disk('s3')->files('', true);
        } catch (Throwable $e) {
            $files = [];

            $errorMessage = "Failed to list objects: {$e->getMessage()} - ";
            $errorMessage .= 'bucket: ' . config('filesystems.disks.s3.bucket');
            $errorMessage .= ' - endpoint: ' . config('filesystems.disks.s3.endpoint');

            $previousError = session('error');
            if ($previousError) {
                $errorMessage = $previousError . '<br />' . $errorMessage;
                $request->session()->forget('error');
            }
            $request->session()->now('error', $errorMessage);
        }

        return view('obs-console', compact('files'));
    }

    public function upload(Request $request): RedirectResponse
    {
        $response = redirect('/');

        if (!$request->hasFile('file_upload')) {
            return $response->with('error', 'File is mandatory');
        }

        $fileObj = $request->file('file_upload');

        if (!$fileObj->isValid()) {
            return $response
                ->with('error', 'There was a problem to upload the file');
        }

        $baseFolder = $request->input('base_folder');
        $originalFileName = $fileObj->getClientOriginalName();

        if ($baseFolder) {
            $originalFileName = $baseFolder . '/' . $originalFileName;
        }

        # Replacing all characters that cannot be freely used in object key names,
        # according to Guidelines on Naming Object Keys, available at
        # <https://support.huaweicloud.com/intl/en-us/ugobs-obs/obs_41_0015.html>
        $objectKey = preg_replace("/[^a-zA-Z0-9!-_.*'()\/]/", '_', $originalFileName);

        try {
            $path = $fileObj->storeAs('', $objectKey, 's3');
            assert(strlen($path) > 0);
        } catch (Throwable $e) {
            return $response->with('error', 'File was not saved to OBS: ' . $e->getMessage());
        }

        return $response->with('success', 'File uploaded successfully');
    }

    public function delete(Request $request): RedirectResponse
    {
        $response = redirect('/');

        $objectKey = $request->input('object_key', '');
        if (strlen($objectKey) == 0) {
            return $response->with('error', "Object key '{$objectKey}' is invalid");
        }

        try {
            Storage::disk('s3')->delete($objectKey);
        } catch (Throwable $e) {
            return $response->with('error', 'Failed to delete object: ' . $e->getMessage());
        }

        return $response->with('success', "Object '{$objectKey}' deleted");
    }
}
