<?php

namespace Drupal\custom_module\Forms;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\custom_module\CustomFormBuilderInterface;
use Drupal\custom_module\Forms\Steps\CustomStep1FormBuilder;
use Drupal\custom_module\Forms\Steps\CustomStep2FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomMultistepForm extends FormBase {

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('class_resolver'),
      $container->get('tempstore.private')
    );
  }

  /**
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected $classResolver;

  /**
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $tempStore;

  public function __construct(ClassResolverInterface $classResolver,
                              PrivateTempStoreFactory $tempStoreFactory) {
    $this->classResolver = $classResolver;
    $this->tempStore = $tempStoreFactory->get(static::class);
  }

  public function getFormId() {
    return 'custom_multistep_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\custom_module\CustomFormBuilderInterface $builder */
    $builder = $this->getPageBuilder();
    $form['page'] = $builder->buildForm($this->getDefaults($builder->getId()));
    $form['actions'] = $this->buildActions($builder);
    return $form;
  }

  protected function buildActions(CustomFormBuilderInterface $formBuilder) {
    return [
      'previous' => [
        '#type' => 'submit',
        '#value' => $this->t('Previous'),
        '#disabled' => NULL === $formBuilder->previousId(),
        '#submit' => [
          '::submitPrevious',
        ],
      ],
      'next' => [
        '#type' => 'submit',
        '#disabled' => NULL === $formBuilder->nextId(),
        '#value' => $this->t('Next'),
      ],
    ];
  }

  /**
   * @return \Drupal\custom_module\Forms\Steps\CustomFormBuilderInterface
   */
  protected function getPageBuilder() {
    if ($this->getPageNo() === CustomStep2FormBuilder::ID) {
      return $this->classResolver->getInstanceFromDefinition(
        CustomStep2FormBuilder::class
      );
    }
    $this->updatePageNo(CustomStep1FormBuilder::ID);
    return $this->classResolver->getInstanceFromDefinition(
      CustomStep1FormBuilder::class
    );
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $builder = $this->getPageBuilder();
    $this->updatePageNo($builder->nextId());
    $this->updateDefaults($builder->getId(), $form_state->getValues());
    $form_state->setRebuild();
  }

  public function submitPrevious(array &$form, FormStateInterface $form_state) {
    $builder = $this->getPageBuilder();
    $this->updatePageNo($builder->previousId());
    $this->updateDefaults($builder->getId(), $form_state->getValues());
    $form_state->setRebuild();
  }

  protected function updatePageNo($newPageNo) {
    $this->tempStore->set('pageno', $newPageNo);
  }

  protected function getPageNo() {
    return $this->tempStore('pageno');
  }

  protected function updateDefaults($pageNo, array $pageDefaults) {
    $allDefaults = $this->tempStore->get('allDefaults');
    $allDefaults[$pageNo] = $pageDefaults;
    $this->tempStore->set('allDefaults', $allDefaults);
  }

  protected function getDefaults($pageNo) {
    $allDefaults = $this->tempStore->get('allDefaults');
    return $allDefaults[$pageNo] ?? [];
  }

}
