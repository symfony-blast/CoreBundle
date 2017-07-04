<?php

/*
 * This file is part of the Blast Project package.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\CoreBundle\Admin;

use Sonata\AdminBundle\Export\Exporter as BaseExporter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class Exporter extends BaseExporter
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Router
     */
    protected $router;

    /**
     * setTokenStorage.
     *
     * @param $tokenStorage     TokenStorageInterface
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * setTranslator.
     *
     * @param $translator     TokenStorageInterface
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * setTwig.
     *
     * @param $twig     \Twig_Environment
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * setRouter.
     *
     * @param $router     Router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }
}
