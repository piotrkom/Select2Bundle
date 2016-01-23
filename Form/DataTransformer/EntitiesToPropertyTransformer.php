<?php

namespace CyberApp\Select2Bundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EntitiesToPropertyTransformer implements DataTransformerInterface
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
        if (is_null($value) || count($value) === 0) {
            return array();
        }

        $data = array();
        foreach ($value as $item) {
            $text = is_null($this->property)
                ? (string) $item
                : $this->propertyAccessor->getValue($item, $this->property);

            $identifier = $this->propertyAccessor->getValue($item, $this->metadata->getIdentifier()[0]);
            $data[$identifier ?: $text] = $text;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (! is_array($value) || count($value) === 0) {
            return new ArrayCollection();
        }

        if ($this->tags && $this->property) {
            $objects = [];
            foreach ($value as $val) {
                if (! is_numeric($value)) {
                    $objects[] = $this->metadata->newInstance();
                    $this->propertyAccessor->setValue(end($objects), $this->property, $val);
                }

                $objects[] = $this->em->getRepository($this->class)->find($val);
            }

            return $objects;
        }

        return $this
            ->em
            ->getRepository($this->class)
            ->findBy([
                $this->metadata->getIdentifier()[0] => $value
            ])
        ;
    }
}
