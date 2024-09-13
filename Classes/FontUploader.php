<?php

namespace Classess;

class FontUploader
{
    private $allowedExtensions = ['ttf']; // Allowed file types
    private $uploadDir = 'uploaded_font/'; // Upload directory
    private $file;

    public function __construct($file) {
        $this->file = $file;
    }

    public function upload() {
        if ($this->isValidUpload()) {
            if ($this->isValidExtension()) {
                return $this->moveFile() ? $this->successMessage() : $this->errorMessage('Failed to move the uploaded file.');
            } else {
                return $this->errorMessage('Invalid file type. Only TTF files are allowed.');
            }
        } else {
            return $this->errorMessage('No file was uploaded or an error occurred during upload.');
        }
    }

    private function isValidUpload() {
        return isset($this->file) && $this->file['error'] == 0;
    }

    private function isValidExtension() {
        $fileExt = strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
        return in_array($fileExt, $this->allowedExtensions);
    }

    private function moveFile() {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $destination = $this->uploadDir . basename($this->file['name']);
        return move_uploaded_file($this->file['tmp_name'], $destination);
    }

    private function successMessage() {
        return '<p class="text-success">Font uploaded successfully!</p>';
    }

    private function errorMessage($message) {
        return '<p class="text-danger">' . $message . '</p>';
    }

}