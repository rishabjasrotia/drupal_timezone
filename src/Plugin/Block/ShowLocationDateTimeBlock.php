<?php

namespace Drupal\drupal_timezone\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\drupal_timezone\ModuleConstants;
use Drupal\drupal_timezone\Services\TimezoneHelper;
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
   * Drupal\Core\Render\RendererInterface definition.
   *
   * @var Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * TimezoneHelper object.
   *
   * @var \Drupal\drupal_timezone\Services\TimezoneHelper
   */
  protected $timezoneHelper;

  /**
   * The kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $kill_switch;

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
   * @param \Drupal\drupal_timezone\Services\TimezoneHelper $timezoneHelper
   *   The TimezoneHelper service.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The page cache kill switch service.
   * @param Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
    TimezoneHelper $timezoneHelper,
    KillSwitch $kill_switch,
    RendererInterface $renderer
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get(ModuleConstants::SETTINGS);
    $this->timezoneHelper = $timezoneHelper;
    $this->kill_switch = $kill_switch;
    $this->renderer = $renderer;
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
      $container->get('drupal_timezone.timezonehelper'),
      $container->get('page_cache_kill_switch'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $this->kill_switch->trigger();
    $formattedDateTime = $this->timezoneHelper->getFormattedDateTimeDetails();
    $dateTimeFormat = explode('-', $formattedDateTime);
    $locationDetails = $this->timezoneHelper->getLocationDetails();
    $build =  [
      '#theme' => 'drupal_timezone_location',
      '#country' => $locationDetails['country'],
      '#city' => $locationDetails['city'],
      '#date_time' => $this->timezoneHelper->getDateTimeDetails(),
      '#time' => $dateTimeFormat[0] ?? '',
      '#date' => $dateTimeFormat[1] ?? '',
      '#cache' => [
        'tags' => $this->config->getCacheTags(),
      ],
    ];
    $this->renderer->addCacheableDependency($build, $config);
    return $build;
  }

}
