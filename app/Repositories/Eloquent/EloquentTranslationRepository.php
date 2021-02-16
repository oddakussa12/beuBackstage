<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/19
 * Time: 18:35
 */
namespace App\Repositories\Eloquent;

use App\Models\TranslationTranslation;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\Contracts\TranslationRepository;


class EloquentTranslationRepository  extends EloquentBaseRepository implements TranslationRepository
{
    /**
     * @param string $key
     * @param string $locale
     * @return string
     */
    public function findByKeyAndLocale($key, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = $this->model->where('translation_key', $key)->with('translations')->first();
        if ($translation && $translation->hasTranslation($locale)) {
            return $translation->translate($locale)->translation_value;
        }

        return '';
    }

    /**
     * @param $locale
     * @param $group
     * @param $namespace
     * @return array
     */
    public function getTranslationsForGroupAndNamespace($locale, $group, $namespace)
    {
        $start = $group . '.';
        $test = $this->model->where('translation_key', 'LIKE', "{$start}%")->whereHas('translations', function (Builder $query) use ($locale) {
            $query->where('translation_locale', $locale);
        })->get();
        $translations = [];
        foreach ($test as $item) {
            $key = str_replace($start, '', $item->translation_key);
            $translations[$key] = $item->translate($locale)->translation_value;
        }
        return $translations;
    }

    /**
     * @return array
     */
    public function allFormatted()
    {
        $allRows = $this->all();
        $allDatabaseTranslations = [];
        foreach ($allRows as $translation) {
            foreach (config('laravellocalization.supportedLocales') as $locale => $language) {
                if ($translation->hasTranslation($locale)) {
                    $allDatabaseTranslations[$locale][$translation->translation_key] = $translation->translate($locale)->translation_value;
                }
            }
        }

        return $allDatabaseTranslations;
    }

    public function saveTranslationForLocaleAndKey($locale, $key, $value)
    {
        $translation = $this->findTranslationByKey($key);
        $translation->translateOrNew($locale)->translation_value = $value;
        $translation->save();
        return $translation;
    }

    public function findTranslationByKey($key)
    {
        return $this->model->firstOrCreate(['translation_key' => $key]);
    }

    /**
     * Update the given translation key with the given data
     * @param string $key
     * @param array $data
     * @return mixed
     */
    public function updateFromImport($key, array $data)
    {
        $translation = $this->findTranslationByKey($key);
        $translation->update($data);
    }

    /**
     * Set the given value on the given TranslationTranslation
     * @param TranslationTranslation $translationTranslation
     * @param string $value
     * @return void
     */
    public function updateTranslationToValue(TranslationTranslation $translationTranslation, $value)
    {
        $translationTranslation->translation_value = $value;
        $translationTranslation->save();
    }
}
