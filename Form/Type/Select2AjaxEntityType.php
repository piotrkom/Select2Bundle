<?php

namespace CyberApp\Select2Bundle\Form\Type;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use CyberApp\Select2Bundle\Form\DataTransformer\EntityToPropertyTransformer;
use CyberApp\Select2Bundle\Form\DataTransformer\EntitiesToPropertyTransformer;

class Select2AjaxEntityType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $configs;

    /**
     * Select2AjaxEntityType constructor
     *
     * @param EntityManager   $em
     * @param array           $configs
     */
    public function __construct(EntityManager $em, $configs = [])
    {
        $this->em = $em;
        $this->configs = $configs;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tags = false;
        if (isset($options['configs']['tags'])) {
            $tags = $options['configs']['tags'];
        }

        $builder->addViewTransformer(
            $options['multiple']
                ? new EntitiesToPropertyTransformer($this->em, $tags, $options['class'], $options['property'])
                : new EntityToPropertyTransformer($this->em, $tags, $options['class'], $options['property'])
        );
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach (['configs', 'multiple', 'remote_path', 'remote_route', 'remote_params', ] as $parameter) {
            $view->vars[$parameter] = $options[$parameter];
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $defaults = [];
        if (null !== $this->configs) {
            $defaults = $this->configs;
        }

        $resolver
            ->setRequired('class')
            ->setDefaults([
                'configs' => [],
                'property' => null,
                'required' => false,
                'compound' => false,
                'multiple' => false,
                'remote_path' => null,
                'remote_route' => null,
                'remote_params' => [],
            ])
            ->setAllowedTypes('class', ['string',])
            ->setAllowedTypes('configs', ['array', 'null',])
            ->setAllowedTypes('property', ['string', 'null',])
            ->setAllowedTypes('multiple', ['boolean',])
            ->setAllowedTypes('remote_path', ['string', 'null',])
            ->setAllowedTypes('remote_route', ['string', 'null',])
            ->setAllowedTypes('remote_params', ['array', 'null',])
            ->setNormalizer('configs', function (Options $options, $configs) use ($defaults) {
                if (null === $configs) {
                    return $defaults;
                }

                return array_merge($defaults, $configs);
            })
        ;
    }

    public function getName()
    {
        return 'select2_ajax_entity';
    }
}
