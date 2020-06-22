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

if (!class_exists("EvtLog")) {
  require_once ("/home/data/httpd/eclipse-php-classes/system/evt_log.class.php");
}

class LDAPConnection {
  /**
   * Ldap URL
   *
   * The Eclipse foundation webdev team is using docker.for.mac.
   * for development. (Theses are not production settings).
   *
   * If you are using something else, you might need
   * to use something different
   *
   * @var string
   */
  var $LdapUrl = "docker.for.mac.localhost:3389";

  /**
   * LDAP default DN
   *
   * @var string
   */
  var $LdapDn = "dc=eclipse,dc=org";

  /**
   * Authenticate with LDAP
   *
   * @param unknown $_user_id
   * @param unknown $_password
   *
   * @return string|boolean
   */
  function authenticate($_user_id, $_password) {
    $ds = ldap_connect($this->LdapUrl);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    $EvtLog = new EvtLog();
    $EvtLog->setLogTable("__ldap_eclipse.org");
    $EvtLog->setPK1($_user_id);
    $EvtLog->setPK2($_SERVER['REMOTE_ADDR']);

    if ($ds) {
      // heck if this is an email address
      if ($_user_from_mail = $this->getUIDFromMail($_user_id, $ds)) {
        $_user_id = $_user_from_mail;
      }

      // Try community login first
      $bind_uid = "uid=" . $_user_id . ",ou=community," . $this->LdapDn;
      $r = @ldap_bind($ds, $bind_uid, $_password);
      if (!$r) {
        $bind_uid = "uid=" . $_user_id . ",ou=people," . $this->LdapDn;
        $r = @ldap_bind($ds, $bind_uid, $_password);
      }

      if ($r) {
        // User login successful
        $EvtLog->setLogAction("AUTH_SUCCESS");
        $EvtLog->insertModLog($_user_id);
        return $bind_uid;
      }
      else {
        $EvtLog->setLogAction("AUTH_FAILURE");
        $EvtLog->insertModLog($_user_id);
        return false;
      }
      ldap_close($ds);
    }
    else {
      $EvtLog->setLogAction("LDAP_FAILURE");
      $EvtLog->insertModLog();
      return false;
    }
  }

  /**
   * Performs a look up of a given email address
   *
   * @param unknown $_mail
   * @param unknown $_ds
   *
   * @return mixed|boolean
   */
  function getUIDFromMail($_mail, $_ds = NULL) {
    if ($_ds == NULL) {
      $ds = ldap_connect($this->LdapUrl);
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    }
    else {
      $ds = $_ds;
    }
    if ($ds) {
      if (preg_match("/@/", $_mail)) {
        // Perform a lookup.
        $sr = ldap_search($ds, $this->LdapDn, "(mail=$_mail)", array(
          "uid"
        ));
        $info = ldap_get_entries($ds, $sr);
        if ($info["count"] > 0) {
          return $info[0]["uid"][0];
        }
      }
    }
    return FALSE;
  }
  
  /**
   * Get the Group Name by Group ID
   * 
   * @param unknown $_gid
   * 
   * @return mixed|boolean
   */
  function getGroupByGid($_gid) { 
    if ($_ds == NULL) { 
      $ds = ldap_connect($this->LdapUrl); 
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); 
    }
    else {
      $ds = $_ds;
    }
    if ($ds) { 
      if(is_int($_gid)) { 
        # Perform a lookup.
        $sr = ldap_search($ds, "ou=group," . $this->LdapDn, "(gidNumber=$_gid)", array("cn")); 
        $info = ldap_get_entries($ds, $sr); 
        if($info["count"] > 0) { 
          return $info[0]["cn"][0];
        }
      }
    }
    return FALSE;
  }

  /**
   * Performs a look up of a given UID
   *
   * @param unknown $_uid
   * @param unknown $_ds
   * @return mixed|boolean
   */
  function getDNFromUID($_uid, $_ds = NULL) {
    if ($_ds == NULL) {
      $ds = ldap_connect($this->LdapUrl);
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    }
    else {
      $ds = $_ds;
    }
    if ($ds) {
      if (preg_match("/[a-zA-Z0-9]/", $_uid)) {
        // Perform a lookup.
        $sr = ldap_search($ds, $this->LdapDn, "(uid=$_uid)", array(
          "uid"
        ));
        $info = ldap_get_entries($ds, $sr);
        if ($info["count"] > 0) {
          return $info[0]["dn"];
        }
      }
    }
    return FALSE;
  }

  /**
   * Performs a look up of a given email address
   *
   * @param unknown $_mail
   * @param unknown $_ds
   * @return mixed|boolean
   */
  function getDNFromMail($_mail, $_ds = NULL) {
    if ($_ds == NULL) {
      $ds = ldap_connect($this->LdapUrl);
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    }
    else {
      $ds = $_ds;
    }
    if ($ds) {
      if (preg_match("/@/", $_mail)) {
        // Perform a lookup.
        $sr = ldap_search($ds, $this->LdapDn, "(mail=$_mail)", array(
          "uid"
        ));
        $info = ldap_get_entries($ds, $sr);
        if ($info["count"] > 0) {
          return $info[0]["dn"];
        }
      }
    }
    return FALSE;
  }

  /**
   * Performs a look up of a given email address
   *
   * @param unknown $_mail
   * @param unknown $_ds
   * @return mixed|boolean
   */
  function getGithubIDFromMail($_mail, $_ds = NULL) {
    if ($_ds == NULL) {
      $ds = ldap_connect($this->LdapUrl);
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    }
    else {
      $ds = $_ds;
    }
    if ($ds) {
      if (preg_match("/@/", $_mail)) {
        // Perform a lookup.
        $sr = ldap_search($ds, $this->LdapDn, "(mail=$_mail)", array(
          "employeeType"
        ));
        $info = ldap_get_entries($ds, $sr);
        if ($info["count"] > 0 and isset($info[0]["employeetype"])) {
          foreach ($info[0]["employeetype"] as $et) {
            // $et contains GITHIB:id or BITBUCKET:id
            $id = explode(":", $et);
            if ($id[0] == "GITHUB") {
              return $id[1];
              last;
            }
          }
        }
      }
    }
    return FALSE;
  }

  /**
   * Set githubid
   *
   * @param unknown $_user_id
   * @param unknown $_password
   * @param unknown $_newValue
   * @return string|number|boolean
   */
  function setGithubID($_user_id, $_password, $_newValue) {
    $debug = false;
    if ($debug) {
      // echo "Changing $_user_id $_password $_attribute to $_newValue<br>";
    }
    $ds = ldap_connect($this->LdapUrl);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    $rValue = "";

    if ($ds) {
      $bind_uid = "uid=" . $_user_id . "," . $this->LdapDn;
      if (preg_match("/^uid/", $_user_id)) {
        $bind_uid = $_user_id;
      }

      $r = @ldap_bind($ds, $bind_uid, $_password);
      $EvtLog = new EvtLog();
      $EvtLog->setLogTable("__ldap_eclipse.org");
      $EvtLog->setPK1("GITHUBID");
      $EvtLog->setPK2($_newValue);

      if ($r) {
        // User login successful
        $userdata["employeeType"] = "GITHUB:$_newValue";
        $result = ldap_mod_add($ds, $bind_uid, $userdata);

        if ($result) {
          $EvtLog->setLogAction("ADD_GITHUBID_SUCCESS");
          $EvtLog->insertModLog($_user_id);
        }
        else {
          $EvtLog->setLogAction("ADD_GITHUB_FAILURE");
          $EvtLog->insertModLog($_user_id);
        }
        $rValue = $result;
      }
      else {
        $EvtLog->setLogAction("ADD_GITHUB_AUTH_FAILURE");
        $EvtLog->insertModLog($_user_id);
        $rValue = -2;
      }
      ldap_close($ds);
    }

    return $rValue;
  }

  /**
   * Get LDAP attributes
   *
   * @param unknown $_dn
   * @param unknown $_attribute
   * @return string|mixed
   */
  function getLDAPAttribute($_dn, $_attribute) {
    $ds = ldap_connect($this->LdapUrl);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    $rValue = "";

    if ($ds) {
      $r = @ldap_bind($ds); // anonymous bind

      $_attribute = strtolower($_attribute);
      if ($r) {
        $sr = ldap_search($ds, $_dn, "(objectClass=*)");
        $info = ldap_get_entries($ds, $sr);

        $ii = 0;
        for ($i = 0; $ii < $info[$i]["count"]; $ii++) {
          $data = $info[$i][$ii];
          if ($data == $_attribute) {
            $rValue = $info[$i][$data][0];
          }
        }
      }
      ldap_close($ds);
    }

    return $rValue;
  }

  /**
   * Validate if email is available
   *
   * @param unknown $_mail
   * @return boolean
   */
  function checkEmailAvailable($_mail) {
    $ds = ldap_connect($this->LdapUrl);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    $rValue = false;

    if ($ds) {
      $ldap_connection = ldap_bind($ds); // anonymous bind
      if ($ldap_connection) {
        $sr = ldap_search($ds, $this->LdapDn, "(mail=$_mail)", array(
          "count"
        ));

        $info = ldap_get_entries($ds, $sr);
        $rValue = $info['count'] <= 0;
      }
    }
    return $rValue;
  }

  /**
   * Change an LDAP attribute
   *
   * @param unknown $_user_id
   * @param unknown $_password
   * @param unknown $_attribute
   * @param unknown $_newValue
   * @return string|number|boolean
   */
  function changeAttributeValue($_user_id, $_password, $_attribute, $_newValue) {
    $ds = ldap_connect($this->LdapUrl);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    $rValue = "";

    if ($ds) {
      $bind_uid = "uid=" . $_user_id . "," . $this->LdapDn;
      if (preg_match("/^uid/", $_user_id)) {
        $bind_uid = $_user_id;
      }

      $r = @ldap_bind($ds, $bind_uid, $_password);
      $EvtLog = new EvtLog();
      $EvtLog->setLogTable("__ldap_eclipse.org");
      $EvtLog->setPK1($_attribute);
      $EvtLog->setPK2($_newValue);

      if ($_attribute == "userPassword") {
        $EvtLog->setPK2($_SERVER['REMOTE_ADDR']);
      }

      if ($r) {
        // User login successful
        $userdata[$_attribute] = $_newValue;
        $result = ldap_mod_replace($ds, $bind_uid, $userdata);

        if ($result) {
          $EvtLog->setLogAction("CHG_ATTRIB_SUCCESS");
          $EvtLog->insertModLog($_user_id);
        }
        else {
          $EvtLog->setLogAction("CHG_ATTRIB_FAILURE");
          $EvtLog->insertModLog($_user_id);
        }
        $rValue = $result;
      }
      else {
        $EvtLog->setLogAction("CHG_ATTRIB_AUTH_FAILURE");
        $EvtLog->insertModLog($_user_id);
        $rValue = -2;
      }
      ldap_close($ds);
    }

    return $rValue;
  }

  /**
   * Verify if user is in group
   *
   * @param unknown $uid
   * @param unknown $group
   * @return boolean
   */
  function checkUserInGroup($uid, $group) {
    $rValue = FALSE;
    $group_dn = "cn=" . $group . ",ou=group," . $this->LdapDn;
    $filter = "(|(member=uid=" . $uid . ",ou=people," . $this->LdapDn . ")(member=uid=" . $uid . ",ou=Community," . $this->LdapDn . "))";
    $ds = ldap_connect($this->LdapUrl);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $r = @ldap_bind($ds); // anonymous bind
    if ($r) {
      $sr = ldap_search($ds, $group_dn, $filter);
      $info = ldap_get_entries($ds, $sr);
      if ($info['count']) {
        $rValue = TRUE;
      }
    }
    ldap_close($ds);
    return $rValue;
  }

  /**
   * Change password
   *
   * @param unknown $_Uid
   * @param unknown $_currPassword
   * @param unknown $_newPassword
   * @return number
   *
   * @deprecated
   */
  function changePassword($_Uid, $_currPassword, $_newPassword) {
    return 0;
  }

}