<?php
/**
 * *****************************************************************************
 * Copyright (c) 2004, 2017 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 * Denis Roy (Eclipse Foundation)- initial API and implementation
 * *****************************************************************************
 */
require_once ("/home/data/httpd/eclipse-php-classes/system/ldapconnection.class.php");
/**
 * This class is deprecated
 *
 * It's here because some files on our sites expect this file
 * to exist but it's mostly for LDAPPerson->redirectIfNotLoggedIn().
 *
 * $App->useSession(TRUE); should probably be used instead.

 * @@deprecated
 */
class LDAPPerson {

  var $uid = "";

  var $cn = "";

  var $givenName = "";

  var $sn = "";

  var $mail = "";

  /**
   * Get UID
   *
   * @return string|unknown
   *
   * @deprecated
   */
  function getUid() {
    return $this->uid;
  }

  /**
   * Get CN
   *
   * @return string
   * @deprecated
   */
  function getCn() {
    return $this->cn;
  }

  /**
   * Get Given Name
   *
   * @return string
   * @deprecated
   */
  function getGivenName() {
    return $this->givenName;
  }

  /**
   * Get SN
   *
   * @return string
   * @deprecated
   */
  function getSn() {
    return $this->Sn;
  }

  /**
   * Get mail
   *
   * @return string
   * @deprecated
   */
  function getMail() {
    return $this->Mail;
  }

  /**
   * Get DN
   *
   * @return string
   * @deprecated
   */
  function getDN() {
    return "uid=" . $this->uid . ",ou=people,dc=eclipse,dc=org";
  }

  /**
   * Set UID
   * @param unknown $_Uid
   * @deprecated
   */
  function setUid($_Uid) {
    $this->uid = $_Uid;
  }

  /**
   * Sign in
   * @param unknown $_uid
   * @param unknown $_password
   *
   * @deprecated
   * @return boolean
   */
  function signIn($_uid, $_password) {
    return FALSE;
  }

  /**
   * Sign out
   *
   * @deprecated
   * @return boolean
   */
  function signOut() {
    return FALSE;
  }

  /**
   * Change password
   *
   * @param unknown $_Uid
   * @param unknown $_currPassword
   * @param unknown $_newPassword
   * @param unknown $_generate
   *
   * @deprecated
   * @return number
   */
  function changePassword($_Uid, $_currPassword, $_newPassword, $_generate) {
    return 0;
  }

  /**
   * Redirect if Not logged in
   *
   * @deprecated
   * @return LDAPPerson|unknown
   */
  function redirectIfNotLoggedIn() {
    // Check for a portal login
    if (isset($_SESSION['portaluser'])) {
      $__LDAPPerson = new LDAPPerson();
      $__LDAPPerson->setUid($_SESSION['portaluser']);
    }

    // Check for a Committer Tools login
    if (isset($_SESSION['dude'])) {
      $__LDAPPerson = $_SESSION['dude'];
    }

    if (isset($__LDAPPerson)) {
      if ($__LDAPPerson->getUid() == "") {
        header("Location: ../login/index.php");
        exit();
      }
    }
    else {
      header("Location: ../login/index.php");
      // added Mward for Ngervais 012312
      exit();
    }

    return $__LDAPPerson;
  }

  /**
   * Get Password Exipiry days
   *
   * @deprecated
   * @return number
   */
  function getPasswordExpiryDays() {
    $userDN = $this->getDN();
    $LDAPConnection = new LDAPConnection();

    $shadowLastChange = $LDAPConnection->getLDAPAttribute($userDN, "shadowLastChange");
    $shadowMax = $LDAPConnection->getLDAPAttribute($userDN, "shadowMax");

    $daysUsed = floor(time() / 60 / 60 / 24) - $shadowLastChange;
    $daysLeft = $shadowMax - $daysUsed;

    return $daysLeft;
  }

}
