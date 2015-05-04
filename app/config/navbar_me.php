<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'home'  => [
            'text'  => 'Me',
            'url'   => $this->di->get('url')->create(''),
            'title' => 'Min nya me'
        ],

        'reports' => [
            'text' => 'Redovisning',
            'url' => $this->di->get('url')->create('redovisning'),
            'title' => 'Redovisningar av kursmoment'
        ],

        'comments' => [
            'text' => 'Gästbok',
            'url' => $this->di->get('url')->create('comment'),
            'title' => 'Där mina gäster berättar hur bra jag är'
        ],

        'source' => [
            'text' => 'Source',
            'url' => $this->di->get('url')->create('source'),
            'title' => 'Sidans källkod'
        ],

        'theme' => [
            'text' => 'Tema',
            'url' => $this->di->get('url')->createRelative('theme.php'),
            'title' => 'Test av tema',

            'submenu' => [
                'items' => [
                    'regions' => [
                        'text' =>'Regioner',
                        'url' => $this->di->get('url')->create('theme.php/regioner'),
                        'title' => 'Regionsöversikt'
                    ],
                    'typography' => [
                        'text' => 'Typografi',
                        'url' => $this->di->get('url')->create('theme.php/typografi?showgrid=true'),
                        'title' => 'Typografisk översikt'
                    ],
                    'font-awesome' => [
                        'text' => 'Ikontest',
                        'url' => $this->di->get('url')->create('theme.php/font-awesome'),
                        'title' => 'Test av font-awesome'
                        ],
                ],
            ],
        ],
        'users' => [
                'text' => 'Användare',
                'url' => $this->di->get('url')->create('users'),
                'title' => 'Sidans användare'
        ],
        'table' => [
                'text' => 'Tabellmodul',
                'url' => 'http://www.student.bth.se/~madc15/phpmvc/Anax-MVC-master -kopia/webroot/tableTest.php',
                'title' => 'Test av egen modul'
                ],
 
/*        // This is a menu item
        'test'  => [
            'text'  => 'Submenu',
            'url'   => $this->di->get('url')->create('submenu'),
            'title' => 'Submenu with url as internal route within this frontcontroller',

            // Here we add the submenu, with some menu items, as part of a existing menu item
            'submenu' => [

                'items' => [

                    // This is a menu item of the submenu
                    'item 0'  => [
                        'text'  => 'Item 0',
                        'url'   => $this->di->get('url')->create('submenu/item-0'),
                        'title' => 'Url as internal route within this frontcontroller'
                    ],

                    // This is a menu item of the submenu
                    'item 2'  => [
                        'text'  => '/humans.txt',
                        'url'   => $this->di->get('url')->asset('/humans.txt'),
                        'title' => 'Url to sitespecific asset',
                        'class' => 'italic'
                    ],

                    // This is a menu item of the submenu
                    'item 3'  => [
                        'text'  => 'humans.txt',
                        'url'   => $this->di->get('url')->asset('humans.txt'),
                        'title' => 'Url to asset relative to frontcontroller',
                    ],
                ],
            ],
        ],
 
        // This is a menu item
        'controller' => [
            'text'  =>'Controller (marked for all descendent actions)',
            'url'   => $this->di->get('url')->create('controller'),
            'title' => 'Url to relative frontcontroller, other file',
            'mark-if-parent-of' => 'controller',
        ],

        // This is a menu item
        'about' => [
            'text'  =>'About',
            'url'   => $this->di->get('url')->create('about'),
            'title' => 'Internal route within this frontcontroller'
        ],
        */
    ],



    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];
