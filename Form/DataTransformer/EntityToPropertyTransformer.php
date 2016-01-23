<?php

namespace CyberApp\Select2Bundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EntityToPropertyTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var boolean
     */
    protected $tags;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * EntityToPropertyTransformer constructor
     *
     * @param EntityManager $em
     * @param boolean       $tags
     * @param string        $class
     * @param string|null   $property
     */
    public function __construct(EntityManager $em, $tags, $class, $property = null)
    {
        $this->em = $em;
        $this->tags = $tags;
        $this->class = $class;
        $this->metadata = $em->getClassMetadata($class);
        $this->property = $property;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        if ($this->metadata->isIdentifierComposite) {
            throw new \RuntimeException('Entities with composite primary key are not supported');
        }
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        if (null === $value) {
            return [];
        }

        $text = is_null($this->property)
            ? (string) $value
            : $this->propertyAccessor->getValue($value, $this->property);

        $identifier = $this->propertyAccessor->getValue($value, $this->metadata->getIdentifier()[0]);
        return [$identifier ?: $text => $text];
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (! is_numeric($value) && $this->tags && $this->property) {
            $object = $this->metadata->newInstance();
            $this->propertyAccessor->setValue($object, $this->property, $value);

            return $object;
        }

        return $this->em->getRepository($this->class)->find($value);
    }
}
