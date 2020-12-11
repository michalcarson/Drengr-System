<?php
return [
    /*
     * Version number should be incremented when changes are made to the tables.
     * This will let the application know to update tables. Version is just a number.
     * No reason to do semver here.
     */
    'version' => 4,

    'namespace' => 'drengr_',

    'tables' => [
        'email_type' =>
            "CREATE TABLE {prefix}email_type (
              id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              name varchar(50) NOT NULL,
              PRIMARY KEY  (id)
            ) {charset};",

        'phone_type' =>
            "CREATE TABLE {prefix}phone_type (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                PRIMARY KEY  (id)
            ) {charset};",

        'group' =>
            "CREATE TABLE {prefix}group (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                authenticity_officer int(11) UNSIGNED,
                sturaesman int(11) UNSIGNED,
                training_officer int(11) UNSIGNED,
                url varchar(50),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id)
            ) {charset};",

        'member' =>
            "CREATE TABLE {prefix}member (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                wp_user_id bigint(20) UNSIGNED,
                wp_user_validated tinyint(1) NOT NULL DEFAULT 0,
                name varchar(255) NOT NULL,
                viking_name varchar(255),
                address text,
                city varchar(255),
                state varchar(255),
                zip varchar(50),
                date_joined date,
                face_picture varchar(255),
                full_picture varchar(255),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id)
            ) {charset};",

        'certification' =>
            "CREATE TABLE {prefix}certification (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                short_name varchar(50),
                drengr_point tinyint(1) DEFAULT '0',
                extra_point tinyint(1) DEFAULT '0',
                print_on_card tinyint(1) DEFAULT '0',
                certify_others tinyint(1) DEFAULT '0',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id)
            ) {charset};",

        'rank' =>
            "CREATE TABLE {prefix}rank (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                PRIMARY KEY  (id)
            ) {charset};",

        'role' =>
            "CREATE TABLE {prefix}role (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                PRIMARY KEY  (id)
            ) {charset};",

        'office' =>
            "CREATE TABLE {prefix}office (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                PRIMARY KEY  (id)
            ) {charset};",

        'member_office' =>
            "CREATE TABLE {prefix}member_office (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                group_id int(11) UNSIGNED,
                member_id int(11) UNSIGNED NOT NULL,
                office_id int(11) UNSIGNED NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY (id),
                CONSTRAINT FOREIGN KEY member_office_group_fk (group_id) REFERENCES {prefix}group (id),
                CONSTRAINT FOREIGN KEY member_office_member_fk (member_id) REFERENCES {prefix}member (id), 
                CONSTRAINT FOREIGN KEY member_office_office_fk (office_id) REFERENCES {prefix}office (id) 
            ) {charset};",

        'group_member' =>
            "CREATE TABLE {prefix}group_member (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                membership_year tinyint(4) NOT NULL,
                group_id int(11) UNSIGNED NOT NULL,
                member_id int(11) UNSIGNED NOT NULL,
                role_id int(11) UNSIGNED,
                international tinyint(1) NOT NULL DEFAULT '0',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id),
                CONSTRAINT FOREIGN KEY group_member_group_fk (group_id) REFERENCES {prefix}group (id),
                CONSTRAINT FOREIGN KEY group_member_member_fk (member_id) REFERENCES {prefix}member (id), 
                CONSTRAINT FOREIGN KEY group_member_role_fk (role_id) REFERENCES {prefix}role (id) 
            ) {charset};",

        'member_certification' =>
            "CREATE TABLE {prefix}member_certification (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                date_achieved date NOT NULL,
                certification_id int(11) UNSIGNED NOT NULL,
                member_id int(11) UNSIGNED NOT NULL,
                assessment_officer_id int(11) UNSIGNED NOT NULL,
                extra_points int(11) NOT NULL DEFAULT '0',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id),
                CONSTRAINT FOREIGN KEY member_certification_cert_fk (certification_id) REFERENCES {prefix}certification (id),
                CONSTRAINT FOREIGN KEY member_certification_member_fk (member_id) REFERENCES {prefix}member (id),
                CONSTRAINT FOREIGN KEY member_certification_officer_fk (assessment_officer_id) REFERENCES {prefix}member (id) 
            ) {charset};",

        'member_email' =>
            "CREATE TABLE {prefix}member_email (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                email_type_id int(11) UNSIGNED NOT NULL,
                member_id int(11) UNSIGNED NOT NULL,
                email_address varchar(255) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id),
                CONSTRAINT FOREIGN KEY member_email_email_type_fk (email_type_id) REFERENCES {prefix}email_type (id),
                CONSTRAINT FOREIGN KEY member_email_member_fk (member_id) REFERENCES {prefix}member (id)
            ) {charset};",

        'member_phone' =>
            "CREATE TABLE {prefix}member_phone (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                phone_type_id int(11) UNSIGNED NOT NULL,
                member_id int(11) UNSIGNED NOT NULL,
                phone_number varchar(50) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id),
                CONSTRAINT FOREIGN KEY member_phone_phone_type_fk (phone_type_id) REFERENCES {prefix}phone_type (id),
                CONSTRAINT FOREIGN KEY member_phone_member_fk (member_id) REFERENCES {prefix}member (id)
            ) {charset};",

        'member_rank' =>
            "CREATE TABLE {prefix}member_rank (
                id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                member_id int(11) UNSIGNED NOT NULL,
                rank_id int(11) UNSIGNED NOT NULL,
                date_achieved date NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime,
                PRIMARY KEY  (id),
                CONSTRAINT FOREIGN KEY member_rank_rank_fk (rank_id) REFERENCES {prefix}rank (id),
                CONSTRAINT FOREIGN KEY member_rank_member_fk (member_id) REFERENCES {prefix}member (id)
            ) {charset};",

    ]
];
