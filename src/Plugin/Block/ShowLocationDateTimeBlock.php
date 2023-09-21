<?php

namespace Drupal\drupal_timezone\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\drupal_timezone\ModuleConstants;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Location Date and Time block.
 *
 * @Block(
 *  id = "show_location_date_time_block",
 *  admin_label = @Translation("Show Location Date and Time"),
 *  category = @Translation("custom"),
 * )
 */
class ShowLocationDateTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Config Factory Interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a ShowLocationDateTimeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config Factory Interface.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get(ModuleConstants::SETTINGS);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $city = $this->config->get('city');
    return [
      '#markup' => '<p>City ' . $city . '</p>'
    ];
  }

}
