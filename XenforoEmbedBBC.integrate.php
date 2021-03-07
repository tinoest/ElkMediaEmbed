<?php

class XenforoEmbedBBC
{
    static public function integrate_load_theme() {{{

        \theme()->addCSSRules('
            .mediacontainer {
                display: inline-block;
                height: calc(40vw/1.77);
                margin: 0 auto;
                width: 40vw;
            }
            .mediacontainer iframe {
                display:block;
                height:100%;
                width:100%;
			}
			.doublePost {
				text-align: center;
				padding: 1em;
			}
        ');

    }}}

    static public function integrate_additional_bbc( &$codes ) {{{
        global $modSettings, $scripturl;

        if (empty($modSettings['enableBBC'])) {
            return;
        }

        $codes[] = array(
            \BBC\Codes::ATTR_TAG                => 'media',
            \BBC\Codes::ATTR_TYPE               => \BBC\Codes::TYPE_UNPARSED_EQUALS_CONTENT,
            \BBC\Codes::ATTR_CONTENT            => '$1',
            \BBC\Codes::ATTR_VALIDATE           => function( &$tag, &$data, $disabled ) {
                $data[0] = self::parseContent($data);
            },
            \BBC\Codes::ATTR_DISALLOW_CHILDREN  => array('media'),
            \BBC\Codes::ATTR_BLOCK_LEVEL        => false,
            \BBC\Codes::ATTR_AUTOLINK           => false,
            \BBC\Codes::ATTR_LENGTH             => 5,
    	);

		$codes[] = array(
			\BBC\Codes::ATTR_TAG 				=> 'user',
			\BBC\Codes::ATTR_TYPE 				=> \BBC\Codes::TYPE_UNPARSED_EQUALS,
			\BBC\Codes::ATTR_TEST 				=> '\d*',
			\BBC\Codes::ATTR_BEFORE 			=> '<span class="bbc_mention"><a href="' . $scripturl . '?action=profile;u=$1">@',
			\BBC\Codes::ATTR_AFTER 				=> '</a></span>',
			\BBC\Codes::ATTR_DISABLED_BEFORE 	=> '@',
			\BBC\Codes::ATTR_DISABLED_AFTER 	=> '',
			\BBC\Codes::ATTR_BLOCK_LEVEL 		=> false,
			\BBC\Codes::ATTR_AUTOLINK 			=> true,
			\BBC\Codes::ATTR_LENGTH 			=> 4,
		);

		$codes[] = array(
			\BBC\Codes::ATTR_TAG 				=> 'doublepost',
			\BBC\Codes::ATTR_TYPE 				=> \BBC\Codes::TYPE_UNPARSED_EQUALS,
			\BBC\Codes::ATTR_TEST 				=> '\d*',
			\BBC\Codes::ATTR_BEFORE 			=> '<div class="doublePost">--- Double Post ---',
			\BBC\Codes::ATTR_AFTER 				=> '</div>',
			\BBC\Codes::ATTR_DISABLED_BEFORE 	=> '@',
			\BBC\Codes::ATTR_DISABLED_AFTER 	=> '',
			\BBC\Codes::ATTR_BLOCK_LEVEL 		=> false,
			\BBC\Codes::ATTR_AUTOLINK 			=> true,
			\BBC\Codes::ATTR_LENGTH 			=> 10,
		);
    }}}

    static public function parseContent(&$data) {{{

        $db = database();

        $result = $db->query('', 'SELECT bbc_match, html_replace FROM {db_prefix}media_embed WHERE site = {string:site}', array ('site' => $data[1]));
        if($db->num_rows($result) > 0) {
            $replace    = $db->fetch_assoc($result);
            $result     = preg_replace('/'.$replace['bbc_match'].'/', $replace['html_replace'], $data[0]);
            return $result;
        }

        return '';

    }}}

}
