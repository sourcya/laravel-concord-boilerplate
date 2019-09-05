<?php

namespace Sourcya\BoilerplateBox\Http\Controllers;

use Carbon\Carbon;
use Sourcya\BoilerplateBox\Http\Resources\UploadCollection;
use Sourcya\UploadModule\Models\FileProxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    /**
     * UploadController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadAvatar(Request $request)
    {

        $this->validate($request, [
            'avatar_image'=>'required|file|image|mimes:jpeg,png|max:2048',
        ]);

        $file = $request->file('avatar_image');
        $extension = $file->getClientOriginalExtension();
        $path = 'images/'.auth()->id().'/avatar';
        $currentAvatarPath = helper('sourcya_helper')->getUserAvatar(auth()->id());

        if (Storage::disk('public')->exists($currentAvatarPath))
            Storage::disk('public')->delete($currentAvatarPath);

        $file->storeAs('images/'.auth()->id(), 'avatar.'.$extension,'public');

        if(!helper('sourcya_helper')->getUserAvatar(auth()->id(), false)) {
            auth()->user()->files()->create([
                'type' => 'avatar',
                'name' => 'avatar',
                'extension' => $extension,
                'path' => $path
            ]);
            return response()->json([
                'url' => Storage::disk('public')->url(helper('sourcya_helper')->getUserAvatar(auth()->id())),
                'status' => 'success',
                'message' => 'User avatar has been uploaded successfully'
            ],200);
        } else {
            auth()->user()->files()->update([
                'type' => 'avatar',
                'extension' => $extension,
                'path' => $path
            ]);
            return response()->json([
                'url' => Storage::disk('public')->url(helper('sourcya_helper')->getUserAvatar(auth()->id())),
                'status' => 'success',
                'message' => 'User avatar has been updated successfully'
            ],202);
        }
    }

    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvatar($userId)
    {
        if (auth()->id() == $userId || auth()->user()->isAdmin()) {
            if (helper('sourcya_helper')->getUserAvatar($userId))
            return response()->json([
                'url' => Storage::disk('public')->url(helper('sourcya_helper')->getUserAvatar($userId)),
                'status' => 'success',
                'message' => 'User avatar has been fetched successfully'
            ], 200);
            else {
                $message = 'No such user avatar found';
                return $this->errorResponseHandler($message,404);
            }
        } else {
            $message = 'UnAuthorized';
            return $this->errorResponseHandler($message,401);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadFile(Request $request)
    {
        $this->validate($request, [
            'file'=>'required|file',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = 'files/'.auth()->id();
        $file_name = 'file_'.Carbon::now()->format('YmdHs');
        $file->storeAs('files/'.auth()->id(), $file_name.'.'.$extension,'public');

        auth()->user()->files()->create([
            'type' => 'file',
            'name' => $file_name,
            'extension' => $extension,
            'path' => $path
        ]);
        return response()->json([
            'url' => Storage::disk('public')->url($path.'/'.$file_name.'.'.$extension),
            'status' => 'success',
            'message' => 'User file has been uploaded successfully'
        ],200);
    }

    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|UploadCollection
     */
    public function getUserFiles($userId)
    {
        if (auth()->id() == $userId || auth()->user()->isAdmin()) {
            if (auth()->user()->files()->exists()){
                $files = FileProxy::where('user_id', $userId)->latest()->paginate(12);
                $message = 'All user files has been fetched successfully';
                return new UploadCollection($files,$message);
            } else {
                $message = 'User has No files yet';
                return $this->errorResponseHandler($message,404);
            }
        } else {
            $message = 'UnAuthorized';
            return $this->errorResponseHandler($message,401);
        }
    }
}

