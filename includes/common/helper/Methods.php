<?php

namespace OrderNotificationForTelegramBot\Common\Helper;

class Methods {
	public static function convertToCamelCase( $method_name ): string {
		$method_name_explode = explode( '_', $method_name );
		$camelCaseMethodName = "";
		foreach ( $method_name_explode as $key => $part ) {
			if ( $key == 0 ) {
				$camelCaseMethodName .= $part;
			} else {
				$camelCaseMethodName .= ucfirst( $part );
			}
		}

		return $camelCaseMethodName;
	}

	public static function generateMethodName( $name ): string {
		return hex2bin( base64_decode( 'Njc2NTc0NWY=' ) ) . $name;
	}

}