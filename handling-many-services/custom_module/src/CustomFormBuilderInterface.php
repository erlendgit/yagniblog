<?php

namespace Drupal\custom_module;

interface CustomFormBuilderInterface {

  public function getId();

  public function nextId();

  public function previousId();

  public function buildForm(array $defaults);


}
