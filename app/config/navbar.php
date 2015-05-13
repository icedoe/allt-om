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
            'text'  => 'Hem',
            'url'   => $this->di->get('url')->create(''),
            'title' => 'Home route of current frontcontroller'
        ],
 
        
         // This is a menu item
        'comments' => [
            'text'  =>'Frågor',
            'url'   => $this->di->get('url')->create('comment'),
            'title' => 'Användarnas frågor'
        ],

        'tags' => [
            'text'  => 'Taggar',
            'url'   => $this->di->get('url')->create('tags'),
            'title' => 'Visa alla taggar',
        ],
 
        // This is a menu item
        'users' => [
            'text'  =>'Användare',
            'url'   => $this->di->get('url')->create('users'),
            'title' => 'Visa registrerade användare',
        ],

       'about' => [
        'text'      => 'Om',
        'url'       => $this->di->get('url')->create('about'),
        'title'     => 'Om sidan'
        ],
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
