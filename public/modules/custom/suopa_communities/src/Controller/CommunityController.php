<?php

namespace Drupal\suopa_communities\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\suopa_communities\Entity\CommunityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CommunityController.
 *
 *  Returns responses for Community routes.
 */
class CommunityController extends ControllerBase implements ContainerInjectionInterface {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new CommunityController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a Community revision.
   *
   * @param int $community_revision
   *   The Community revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($community_revision) {
    $community = $this->entityTypeManager()->getStorage('community')
      ->loadRevision($community_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('community');

    return $view_builder->view($community);
  }

  /**
   * Page title callback for a Community revision.
   *
   * @param int $community_revision
   *   The Community revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($community_revision) {
    $community = $this->entityTypeManager()->getStorage('community')
      ->loadRevision($community_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $community->label(),
      '%date' => $this->dateFormatter->format($community->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Community.
   *
   * @param \Drupal\suopa_communities\Entity\CommunityInterface $community
   *   A Community object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CommunityInterface $community) {
    $account = $this->currentUser();
    $community_storage = $this->entityTypeManager()->getStorage('community');

    $langcode = $community->language()->getId();
    $langname = $community->language()->getName();
    $languages = $community->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $community->label()]) : $this->t('Revisions for %title', ['%title' => $community->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all community revisions") || $account->hasPermission('administer community entities')));
    $delete_permission = (($account->hasPermission("delete all community revisions") || $account->hasPermission('administer community entities')));

    $rows = [];

    $vids = $community_storage->revisionIds($community);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\suopa_communities\CommunityInterface $revision */
      $revision = $community_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $community->getRevisionId()) {
          $link = $this->l($date, new Url('entity.community.revision', [
            'community' => $community->id(),
            'community_revision' => $vid,
          ]));
        }
        else {
          $link = $community->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.community.translation_revert', [
                'community' => $community->id(),
                'community_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.community.revision_revert', [
                'community' => $community->id(),
                'community_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.community.revision_delete', [
                'community' => $community->id(),
                'community_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['community_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
