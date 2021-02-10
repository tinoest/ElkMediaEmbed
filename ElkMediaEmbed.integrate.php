<?php

class ElkMediaEmbed
{
    static public function integrate_load_theme() {{{
        //loadCSSFile('mediaEmbed.css');

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
        ');

    }}}

    static public function integrate_bbc_codes( &$codes ) {{{
        global $modSettings;

        if (empty($modSettings['enableBBC'])) {
            return;
        }

        if (!empty($modSettings['disabledBBC'])) {
            foreach (explode(',', $modSettings['disabledBBC']) as $tag) {
                if ($tag === 'media') {
                    return;
                }
            }
        }

        $codes[] = array(
            'tag'               => 'media',
            'before'            => '<media>',
            'after'             => '</media>',
            'block_level'       => true,
            'disallow_children' => array('media'),
        );

    }}}

    static public function integrate_additional_bbc( &$codes ) {{{
        global $modSettings;

        if (empty($modSettings['enableBBC'])) {
            return;
        }

        $codes[] = array(
            \BBC\Codes::ATTR_TAG                => 'media',
            \BBC\Codes::ATTR_TYPE               => \BBC\Codes::TYPE_UNPARSED_EQUALS_CONTENT,
            \BBC\Codes::ATTR_CONTENT            => '$1',
            \BBC\Codes::ATTR_VALIDATE           => function( &$tag, &$data, $disabled ) {
                self::parseContent($data);
            },
            \BBC\Codes::ATTR_DISALLOW_CHILDREN  => array('media'),
            \BBC\Codes::ATTR_BLOCK_LEVEL        => false,
            \BBC\Codes::ATTR_AUTOLINK           => false,
            \BBC\Codes::ATTR_LENGTH             => 5,
        );

    }}}

    static public function integrate_bbc_buttons(&$bbc_tags) {{{
        global $context;

        $where = $bbc_tags['row2'][3];
        // And here we insert the new value after email
        $bbc_tags['row2'][3] = elk_array_insert($where, 'email', array('media'), 'after', false);

        \theme()->addInlineJavascript('
            $.sceditor.command
                .set("media", {
                    // Show the button on/off state
                    state: function() {
                        var currentNode = this.currentNode();

                        return $(currentNode).is("media") || $(currentNode).parents("media").length > 0 ? 1 : 0;
                    },
                    exec: function () {
                        this.insert("[media] ", "[/media]", false)
                    },
                    txtExec: ["[media]", "[/media]"],
                    tooltip: "Media"
                }
            );'
        );


        // Image to display
        $context['html_headers'] .= '<style>.sceditor-button-media div {background: url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAaCAYAAACzdqxAAAAAQklEQVR42u2UMQoAMAgDfbo/bx1aEKdamqVc4BaHG4LEIkOBVhyxzTp6h+rQizO34swzMR3T8ZmYreCP6ZiO/9yKCRYcUkNRmtuZAAAAAElFTkSuQmCC\');}</style>';

    }}}

    static public function parseContent(&$data) {{{
        if(isset($data[1])) { 
            switch($data[1]) {
                case 'youtube':
                    $data[0] = '<div class="mediacontainer"><iframe allowfullscreen src="https://www.youtube.com/embed/'.$data[0].'?wmode=opaque" data-youtube-id="'.$data[0].'"></iframe></div>&nbsp;';
                    break;
                default:
                    break;
            }
        }

        return;
    }}}

}
