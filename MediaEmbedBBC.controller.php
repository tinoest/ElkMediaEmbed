<?php
if (!defined('ELK')) {
	die('Hacking attempt...');
}

class MediaEmbedBBC_Controller extends Action_Controller {

    public function action_index() {{{
        // We really only have one job to do..
        $this->action_admin();

    }}}

    public function action_admin() {{{
        global $context, $scripturl, $txt;

        loadLanguage('MediaEmbedBBC');

        isAllowedTo('media_embed_manage');

        $context['page_title'] = $txt['media-embed-title'];

		require_once(SUBSDIR . '/GenericList.class.php');

        $listOptions = array(
            'id' => 'media_bbc_list',
            'items_per_page'    => 30,
            'base_href'         => $scripturl . '?action=MediaEmbedBBC;',
            'default_sort_col'  => 'site',
            'get_items' => array(
				'function' => array($this, 'list_get_media_bbc'),
            ),
            'get_count' => array(
				'function' => array($this, 'list_get_num_media_bbc'),
            ),
            'columns' => array(
                'site' => array(
                    'header' => array(
                        'value'     => $txt['media-embed-site'],
                    ),
                    'data' => array(
                        'db'        => 'site',
                        'style'     => 'width: 10%;',
                    ),
                    'sort' => array(
                        'default'   => 'site',
                        'reverse'   => 'site DESC',
                    ),
                ),
                'url_match' => array(
                    'header' => array(
                        'value' => $txt['media-embed-url'],
                    ),
                    'data' => array(
                        'db'        => 'url_match',
                        'style'     => 'width: 25%;',
                    ),
                    'sort' =>  array(
                        'default'   => 'url_match',
                        'reverse'   => 'url_match DESC',
                    ),
                ),
                'bbc_replace' => array(
                    'header' => array(
                        'value' => $txt['media-embed-bbc'],
                    ),
                    'data' => array(
                        'db'        => 'bbc_replace',
                        'style'     => 'width: 5%;',
                    ),
                    'sort' =>  array(
                        'default'   => 'bbc_replace',
                        'reverse'   => 'bbc_replace DESC',
                    ),
                ),
                'bbc_match' => array(
                    'header' => array(
                        'value' => $txt['media-embed-match'],
                    ),
                    'data' => array(
                        'db'        => 'bbc_match',
                        'style'     => 'width: 15%;',
                    ),
                    'sort' =>  array(
                        'default'   => 'bbc_match',
                        'reverse'   => 'bbc_match DESC',
                    ),
                ),
                'html_replace' => array(
                    'header' => array(
                        'value' => $txt['media-embed-html'],
                    ),
                    'data' => array(
                        'function'  => function($rows) {
                            return '<pre>'.htmlspecialchars($rows['html_replace']).'</pre>';
                        },
                        'style'     => 'width: 35%; overflow: hidden;',
                    ),
                    'sort' =>  array(
                        'default'   => 'html_replace',
                        'reverse'   => 'html_replace DESC',
                    ),
                ),
				'action' => array(
					'header' => array(
						'value' => $txt['media-embed-actions'],
						'class' => 'centertext',
					),
					'data' => array(
						'sprintf' => array (
							'format' => '
								<a href="?action=admin;area=MediaEmbedBBC;sa=edit;id=%1$d;' . $context['session_var'] . '=' . $context['session_id'] . '" accesskey="p">'.$txt['media-embed-edit'].'</a>&nbsp;
								<a href="?action=admin;area=MediaEmbedBBC;sa=delete;id=%1$d;' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(' . JavaScriptEscape('Are you sure you want to delete?') . ') && submitThisOnce(this);" accesskey="d">'.$txt['media-embed-delete'].'</a>',
							'params' => array(
								'id' => true,
							),
						),
						'class' => 'centertext nowrap',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=MediaEmbedBBC;sa=add;',
				'include_sort' => true,
				'include_start' => true,
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="action_edit" value="' . $txt['media-embed-add'] . '" class="right_submit" />',
				),
			),
        );

        createList($listOptions);

        $context['sub_template'] = 'show_list';
        $context['default_list'] = 'media_bbc_list';
    }}}

    public function list_get_media_bbc($start, $items_per_page, $sort) {{{
        $db = database();

        $request = $db->query('', '
            SELECT *
            FROM {db_prefix}media_embed
            WHERE 1=1
            ORDER BY {raw:sort}
            LIMIT {int:start}, {int:per_page}',
            array(
                'sort'              => $sort,
                'start'             => $start,
                'per_page'          => $items_per_page,
                'regular_id_group'  => 0,
            )
        );

        $rows = array();
        while ($row = $db->fetch_assoc($request)) {
            $rows[] = $row;
        }
        $db->free_result($request);

        return $rows;
    }}}

    public function list_get_num_media_bbc() {{{

        $db = database();

        $request = $db->query('', '
            SELECT COUNT(*)
            FROM {db_prefix}media_embed
            WHERE 1=1',
            array()
        );
        list ($num_rows) = $db->fetch_row($request);
        $db->free_result($request);

        return $num_rows;
    }}}

}

?>
