<?php
/**
 * Contact: lander931@mail.ru
 */
if (!defined('DATALIFEENGINE')) {
    die("Hacking attempt!");
}

/** Получаем стек категорий
 * @param $category_id
 * @param $category_list
 * @param null $stack
 * @return array|null
 */
function getStackCategory($category_id, $category_list, &$stack = null)
{
    $category = $category_list[$category_id];
    $stack[] = [
        'uri' => get_url($category_id),
        'name' => $category['name']
    ];
    if ($category_list[$category_id]['parentid'] > 0) {
        return getStackCategory($category_list[$category['parentid']]['id'], $category_list, $stack);
    }
    return $stack;
}


/**
 * @var string $category_id
 * @var array $config
 */

if ($category_id){
    // Получаем из кеша список категорий
    $cat_info = get_vars("category");
    $stack = array_reverse(getStackCategory($category_id, $cat_info));

    $i = 0;
    $markup_items = '';
    foreach ($stack as $item) {
        $i++;
        $markup_items .= ',{"@type": "ListItem","position": ' . $i . ',"item": {"@id": "' . $config['http_home_url'] . $item['uri'] . '/' . '","name": "' . $item['name'] . '"}}';
    }

// Если это страница поста, то добавляем итем
    if (isset($_GET['newsid'])) {
        $markup_items .= ',{"@type": "ListItem","position": ' . ++$i . ',"item": {"@id": "' . $GLOBALS['full_link'] . '","name": "' . $GLOBALS['row']['title'] . '"}}';
    }

    $markup_items = substr($markup_items, 1);
    $markup_data = '
<!--Markup Breadcrumbs -->
<script type="application/ld+json">{"@context": "http://schema.org","@type": "BreadcrumbList","itemListElement": [' . $markup_items . ']}</script>
<!--/Markup Breadcrumbs -->';

    echo $markup_data;
}