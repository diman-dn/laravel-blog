<?php

namespace App\Http\Controllers\Admin;

use App\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SubscribeEmail;

class SubscribersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = Subscription::all();

        return view('admin.subs.index', [
            'subs' => $subscriptions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.subs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:subscriptions'
        ]);
        $new_subs = Subscription::add($request->get('email'));
        if($request->get('verify')) {
            $new_subs->generateToken();
            \Mail::to($request->get('email'))->send(new SubscribeEmail($new_subs));
        }
        return redirect()->back()->with('status', 'Новый подписчик ' . $request->get('email') . ' был успешно добавлен.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Subscription::find($id)->remove();
        return redirect()->back()->with('status', 'Подписчик был успешно удален.');
    }
}
