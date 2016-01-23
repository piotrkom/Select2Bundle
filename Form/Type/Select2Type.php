<?php

namespace CyberApp\Select2Bundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Select2Type extends AbstractType
{
    /**
     * @var string
     */
    protected $widget;

    /**
     * @var array
     */
    protected $configs;

    /**
     * Select2Type constructor
     *
     * @param string $widget  Parent form widget
     * @param array  $configs Default configuration
     */
    public function __construct($widget, array $configs = array())
    {
        $this->widget = $widget;
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['configs'] = $options['configs'];

        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'select2'
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $defaults = [];
        if (null !== $this->configs) {
            $defaults = $this->configs;
        }

        $resolver
            ->setDefault('configs', $defaults)
            ->setAllowedTypes('configs', ['array', 'null',])
            ->setNormalizer('configs', function (Options $options, $configs) use ($defaults) {
                if (null === $configs) {
                    return $defaults;
                }

                return array_merge($defaults, $configs);
            })
        ;
    }

    public function getParent()
    {
        return $this->widget;
    }

    public function getName()
    {
        return 'select2_' . $this->widget;
    }
}
