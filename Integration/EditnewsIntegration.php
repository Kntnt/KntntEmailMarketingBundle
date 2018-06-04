<?php

/*
 * @copyright  Kntnt Sweden AB
 * @author   Thomas Barregren
 *
 * @link    https:/www.kntnt.se/
 *
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\KntntEmailMarketingBundle\Integration;

class EditnewsIntegration extends EmailAbstractIntegration {

  public function getName() {
    return 'Editnews';
  }

  public function getDisplayName() {
    return 'EditNews';
  }

  public function getAuthenticationType() {
    return 'basic';
  }

  public function getRequiredKeyFields() {
    $this->keys['username'] = "NONE"; // EditNews don't require user name
    return [ 'password' => 'mautic.integration.keyfield.api' ];
  }

  public function getAvailableLeadFields( $settings = [] ) {

    if ( isset( $settings['list'] ) ) {
      // Ajax update
      $listId = $settings['list'];
    }
    elseif ( ! empty( $settings['feature_settings']['list_settings']['list'] ) ) {
      // Form load
      $listId = $settings['feature_settings']['list_settings']['list'];
    }
    elseif ( ! empty( $settings['list_settings']['list'] ) ) {
      // Push action
      $listId = $settings['list_settings']['list'];
    }

    if ( empty( $listId ) ) {
      return;
    }

    $settings['cache_suffix'] = $cacheSuffix = '.' . $listId;
    if ( $lead_fields = parent::getAvailableLeadFields( $settings ) ) {
      return $lead_fields;
    }

    $lead_fields['email'] = [
      'label'    => 'Email', // TODO: Translate
      'type'     => 'string',
      'required' => true,
    ];

    $lead_fields['name'] = [
      'label'    => 'Name', // TODO: Translate
      'type'     => 'string',
      'required' => false,
    ];

    $fields = $this->getApiHelper()->get_fields();
    foreach ( $fields as $field ) {
      $lead_fields[ $field ] = [
        'label'    => $field,
        'type'     => 'string',
        'required' => false,
      ];
    }

    $this->cache->set( 'leadFields' . $cacheSuffix, $lead_fields );

    return $lead_fields;

  }

  public function pushLead( $lead, $config = [] ) {

    if ( ! $this->isAuthorized() ) return false;

    $config = $this->mergeConfigToFeatureSettings( $config );
    $address = $this->populateLeadData( $lead, $config );

    if ( ! $address || ! isset( $address['email'] ) || ! isset( $config['list_settings'] ) ) return false;

    $list = $config['list_settings']['list'];
    $welcome_letter = $config['list_settings']['welcome_letter'];
    $sender = $config['list_settings']['sender'];
    $verify_url = $config['list_settings']['verify_url'];

    try {
      $this->getApiHelper()->add_recepient_to_list( $address, $list, $verify_url, $welcome_letter, $sender );
      return true;
    }
    catch ( \Exception $e ) {
      $this->logIntegrationError( $e );
      return false;
    }

  }

  public function getFormSettings() {
    $settings = parent::getFormSettings();
    $settings['dynamic_contact_fields'] = true;
    return $settings;
  }

}
