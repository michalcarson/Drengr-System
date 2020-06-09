<?php

namespace Drengr\App;

use Drengr\Framework\Database;
use Drengr\Framework\ListingFactory;
use Drengr\Framework\Plugin;
use Drengr\Model\Group;

class Admin extends Plugin
{
    protected $config;
    protected $database;
    protected $parentSlug = 'drengr-system-menu';
    protected $capability = 'manage_options';
    protected $menuPrefix = 'drengr';

    protected $submenus = [
        [
            'pageTitle' => 'Members',
            'function' => 'renderMemberList',
        ],
        [
            'pageTitle' => 'Groups',
            'function' => 'renderGroupList',
        ],
    ];
    /**
     * @var ListingFactory
     */
    private $listingFactory;

    public function __construct(array $config, Database $database, ListingFactory $listingFactory)
    {
        $this->config = $config;
        $this->database = $database;
        $this->listingFactory = $listingFactory;
    }

    public function register($page = '')
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        register_activation_hook($page, [$this, 'activate']);
    }

    public function activate()
    {
        $this->database->updateTablesIfNeeded();
    }

    public function registerMenu()
    {
        add_menu_page(
            'Drengr Module',
            'Drengr System',
            $this->capability,
            $this->parentSlug,
            [$this, 'renderDefaultPage']
        );

        foreach ($this->submenus as $submenu) {
            $parentSlug = isset($submenu['parentSlug']) ? $submenu['parentSlug'] : $this->parentSlug;
            $menuSlug = isset($submenu['menuSlug'])
                ? $submenu['menuSlug']
                : sanitize_title_with_dashes($this->menuPrefix . ' ' . $submenu['pageTitle']);
            $menuTitle = isset($submenu['menuTitle']) ? $submenu['menuTitle'] : $submenu['pageTitle'];
            $capability = isset($submenu['capability']) ? $submenu['capability'] : $this->capability;

            add_submenu_page(
                $parentSlug,
                $submenu['pageTitle'],
                $menuTitle,
                $capability,
                $menuSlug,
                [$this, $submenu['function']]
            );
        }
    }

    public function renderDefaultPage()
    {
        echo "<h1>Drengr Admin Module</h1><br/>Default Page<br/>";
        /*
         * notifications:
         *    user's linking site profile to drengr profile--needs approval
         *    group leader designating AO or TO
         *
         * progress
         *     members approved for Karl award
         *     members within a few points of Drengr award
         */
    }

    public function renderMemberList()
    {
        echo '<ul><li>Me me me</li><li>Myself</li><li>I</li></ul>';
    }

    public function renderGroupList()
    {
        $listing = $this->listingFactory->create(Group::class);
        $listing->pageHeading(Group::PLURAL)
            ->formTag()
            ->prepare_items()
            ->display();
        $listing->endForm();
    }
}
