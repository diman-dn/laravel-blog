<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;
    const IS_FEATURED = 1;
    const IS_STANDART = 0;

    protected $fillable = ['title', 'content'];

    /**
     * Связь поста с категорией
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne(Category::class);
    }

    /**
     * Связь поста с автором
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(User::class);
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
        Storage::delete('uploads/' . $this->image);
        $this->delete();
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
        Storage::delete('uploads/' . $this->image);
        // Создаем имя для загружаемого изображения
        $filename = str_random(10) . '.' . $image->extension();
        // Сохраняем под новым именем в папку public/uploads
        $image->saveAs('uploads', $filename);
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
            return false;
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
}
