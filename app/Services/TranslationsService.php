<?php

namespace App\Services;

use App\Repositories\Contracts\FileTranslationRepository;
use App\Repositories\Contracts\TranslationRepository;
//use Modules\Translation\ValueObjects\TranslationGroup;
use Illuminate\Support\Collection;
class TranslationsService
{
    /**
     * @var FileTranslationRepository
     */
    private $fileTranslations;
    /**
     * @var TranslationRepository
     */
    private $databaseTranslations;

    public function __construct()
    {
        $this->fileTranslations = app(FileTranslationRepository::class);
        $this->databaseTranslations = app(TranslationRepository::class);
    }

    /**
     * Get the file translations & the database translations, overwrite the file translations by db translations
     * @return TranslationGroup
     */
    public function getFileAndDatabaseMergedTranslations()
    {
        $allFileTranslations = $this->fileTranslations->all();
        $allDatabaseTranslations = $this->databaseTranslations->allFormatted();
        foreach ($allFileTranslations as $locale => $fileTranslation) {
            foreach ($fileTranslation as $key => $translation) {
                if (is_string($translation) === false) {
                    unset($allFileTranslations[$locale][$key]);
                }
                if (isset($allDatabaseTranslations[$locale][$key])) {
                    $allFileTranslations[$locale][$key] = $allDatabaseTranslations[$locale][$key];
                    unset($allDatabaseTranslations[$locale][$key]);
                }
            }
        }

        $this->addDatabaseOnlyTranslations($allFileTranslations, $allDatabaseTranslations);
        $this->filterOnlyActiveLocales($allFileTranslations);
        return $this->reArrangeTranslations($allFileTranslations);
    }

    /**
     * Filter out the non-active locales
     * @param array $allFileTranslations
     */
    private function filterOnlyActiveLocales(array &$allFileTranslations)
    {
        $activeLocales = $this->getActiveLocales();

        foreach ($allFileTranslations as $locale => $value) {
            if (! in_array($locale, $activeLocales)) {
                unset($allFileTranslations[$locale]);
            }
        }
    }

    /**
     * Get the currently active locales
     * @return array
     */
    private function getActiveLocales()
    {
        $locales = [];

        foreach (config('laravellocalization.supportedLocales') as $locale => $translation) {
            $locales[] = $locale;
        }

        return $locales;
    }

    /**
     * @param array $allFileTranslations
     * @param array $allDatabaseTranslations
     */
    private function addDatabaseOnlyTranslations(array &$allFileTranslations, array $allDatabaseTranslations)
    {
        foreach ($allDatabaseTranslations as $locale => $group) {
            foreach ($group as $key => $value) {
                $allFileTranslations[$locale][$key] = $value;
            }
        }
    }

    /**
     * @param array $translationsRaw
     * @return Collection
     */
    private function reArrangeTranslations(array $translationsRaw)
    {
        $translations = [];

        foreach ($translationsRaw as $locale => $translationGroup) {
            foreach ($translationGroup as $key => $translation) {
                $translations[$key][$locale] = $translation;
            }
        }

        return new Collection($translations);
    }

    public function saveTranslationForLocaleAndKey($locale , $key , $value)
    {
        return $this->databaseTranslations->saveTranslationForLocaleAndKey($locale , $key , $value);
    }
}
