<?php

namespace Tests\Unit\Adapter\ServerQuery;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;

require_once 'lib/TeamSpeak3/Adapter/ServerQuery/Reply.php';

/**
 * Class ReplyTest
 * 
 * Constants: S_... - Sample response for a command (raw formatting) from server.
 *            E_... - Expected (parsed) response (i.e. from _Helper_String) from framework
 *
 * @package Tests\Unit\Adapter\ServerQuery
 */
class ReplyTest extends TestCase
{
  private static $S_WELCOME_L0 = 'TS3';

  private static $S_WELCOME_L1 = 'Welcome to the TeamSpeak 3 ServerQuery interface, type "help" for a list of commands and "help <command>" for information on a specific command.';
  
  private static $S_ERROR_OK = 'error id=0 msg=ok';
  
  // Default virtual server
  // Response from `serverlist` command on default virtual server
  private static $S_SERVERLIST = 'virtualserver_id=1 virtualserver_port=9987 virtualserver_status=online virtualserver_clientsonline=1 virtualserver_queryclientsonline=1 virtualserver_maxclients=32 virtualserver_uptime=5470 virtualserver_name=TeamSpeak\s]I[\sServer virtualserver_autostart=1 virtualserver_machine_id';
  
  // Expected string output after parsing for `serverlist` command.
  private static $E_SERVERLIST = 'virtualserver_id=1 virtualserver_port=9987 virtualserver_status=online virtualserver_clientsonline=1 virtualserver_queryclientsonline=1 virtualserver_maxclients=32 virtualserver_uptime=5470 virtualserver_name=TeamSpeak ]I[ Server virtualserver_autostart=1 virtualserver_machine_id';
  
  private static $E_SERVERLIST_ARRAY = [
    'virtualserver_id' => 1,
    'virtualserver_port' => 9987,
    'virtualserver_status' => 'online',
    'virtualserver_clientsonline' => 1,
    'virtualserver_queryclientsonline' => 1,
    'virtualserver_maxclients' => 32,
    'virtualserver_uptime' => 5470,
    'virtualserver_name' => 'TeamSpeak ]I[ Server',
    'virtualserver_autostart' => 1,
    'virtualserver_machine_id' => null
  ];
  
  // 3 users connected
  private static $S_CLIENTLIST = 'clid=1 cid=1 client_database_id=1 client_nickname=serveradmin\sfrom\s[::1]:59642 client_type=1|clid=2 cid=1 client_database_id=3 client_nickname=Unknown\sfrom\s[::1]:59762 client_type=1|clid=3 cid=1 client_database_id=3 client_nickname=Unknown\sfrom\s[::1]:59766 client_type=1';
  
  private static $S_CHANNELLIST = 'cid=1 pid=0 channel_order=0 channel_name=Default\sChannel total_clients=3 channel_needed_subscribe_power=0|cid=2 pid=1 channel_order=0 channel_name=Test\sParent\s1 total_clients=0 channel_needed_subscribe_power=0|cid=3 pid=1 channel_order=2 channel_name=Test\sParent\s2 total_clients=0 channel_needed_subscribe_power=0|cid=5 pid=3 channel_order=0 channel_name=P2\s-\sSub\s1 total_clients=0 channel_needed_subscribe_power=0|cid=6 pid=3 channel_order=5 channel_name=P2\s-\sSub\s2 total_clients=0 channel_needed_subscribe_power=0|cid=4 pid=1 channel_order=3 channel_name=Test\sParent\s3 total_clients=0 channel_needed_subscribe_power=0|cid=7 pid=4 channel_order=0 channel_name=P3\s-\sSub\s1 total_clients=0 channel_needed_subscribe_power=0|cid=8 pid=4 channel_order=7 channel_name=P3\s-\sSub\s2 total_clients=0 channel_needed_subscribe_power=0';
  
  public function testConstructor() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $this->assertInstanceOf(\TeamSpeak3_Adapter_ServerQuery_Reply::class,
      $reply);
  }
  
  public function testToString() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $replyString = $reply->toString();
    
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class,
      $replyString);
    
    $replyString = (string) $replyString;
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_STRING,
      $replyString);
    $this->assertSame(static::$E_SERVERLIST, $replyString);
  }
  
  public function testToLines() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $replyLines = $reply->toLines();
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $replyLines);
    $this->assertCount(1, $replyLines);
    
    $reply = array_pop($replyLines);
    
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class, $reply);
    $this->assertSame(static::$E_SERVERLIST, (string) $reply);
  }
  public function testToTable() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $replyTable = $reply->toTable();
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $replyTable);
    $this->assertCount(1, $replyTable);
    
    $reply = array_pop($replyTable);
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $reply);
    $this->assertCount(10, $reply);
  
    while ($replyPiece = \array_pop($reply)) {
      $this->assertInstanceOf(\TeamSpeak3_Helper_String::class,
        $replyPiece);
      
      $replyPiece = explode('=', $replyPiece, 2);
      $this->assertArrayHasKey($replyPiece[0], static::$E_SERVERLIST_ARRAY);
      
      // Basic check to see if we have `key=val` pair or only `key` flag
      if (\count($replyPiece) > 1) {
        $this->assertSame(
          (string) static::$E_SERVERLIST_ARRAY[$replyPiece[0]],
          $replyPiece[1]
        );
      } else {
        $this->assertNull(static::$E_SERVERLIST_ARRAY[$replyPiece[0]]);
      }
    }
  }
  public function testToArray() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST), 
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $replyArray = $reply->toArray();
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $replyArray);
    
    $reply = array_pop($replyArray);
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $reply);
    $this->assertCount(10, $reply);
    
    // Individually check these since next step will simplify arrays for testing
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class, 
      $reply['virtualserver_status']);
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class,
      $reply['virtualserver_name']);
    
    $reply['virtualserver_status'] = (string) $reply['virtualserver_status'];
    $reply['virtualserver_name'] = (string) $reply['virtualserver_name'];
    
    $this->assertArraySubset($reply, static::$E_SERVERLIST_ARRAY, true);
    
  }
  public function testToAssocArray() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $reply = $reply->toAssocArray('virtualserver_id');
    
    $this->assertArrayHasKey(1, $reply);
    
    $reply = $reply[1];
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $reply);
    
    $reply['virtualserver_status'] = (string) $reply['virtualserver_status'];
    $reply['virtualserver_name'] = (string) $reply['virtualserver_name'];
    
    $this->assertSame(static::$E_SERVERLIST_ARRAY, $reply);
  }
  public function testToList() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $reply = $reply->toList();
  
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $reply);
    
    $reply['virtualserver_status'] = (string) $reply['virtualserver_status'];
    $reply['virtualserver_name'] = (string) $reply['virtualserver_name'];
    
    $this->assertSame(static::$E_SERVERLIST_ARRAY, $reply);
  }
  public function testToObjectArray() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $replyObject = $reply->toObjectArray();
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $replyObject);
    $this->assertCount(1, $replyObject);
    $this->assertArrayHasKey(0, $replyObject);
    
    $reply = $replyObject[0];
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_OBJECT, $reply);
    
    // Individually check these since next step will simplify arrays for testing
    $this->assertAttributeInstanceOf(\TeamSpeak3_Helper_String::class, 
      'virtualserver_status', $reply);
    $this->assertAttributeInstanceOf(\TeamSpeak3_Helper_String::class, 
      'virtualserver_name', $reply);
    
    $reply->virtualserver_status = (string) $reply->virtualserver_status;
    $reply->virtualserver_name = (string) $reply->virtualserver_name;
    
    // Note: This only works for expected array depth 1 (i.e. non-recursive)
    $this->assertEquals((object) static::$E_SERVERLIST_ARRAY, $reply);
  }
  public function testGetCommandString() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $command = $reply->getCommandString();
    
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class, $command);
    $this->assertEmpty((string) $command);
  }
  public function testGetNotifyEvents() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    $notifyEvents = $reply->getNotifyEvents();
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $notifyEvents);
    $this->assertEmpty($notifyEvents);
  }
  public function testGetErrorProperty() {
    $reply = new \TeamSpeak3_Adapter_ServerQuery_Reply([
      new \TeamSpeak3_Helper_String(static::$S_SERVERLIST),
      new \TeamSpeak3_Helper_String(static::$S_ERROR_OK)
    ]);
    
    $errorPropertyId = $reply->getErrorProperty('id');
    
    $this->assertSame(0, $errorPropertyId);
    
    $errorPropertyMsg = $reply->getErrorProperty('msg');
    
    $this->assertInstanceOf(\TeamSpeak3_Helper_String::class,
      $errorPropertyMsg);
    
    $this->assertSame('ok', (string) $errorPropertyMsg);
  }
}