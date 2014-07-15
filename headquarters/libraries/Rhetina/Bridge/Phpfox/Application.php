<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox;

use Rhetina\Component\Application\PlatformDependentApplication;

/**
 * Application Class
 *
 * @author Alexandru Furculita <alex@rhetina.com>
 */
class Application extends PlatformDependentApplication
{
  /**
   * @param string $environment
   * @param bool   $debug
   */
  public function __construct($environment, $debug)
    {
        $this->platform = 'Phpfox';
        parent::__construct($environment, $debug);
    }
}
