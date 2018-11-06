<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * Метод добавления нового пользователя в список рассылки
     * @param $email
     * @return static
     */
    public static function add($email)
    {
        $sub = new static;
        $sub->email = $email;
        $sub->save();

        return $sub;
    }

    /**
     * Метод генерации токена для подтверждения подписки
     */
    public function generateToken()
    {
        $this->token = str_random(100);
        $this->save();
    }

    /**
     * Метод удаления пользователя из списка рассылки
     */
    public function remove()
    {
        $this->delete();
    }
}
