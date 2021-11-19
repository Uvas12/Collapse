<?php

$wgExtensionCredits['parserhook'][] = array(
  'name' => 'Collapse',
  'version' => '1',
  'author' => 'La pluma azul',
  'url' => 'http://www.mediawiki.org/wiki/Extension:Collapse',
  'description' => 'Permite que el contenido sea despegable.'
);

$mDefaultArgs =
        array(  'status' => 'hide',
                'showtext' => '[mostrar contenido]',
                'hidetext' => '[ocultar contenido]',
                'linkstyle' => 'font-size:smaller' );
$mCount = 0;

$wgExtensionFunctions[] = 'efCollapseSetup';
$wgHooks['BeforePageDisplay'][] = 'efCollapseAddScript';

function efCollapseSetup() {
	    global $wgParser;
        $wgParser->setHook( 'collapse', 'efCollapseRender' );

}

function efCollapseAddScript( &$output ) {
        $script = <<<EOD
<script type="text/javascript">
function toggleDisplay( id, hidetext, showtext ) {
        link = document.getElementById( id + "l" ).childNodes[0];
        with( document.getElementById( id ).style ) {
                if( display == "none" ) {
                        display = "inline";
                        link.nodeValue = hidetext;
                } else {
                        display = "none";
                        link.nodeValue = showtext;
                }
        }
}
</script>
EOD;
        $output->addScript( $script );
        return true;
}

function efCollapseRender( $input, $args, $parser, $frame )
{
        global $mCount;
        global $mDefaultArgs;

        $mCount++;
        $id = 'toggledisplay' . $mCount;
        $linkid = $id . 'l';

        extract( array_merge( $mDefaultArgs, $args ) );
        $hidetext = $parser->recursiveTagParse( htmlspecialchars( $hidetext ), $frame );
        $showtext = $parser->recursiveTagParse( htmlspecialchars( $showtext ), $frame );

        if( $status == 'hide' ) {
                $display = 'none';
                $linktext = $showtext;
        } else {
                $display = 'inline';
                $linktext = $hidetext;
        }

        $result = ''; 
$result .= <<<EOD
<a id='$linkid' href='javascript:toggleDisplay( "$id", "$hidetext", "$showtext" )' style='$linkstyle'>$linktext</a>
EOD;
        $result .= '<div id="' . $id . '" style="display:' . $display . ';">'; 
        $result .= $parser->recursiveTagParse( $input, $frame );
        $result .= '</div>';

        return $result;
}
