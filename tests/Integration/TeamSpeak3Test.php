<?php

namespace Tests\System;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;

require_once 'lib/TeamSpeak3/TeamSpeak3.php';

/**
* Class ReplyTest
*
* Constants: S_... - Sample response for a command (raw formatting) from server.
*            E_... - Expected (parsed) response (i.e. from _Helper_String) from framework
*
* @package Tests\Unit\Adapter\ServerQuery
*/
class TeamSpeak3Test extends TestCase
{
  protected static $URI_SCHEME = 'serverquery';
  protected static $HOSTNAME = '127.0.0.1';
  protected static $PORT = [
    'server_query' => '10011'
  ];
  
  public function testConstructor() {
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://' 
      . static::$HOSTNAME . ':' . static::$PORT['server_query'] . '/');
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class,
      $ts3host);
  }
  
  public function testConstructorExceptionBadHost() {
    $this->expectException(\TeamSpeak3_Transport_Exception::class);
    $this->expectExceptionMessage('php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known');
    
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://TestBadHost' 
      . ':' . static::$PORT['server_query'] . '/');
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class,
      $ts3host);
  }
  
  public function testConstructorExceptionBadPort() {
    $this->expectException(\TeamSpeak3_Transport_Exception::class);
    $this->expectExceptionMessage('Connection refused');
    
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://' 
      . static::$HOSTNAME . ':12345/');
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class,
      $ts3host);
  }
  
}