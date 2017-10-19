<?php
/**
 * *****************************************************************************
 * Copyright (c) 2005, 2017 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 * Denis Roy (Eclipse Foundation)- initial API and implementation
 * Christopher Guindon (Eclipse Foundation) - Adding the concept of
 * DBConnectionBase.
 * *****************************************************************************
 */

class DBConnectionBase {

  /**
   * The MySQL server.
   *
   * @var string
   */
  protected $MysqlUrl = "";

  /**
   * The MySQL username.
   *
   * @var string
   */
  protected $MysqlUser = "";

  /**
   * The MySQL password.
   *
   * @var string
   */
  protected $MysqlPassword = "";

  /**
   * The MySQL database.
   *
   * @var string
   */
  protected $MysqlDatabase = "";

  /**
   * Use MySQLi
   *
   * @var bool
   */
  protected $Mysqli = NULL;

  /**
   * Mysql link identifier
   */
  static $dbh = array();

  /**
   * Valid caller paths
   *
   * @var array
   */
  protected $validPaths = array();

  /**
   * Constructor
   */
  public function __construct() {
    // Apply settings that applies to all db classes
    $this->setMysqlUrl("mariadb");
    $this->setMysqlUser("admin");
    $this->setMysqlPassword("my-secret-pw");
    $this->setValidPaths("/localsite");
    $this->setValidPaths("/home/data/httpd");
  }

  /**
   * Connect to MySQL database
   *
   * @return unknown
   */
  public function connect() {

    $this->validateCaller();

    $dbh = mysql_connect($this->getMysqlUrl(), $this->getMysqlUser(), $this->getMysqlPassword());
    if (!$dbh) {
      echo ("<p>Unable to connect to the database server at this time (" . get_class($this) . ").</p>");
      die();
    }

    $db_selected = mysql_select_db($this->getMysqlDatabase(), $dbh);
    if (!$db_selected) {
      die("Error database not found:" . $this->getMysqlDatabase() . ": " . mysql_error());
    }

    self::setDbh($dbh);
    return $dbh;
  }

  /**
   * Close MySQL connection
   */
  public function disconnect() {
    $dbh = self::getDbh();
    if (!empty($dbh) && gettype($dbh) === "resource") {
      mysql_close($dbh);
      self::setDbh(NULL);
    }
  }

  /**
   * Get Mysql Link identifier
   *
   * @return string
   */
  static public function getDbh() {
    if (!empty(self::$dbh[get_called_class()])) {
      return self::$dbh[get_called_class()];
    }
    return NULL;
  }

  /**
   * Validate database caller
   *
   * @return boolean or DIE!!
   */
   public function validateCaller() {
    $debug_backtrace = debug_backtrace();
    $valid_caller = FALSE;
    $valid_path = $this->getValidPaths();

    foreach ($debug_backtrace as $trace) {
      if (empty($trace['file'])) {
        continue;
      }

      $caller = $trace['file'];
      foreach ($valid_path as $path) {
        if (strstr($caller, $path)) {
          $valid_caller = TRUE;
          break;
        }
      }

      // stop the loop after finding a valid caller
      if ($valid_caller) {
        break;
      }
    }

    if (!$valid_caller) {
      echo "Execution from Invalid Path (" . get_class($this) . "). This attempt has been logged. Please contact webmaster@eclipse.org";
      exit();
    }

    return TRUE;
  }

  /**
   * Set Mysql Link identifier
   *
   * @return string
   */
  static protected function setDbh($link_identifier = "") {
    self::$dbh[get_called_class()] = $link_identifier;
  }

  /**
   * Get MysqlDatabase
   */
  protected function getMysqlDatabase() {
    return $this->MysqlDatabase;
  }

  /**
   * Set MysqlDatabase
   *
   * @param string $database
   */
  protected function setMysqlDatabase($database = "") {
    return $this->MysqlDatabase = $database;
  }

  /**
   * Get MysqlPassword
   */
  protected function getMysqlPassword() {
    return $this->MysqlPassword;
  }

  /**
   * Set MysqlPassword
   *
   * @param string $password
   */
  protected function setMysqlPassword($password = "") {
    $this->MysqlPassword = $password;
  }

  /**
   * Get MysqlUrl
   *
   * @return string $MysqlUrl
   */
  protected function getMysqlUrl() {
    return $this->MysqlUrl;
  }

  /**
   * Set MysqlUrl
   *
   * @param string $url
   */
  protected function setMysqlUrl($url = "") {
    $this->MysqlUrl = $url;
  }

  /**
   * Get MysqlUser
   */
  protected function getMysqlUser() {
    return $this->MysqlUser;
  }

  /**
   * Set MysqlUser
   *
   * @param string $user
   */
  protected function setMysqlUser($user = "") {
    $this->MysqlUser = $user;
  }


  /**
   * Set validPaths
   */
  protected function getValidPaths() {
    return $this->validPaths;
  }

  /**
   * Set validPaths
   *
   * @param string $path
   */
  protected function setValidPaths($path = "") {
    $this->validPaths[] = $path;
  }

}
