<?php
namespace Sourcya\BoilerplateBox\Http\Controllers\Traits;

use Illuminate\Notifications\Notifiable as BaseNotifiable;
use Sourcya\NotificationModule\Models\ExtendedDatabaseNotificationProxy;
trait Notifiable
{
    use BaseNotifiable;

    /**
     * @return mixed
     */
    public function notifications()
    {
        return $this->morphMany(ExtendedDatabaseNotificationProxy::modelClass(), 'notifiable')->orderBy('created_at', 'desc');
    }
}
