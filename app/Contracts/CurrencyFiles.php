<?php

namespace App\Services\Contracts;

Interface CurrencyFiles {

    public function saveCurrentFile();

    public function getCurrentData();

    public function updateData();
}