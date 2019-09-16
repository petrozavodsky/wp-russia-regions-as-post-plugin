<?php add_action('init', 'region_post_init'); // Использовать функцию только внутри хука init
function region_post_init() {
	$labels = array(
		'name' => 'Регионы',
		'singular_name' => 'Регион', // админ панель Добавить->Функцию
		'add_new' => 'Добавить регион',
		'add_new_item' => 'Добавить регион', // заголовок тега <title>
		'edit_item' => 'Редактировать регион',
		'new_item' => 'Новый регион',
		'all_items' => 'Все регионы',
		'view_item' => 'Просмотр на сайте',
		'search_items' => 'Искать регион',
		'not_found' =>  'Нет ничего',
		'not_found_in_trash' => 'Нет ничего',
		'menu_name' => 'Регионы' // ссылка в меню в админке
	);
	$args = array(
		'labels' => $labels,
        'description' => 'Субъекты РФ',
		'public' => true,
		'show_ui' => true, // показывать интерфейс в админке
		'show_in_rest' => true,
		'exclude_from_search' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'capability_type' => 'page',
		'menu_icon' => 'dashicons-location-alt', // иконка в меню
		'menu_position' => 30, // порядок в меню
		'supports' => array('title', 'editor', 'page-attributes'),
		'rewrite' => true,
        'show_admin_column' => true,
		'query_var' => true,
		'can_export' => true
	);
	register_post_type('region', $args);
}

/* ==============================================
	Добавляем мета к этому типу записи
================================================= */
add_action('add_meta_boxes', 'metakey_region_fields', 1);

function metakey_region_fields() {
	add_meta_box( 'metabox', 'Параметры', 'ext_region_fields_box_func', 'region', 'normal', 'high');
}
// код блока
function ext_region_fields_box_func( $post ){
?>
<input class="hidden" id="regioncode"
       name="ext_region[regioncode]"
       type="number"
       required
       value="<?php echo esc_attr(get_post_meta($post->ID, 'regioncode', true)); ?>"
       />
<input type="hidden" name="ext_region_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
<?php }

// включаем обновление полей при сохранении
add_action('save_post', 'metakey_region_fields_update', 0);
/* Сохраняем данные, при сохранении поста */
function metakey_region_fields_update( $post_id ){
	if ( !wp_verify_nonce($_POST['ext_region_fields_nonce'], __FILE__) ) return false; // проверка
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // если это автосохранение
	if ( !current_user_can('edit_post', $post_id) ) return false; // если юзер не имеет право редактировать запись

	if( !isset($_POST['ext_region']) ) return false;

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$_POST['ext_region'] = array_map('trim', $_POST['ext_region']);
	foreach( $_POST['ext_region'] as $key=>$value ){
		if( empty($value) ){
			delete_post_meta($post_id, $key); // удаляем поле если значение пустое
			continue;
		}

        if ($key) {
            update_post_meta($post_id, $key, intval($value)); // Приводим номер региона к числу
        } else {
            // А тут оставнлю на случай если может еще что то захочется сохранить
            // update_post_meta($post_id, $key, $value);
            // о валидации $value в этой секции надо позаботиться отдельно
         }
	}
	return $post_id;
}
?>
