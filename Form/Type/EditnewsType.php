<?php

/*
 * @copyright  Kntnt Sweden AB
 * @author   Thomas Barregren
 *
 * @link    https:/www.kntnt.se/
 *
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\KntntEmailMarketingBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory; // DEPRECATED. MUST BE FIXED BEFORE MAUTIC 3.0.
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolverInterface; // DEPRECATED: USE OptionsResolver INSTEAD.

class EditnewsType extends AbstractType {

  private $factory;

  protected $session;

  protected $coreParametersHelper;

  public function __construct( MauticFactory $factory, Session $session, CoreParametersHelper $coreParametersHelper ) {
    $this->factory = $factory;
    $this->session = $session;
    $this->coreParametersHelper = $coreParametersHelper;
  }

  public function buildForm( FormBuilderInterface $builder, array $options ) {

    $helper = $this->factory->getHelper( 'integration' );

    $editnews = $helper->getIntegrationObject( 'Editnews' );

    $api = $editnews->getApiHelper();

    try {
      $choices = $api->get_address_lists();
    }
    catch ( \Exception $e ) {
      $choices = [];
      $error = $e->getMessage();
    }

    $builder->add( 'list', 'choice', [
      'choices'  => $choices,
      'label'    => 'kntnt.emailmarketing.list',
      'required' => false,
      'attr'     => [
        'tooltip'  => 'kntnt.emailmarketing.list.tooltip',
        'onchange' => 'Mautic.getIntegrationLeadFields(\'Editnews\', this, {"list": this.value});',
      ],
    ] );

    $builder->add( 'verify_subject', 'text', [
      'label'    => 'kntnt.emailmarketing.verify_subject',
      'required'   => false,
      'attr'       => [
        'class'   => 'form-control',
        'tooltip'  => 'kntnt.emailmarketing.verify_subject.tooltip',
      ],
    ] );

    $builder->add( 'verify_url', 'text', [
      'label'    => 'kntnt.emailmarketing.verify_url',
      'required'   => false,
      'attr'       => [
        'class'   => 'form-control',
        'tooltip'  => 'kntnt.emailmarketing.verify_url.tooltip',
      ],
    ] );

    try {
      $choices = $api->get_welcome_letters();
    }
    catch ( \Exception $e ) {
      $choices = [];
      $error = $e->getMessage();
    }

    $builder->add( 'welcome_letter', 'choice', [
      'choices'  => $choices,
      'label'    => 'kntnt.emailmarketing.welcome_letter',
      'required' => false,
      'attr'     => [
        'tooltip'  => 'kntnt.emailmarketing.welcome_letter.tooltip',
      ],
    ] );

    try {
      $choices = $api->get_senders();
    }
    catch ( \Exception $e ) {
      $choices = [];
      $error = $e->getMessage();
    }

    $builder->add( 'sender', 'choice', [
      'choices'  => $choices,
      'label'    => 'kntnt.emailmarketing.sender',
      'required' => false,
      'attr'     => [
        'tooltip'  => 'kntnt.emailmarketing.sender.tooltip',
      ],
    ] );

    if ( ! empty( $error ) ) {
      $builder->addEventListener( FormEvents::PRE_SET_DATA, function ( FormEvent $event ) use ( $error ) {
        $form = $event->getForm();
        if ( $error ) {
          $form['list']->addError( new FormError( $error ) );
        }
      } );
    }

    if ( isset( $options['form_area'] ) && $options['form_area'] == 'integration' ) {
      $leadFields = $this->factory->getModel( 'plugin' )->getLeadFields();

      $formModifier = function ( FormInterface $form, $data ) use ( $editnews, $leadFields ) {
        $integrationName = $editnews->getName();
        $session = $this->session;
        $limit = $session->get( 'mautic.plugin.' . $integrationName . '.lead.limit', $this->coreParametersHelper->getParameter( 'default_pagelimit' ) );
        $page = $session->get( 'mautic.plugin.' . $integrationName . '.lead.page', 1 );
        $settings = [
          'silence_exceptions' => false,
          'feature_settings'   => [
            'list_settings' => $data,
          ],
          'ignore_field_cache' => ($page == 1 && 'POST' !== $_SERVER['REQUEST_METHOD']) ? true : false,
        ];
        try {
          $fields = $editnews->getFormLeadFields($settings);

          if ( ! is_array( $fields ) ) {
            $fields = [];
          }
          $error = '';
        }
        catch ( \Exception $e ) {
          $fields = [];
          $error = $e->getMessage();
          $page = 1;
        }

        list( $specialInstructions ) = $editnews->getFormNotes( 'leadfield_match' );
        $form->add( 'leadFields', 'integration_fields', [
          'label'                => 'mautic.integration.leadfield_matches',
          'required'             => true,
          'mautic_fields'        => $leadFields,
          'integration'          => $editnews->getName(),
          'integration_object'   => $editnews,
          'limit'                => $limit,
          'page'                 => $page,
          'data'                 => $data,
          'integration_fields'   => $fields,
          'special_instructions' => $specialInstructions,
          'mapped'               => true,
          'error_bubbling'       => false,
        ] );

        if ( $error ) {
          $form->addError( new FormError( $error ) );
        }
      };

      $builder->addEventListener( FormEvents::PRE_SET_DATA, function ( FormEvent $event ) use ( $formModifier ) {
        $data = $event->getData();
        if ( isset( $data['leadFields']['leadFields'] ) ) {
          $data['leadFields'] = $data['leadFields']['leadFields'];
        }
        $formModifier( $event->getForm(), $data );
      } );

      $builder->addEventListener( FormEvents::PRE_SUBMIT, function ( FormEvent $event ) use ( $formModifier ) {
        $data = $event->getData();
        if ( isset( $data['leadFields']['leadFields'] ) ) {
          $data['leadFields'] = $data['leadFields']['leadFields'];
        }
        $formModifier( $event->getForm(), $data );
      } );
    }
  }

  public function setDefaultOptions( OptionsResolverInterface $resolver ) {
    $resolver->setOptional( [ 'form_area' ] );
  }

  public function getName() {
    return 'emailmarketing_editnews';
  }

}
