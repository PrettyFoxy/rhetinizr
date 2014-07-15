<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

// File Path: \module\admincp\include\component\controller\index.class.php
// Line: 372

if (!class_exists('Rhetina')) {
    return;
}
$aMenus = isset($aMenus) ? (array) $aMenus : array();
$aMenus['rhetinizr.rhetinizr'] = array(
    'rhetinizr.rhetinizr' => array(
        'rhetinizr.dashboard' => 'admincp.rhetinizr.dashboard',
        'rhetinizr.products' => 'admincp.rhetinizr.products',
        'rhetinizr.news' => 'admincp.rhetinizr.news',
        'rhetinizr.terms' => 'admincp.rhetinizr.terms'
    )
);
