<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractTestCase extends TestCase
{
    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        $container = new ContainerBuilder();
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        $container->set('validator', $this->validator);
    }

    protected function getValidationErrors(Object $entity): void
    {
        $errors = $this->validator->validate($entity);
        $this->assertCount(0, $errors);

    }

    protected function testGettersAndSetters(object $entity, array $propertyValues): void
    {
        foreach ($propertyValues as $propertyName => $propertyValue) {
            $setter = 'set' . ucfirst($propertyName);
            $getter = 'get' . ucfirst($propertyName);

            $entity->$setter($propertyValue);
            $this->assertEquals($propertyValue, $entity->$getter());
        }
    }

}