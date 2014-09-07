<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

if (false == file_exists( PHPFOX_DIR_MODULE . 'rhetinizr/headquarters/vendor/autoload.php' )) {
	return;
}

include PHPFOX_DIR_MODULE . 'rhetinizr/headquarters/vendor/autoload.php';

//function convert( $size )
//{
//  $unit = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );
//
//  return @round( $size / pow( 1024, ( $i = floor( log( $size, 1024 ) ) ) ), 2 ) . ' ' . $unit[$i];
//}
//
//$a = memory_get_usage( true );

if (PHPFOX_DEBUG === true) {
	Rhetina::cacheClear();
	Rhetina::boot( 'dev', true );
} else {
	Rhetina::boot( 'prod', false );
}
//$b = memory_get_usage( true );
//echo convert( $b - $a );