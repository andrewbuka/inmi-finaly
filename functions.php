<?php 
    add_action( 'wp_enqueue_scripts', function() {
        
        //  if( is_page( 96 )  || is_page( 152)){
        //     wp_enqueue_style( 'not-main', get_template_directory_uri() . '/assets/styles/not-main.css');
        // }

        
        // wp_enqueue_style( 'bootstrap', get_template_directory_uri() . 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css');
        // wp_enqueue_style( 'font-popins', get_template_directory_uri() . 'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap');
        // wp_enqueue_style( 'font-awesome.min', get_template_directory_uri() . 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
       
        
       
        
        
        wp_enqueue_style( 'style', get_template_directory_uri() . '/assets/css/style.css');
        wp_enqueue_style( 'my-styles', get_template_directory_uri() . '/assets/css/my-styles.css');
        wp_enqueue_style( 'yur-styles', get_template_directory_uri() . '/assets/css/yur-styles.css');


        wp_enqueue_style( 'nice-select', get_template_directory_uri() . '/assets/css/nice-select.css');

        wp_enqueue_style( 'font-awesome.min', get_template_directory_uri() . '/assets/css/font-awesome.min.css');

        wp_enqueue_style( 'bootstrap-grid', get_template_directory_uri() . '/assets/css/bootstrap-grid.css');

        wp_enqueue_style( 'slick.min', get_template_directory_uri() . '/assets/css/slick.min.css');
        wp_enqueue_style( 'basket', get_template_directory_uri() . '/assets/css/basket.css');
       
        
        wp_enqueue_style( 'animate', get_template_directory_uri() . '/assets/css/animate.css');


 

       


        wp_deregister_script( 'jquery' );
	    wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js');
	    wp_enqueue_script( 'jquery' );


        // wp_enqueue_script( 'jquery', get_template_directory_uri() . '/assets/js/jquery.js', array('jquery'), 'null', true );
        wp_enqueue_script( 'jquery-2.2.4.min', get_template_directory_uri() . '/assets/js/jquery-2.2.4.min.js', array('jquery'), 'null', true );


        wp_enqueue_script( 'slick.min', get_template_directory_uri() . '/assets/js/slick.min.js', array(), 'null', true );

   

        wp_enqueue_script( 'jquery.fancybox', get_template_directory_uri() . '/assets/js/jquery.fancybox.js', array('jquery'), 'null', true );

        wp_enqueue_script( 'jquery.nice-select', get_template_directory_uri() . '/assets/js/jquery.nice-select.js', array('jquery'), 'null', true );
        
        
        wp_enqueue_script( 'wow', get_template_directory_uri() . '/assets/js/wow.js', array(), 'null', true );


        
        wp_enqueue_script( 'my-js', get_template_directory_uri() . '/assets/js/my-js.js', array(), 'null', true );

        wp_enqueue_script( 'lazyload.min', get_template_directory_uri() . '/assets/js/lazyload.min.js', array(), 'null', true );
    wp_enqueue_script( 'domHelper', get_template_directory_uri() . '/assets/js/helpers/domHelper.js', array(), 'null', true );

        wp_enqueue_script( 'fizProductData', get_template_directory_uri() . '/assets/js/data/fizProductData.js', array(), 'null', true );
         wp_enqueue_script( 'basket', get_template_directory_uri() . '/assets/js/basket.js', array(), 'null', true );
         wp_localize_script( 'basket', 'inmiBasketOrder', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'inmi_send_order' ),
        ) );


        wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/scripts.js', array(), 'null', true );




        
        
       
        
       

        
        
        
        
        });

        
    

        


    add_theme_support( 'post-thumbnails');
    add_theme_support( 'custom-logo');
    add_theme_support( 'title-tag');





add_filter( 'upload_mimes', 'svg_upload_allow' );

# Добавляет SVG в список разрешенных для загрузки файлов.
function svg_upload_allow( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';

	return $mimes;
}




add_action( 'wp_ajax_inmi_send_order', 'inmi_send_order' );
add_action( 'wp_ajax_nopriv_inmi_send_order', 'inmi_send_order' );

function inmi_send_order() {
    check_ajax_referer( 'inmi_send_order', 'nonce' );

    $firstname = sanitize_text_field( wp_unslash( $_POST['firstname'] ?? '' ) );
    $email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $telephone = sanitize_text_field( wp_unslash( $_POST['telephone'] ?? '' ) );
    $shipping = sanitize_text_field( wp_unslash( $_POST['shipping'] ?? '' ) );
    $payment = sanitize_text_field( wp_unslash( $_POST['payment'] ?? '' ) );
    $address = sanitize_text_field( wp_unslash( $_POST['address'] ?? '' ) );
    $comment = sanitize_textarea_field( wp_unslash( $_POST['comment'] ?? '' ) );
    $total = sanitize_text_field( wp_unslash( $_POST['total'] ?? '' ) );
    $products_json = wp_unslash( $_POST['products'] ?? '[]' );
    $products = json_decode( $products_json, true );

    if ( ! is_array( $products ) ) {
        $products = array();
    }

    if ( empty( $firstname ) || empty( $email ) || empty( $telephone ) || empty( $products ) ) {
        wp_send_json_error( array( 'message' => 'Заполните обязательные поля и проверьте корзину.' ), 400 );
    }

    $message_lines = array(
        'Новый заказ с сайта ' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
        '',
        'Покупатель:',
        'ФИО: ' . $firstname,
        'Email: ' . $email,
        'Телефон: ' . $telephone,
        '',
        'Доставка и оплата:',
        'Способ доставки: ' . $shipping,
    );

    if ( ! empty( $address ) ) {
        $message_lines[] = 'Адрес доставки: ' . $address;
    }

    $message_lines[] = 'Способ оплаты: ' . $payment;

    if ( ! empty( $comment ) ) {
        $message_lines[] = 'Комментарий: ' . $comment;
    }

    $message_lines[] = '';
    $message_lines[] = 'Состав заказа:';

    foreach ( $products as $index => $product ) {
        $title = sanitize_text_field( $product['title'] ?? '' );
        $count = absint( $product['count'] ?? 0 );
        $price = (float) ( $product['price'] ?? 0 );
        $line_total = $price * $count;

        if ( empty( $title ) || $count < 1 ) {
            continue;
        }

        $message_lines[] = sprintf(
            '%d. %s — %d шт. × %s BYN = %s BYN',
            $index + 1,
            $title,
            $count,
            number_format( $price, 2, '.', ' ' ),
            number_format( $line_total, 2, '.', ' ' )
        );
    }

    $message_lines[] = '';
    $message_lines[] = 'Итого: ' . $total . ' BYN';

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: INMI Order <andrey.buko@mail.ru>',
        'Reply-To: ' . $firstname . ' <' . $email . '>',
    );

    $sent = wp_mail(
        'andrew.buka90@gmail.com',
        'Новый заказ с сайта INMI',
        implode( "\n", $message_lines ),
        $headers
    );

    if ( ! $sent ) {
        wp_send_json_error( array( 'message' => 'Не удалось отправить заказ. Попробуйте позже.' ), 500 );
    }

    wp_send_json_success( array( 'message' => 'Спасибо! Ваш заказ успешно отправлен.' ) );
}



/**
 * Build SEO metadata for all theme pages from page context and product specificity.
 */
function inmi_get_seo_meta() {
    static $meta = null;

    if ( null !== $meta ) {
        return $meta;
    }

    $site_name = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'name' ) : 'INMI';
    if ( ! $site_name || 'SugarSite' === $site_name ) {
        $site_name = 'INMI';
    }

    $title = isset( $GLOBALS['inmi_custom_title'] ) ? $GLOBALS['inmi_custom_title'] : '';
    $description = isset( $GLOBALS['inmi_custom_description'] ) ? $GLOBALS['inmi_custom_description'] : '';
    $keywords = isset( $GLOBALS['inmi_custom_keywords'] ) ? $GLOBALS['inmi_custom_keywords'] : '';

    $page_title = function_exists( 'get_the_title' ) ? wp_strip_all_tags( get_the_title() ) : '';
    $template = function_exists( 'get_page_template_slug' ) ? (string) get_page_template_slug() : '';
    $content = function_exists( 'get_post_field' ) ? wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', get_the_ID() ) ) ) : '';
    $acf_description = '';

    if ( function_exists( 'get_field' ) ) {
        $acf_description = wp_strip_all_tags( (string) get_field( 'description' ) );
    }

    $is_product_template = ( false !== strpos( $template, '-yur.php' ) || false !== strpos( $template, '-fiz.php' ) || false !== strpos( $template, '-org.php' ) );

    if ( ! $title ) {
        if ( function_exists( 'is_front_page' ) && is_front_page() ) {
            $title = 'INMI — биопрепараты и микробиологические решения для дома, агро и бизнеса';
        } elseif ( false !== strpos( $template, 'yur-page.php' ) ) {
            $title = 'Биопрепараты для юридических лиц — каталог B2B | INMI';
        } elseif ( false !== strpos( $template, 'basket.php' ) ) {
            $title = 'Оформление заказа биопрепаратов | INMI';
        } elseif ( false !== strpos( $template, 'payment.php' ) ) {
            $title = 'Оплата и доставка биопрепаратов | INMI';
        } elseif ( false !== strpos( $template, 'how-buing.php' ) ) {
            $title = 'Как купить биопрепараты INMI';
        } elseif ( false !== strpos( $template, 'requisites.php' ) ) {
            $title = 'Реквизиты INMI';
        } elseif ( $is_product_template && $page_title ) {
            $title = $page_title . ' — биопрепарат: описание, применение и инструкция | INMI';
        } elseif ( $page_title ) {
            $title = $page_title . ' | INMI';
        } else {
            $title = 'INMI — биопрепараты и микробиологические решения';
        }
    }

    if ( ! $description ) {
        if ( $is_product_template && $page_title ) {
            $source = $acf_description ? $acf_description : $content;
            $description = $page_title . ' от INMI: назначение, характеристики, форма выпуска, инструкция по применению и возможность заказать препарат.';
            if ( $source ) {
                $description = $page_title . ' от INMI. ' . $source;
            }
        } elseif ( false !== strpos( $template, 'yur-page.php' ) ) {
            $description = 'Каталог биопрепаратов INMI для юридических лиц: биоконсерванты, очистка воды, кормовые добавки, микробные удобрения и решения для рыбоводства.';
        } elseif ( function_exists( 'is_front_page' ) && is_front_page() ) {
            $description = 'INMI разрабатывает и предлагает биопрепараты для септиков, очистки воды, растениеводства, силосования, кормов и промышленных задач.';
        } elseif ( false !== strpos( $template, 'basket.php' ) ) {
            $description = 'Проверьте выбранные биопрепараты INMI, укажите контактные данные и оформите заказ через корзину сайта.';
        } elseif ( false !== strpos( $template, 'payment.php' ) ) {
            $description = 'Информация об оплате и получении биопрепаратов INMI для физических и юридических лиц.';
        } elseif ( false !== strpos( $template, 'how-buing.php' ) ) {
            $description = 'Пошаговая инструкция по выбору, заказу и покупке биопрепаратов INMI на сайте.';
        } elseif ( false !== strpos( $template, 'requisites.php' ) ) {
            $description = 'Юридические и платежные реквизиты INMI для оформления документов и оплаты заказов.';
        } else {
            $description = $content ? $content : 'Биопрепараты и микробиологические решения INMI для бытовых, сельскохозяйственных и промышленных задач.';
        }
    }

    $description = function_exists( 'wp_html_excerpt' ) ? wp_html_excerpt( trim( preg_replace( '/\s+/', ' ', $description ) ), 160, '...' ) : substr( $description, 0, 160 );

    if ( ! $keywords ) {
        $base_keywords = [ 'INMI', 'биопрепараты', 'микробиологические препараты' ];
        if ( $page_title ) {
            array_unshift( $base_keywords, $page_title );
        }
        if ( $is_product_template ) {
            $base_keywords[] = 'инструкция по применению';
            $base_keywords[] = 'купить биопрепарат';
        }
        $keywords = implode( ', ', array_unique( $base_keywords ) );
    }

    $canonical = function_exists( 'wp_get_canonical_url' ) ? wp_get_canonical_url() : '';
    if ( ! $canonical && function_exists( 'get_permalink' ) ) {
        $canonical = get_permalink();
    }

    $meta = [
        'title'       => $title,
        'description' => $description,
        'keywords'    => $keywords,
        'canonical'   => $canonical,
        'site_name'   => $site_name,
        'type'        => $is_product_template ? 'product' : 'website',
    ];

    return $meta;
}

function inmi_render_seo_meta() {
    $meta = inmi_get_seo_meta();
    ?>
	<meta name="keywords" content="<?php echo esc_attr( $meta['keywords'] ); ?>">
	<meta name="description" content="<?php echo esc_attr( $meta['description'] ); ?>">
	<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
	<?php if ( ! empty( $meta['canonical'] ) ) : ?>
	<link rel="canonical" href="<?php echo esc_url( $meta['canonical'] ); ?>">
	<?php endif; ?>
	<meta property="og:locale" content="ru_RU">
	<meta property="og:type" content="<?php echo esc_attr( $meta['type'] ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( $meta['site_name'] ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $meta['title'] ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $meta['description'] ); ?>">
	<?php if ( ! empty( $meta['canonical'] ) ) : ?>
	<meta property="og:url" content="<?php echo esc_url( $meta['canonical'] ); ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $meta['title'] ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $meta['description'] ); ?>">
	<script type="application/ld+json"><?php echo wp_json_encode( [ '@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => $meta['title'], 'description' => $meta['description'], 'url' => $meta['canonical'] ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
    <?php
}

