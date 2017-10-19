<?php
/**
 * *****************************************************************************
 * Copyright (c) 2007, 2017 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 * Denis Roy (Eclipse Foundation)- initial API and implementation
 * Karl Matthias (Eclipse Foundation) - Based completely on work of Denis Roy
 * Christopher Guindon (Eclipse Foundation) - Adding the concept of
 * DBConnectionBase.
 * *****************************************************************************
 */

require_once ('DBConnectionBase.php');

class DBConnectionPortalRO extends DBConnectionBase {

  function __construct() {
    parent::__construct();
    $this->setMysqlDatabase("myfoundation_prod");
  }

}