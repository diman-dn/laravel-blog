<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Post extends Model
{
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;
    const IS_FEATURED = 1;
    const IS_STANDART = 0;

    protected $fillable = ['title', 'content', 'date', 'description'];

    /**
     * Связь поста с категорией
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
//        return $this->belongsTo(Category::class, 'category_id');
        return $this->belongsTo(Category::class);
    }

    /**
     * Связь поста с автором
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Связь поста с комментариями
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Связь поста с тегами
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        );
    }

    /**
     * Создание SEO ссылок (slug)
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Метод добавления нового поста
     * @param $fields
     * @return static
     */
    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->user_id = 1;
        $post->save();

        return $post;
    }

    /**
     * Метод редактирования поста
     * @param $fields
     */
    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    /**
     * Метод удаления поста
     */
    public function remove()
    {
        // Удаляем изображение, перед удалением поста
        $this->removeImage();
        $this->delete();
    }

    /**
     * Метод удаления изображения поста, если оно существует
     */
    public function removeImage()
    {
        if($this->image != null) {
            Storage::delete('uploads/' . $this->image);
        }
    }

    /**
     * Метод загрузки/обновления изображения к посту
     * @param $image
     * @return int
     */
    public function uploadImage($image)
    {
        if($image == null) return 0;

        // Удаляем старое изображение перед сохранением/обновлением нового изображения
        $this->removeImage();
        // Создаем имя для загружаемого изображения
        $filename = str_random(10) . '.' . $image->extension();
        // Сохраняем под новым именем в папку public/uploads
        $image->storeAs('uploads', $filename);
        // Сохраняем имя изображения в таблицу к посту
        $this->image = $filename;
        $this->save();
    }

    /**
     * Метод получения изображения поста
     * @return bool|string
     */
    public function getImage()
    {
        if($this->image == null) {
            return '/img/no-image.png';
        }
        return '/uploads/' . $this->image;
    }

    /**
     * Метод сохранения (привязки) категории
     * @param $id
     * @return int
     */
    public function setCategory($id)
    {
        if($id == null) return 0;
        // Вариант 1 (сохраниение через связь)
//        $category = Category::find($id);
//        $this->category()->save($category);
        // Вариант 2
        $this->category_id = $id;
        $this->save();
    }

    /**
     * Метод сохранения тегов
     * @param $ids
     * @return int
     */
    public function setTags($ids)
    {
        if($ids == null) return 0;
        // Синхронизация с массивом $ids
        $this->tags()->sync($ids);
    }

    /**
     * Метод сохраниения поста в черновик
     */
    public function setDraft()
    {
        $this->status = Post::IS_DRAFT;
        $this->save();
    }

    /**
     * Метод сохранения поста в опубликованные
     */
    public function setPublic()
    {
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }

    /**
     * Метод изменения статуса поста (черновик/опубликован)
     * @param $value
     */
    public function toggleStatus($value)
    {
        if($value == null) {
            return $this->setDraft();
        }
        return $this->setPublic();
    }

    /**
     * Метод добавления поста в рекомендуемые
     */
    public function setFeatured()
    {
        $this->is_featured = Post::IS_FEATURED;
        $this->save();
    }

    /**
     * Метод исключения поста из рекомендуемых
     */
    public function setStandart()
    {
        $this->is_featured = Post::IS_STANDART;
        $this->save();
    }

    /**
     * Метод переключения поста (рекомендован/стандарт)
     * @param $value
     */
    public function toggleFeatured($value)
    {
        if($value == null) {
            return $this->setStandart();
        }
        return $this->setFeatured();
    }

    /**
     * Форматирование даты в формат для БД
     * @param $value
     */
    public function setDateAttribute($value)
    {
        $date = Carbon::createFromFormat('d/m/y', $value)->format('Y-m-d');
        $this->attributes['date'] = $date;
    }

    /**
     * Форматирование даты в формат для пользователей
     * @param $value
     * @return string
     */
    public function getDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/y');
    }

    /**
     * Получение названия категории
     * @return string
     */
    public function getCategoryTitle()
    {
        return $this->category != null ? $this->category->title : 'Нет категории';
    }

    /**
     * Получение массива тегов строкой
     * @return string
     */
    public function getTagsTitles()
    {
        return !$this->tags->isEmpty() ? implode(', ', $this->tags->pluck('title')->all()) : 'Нет тегов';
    }

    /**
     * Получение id категории или null, если ее нет
     * @return null
     */
    public function getCategoryID()
    {
        return $this->category != null ? $this->category->id : null;
    }

    /**
     * Форматирование даты для фронт-енда
     * @return string
     */
    public function getDate()
    {
        return Carbon::createFromFormat('d/m/y', $this->date)->format('F d, Y');
    }

    /**
     * Проверка наличия предыдущего поста
     * @return mixed
     */
    public function hasPrevious()
    {
        return self::where('id', '<', $this->id)->max('id');
    }

    /**
     * Получение предыдущего поста
     * @return mixed
     */
    public function getPrevious()
    {
        $postID = $this->hasPrevious(); // id
        return self::find($postID);
    }

    /**
     * Проверка наличия следующего поста
     * @return mixed
     */
    public function hasNext()
    {
        return self::where('id', '>', $this->id)->min('id');
    }

    /**
     * Получение следующего поста
     * @return mixed
     */
    public function getNext()
    {
        $postID = $this->hasNext();
        return self::find($postID);
    }

    /**
     * Получение всех постов, кроме текущего
     * @return static
     */
    public function related()
    {
        return self::all()->except($this->id);
    }

    /**
     * Проверка наличия категории у поста
     * @return bool
     */
    public function hasCategory()
    {
        return $this->category != null ? true : false;
    }

    /**
     * Получить популярные посты
     * @return mixed
     */
    public static function getPopularPosts()
    {
        return self::orderBy('views', 'desc')->take(3)->get();
    }

    /**
     * Получить рекомендуемые посты
     * @return mixed
     */
    public static function getFeaturedPosts()
    {
        return self::where('is_featured', 1)->take(3)->get();
    }

    /**
     * Получить недавние посты
     * @return mixed
     */
    public static function getRecentPosts()
    {
        return self::orderBy('date', 'desc')->take(4)->get() ;
    }

    /**
     * Получить все одобренные комментарии для текущего поста
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getComments()
    {
        return $this->comments()->where('status', 1)->get();
    }
}
