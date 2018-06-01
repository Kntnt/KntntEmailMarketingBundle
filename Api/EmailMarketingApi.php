<?php

/*
 * @copyright  Kntnt Sweden AB
 * @author   Thomas Barregren
 *
 * @link    https:/www.kntnt.se/
 *
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\KntntEmailMarketingBundle\Api;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class EmailMarketingApi {

  protected $integration;

  protected $keys;

  public function __construct(AbstractIntegration $integration) {
    $this->integration = $integration;
    $this->keys    = $integration->getKeys();
  }

}
