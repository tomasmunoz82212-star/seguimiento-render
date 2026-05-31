use App\View\Composers\NotificationComposer;
use Illuminate\Support\Facades\View;

public function boot()
{
    View::composer('*', NotificationComposer::class);
}