<?php

namespace Tests\System;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;
use PHPUnit\Framework\Exception;

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
  protected static $USERNAME = 'serveradmin';
  protected static $PASSWORD = '';
  protected static $HOSTNAME = '127.0.0.1';
  protected static $PORT = [
    'server_query' => '10011',
    'voice' => '9987'
  ];
  
  protected function setUp() {
    parent::setUp();
    if(!(static::$PASSWORD = getenv('TS3_SERVERQUERY_ADMIN_PASSWORD')))
    {
      $this->markTestSkipped('Skipping integration tests. Required `TS3_SERVERQUERY_ADMIN_PASSWORD` environment variable empty or not set.');
      throw new Exception('Integration tests require `TS3_SERVERQUERY_ADMIN_PASSWORD` environment variable be set, but was either not set or empty.');
    }
  }
  
  public function testConstructorHost() {
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://' 
      . static::$HOSTNAME . ':' . static::$PORT['server_query'] . '/');
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class,
      $ts3host);
  }
  
  public function testConstructorExceptionBadHost() {
    $this->expectException(\TeamSpeak3_Transport_Exception::class);
    $this->expectExceptionMessageRegExp('/getaddrinfo failed/');
    
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
  
  public function testConstructorVirtualServer() {
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']);
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class,
      $ts3host);
  }
  
  public function testConstructorVirtualServerLogin() {
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query'] . '/?server_port=' . static::$PORT['voice']);
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class,
      $ts3host);
  }
}