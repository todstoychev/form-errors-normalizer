<?php

namespace Todstoychev\FormErrorsNormalizer\Tests\Component\Serializer\Normalizer;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Serializer\Exception\LogicException;
use Todstoychev\FormErrorsNormalizer\Component\Serializer\Normalizer\FormErrorsNormalizer;

class FormErrorsNormalizerTest extends TestCase
{
    /**
     * @var FormErrorsNormalizer
     */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = new FormErrorsNormalizer();
    }

    public function testNormalize()
    {
        $form = static::createMock(Form::class);
        $child = static::createMock(Form::class);
        $formError = static::createMock(FormError::class);
        $formError->method('getMessage')
            ->willReturn('Main error')
        ;
        $childError = static::createMock(FormError::class);
        $childError->method('getMessage')
            ->willReturn('Child error')
        ;
        $child->method('getErrors')
            ->willReturn([$childError])
        ;
        $child->method('getName')
            ->willReturn('child')
        ;
        $child->method('all')
            ->willReturn([])
        ;
        $form->method('getErrors')
            ->willReturn([$formError])
        ;
        $form->method('all')
            ->willReturn([$child])
        ;

        $expected = [
            'Main error',
            'child' => [
                'Child error',
            ],
        ];

        $actual = $this->normalizer->normalize($form);

        static::assertEquals($actual, $expected);
    }

    public function testThrowsException()
    {
        static::expectException(LogicException::class);

        $this->normalizer->normalize(new ArrayObject());
    }

    public function testSupportsNormalizationPositive()
    {
        $form = static::createMock(Form::class);
        $actual = $this->normalizer->supportsNormalization($form);

        static::assertTrue($actual);
    }

    public function testSupportsNormalizationNegative()
    {
        $actual = $this->normalizer->supportsNormalization(new ArrayObject());

        static::assertFalse($actual);
    }
}
