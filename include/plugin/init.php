<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

include PHPFOX_DIR_MODULE . 'rhetinizr/headquarters/vendor/autoload.php';

if (PHPFOX_DEBUG === true) {

} else {

}

//function convert( $size )
//{
//  $unit = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );
//
//  return @round( $size / pow( 1024, ( $i = floor( log( $size, 1024 ) ) ) ), 2 ) . ' ' . $unit[$i];
//}

//$a = memory_get_usage( true )
Rhetina::cacheClear();
Rhetina::boot( 'dev', true );
//$b = memory_get_usage( true );
//echo convert( $b - $a );