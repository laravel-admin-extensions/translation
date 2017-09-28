<?php

namespace Encore\Admin\Translation;

use Encore\Admin\Admin;
use Encore\Admin\Extension;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class Translation extends Extension
{
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('translation', __CLASS__);
    }

    public static function getLocales()
    {
        $exists = array_map(function ($directory) {
            return basename($directory);
        }, app('files')->directories(resource_path('lang')));

        return array_unique(array_merge($exists, static::config('locales', [])));
    }

    public static function getGroups()
    {
        $groups = TranslationModel::select(DB::raw('DISTINCT `group`'))->get()->toarray();

        $groups = array_flatten($groups);

        return array_unique(array_merge($groups, static::config('groups', [])));
    }

    public function importTranslations($locale = null, $force = false)
    {
        if (is_null($locale)) {
            $locale = config('app.locale');
        }

        $langPath = resource_path('lang').'/'.$locale;

        $counter = 0;

        foreach (app('files')->allfiles($langPath) as $file) {
            $info = pathinfo($file);
            $group = $info['filename'];

            $translations = \Lang::getLoader()->load($locale, $group);
            if ($translations && is_array($translations)) {
                foreach (array_dot($translations) as $key => $value) {
                    $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $force);
                    $counter += $importedTranslation ? 1 : 0;
                }
            }
        }

        return $counter;
    }

    public function importTranslation($key, $value, $locale, $group, $force = false)
    {
        if (is_array($value)) {
            return false;
        }

        $translation = TranslationModel::firstOrNew([
            'locale' => $locale,
            'group'  => $group,
            'key'    => $key,
        ]);

        if ($force || !$translation->value) {
            $translation->value = (string) $value;
            $translation->status = TranslationModel::STATUS_SAVED;
            $translation->save();

            return true;
        }

        return false;
    }

    /**
     * @param $locale
     */
    public function resetTranslations($locale)
    {
        TranslationModel::where('locale', $locale)->delete();
    }

    public function exportAllTranslations()
    {
        $groups = TranslationModel::select(DB::raw('DISTINCT `group`'))->get();

        foreach ($groups as $group) {
            $this->exportTranslations($group->group);
        }
    }

    public function exportTranslations($group)
    {
        $translations = TranslationModel::where('group', $group)->get();

        $tree = [];

        foreach ($translations as $translation) {
            array_set($tree[$translation->locale][$translation->group], $translation->key, $translation->value);
        }

        foreach ($tree as $locale => $groups) {
            if (isset($groups[$group])) {
                $translations = $groups[$group];
                $path = resource_path('lang/'.$locale.'/'.$group.'.php');
                $output = "<?php\n\nreturn ".var_export($translations, true).";\n";
                app('files')->put($path, $output);
            }
        }

        TranslationModel::where('group', $group)->update(['status' => TranslationModel::STATUS_SAVED]);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        /* @var \Illuminate\Routing\Router $router */
        Route::group(['prefix' => config('admin.route.prefix')], function ($router) {
            $attributes = array_merge([
                'middleware' => config('admin.route.middleware'),
            ], static::config('route', []));

            Route::group($attributes, function ($router) {

                /* @var \Illuminate\Routing\Router $router */
                $router->resource('translations', 'Encore\Admin\Translation\TranslationController');
            });
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Translations', 'translations', 'fa-lang');

        parent::createPermission('Translations', 'ext.translations', 'translations*');
    }
}
