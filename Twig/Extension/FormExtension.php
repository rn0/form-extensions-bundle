<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\FormExtensionsBundle\Twig\Extension;

use Symfony\Component\Form\FormView;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class FormExtension extends \Twig_Extension
{
    /**
     * @var bool
     */
    protected $ckeditorIncluded;

    /**
     * @var bool
     */
    protected $ckeditorInitializerIncluded;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @param $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->ckeditorIncluded = false;
        $this->ckeditorInitializerIncluded = false;
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fsi_form_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'form_group' => new \Twig_Function_Node('Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', array('is_safe' => array('html'))),
            'form_group_is_valid' => new \Twig_Function_Method($this, 'formGroupIsValid'),
            'include_ckeditor' => new \Twig_Function_Method($this, 'includeCkeditor', array('is_safe' => array('html'))),
            'ckeditor_initializer' => new \Twig_Function_Method($this, 'ckeditorInitializer', array('is_safe' => array('html'))),
        );
    }

    /**
     * @return string
     */
    public function includeCkeditor()
    {
        if (!$this->environment->hasExtension('assets')) {
            return;
        }

        if (!$this->ckeditorIncluded) {
            $this->ckeditorIncluded = true;


            $jsPath = $this->environment
                ->getExtension('assets')
                ->getAssetUrl($this->basePath . 'ckeditor.js');

            $script = sprintf('<script type="text/javascript" src="%s"></script>', $jsPath);

            return $script;
        }
    }

    /**
     * @param bool $force
     * @return mixed
     */
    public function ckeditorInitializer($force = false)
    {
        if ($this->ckeditorInitializerIncluded && !$force) {
            return;
        }

        $this->ckeditorInitializerIncluded = true;

        $template = $this->environment->loadTemplate('@FSiFormExtensions/Form/form_div_layout.html.twig');
        return $template->displayBlock('ckeditor_initializer', array());
    }

    /**
     * @param FormView $view
     * @param $group
     * @return bool
     */
    public function formGroupIsValid(FormView $view, $group)
    {
        $valid = true;
        foreach ($view as $child) {
            if ($child->vars['group'] === $group) {
                if (!$child->vars['valid']) {
                    $valid = false;
                }
            }
        }

        return $valid;
    }
}
