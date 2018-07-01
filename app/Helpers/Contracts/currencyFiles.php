<?php

namespace App\Helpers\Contracts;

Interface currencyFiles {

    public function saveCurrentFile();

    public function getCurrentData();

    public function updateData();
}