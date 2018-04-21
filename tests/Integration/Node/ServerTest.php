<?php

namespace Tests\Integration\Node;

use \PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;
use \PHPUnit\Framework\Exception;

require_once 'lib/TeamSpeak3/TeamSpeak3.php';

/**
 * Class ServerTest
 * 
 * Most of these depend on \TeamSpeak3 and a running TeamSpeak3 server.
 * @todo: Convert tests to unit level, removing dependency through mocking
 *
 * Constants:
 *  S_... - Sample response for a command (raw formatting) from server
 *  E_... - Expected (parsed) response (i.e. from _Helper_String) from framework
 *
 * @package Tests\Unit\Adapter\ServerQuery
 */
class ServerTest extends TestCase
{
  protected static $URI_SCHEME  = 'serverquery';
  protected static $USERNAME    = 'serveradmin';
  protected static $PASSWORD    = ''; // Set through env var, see `setUp()`.
  protected static $HOSTNAME    = '127.0.0.1';
  protected static $PORT        = [
    'server_query' => '10011',
    'voice'        => '9987'
  ];
  protected static $SERVER_ID   = 1;
  protected static $SERVER_NAME = 'TeamSpeak ]I[ Server';
  protected static $SERVER_DEFAULT_CHANNEL_ID = 1;
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
  
  public function testChannelListReset() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    $this->assertNull($ts3Server->channelListReset());
  
  }
  
  // @todo: Review `channelGetDefault()`, throws when no default is set?
  public function testChannelGetDefault() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    /** @var \TeamSpeak3_Node_Channel $channelDefault */
    $channelDefault = $ts3Server->channelGetDefault();
    
    $this->assertInstanceOf(
      \TeamSpeak3_Node_Channel::class,
      $channelDefault
    );
    
    $this->assertSame(
      static::$SERVER_DEFAULT_CHANNEL_ID,
      $channelDefault->getId()
    );
  }
  
  // @todo: Implement
  public function testChannelCreate() {
  
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $channelID = $ts3Server->channelCreate();
  
    $this->assertInternalType(PHPUnit_IsType::TYPE_INT, $channelID);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  public function testChannelDelete() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull($ts3Server->channelDelete());
  }
  
  // @todo: Implement
  public function testChannelMove() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull($ts3Server->channelMove());
  }
  
  public function testChannelIsSpacer() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $channelDefault = $ts3Server->channelGetDefault();
    
    $this->assertFalse($ts3Server->channelIsSpacer($channelDefault));
  }
  
  // @todo: Implement
  public function testChannelSpacerCreate() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $channelID = $ts3Server->channelSpacerCreate();
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_INT, $channelID);
    
    // Check all align, types, etc:
    // - \TeamSpeak3::SPACER_ALIGN_*
    // - \TeamSpeak3::SPACER_*
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  public function testChannelSpacerGetTypeExceptionChannelNotSpacer() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('invalid channel flags');
    $this->expectExceptionCode(0x307);
    
    $spacerType = $ts3Server->channelSpacerGetType(
      $ts3Server->channelGetDefault()->getId()
    );
    
    // Check all types
    // - \TeamSpeak3::SPACER_*
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  public function testChannelSpacerGetAlignExceptionChannelNotSpacer() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('invalid channel flags');
    $this->expectExceptionCode(0x307);
    
    $spacerAlign = $ts3Server->channelSpacerGetAlign(
      $ts3Server->channelGetDefault()->getId()
    );
    
    // Check all align
    // - \TeamSpeak3::SPACER_ALIGN_*
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // Check Channel Permissions
  
  public function testChannelPermList() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $permList = $ts3Server->channelPermList(
      $ts3Server->channelGetDefault()->getId()
    );
  
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $permList);
    
    // @todo: Define "default channel's perm list" for comparison.
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  public function testChannelPermAssign() {
  
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull($ts3Server->channelPermAssign(
      $ts3Server->channelGetDefault()->getId(),
      'permid',
      'permvalue'
    ));
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  public function testChannelPermRemove() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull(
      $ts3Server->channelPermRemove(
        $ts3Server->channelGetDefault()->getId(),
        'permid'
      )
    );
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // Check Channel Client Permissions
  
  // @todo: Implement
  public function testChannelClientPermList() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $permList = $ts3Server->channelClientPermList(
      $ts3Server->channelGetDefault()->getId(),
      $ts3Server->whoamiGet('client_id')
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $permList);
    
    // @todo: Define "default channel's perm list" for comparison.
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  public function testChannelClientPermListExceptionInvalidClientId() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('invalid clientID');
    $this->expectExceptionCode(0x307);
    
    $permList = $ts3Server->channelClientPermList(
      $ts3Server->channelGetDefault()->getId(),
      $ts3Server->whoamiGet('client_id')
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $permList);
    
    // @todo: Define "default channel's perm list" for comparison.
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  public function testChannelClientPermAssign() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull(
      $ts3Server->channelClientPermAssign(
        $ts3Server->channelGetDefault()->getId(),
        $ts3Server->whoamiGet('client_id'),
        'permid',
        'permvalue'
      )
    );
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  } // @todo: Implement
  
  public function testChannelClientPermRemove() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull(
      $ts3Server->channelClientPermRemove(
        $ts3Server->channelGetDefault()->getId(),
        $ts3Server->whoamiGet('client_id'),
        'permid'
      )
    );
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // Check Channel Files
  
  // @todo: Implement
  public function testChannelFileList() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $fileList = $ts3Server->channelFileList(
      $ts3Server->channelGetDefault()->getId()
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileList);
    
    // @todo: Define "default channel's perm list" for comparison.
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  public function testChannelFileListExceptionDatabaseEmptyResult() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('database empty result set');
    //$this->expectExceptionCode(0x307); // Should be error code 1281
    
    $fileList = $ts3Server->channelFileList(
      $ts3Server->channelGetDefault()->getId()
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileList);
    
    // @todo: Define "default channel's perm list" for comparison.
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  // @todo: `channelFileInfo()` should require path, not use default
  public function testChannelFileInfo() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $fileInfo = $ts3Server->channelFileInfo(
      $ts3Server->channelGetDefault()->getId()
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  // @todo: `channelFileInfo()` should require path, not use default
  public function testChannelFileInfoExceptionFileNotExists() {
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('invalid file name');
    //$this->expectExceptionCode(0x307); // Should be error code 2048
    
    $fileInfo = $ts3Server->channelFileInfo(
      $ts3Server->channelGetDefault()->getId()
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  // @todo: `channelFileInfo()` require `oldname`, `newname` (without default)
  public function testChannelFileRename() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $fileInfo = $ts3Server->channelFileRename(
      $ts3Server->channelGetDefault()->getId()
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  public function testChannelFileRenameExceptionFileExists() {
    
    //$this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('file already exists');
    //$this->expectExceptionCode(0x307); // Should be error code 2050
    
    $fileInfo = $ts3Server->channelFileRename(
      $ts3Server->channelGetDefault()->getId(),
      '',
      '/ThisFileDoesNotExist.txt'
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Exception needs non-generic message
  public function testChannelFileRenameExceptionFileNotExists() {
    
    //$this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('file input/output error');
    //$this->expectExceptionCode(0x307); // Should be error code 2052
    
    $fileInfo = $ts3Server->channelFileRename(
      $ts3Server->channelGetDefault()->getId(),
      '',
      '/ThisFileDoesNotExist.txt',
      '/ThisWillNotBeCreated.txt'
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: `channelFileRename` (reply error), missing parameter exception code?
  public function testChannelFileRenameExceptionMissingParamChannelPassword() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('parameter not found');
    //$this->expectExceptionCode(0x307); // No error code set.
    
    $fileInfo = $ts3Server->channelFileRename(
      $ts3Server->channelGetDefault()->getId(),
      null,
      '/ThisFileDoesNotExist.txt'
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  // @todo: Implement
  // @todo: `channelFileDelete()` require `name` (without default)
  public function testChannelFileDelete() {
    
    $this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
    
    $this->assertNull($ts3Server->channelFileDelete(
      $ts3Server->channelGetDefault()->getId(),
      '',
      '/testChannelFileDelete.deleteme.txt'
    ));
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
  public function testChannelFileDeleteException() {
    
    //$this->markTestIncomplete();
    
    /** @var \TeamSpeak3_Node_Server $ts3Server */
    $ts3Server = \TeamSpeak3::factory(
      static::$URI_SCHEME . '://'
      . static::$USERNAME . ':' . static::$PASSWORD . '@'
      . static::$HOSTNAME . ':' . static::$PORT['server_query']
      . '/?server_port=' . static::$PORT['voice']
    );
  
    $this->expectException(\TeamSpeak3_Adapter_ServerQuery_Exception::class);
    $this->expectExceptionMessage('invalid file path');
    //$this->expectExceptionCode(0x307); // This should be error code 2054
    
    $this->assertNull(
      $ts3Server->channelFileDelete(
        $ts3Server->channelGetDefault()->getId(),
        '',
        '/ThisFileDoesNotExist.txt'
      )
    );
    
    $this->assertInternalType(PHPUnit_IsType::TYPE_ARRAY, $fileInfo);
    
    //$this->assertSame(
    //  static::$SERVER_DEFAULT_CHANNEL_ID,
    //  $channelDefault->getId()
    //);
  }
  
}