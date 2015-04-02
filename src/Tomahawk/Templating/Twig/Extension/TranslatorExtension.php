<?php

namespace Tomahawk\Templating\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

class TranslatorExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Construct
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @api
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @api
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('trans', array($this, 'trans')),
            new \Twig_SimpleFunction('trans_choice', array($this, 'transChoice')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translator';
    }
}
