<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Component\Application;

/**
 * PlatformDependentApplication Class
 *
 * @author Alexandru Furculita <alex@rhetina.com>
 */
class PlatformDependentApplication extends Application implements PlatformDependentInterface
{
    protected $platform;

    public function getPlatform()
    {
        return $this->platform;
    }

}
