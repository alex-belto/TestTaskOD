<?php

namespace App\Service\Form;

use Symfony\Component\Form\FormInterface;

class FormErrorHandler
{
    public function handleError(FormInterface $form): array
    {
        $errors = [];
        $formErrors = $form->getErrors(true);
        foreach ($formErrors as $error) {
            $errorName = $error->getOrigin()->getName();
            if (!array_key_exists($errorName, $errors)) {
                $errors[$errorName] = [];
            }
            $errors[$errorName][] = $error->getMessage();
        }

        return $errors;
    }

}