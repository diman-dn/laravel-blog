<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    const IS_BANNED = 1;
    const IS_ACTIVE = 0;
    const IS_ADMIN = 1;
    const IS_NORMAL = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'text_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Связь пользователя со статьями
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Связь пользователя с комментариями
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Метод создания нового пользователя
     * @param $fields
     * @return static
     */
    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->save();

        return $user;
    }

    /**
     * Метод редактирования информации о пользователе
     * @param $fields
     */
    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    /**
     * Метод сохранения пароля если он был изменен
     * @param $password
     */
    public function generatePassword($password)
    {
        if($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }

    /**
     * Метод удаления пользователя
     */
    public function remove()
    {
        $this->removeAvatar();
        $this->delete();
    }

    /**
     * Метод удаления аватара пользователя, если он существует
     */
    public function removeAvatar()
    {
        if($this->avatar != null) {
            Storage::delete('uploads/' . $this->avatar);
        }
    }

    /**
     * Метод загрузки/обновления аватара пользователя
     * @param $image
     * @return int
     */
    public function uploadAvatar($image)
    {
        if($image == null) return 0;

        $this->removeAvatar();
        $filename = str_random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->avatar = $filename;
        $this->save();
    }

    /**
     * Метод получения аватара пользователя
     * @return string
     */
    public function getAvatar()
    {
        if($this->avatar == null) {
            return '/img/no-avatar.png';
        }
        return '/uploads/' . $this->avatar;
    }

    /**
     * Метод изменения пользователя на "админ"
     */
    public function makeAdmin()
    {
        $this->is_admin = User::IS_ADMIN;
        $this->save();
    }

    /**
     * Метод изменения пользователя на "не админ"
     */
    public function makeNormal()
    {
        $this->is_admin = User::IS_NORMAL;
        $this->save();
    }

    /**
     * Метод переключения пользователя (админ/не админ)
     * @param $value
     */
    public function toggleAdmin($value)
    {
        if($value == null) {
            return $this->makeNormal();
        }
        return $this->makeAdmin();
    }

    /**
     * Метод изменения статуса пользователя на "забанен"
     */
    public function ban()
    {
        $this->status = User::IS_BANNED;
        $this->save();
    }

    /**
     * Метод изменения статуса пользователя на "активен"
     */
    public function unban()
    {
        $this->status = User::IS_ACTIVE;
        $this->save();
    }

    /**
     * Метод переключения статуса пользователя (забанен/активен)
     * @param $value
     */
    public function toggleBan($value)
    {
        if($value == null) {
            return $this->unban();
        }
        return $this->ban();
    }
}
