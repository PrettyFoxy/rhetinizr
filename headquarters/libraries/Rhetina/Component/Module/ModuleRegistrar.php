<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Component\Module;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class ModuleRegistrar
 *
 * @package Rhetina\Component\Module
 */
class ModuleRegistrar implements \JsonSerializable
{

  /**
   * @var
   */
  private $modules;

  /**
   *
   */
  public function __construct()
  {
    $this->modules = array();
  }

  /**
   * @param Bundle $module
   */
  public function addModule( Bundle $module )
  {
    $alias = strtolower( str_replace( 'Bundle', '', $module->getName() ) );

    $this->modules[$alias] = $module;
  }

  /**
   * @return mixed
   */
  public function getModules()
  {
    return $this->modules;
  }

  /**
   * (PHP 5 &gt;= 5.4.0)<br/>
   * Specify data which should be serialized to JSON
   *
   * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
   * @return mixed data which can be serialized by <b>json_encode</b>,
   *       which is a value of any type other than a resource.
   */
  public function jsonSerialize()
  {
    return array_keys($this->modules);
  }
}