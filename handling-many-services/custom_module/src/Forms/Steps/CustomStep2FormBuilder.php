<?php

namespace Drupal\custom_module\Forms\Steps;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\custom_module\CustomFormBuilderInterface;

class CustomStep2FormBuilder implements CustomFormBuilderInterface {

  use StringTranslationTrait;

  const ID = '2';

  public function getId() {
    return static::ID;
  }

  public function buildForm(array $defaults) {
    return [
      'hi' => [
        '#type' => 'checkbox',
        '#title' => $this->t('I say hi!'),
        '#default_value' => $defaults['hi'],
      ],
    ];
  }

  public function nextId() {
    return;
  }

  public function previousId() {
    return CustomStep1FormBuilder::ID;
  }

}
