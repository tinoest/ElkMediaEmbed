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
        // And here we insert the new value after code
        $bbc_tags['row2'][3] = elk_array_insert($where, 'link', array('media'), 'after', false);

        addInlineJavascript('
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
            );

            $.sceditor.plugins.bbcode.bbcode
                .set("media", {
                    tags: {
                        marquee: null,
                    },
                    isInline: false,
                    format: function(element, content) {
                        // Coming from wysiwyg (html) mode to bbc (text) mode
                        return "[media]" + content + "[/media]";
                    },
                    html: function(element, attrs, content) {
                        // Going to wysiwyg from bbc
                        return "<media>" + content.replace("[", "&#91;") + "</media>";
                    }
                }
            );'
        );


        // Image to display
        $context['html_headers'] .= '<style>.sceditor-button-media div {background: url(data:image/gif;base64,R0lGODlhFwAWAJECAAAAAP///////////yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJCgACACwAAAAAFwAWAAACL5SPqcvtD6OcVIJ7sNWCJ6BcmLiAIUl+4tqBbleW7xurLR2N46SZZgUMCofEIrEAACH5BAkKAAIALAAAAAAXABYAAAIwlI+py+0Po5w0gnsukDpjtSXaNoJLWSIjSgpkqJ7te4Ivbd0u/HzhVwkKh8SisVgAACH5BAkKAAIALAAAAAAXABYAAAIulI+py+0Po5z0gXsukDpjtSXaNnJdCY4kKaxmC39i2mKytbrRF97VDwwKh8RhAQAh+QQJCgACACwAAAAAFwAWAAACMJSPqcvtD6OctIF7LpA646htIdeNxoaMmCh8H7O2LSpb4ko/+E2eZ14JCofEovFYAAAh+QQJCgACACwAAAAAFwAWAAACMpSPqcvtD6OcdIF7LpA646htYbJZ3Zgp2CqIZfupYgsjrwy7JLP2XAm8VYbEovGITBoKACH5BAkKAAIALAAAAAAXABYAAAIxlI+py+0Po5w0gXsukDrjqG2hxYxj1mCi8LEnorIunIrqRi+3zeE+XgkKh8Si8agoAAAh+QQJCgACACwAAAAAFwAWAAACMpSPqcvtD6OcFAFwLpY3d95pW9Zogmg52HqOopmynzEua1ifZXtP25+rCIfEovGITBQAACH5BAkKAAIALAAAAAAXABYAAAIqlI+py+0Po5xUgVvPBVlsjoDPF0ocZnzkcopehKEaFLtmanf6zvf+nykAACH5BAkKAAIALAAAAAAXABYAAAIslI+py+0Po5w0AQDxuplxpIFeKJCHeYbXupajqKDGd8bvI5c5XPX+DwwKhwUAIfkECQoAAgAsAAAAABcAFgAAAiiUj6nL7Q+jnLQKgK3BIPbEfY2IhE5oZuSybl2rtKo2W5mG5/rO908BACH5BAUKAAIALAAAAAAXABYAAAItlI+py+0Po5yUglvPBZGnzXmLiIACWYLbGZ4NamDtq8iYFd7TDWf+DwwKh5ACADs=);}</style>';

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
