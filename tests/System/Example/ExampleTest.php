<?php

namespace Tests\System\Example;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;
use \PHPUnit\Framework\Exception;

require_once 'lib/TeamSpeak3/TeamSpeak3.php';

/**
 * Class HtmlTest
 *
 * @todo    : Convert tests to unit level, removing dependency through mocking
 *
 * Constants:
 *  S_... - Sample response for a command (raw formatting) from server
 *  E_... - Expected (parsed) response (i.e. from _Helper_String) from framework
 *
 * @package Tests\System\Viewer
 */
class ExampleTest extends TestCase
{
  protected static $URI_SCHEME                = 'serverquery';
  protected static $USERNAME                  = 'serveradmin';
  protected static $PASSWORD                  = ''; // Set through env var, see `setUp()`.
  protected static $HOSTNAME                  = '127.0.0.1';
  //protected static $HOSTNAME                  = '192.168.0.21';
  protected static $PORT                      = [
    'server_query' => '10011',
    'voice'        => '9987'
  ];
  protected static $SERVER_ID                 = 1;
  protected static $SERVER_NAME               = 'TeamSpeak ]I[ Server';
  protected static $SERVER_DEFAULT_CHANNEL_ID = 1;
  protected static $NICKNAME                  = 'TestClient';
  protected static $TRANSPORT                 = [
    'timeout' => 12,
  ];
  protected static $CHANNEL                   = [
    'id'   => 1,
    'name' => 'Default Channel'
  ];
  protected static $CLIENT                    = [
    'id'   => 1,
    'name' => 'serveradmin'
  ];
  
  protected function setUp() {
    parent::setUp();
    if (!(static::$PASSWORD = getenv('TS3_SERVERQUERY_ADMIN_PASSWORD'))) {
      $this->markTestSkipped(
        'Skipping integration tests. Required `TS3_SERVERQUERY_ADMIN_PASSWORD` environment variable empty or not set.'
      );
      throw new Exception(
        'Integration tests require `TS3_SERVERQUERY_ADMIN_PASSWORD` environment variable be set, but was either not set or empty.'
      );
    }
  }
  
  public function testVirtualServerKickClient() {
    // connect to local server, authenticate and spawn an object for the virtual server on port 9987
    /** @var \TeamSpeak3_Node_Server $ts3_VirtualServer */
    $ts3_VirtualServer = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    // kick the client with ID 123 from the server
    //$ts3_VirtualServer->clientKick(
    //  5,
    //  \TeamSpeak3::KICK_SERVER,
    //  "evil kick XD"
    //);
  
    // spawn an object for the client by unique identifier and do the kick
    //$ts3_VirtualServer->clientGetByUid("FPMPSC6MXqXq751dX7BKV0JniSo=")->kick(
    //  \TeamSpeak3::KICK_SERVER,
    //  "evil kick XD"
    //);
  
    // spawn an object for the client by current nickname and do the kick
    $ts3_VirtualServer->clientGetByName("AudioBot")->kick(
      \TeamSpeak3::KICK_SERVER,
      "evil kick XD"
    );
  }
}