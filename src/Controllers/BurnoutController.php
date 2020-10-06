<?php /** @noinspection PhpUnhandledExceptionInspection */


namespace DefStudio\Burnout\Controllers;


use DefStudio\Burnout\Models\BurnoutEntry;
use Facade\Ignition\ErrorPage\Renderer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;


class BurnoutController
{
    private Renderer $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function index()
    {
        $this->authorize();


        $entries = BurnoutEntry::orderBy('id', 'desc')->get();
        return view('burnout::index')->with('entries', $entries);
    }

    public function show(int $burnout_entry)
    {
        $this->authorize();


        $burnout_entry = BurnoutEntry::findOrFail($burnout_entry);

        $result = $this->renderer->render('errorPage', $burnout_entry->view_data());

        return response($result);
    }

    public function destroy(int $burnout_entry)
    {
        $this->authorize();

        $burnout_entry = BurnoutEntry::findOrFail($burnout_entry);

        $burnout_entry->delete();

        return redirect()->to(route('burnout.index'));
    }

    public function clear()
    {
        $this->authorize();

        BurnoutEntry::truncate();


        return redirect()->to(route('burnout.index'));
    }

    private function authorize(): void
    {
        if (App::environment() != 'local') return;

        if ($this->is_current_user_allowed()) return;

        throw new AuthorizationException();
    }

    private function is_current_user_allowed(): bool
    {

        if (Auth::guest()) return false;

        if (!in_array(Auth::user()->email, config('burnout.allowed_users'))) return false;

        return true;
    }
}
