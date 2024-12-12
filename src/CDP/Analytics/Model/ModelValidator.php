<?php 

namespace App\CDP\Analytics\Model;

use App\Webhook\Error\Exception\WebhookException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ModelValidator
{
    public function __construct(
        private ValidatorInterface $validator
    )
    {
    }

    public function validate(ModelInterface $model): void
    {
        $errors = $this->validator->validate($model);

        if (count($errors) > 0) {
            $failingProperties = [];

            foreach ($errors as $error) {
                $failingProperties[] = $error->getPropertyPath();
            }

            $reflectionClass = new \ReflectionClass($model);

            throw new WebhookException(
                'Invalid ' . $reflectionClass->getShortName() . ' properties: '
                . implode(', ', $failingProperties)
            );
        }
    }
}