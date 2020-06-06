<?php
return [
    /*
     * Version number should be incremented when changes are made to the tables.
     * This will let the application know to update tables. Version is just a number.
     * No reason to do semver here.
     */
    'version' => 1,

    'tables' => [
        'certification' =>
            "CREATE TABLE {prefix}drengr_certification (
              id int(11) unsigned NOT NULL AUTO_INCREMENT,
              name varchar(50),
              short_name varchar(50),
              drengr_point tinyint(1) DEFAULT '0',
              extra_point tinyint(1) DEFAULT '0',
              print_on_card tinyint(1) DEFAULT '0',
              certify_others tinyint(1) DEFAULT '0',
              PRIMARY KEY  (id)
            ) {charset};"
    ]
];
