<?php

/**
 * @file
 * Contains \Drupal\commerce_product\Entity\ProductVariation.
 */

namespace Drupal\commerce_product\Entity;

use Drupal\commerce_product\ProductVariationInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Product Variation entity.
 *
 * @ContentEntityType(
 *   id = "commerce_product_variation",
 *   label = @Translation("Product variation"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\commerce_product\Form\ProductVariationForm",
 *     },
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler"
 *   },
 *   admin_permission = "administer products",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   base_table = "commerce_product_variation",
 *   data_table = "commerce_product_variation_field_data",
 *   entity_keys = {
 *     "id" = "variation_id",
 *     "label" = "sku",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "bundle" = "type"
 *   },
 *   bundle_entity_type = "commerce_product_variation_type",
 *   field_ui_base_route = "entity.commerce_product_variation_type.edit_form",
 * )
 */
class ProductVariation extends ContentEntityBase implements ProductVariationInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->get('type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getSku() {
    return $this->get('sku')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSku($sku) {
    $this->set('sku', $sku);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    $this->get('uid')->target_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    return $this->set('uid', $uid);
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entityType) {
    $fields['variation_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Variation ID'))
      ->setReadOnly(TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\commerce_product\Entity\ProductVariation::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'));

    $fields['sku'] = BaseFieldDefinition::create('string')
      ->setLabel(t('SKU'))
      ->setDescription(t('The unique, human-readable identifier for a product variation.'))
      ->setRequired(TRUE)
      ->addConstraint('ProductSku')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type'))
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active'))
      ->setDescription(t('Disabled product variations cannot be added to shopping carts.'))
      ->setDefaultValue(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'default_value' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
        'settings' => [
          'display_label' => TRUE
        ]
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the product variation was created.'))
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the product variation was last edited.'))
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
