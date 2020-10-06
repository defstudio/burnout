<?php


namespace DefStudio\Burnout\Controllers;


use DefStudio\Burnout\Models\BurnoutEntry;
use Facade\Ignition\ErrorPage\Renderer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\App;


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

    private function authorize(): void
    {
        if (App::environment() == 'production') {
            throw new AuthorizationException();
        }
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


}
