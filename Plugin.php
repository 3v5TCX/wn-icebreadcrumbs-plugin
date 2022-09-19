<?php namespace icecollection\Breadcrumbs;

use System\Classes\PluginBase;
use Cms\Classes\Page;
use Cms\Classes\Theme;

/**
 * Breadcrumbs Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'icecollection.breadcrumbs::lang.plugin.name',
            'description' => 'icecollection.breadcrumbs::lang.plugin.description',
            'author'      => 'icecollection.breadcrumbs::lang.plugin.author',
            'icon'        => 'icon-ellipsis-h',
            'homepage'    => 'http://www.iicicecollection.ru/'
        ];
    }

    public function registerPermissions()
    {
        return [
            'icecollection.breadcrumbs.access_breadcrumbs' => ['tab' => 'icecollection.breadcrumbs::lang.permissions.tab', 'label' => 'icecollection.breadcrumbs::lang.permissions.access_breadcrumbs']
        ];
    }

    public function register()
    {
        \Event::listen('backend.form.extendFields', function($widget) {
            if (!$widget->model instanceof \Cms\Classes\Page) return;

            if (!($theme = Theme::getEditTheme())) {
                throw new ApplicationException(Lang::get('cms::lang.theme.edit.not_found'));
            }

            $pages = Page::all()->sort(function($a, $b){
                return strcasecmp($a->title, $b->title);
            });

            $pageOptions = $this->buildPageOptions($pages);
            $widget->addFields(
                [
                    'settings[child_of]' => [
                        'label'   => 'icecollection.breadcrumbs::lang.settings.child_of.label',
                        'type'    => 'dropdown',
                        'tab'     => 'Breadcrumbs',
                        'span'    => 'left',
                        'options' => $pageOptions,
                        'comment' => 'icecollection.breadcrumbs::lang.settings.child_of.comment',
                    ],
                    'settings[hide_crumb]' => [
                        'label'   => 'icecollection.breadcrumbs::lang.settings.hide_crumb.label',
                        'type'    => 'checkbox',
                        'tab'     => 'Breadcrumbs',
                        'span'    => 'right',
                        'comment' => 'icecollection.breadcrumbs::lang.settings.hide_crumb.comment',
                    ]
                ],
                'primary'
            );
        });
    }

    public function registerComponents()
    {
        return [
            'icecollection\Breadcrumbs\Components\Breadcrumbs' => 'breadcrumbs'
        ];
    }

    private function buildPageOptions($pages)
    {
        $pageOptions = [
            'mey_no_parent' => 'нет'
        ];

        foreach($pages as $page) {
            $pageOptions[$page->baseFileName] = "{$page->title} ({$page->url})";
        }

        return $pageOptions;
    }
}
