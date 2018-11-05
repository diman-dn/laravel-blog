<?php

namespace App\Providers;

use App\Comment;
use Illuminate\Support\ServiceProvider;
use App\Post;
use App\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // При загрузке pages._sidebar - вызываем функцию-колбек с запросами
        view()->composer('pages._sidebar', function ($view) {
            // Запросы для _sidebar
            $view->with('popularPosts', Post::getPopularPosts());
            $view->with('featuredPosts', Post::getFeaturedPosts());
            $view->with('recentPosts', Post::getRecentPosts());
            $view->with('categories', Category::all());
        });

        view()->composer('admin._sidebar', function ($view) {
            // Запросы для _sidebar
            $view->with('newCommentsCount', Comment::where('status', 0)->count());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
