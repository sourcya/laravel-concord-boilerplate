<?php

namespace Sourcya\BoilerplateBox\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Sourcya\NotificationModule\Models\ExtendedDatabaseNotification;
use Sourcya\BoilerplateBox\Http\Resources\NotificationCollection;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1');
    }

    /**
     * Display a listing of all user notifications.
     *
     * @return NotificationCollection
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(12);
        $message = 'All user notifications has been fetched successfully';
        return new NotificationCollection($notifications,$message);
    }

    /**
     * Display a listing of all un read user notifications.
     *
     * @return NotificationCollection
     */
    public function unRead()
    {

        $notifications = auth()->user()->unreadNotifications()->latest()->paginate(12);

        $message = 'All user un read notifications has been fetched successfully';
        return new NotificationCollection($notifications,$message);

    }

    /**
     * Mark all user notifications as read
     *
     * @return \Illuminate\Http\Response
     */
    public function markAll()
    {
        if (auth()->user()->unreadNotifications()->exists()){
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json([
                'status' => 'success',
                'message' => 'All user notifications has been marked as read successfully'
            ], 200);
        } else {
            $message = 'No notifications to set as read';
            return $this->errorResponseHandler($message,404);
        }
    }

    /**
     * Mark single user notifications as read
     *
     * @return \Illuminate\Http\Response
     */
    public function markSingle(ExtendedDatabaseNotification $notification)
    {
        if (auth()->id() == $notification->notifiable()->pluck('id')->implode(' ')){
            $notification->markAsRead();
            return response()->json([
                'status' => 'success',
                'message' => 'User notification has been marked as read successfully'
            ], 200);
        } else {
            $message = 'UnAuthorized';
            return $this->errorResponseHandler($message,401);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param ExtendedDatabaseNotification $notification
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ExtendedDatabaseNotification $notification)
    {
        if (auth()->id() == $notification->notifiable()->pluck('id')->implode(' ')){
            $notification->delete();
            return response()->json([
                'success' => true,
                'message' => 'User notification deleted successfully'
            ], 200);
        } else {
            $message = 'UnAuthorized';
            return $this->errorResponseHandler($message,401);
        }
    }

}
