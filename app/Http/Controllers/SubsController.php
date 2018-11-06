<?php

namespace App\Http\Controllers;

use App\Mail\SubscribeEmail;
use App\Subscription;
use Illuminate\Http\Request;

class SubsController extends Controller
{
    /**
     * Метод обработки заявки на подписку
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:subscriptions'
        ]);

        $subscriber = Subscription::add($request->get('email'));
        $subscriber->generateToken();
        \Mail::to($subscriber)->send(new SubscribeEmail($subscriber));

        return redirect()->back()->with('status', 'Проверьте Вашу почту.');
    }

    /**
     * Метод подтверждения подписки на рассылку
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify($token)
    {
        $subscriber = Subscription::where('token', $token)->firstOrFail();
        $subscriber->token = null;
        $subscriber->save();
        return redirect('/')->with('status', 'Ваша почта была подтверждена!');
    }
}
