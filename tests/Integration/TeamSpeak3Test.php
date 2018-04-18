<?php

namespace Tests\System;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;
use PHPUnit\Framework\Exception;

require_once 'lib/TeamSpeak3/TeamSpeak3.php';

/**
 * Class TeamSpeak3Test
 *
 * Constants: S_... - Sample response for a command (raw formatting) from
 * server. E_... - Expected (parsed) response (i.e. from _Helper_String) from
 * framework
 *
 * @package Tests\Unit\Adapter\ServerQuery
 */
class TeamSpeak3Test extends TestCase
{
  protected static $URI_SCHEME  = 'serverquery';
  protected static $USERNAME    = 'serveradmin';
  protected static $PASSWORD    = '';
  protected static $HOSTNAME    = '127.0.0.1';
  protected static $PORT        = [
    'server_query' => '10011',
    'voice'        => '9987'
  ];
  protected static $SERVER_ID   = 1;
  protected static $SERVER_NAME = 'TeamSpeak ]I[ Server';
  protected static $NICKNAME    = 'TestClient';
  protected static $TRANSPORT     = [
    'timeout' => 12,
  ];
  protected static $CHANNEL     = [
    'id' => 1,
    'name' => 'Default Channel'
  ];
  protected static $CLIENT     = [
    'id' => 1,
    'name' => 'serveradmin'
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
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
  }
  
  public function testConstructorExceptionBadHost() {
    $this->expectException(\TeamSpeak3_Transport_Exception::class);
    $this->expectExceptionMessageRegExp('/getaddrinfo failed/');
    
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://TestBadHost' 
      . ':' . static::$PORT['server_query'] . '/');
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
  }
  
  public function testConstructorExceptionBadPort() {
    $this->expectException(\TeamSpeak3_Transport_Exception::class);
    $this->expectExceptionMessage('Connection refused');
    
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://' 
      . static::$HOSTNAME . ':12345/');
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
  }
  
  public function testConstructorVirtualServer() {
    $ts3server = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']);
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class, $ts3server);
  }
  
  public function testConstructorVirtualServerLogin() {
    $ts3server = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query'] 
      . '/?server_port=' . static::$PORT['voice']);
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class, $ts3server);
  }
  
  // Test ServerQuery factory URI query variables
  
  public function testConstructorHostTimeout() {
    $ts3host = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?timeout=' . static::$TRANSPORT['timeout']
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    $this->assertSame(static::$TRANSPORT['timeout'],$ts3host->getParent()->getTransport()->getConfig('timeout'));
  }
  
  public function testConstructorHostBlocking() {
    $ts3host = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?blocking=1'
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    $this->assertSame(
      1,
      $ts3host->getParent()->getTransport()->getConfig('blocking')
    );
  }
  
  // @todo: Fix SSL error when using `tls` option. Marking incomplete for now.
  // SSL operation failed with code 1. OpenSSL Error messages:
  // error:140770FC:SSL routines:SSL23_GET_SERVER_HELLO:unknown protocol
  public function testConstructorHostTls() {
    $this->markTestIncomplete();
    $ts3host = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?tls=1'
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    $this->assertSame(
      1,
      $ts3host->getParent()->getTransport()->getConfig('tls')
    );
  }
  
  public function testConstructorHostQueryName() {
    /** @var \TeamSpeak3_Node_Host $ts3host */
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?nickname=' . static::$NICKNAME);
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    /** @var \TeamSpeak3_Helper_String $actualNickname */
    $actualNickname = $ts3host->getPredefinedQueryName();
    
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class, $actualNickname);
    $this->assertSame(static::$NICKNAME, (string) $actualNickname);
  }
  
  public function testConstructorVirtualServerQueryServerId() {
    /** @var \TeamSpeak3_Node_server $ts3server */
    $ts3server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_id=' . static::$SERVER_ID);
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class, $ts3server);
    
    $this->assertSame(static::$SERVER_ID, $ts3server->getId());
  }
  
  public function testConstructorVirtualServerQueryServerUid() {
    /** @var \TeamSpeak3_Node_server $ts3server */
    $ts3server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_id=' . static::$SERVER_ID);
    $serverUniqueId = (string) $ts3server->getProperty('virtualserver_unique_identifier');
  
    // @todo Need independent way to get UID rather than `server_port` above.
    /** @var \TeamSpeak3_Node_server $ts3server */
    $ts3server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_uid=' . $serverUniqueId);
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class, $ts3server);
    
    $this->assertSame($serverUniqueId,
      (string) $ts3server->getProperty('virtualserver_unique_identifier'));
  }
  
  public function testConstructorHostQueryServerPort() {
    /** @var \TeamSpeak3_Node_Server $ts3server */
    $ts3server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']);
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class, $ts3server);
    
    $this->assertSame((int) static::$PORT['voice'],
      $ts3server->getParent()->serverSelectedPort());
  }
  
  public function testConstructorHostQueryServerName() {
    /** @var \TeamSpeak3_Node_server $ts3server */
    $ts3server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_name=' . rawurlencode(static::$SERVER_NAME));
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Server::class, $ts3server);
    
    $this->assertSame(static::$SERVER_NAME, (string) $ts3server);
  }
  
  public function testConstructorVirtualServerChannelId() {
    /** @var \TeamSpeak3_Node_Channel $ts3Channel */
    $ts3Channel = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query'] 
      . '/?server_port=' . static::$PORT['voice']
      . '&channel_id=' . static::$CHANNEL['id']
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Channel::class, $ts3Channel);
    
    // `getId()` returns `nodeId` and is mapped to `cid` on instantiation.
    $this->assertSame(static::$CHANNEL['id'], $ts3Channel->getId());
  }
  
  public function testConstructorVirtualServerChannelName() {
    /** @var \TeamSpeak3_Node_Channel $ts3Channel */
    $ts3Channel = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
      . '&channel_name=' . rawurlencode(static::$CHANNEL['name'])
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Channel::class, $ts3Channel);
    
    $this->assertSame(static::$CHANNEL['name'],
      (string) $ts3Channel->getProperty('channel_name'));
  }
  
  public function testConstructorVirtualServerClientId() {
    // Connect test query client first
    /** @var \TeamSpeak3_Node_Server $ts3ClientTest */
    $ts3ClientTest = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    // todo: Find where`\TeamSpeak3_Node_Server::whoamiGet()` comes from.
    // `whoamiGet()` is not defined in main or parent class, why does this work?
    $clientID = $ts3ClientTest->whoamiGet('client_id');
    
    /** @var \TeamSpeak3_Node_Client $ts3Client */
    $ts3ClientAdminSelect = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
      . '&client_id=' . $clientID
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Client::class,
      $ts3ClientAdminSelect);
    
    // `getId()` returns `nodeId` and is mapped to `clid` on instantiation.
    $this->assertSame($clientID, $ts3ClientAdminSelect->getId());
  }
  
  public function testConstructorVirtualServerClientUid() {
    // Connect test query client first
    /** @var \TeamSpeak3_Node_Server $ts3ClientTest */
    $ts3ClientTest = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    /** @var \TeamSpeak3_Node_Client $ts3Client */
    $ts3ClientAdminSelect = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
      . '&client_id=' . $ts3ClientTest->whoamiGet('client_id')
    );
    
    $this->assertInstanceOf(
      \TeamSpeak3_Node_Client::class,
      $ts3ClientAdminSelect
    );
  
    $this->assertInstanceOf(
      \TeamSpeak3_Helper_String::class,
      $ts3ClientAdminSelect->getProperty('client_unique_identifier')
    );
  
    // @todo: Re-enable this once below behavior fixed.
    //$this->assertEmpty(
    //  (string) $ts3ClientAdminSelect->getProperty('client_unique_identifier')
    //);
    
    // This is probably a bug.
    // When testing from (telnet) ServerQuery interface:
    // $ telnet localhost 10011
    // > use 1
    // > whoami
    // virtualserver_status = online virtualserver_id = 1 virtualserver_unique_identifier = K2Xk7tWL\/bHPPAAwESkv90jQnHE = virtualserver_port = 9987 client_id = 14 client_channel_id = 1 client_nickname = Unknown\sfrom\s172.17.0.1:52722 client_database_id = 0 client_login_name client_unique_identifier client_origin_server_id = 0
    // error id = 0 msg = ok
    // As can be seen, `client_unique_identifier` is not set.
    // So `getProperty()` should probably return null or empty string at least.
    $this->assertSame(
      'ServerQuery',
      (string) $ts3ClientAdminSelect->getProperty('client_unique_identifier')
    );
  }
  
  public function testConstructorVirtualServerClientName() {
    // Connect test query client first
    /** @var \TeamSpeak3_Node_Server $ts3ClientTest */
    $ts3ClientTest = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    // todo: Find where`\TeamSpeak3_Node_Server::whoamiGet()` comes from.
    // `whoamiGet()` is not defined in main or parent class, why does this work?
    $clientNickname = (string) $ts3ClientTest->whoamiGet('client_nickname');
  
    /** @var \TeamSpeak3_Node_Client $ts3Client */
    $ts3ClientAdminSelect = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
      . '&client_name=' . rawurlencode($clientNickname)
    );
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Client::class,
      $ts3ClientAdminSelect);
    
    // `getId()` returns `nodeId` and is mapped to `cid` on instantiation.
    $this->assertSame(
      $clientNickname,
      (string) $ts3ClientAdminSelect->getProperty('client_nickname')
    );
  }
  
  // Test ServerQuery factory URI fragments
  
  public function testConstructorHostFragmentOfflineAsVirtual() {
    /** @var \TeamSpeak3_Node_Host $ts3host */
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/#use_offline_as_virtual');
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    $this->assertTrue($ts3host->getUseOfflineAsVirtual());
  }
  
  public function testConstructorHostFragmentClientsBeforeChannels() {
    /** @var \TeamSpeak3_Node_Host $ts3host */
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/#clients_before_channels');
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    $this->assertTrue($ts3host->getLoadClientlistFirst());
  }
  
  public function testConstructorHostFragmentNoQueryClients() {
    /** @var \TeamSpeak3_Node_Host $ts3host */
    $ts3host = \TeamSpeak3::factory(static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/#no_query_clients');
    
    $this->assertInstanceOf(\TeamSpeak3_Node_Host::class, $ts3host);
    
    $this->assertTrue($ts3host->getExcludeQueryClients());
  }
}