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
        if(isset($data[1])) { 
            switch($data[1]) {
                case 'youtube':
                    return '<div class="mediacontainer"><iframe allowfullscreen src="https://www.youtube.com/embed/'.$data[0].'?wmode=opaque" data-youtube-id="'.$data[0].'"></iframe></div>&nbsp;';
					break;
				case 'gfycat':
					if(preg_match('/height=([0-9]+);id=([a-zA-Z]+);width=([0-9]+)/', $data[0], $matches)) {
						return '<div style="position:relative; padding-bottom:56.25%; max-height:'.$matches[1].'px; max-width:'.$matches[3].'px"><iframe src="//gfycat.com/ifr/'.$matches[2].'" frameborder=\'0\' scrolling=\'no\' width=\'100%\' height=\'100%\' style=\'position:absolute;top:0;left:0;\' allowfullscreen></iframe></div>';
					}
					break;
                default:
                    break;
            }
        }

        return $data[0];
    }}}

}
