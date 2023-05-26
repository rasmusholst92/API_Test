<?php

namespace Validation;

class UserValidation
{
    public static function validate($data)
    {
        if ($data === null || !is_array($data)) {
            throw new \Exception('Invalid data format: not an array.');
        }

        $requiredFields = ['username', 'email'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = 'Invalid data format: ' . $field . ' field is required.';
            }
        }

        if (!empty($errors)) {
            throw new \Exception(json_encode($errors));
        }
    }
}