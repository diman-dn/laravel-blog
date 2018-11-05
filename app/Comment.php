<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const ALLOW = 1;
    const DISALLOW = 0;

    /**
     * Связь комментария с постом
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Связь комментария с автором
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Метод одобрения комментария
     */
    public function allow()
    {
        $this->status = Comment::ALLOW;
        $this->save();
    }

    /**
     * Метод блокировки комментария
     */
    public function disallow()
    {
        $this->status = Comment::DISALLOW;
        $this->save();
    }

    /**
     * Метод переключения статуса комментария (одобрен/блокирован)
     */
    public function toggleStatus()
    {
        if($this->status == Comment::DISALLOW) {
            return $this->allow();
        }
        return $this->disallow();
    }

    /**
     * Метод удаления комментария
     */
    public function remove()
    {
        $this->delete();
    }
}
