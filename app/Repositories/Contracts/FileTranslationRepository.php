<?php

namespace App\Repositories\Contracts;

interface FileTranslationRepository
{
    /**
     * Get all the translations for all modules on disk
     * @return array
     */
    public function all();
}
