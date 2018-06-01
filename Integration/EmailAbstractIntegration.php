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

use Mautic\PluginBundle\Integration\AbstractIntegration;

abstract class EmailAbstractIntegration extends AbstractIntegration {

  protected $pushContactLink = false;

  public function getSupportedFeatures() {
    return [ 'push_lead' ];
  }

  public function appendToForm( &$builder, $data, $formArea ) {
    if ( $formArea == 'features' || $formArea == 'integration' ) {
      if ( $this->isAuthorized() ) {
        $name = strtolower( $this->getName() );
        if ( $this->factory->serviceExists( 'mautic.form.type.emailmarketing.' . $name ) ) {
          if ( $formArea == 'integration' && isset( $data['leadFields'] ) && empty( $data['list_settings']['leadFields'] ) ) {
            $data['list_settings']['leadFields'] = $data['leadFields'];
          }
          $builder->add( 'list_settings', 'emailmarketing_' . $name, [
            'label'     => false,
            'form_area' => $formArea,
            'data'      => ( isset( $data['list_settings'] ) ) ? $data['list_settings'] : [],
          ] );
        }
      }
    }
  }

  public function getFormTheme() {
    return 'KntntEmailMarketingBundle:FormTheme\EmailMarketing';
  }

  public function getApiHelper() {

    static $helper;

    if ( empty( $helper ) ) {
      $class = '\\MauticPlugin\\KntntEmailMarketingBundle\\Api\\' . $this->getName() . 'Api';
      $helper = new $class( $this );
    }

    return $helper;

  }

  public function mergeConfigToFeatureSettings( $config = [] ) {

    $featureSettings = $this->settings->getFeatureSettings();

    if ( isset( $config['config']['list_settings']['leadFields'] ) ) {
      $config['config']['leadFields'] = $this->formatMatchedFields( $config['config']['list_settings']['leadFields'] );
      unset( $config['config']['list_settings']['leadFields'] );
    }

    if ( empty( $config['integration'] ) || ( ! empty( $config['integration'] ) && $config['integration'] == $this->getName() ) ) {
      $featureSettings = array_merge( $featureSettings, $config['config'] );
    }

    return $featureSettings;

  }

}
