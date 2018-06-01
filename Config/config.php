<?php

/*
 * @copyright  Kntnt Sweden AB
 * @author   Thomas Barregren
 *
 * @link    https:/www.kntnt.se/
 *
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
  'name'    => 'Kntnt Email Marketing',
  'description' => 'Enables integration with Kntnt supported email marketing services.',
  'version'   => '1.0',
  'author'   => 'Kntnt',
  'services' => [
    'forms' => [
      'mautic.form.type.emailmarketing.editnews' => [
        'class'   => 'MauticPlugin\KntntEmailMarketingBundle\Form\Type\EditnewsType',
        'arguments' => ['mautic.factory', 'session', 'mautic.helper.core_parameters'],
        'alias'   => 'emailmarketing_editnews',
      ],
    ],
    'integrations' => [
      'mautic.integration.editnews' => [
        'class'   => \MauticPlugin\KntntEmailMarketingBundle\Integration\EditnewsIntegration::class,
        'arguments' => [
        ],
      ],
    ],
  ],
];
