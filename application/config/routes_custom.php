<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config['custom_routes'] = 
array(
    # FRONT
    'contact' => array(
        'route' => 'contact',
        'class' => 'front/contact'
    ),
    'blogs' => array(
        'route' => 'blogs',
        'class' => 'front/blogs'
    ),
    'blog' => array(
        'route' => 'blog/{alias}',
        'class' => 'front/blog/$1'
    ),
    'tour' => array(
        'route' => 'tour',
        'class' => 'front/tour'
    ),
    'tours' => array(
        'route' => 'tour/{alias}',
        'class' => 'front/tours/$1'
    ),
    'about' => array(
        'route' => 'about',
        'class' => 'front/about'
    ),
    'faq' => array(
        'route' => 'faq',
        'class' => 'front/faq'
    ),
    'gallery' => array(
        'route' => 'gallery',
        'class' => 'front/gallery'
    ),
    'gallery_detail' => array(
        'route' => 'gallery_detail/{id}',
        'class' => 'front/gallery_detail/$1'
    ),
    # - END FRONT
    # ADMIN
    'login' => array(
        'route' => 'login',
        'class' => 'auth/login'
    ),
    'logout' => array(
        'route' => 'logout',
        'class' => 'auth/logout'
    ),
    'admin_dashboard' => array(
        'route' => 'admin/dashboard',
        'class' => 'AdminDashboard'
    ),
    'admin_change_password' => array(
        'route' => 'admin/changepass',
        'class' => 'AdminDashboard/changepass'
    ),
    'admin_sliders' => array(
        'route' => 'admin/sliders',
        'class' => 'AdminSliders'
    ),
    'admin_sliders_form' => array(
        'route' => 'admin/sliders/form',
        'class' => 'AdminSliders/form'
    ),
    'admin_sliders_detail' => array(
        'route' => 'admin/sliders/detail/{id}',
        'class' => 'AdminSliders/detail/$1'
    ),
    'admin_sliders_delete' => array(
        'route' => 'admin/sliders/delete/{id}',
        'class' => 'AdminSliders/delete/$1'
    ),
    'admin_quotes_index' => array(
        'route' => 'admin/quotes',
        'class' => 'AdminQuotes'
    ),
    'admin_quotes_detail' => array(
            'route' => 'admin/quotes/detail/{id}',
            'class' => 'AdminQuotes/detail/$1'
    ),
    'admin_quotes_form' => array(
            'route' => 'admin/quotes/form',
            'class' => 'AdminQuotes/form'
    ),
    'admin_quotes_delete' => array(
            'route' => 'admin/quotes/delete/{id}',
            'class' => 'AdminQuotes/delete/$1'
    ),
    'admin_testimonies_index' => array(
        'route' => 'admin/testimonies',
        'class' => 'AdminTestimonies'
    ),
    'admin_testimonies_detail' => array(
            'route' => 'admin/testimonies/detail/{id}',
            'class' => 'AdminTestimonies/detail/$1'
    ),
    'admin_testimonies_form' => array(
            'route' => 'admin/testimonies/form',
            'class' => 'AdminTestimonies/form'
    ),
    'admin_testimonies_delete' => array(
            'route' => 'admin/testimonies/delete/{id}',
            'class' => 'AdminTestimonies/delete/$1'
    ),
    'admin_about_history' => array(
        'route' => 'admin/about/history',
        'class' => 'AdminAbout/history'
    ),
    'admin_about_mission' => array(
        'route' => 'admin/about/mission',
        'class' => 'AdminAbout/mission'
    ),
    'admin_about_counter' => array(
        'route' => 'admin/about/counter',
        'class' => 'AdminAbout/counter'
    ),
    'admin_afiliasi' => array(
        'route' => 'admin/afiliasi',
        'class' => 'AdminAfiliasi'
    ),
    'admin_afiliasi_form' => array(
        'route' => 'admin/afiliasi/form',
        'class' => 'AdminAfiliasi/form'
    ),
    'admin_afiliasi_detail' => array(
        'route' => 'admin/afiliasi/detail/{id}',
        'class' => 'AdminAfiliasi/detail/$1'
    ),
    'admin_afiliasi_delete' => array(
        'route' => 'admin/afiliasi/delete/{id}',
        'class' => 'AdminAfiliasi/delete/$1'
    ),
    'admin_tours' => array(
        'route' => 'admin/tours',
        'class' => 'AdminTours'
    ),
    'admin_tours_image_delete' => array(
            'route' => 'admin/tours/delete_image/{id}/{img_name}',
            'class' => 'AdminTours/delete_image/$1/$2'
    ),
    'admin_tours_thumbnail_delete' => array(
            'route' => 'admin/tours/delete_thumbnail/{id}/{img_name}',
            'class' => 'AdminTours/delete_thumbnail/$1/$2'
    ),
    'admin_tours_form' => array(
        'route' => 'admin/tours/form',
        'class' => 'AdminTours/form'
    ),
    'admin_tours_detail' => array(
        'route' => 'admin/tours/detail/{id}',
        'class' => 'AdminTours/detail/$1'
    ),
    'admin_tours_delete' => array(
        'route' => 'admin/tours/delete/{id}',
        'class' => 'AdminTours/delete/$1'
    ),
    'admin_tours_categories' => array(
        'route' => 'admin/tourcategories',
        'class' => 'AdminTourCategories'
    ),
    'admin_tours_categories_form' => array(
        'route' => 'admin/tourcategories/form',
        'class' => 'AdminTourCategories/form'
    ),
    'admin_tours_categories_delete' => array(
        'route' => 'admin/tourcategories/delete/{id}',
        'class' => 'AdminTourCategories/delete/$1'
    ),
    'admin_blog_index' => array(
        'route' => 'admin/blog',
        'class' => 'AdminBlog'
    ),
    'admin_blog_form' => array(
            'route' => 'admin/blog/form',
            'class' => 'AdminBlog/form'
    ),
    'admin_blog_delete' => array(
            'route' => 'admin/blog/delete/{id}',
            'class' => 'AdminBlog/delete/$1'
    ),
    'admin_blog_image_delete' => array(
            'route' => 'admin/blog/delete_image/{id}/{img_name}',
            'class' => 'AdminBlog/delete_image/$1/$2'
    ),
    'admin_blog_categories' => array(
        'route' => 'admin/blogcategories',
        'class' => 'AdminBlogCategories'
    ),
    'admin_blog_categories_detail' => array(
        'route' => 'admin/blogcategories/detail/{id}',
        'class' => 'AdminBlogCategories/detail/$1'
    ),
    'admin_blog_categories_form' => array(
        'route' => 'admin/blogcategories/form',
        'class' => 'AdminBlogCategories/form'
    ),
    'admin_blog_categories_delete' => array(
        'route' => 'admin/blogcategories/delete/{id}',
        'class' => 'AdminBlogCategories/delete/$1'
    ),
    'admin_gallery' => array(
        'route' => 'admin/gallery',
        'class' => 'AdminGallery'
    ),
    'admin_gallery_form' => array(
            'route' => 'admin/gallery/form',
            'class' => 'AdminGallery/form'
    ),
    'admin_gallery_delete' => array(
            'route' => 'admin/gallery/delete/{id}',
            'class' => 'AdminGallery/delete/$1'
    ),
    'admin_gallery_image_delete' => array(
            'route' => 'admin/gallery/delete_image/{id}/{img_name}',
            'class' => 'AdminGallery/delete_image/$1/$2'
    ),
    'admin_gallery_categories' => array(
        'route' => 'admin/gallerycategories',
        'class' => 'AdminGalleryCategories'
    ),
    'admin_gallery_categories_form' => array(
            'route' => 'admin/gallerycategories/form',
            'class' => 'AdminGalleryCategories/form'
    ),
    'admin_gallery_categories_delete' => array(
            'route' => 'admin/gallerycategories/delete/{id}',
            'class' => 'AdminGalleryCategories/delete/$1'
    ),
    'admin_contact' => array(
        'route' => 'admin/contact',
        'class' => 'AdminContact'
    ),
    'admin_contact_information_form' => array(
        'route' => 'admin/contact/form',
        'class' => 'AdminContact/form'
    ),
    'admin_information' => array(
        'route' => 'admin/information',
        'class' => 'AdminInformation'
    ),
    # - END ADMIN    
);