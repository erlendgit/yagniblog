<?php

namespace Drupal\custom_module\Forms\Steps;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\custom_module\CustomFormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomStep1FormBuilder implements CustomFormBuilderInterface, ContainerInjectionInterface {

  use StringTranslationTrait;

  const ID = '1';

  public static function create(ContainerInterface $container) {
    return new static($container->get('current_user'));
  }

  public function getId() {
    return static::ID;
  }

  public function nextId() {
    return CustomStep2FormBuilder::ID;
  }

  public function previousId() {
    return;
  }

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  public function buildForm(array $defaults) {
    return [
      'email' => [
        '#type' => 'email',
        '#title' => $this->t("E-mailaddress"),
        '#default_value' =>
          $defaults['email']
          ?? $this->currentUser->getEmail(),
      ],
    ];
  }

}
