<?php

/*
 * @copyright   Kntnt Sweden AB
 * @author      Thomas Barregren
 *
 * @link        https:/www.kntnt.se/
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\KntntEmailMarketingBundle\Api;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class EditnewsApi extends EmailMarketingApi {

  private $server;

  public function __construct( AbstractIntegration $integration ) {
    parent::__construct( $integration );
    $this->server = new \SoapClient( 'https://api.editnews.com/v1/api.asmx?WSDL', [
      'trace'      => 0,
      'cache_wsdl' => WSDL_CACHE_BOTH,
    ] );
  }

  public function get_address_lists() {
    $lists = $this->soap_call( 'AddressLists' );
    $lists = $this->make_array_of( 'AddressList', $lists );
    $lists = $this->pluck( 'listID', 'listName', $lists );
    return $lists;
  }

  public function get_fields() {
    $fields = $this->soap_call( 'FieldList' );
    $fields = $this->make_array_of( 'Field', $fields );
    $fields = $this->pluck( 'fieldId', 'fieldName', $fields );
    return $fields;
  }

  public function get_senders() {
    $senders = $this->soap_call( 'SenderList' );
    $senders = $this->make_array_of( 'Address', $senders );
    $senders = $this->pluck( 'userID', [ 'email', 'name' ], $senders );
    foreach ( $senders as &$sender ) {
      $sender = $sender['name'] . ' <' . $sender['email'] . '>';
    }
    return $senders;
  }

  public function get_welcome_letters() {
    $letters = $this->soap_call( 'GetWelcomeLetters' );
    $letters = $this->make_array_of( 'Document', $letters );
    $letters = $this->pluck( 'documentId', 'name', $letters );
    return $letters;
  }

  public function add_recepient_to_list( $address, $list, $verify_subject, $verify_url, $welcome_issue, $sender ) {
    $this->soap_call( 'RecipientVerifiedSubscribe', [
      'address' => $this->create_address( $address ),
      'lists'   => [ $this->create_list( $list ) ],
      'extra'   => [
        $this->create_field( 'verifysubject', $verify_subject ),
        $this->create_field( 'verifyurl', $verify_url ),
        $this->create_field( 'welcomeissue', $welcome_issue ),
        $this->create_field( 'senderid', $sender ),
      ],
    ] );
  }

  public function create_address( $address, $user_id = 0 ) {

    $fields = [];
    foreach (
      array_diff_key( $address, array_flip( [
        'email',
        'name',
      ] ) ) as $name => $value
    ) {
      $fields[] = $this->create_field( $name, $value );
    }

    $address = [
      'userID'  => $user_id,
      'email'   => $address['email'],
      'name'    => $address['name'],
      'fields'  => $fields,
      'created' => date( 'c' ),
      'state'   => null,
    ];

    return (object) $address;

  }

  private function create_list( $list_id ) {
    return (object) [
      'listID' => $list_id,
    ];
  }

  private function create_field( $name, $value, $field_id = 0 ) {
    return (object) [
      'fieldId'    => $field_id,
      'fieldName'  => $name,
      'fieldValue' => $value,
    ];
  }

  private function make_array_of( $type, $in ) {

    if ( ! isset( $in->$type ) ) {
      return [];
    }

    if ( is_object( $in->$type ) ) {
      $in->$type = [ $in->$type ];
    }

    $out = [];
    foreach ( $in->$type as $obj ) {
      $out[] = get_object_vars( $obj );
    }

    return $out;

  }

  private function pluck( $key, $value, $array ) {
    $out = [];
    foreach ( $array as $arr ) {
      if ( is_array( $value ) ) {
        foreach ( $value as $v ) {
          $out[ $arr[ $key ] ][ $v ] = $arr[ $v ];
        }
      }
      else {
        $out[ $arr[ $key ] ] = $arr[ $value ];
      }
    }
    return $out;
  }

  private function soap_call( $function_name, $args = [] ) {
    $args['apiKey'] = $this->keys['password'];
    $res = $this->server->__soapCall( $function_name, [ (object) $args ] );
    if ( property_exists( $res, $function_name . 'Result' ) ) {
      return $res->{$function_name . 'Result'};
    }
  }

}