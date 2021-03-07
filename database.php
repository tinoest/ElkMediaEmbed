<?php
/**
 * @package ElkMediaEmbed
 * @version 1.0.0
 * @author tinoest https://tinoest.co.uk
 * @license BSD 3.0 http://opensource.org/licenses/BSD-3-Clause/
 *
 *
 */

define('TP_MINIMUM_PHP_VERSION', '7.0.0');

global $db_prefix, $package_log, $db_type;


if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('ELK')) {
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('ELK')) {
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as ElkArte\'s index.php.');
}

if ((!function_exists('version_compare') || version_compare(TP_MINIMUM_PHP_VERSION, PHP_VERSION, '>='))) {
	die('<strong>Install Error:</strong> - please install a version of php greater than '.TP_MINIMUM_PHP_VERSION);
}

if($db_type == 'mysql') {
    $type = array('null' => true );
}
else {
    $type = array('default' => '' );
}

$tables = array(
    'media_embed' => array(
        'columns' => array(
            array('name' => 'site',         'type' => 'varchar',    'size' => 250)  + $type,
            array('name' => 'match',        'type' => 'mediumtext') + $type,
            array('name' => 'bbc_replace',  'type' => 'mediumtext') + $type,
            array('name' => 'bbc_match',    'type' => 'mediumtext') + $type,
            array('name' => 'html_replace', 'type' => 'mediumtext') + $type,
        ),
        'indexes' => array(
            array('type' => 'unique', 'columns' => array('site'),),
        ),
    ),
);

$db_table   = db_table();

// Create the tables, if they don't already exist
foreach ($tables as $table => $data) {
    $db_table->db_create_table('{db_prefix}' . $table, $data['columns'], $data['indexes'], array(), 'ignore');
}

addDefaults();

function addDefaults() {{{
    $db = database();

	// Check for blocks in table, if none insert default blocks.
	$request = $db->query('', '
		SELECT * FROM {db_prefix}media_embed LIMIT 1'
	);

	if ($db->num_rows($request) < 1) {
 
        $data = array (
            'site'          => 'youtube',
            'match'         => '',
            'bbc_replace'   => '',
            'bbc_match'     => '[a-zA-Z0-9]+',
            'html_replace'  => '<div class="mediacontainer"><iframe allowfullscreen src="https://www.youtube.com/embed/$0?wmode=opaque" data-youtube-id="$0"></iframe></div>&nbsp;'
        );

        $db->insert('ignore', '{db_prefix}media_embed', array( 'site' => 'string', 'match' => 'string', 'bbc_replace' => 'string', 'bbc_match' => 'string', 'html_replace' => 'string' ), $data, array('site'));
    }

}}}

?>
