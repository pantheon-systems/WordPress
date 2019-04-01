<?php

/**
 * Class WC_EBANX_Errors
 */
class WC_EBANX_Errors {
	/**
	 * The possible errors that EBANX can throw
	 *
	 * @return array An error array by user country
	 */
	public static function get_errors() {
		return array(
			'pt-br' => array(
				'GENERAL'                    => 'Não foi possível concluir a compra. Por favor, tente novamente ou entre em contato com o site.',
				'BP-DR-13'                   => 'Ei, você esqueceu de preencher o seu nome.',
				'BP-DR-14'                   => 'Epa, o seu nome está com mais caracteres do que o limite permitido. Procure abreviá-lo ou então coloque apenas um sobrenome.',
				'BP-DR-15'                   => 'Espera aí! Faltou preencher o e-mail.',
				'BP-DR-17'                   => 'Desculpa, mas o e-mail enviado é inválido.',
				'BP-DR-19'                   => 'Ei, precisamos da sua data de nascimento.',
				'BP-DR-20'                   => 'A sua data de nascimento deve estar formatada em dd/mm/aaaa.',
				'BP-DR-21'                   => 'Ei, você precisa ter mais de 16 anos para realizar essa transação.',
				'BP-DR-22'                   => 'Desculpa, mas o documento enviado é inválido.',
				'BP-DR-23'                   => 'Preencha com um documento válido:',
				'BP-DR-24'                   => 'Falta pouco! Preencha o seu CEP.',
				'BP-DR-25'                   => 'Ei, você esqueceu de informar o seu endereço.',
				'BP-DR-26'                   => 'Não se esqueça de preencher o número do seu endereço.',
				'BP-DR-27'                   => 'Ei, você esqueceu de preencher a sua cidade.',
				'BP-DR-28'                   => 'Ops, faltou preencher o Estado.',
				'BP-DR-29'                   => 'Confira uma lista com os códigos dos estados brasileiros: https://goo.gl/qCk2V.',
				'BP-DR-31'                   => 'Preencher o telefone é obrigatório, beleza?',
				'BP-DR-32'                   => 'O número de telefone informado é inválido.',
				'BP-DR-34'                   => 'Infelizmente o número de parcelas selecionado não está disponível. Tente de novo com outro número.',
				'BP-DR-39'                   => 'O documento precisa estar ativo para completar essa transação. Entre em contato com o suporte do EBANX (suporte@ebanx.com) para mais informações.',
				'BP-DR-40'                   => 'Ops, você quer fazer uma transação que ultrapassa o limite permitido pelo EBANX. Tente de novo com um valor menor.',
				'BP-DR-42'                   => 'Como a sua transação é com CNPJ, é preciso preencher o responsável.',
				'BP-DR-43'                   => 'Ei, você esqueceu de preencher o seu nome.',
				'BP-DR-44'                   => 'Ops, faltou informar o número do seu documento.',
				'BP-DR-45'                   => 'Ei, você esqueceu de preencher a sua data de nascimento.',
				'BP-DR-46'                   => 'O documento precisa estar ativo para completar essa transação. Entre em contato com o suporte do EBANX (suporte@ebanx.com) para mais informações.',
				'BP-DR-49'                   => 'Ei, você esqueceu de preencher o número do seu cartão.',
				'BP-DR-51'                   => 'Ei, faltou uma informação aqui! Preencha com o nome que aparece em seu cartão.',
				'BP-DR-54'                   => 'Ops, faltou o CVV do cartão.',
				'BP-DR-56'                   => 'Ei, faltou preencher a data de vencimento do cartão.',
				'BP-DR-68'                   => 'Ei, você esqueceu de preencher o número da sua conta.',
				'BP-DR-69'                   => 'Ops! Você excedeu o número de caracteres permitidos, preencha novamente.',
				'BP-DR-70'                   => 'Ei, você esqueceu de preencher o número da sua agência.',
				'BP-DR-71'                   => 'Você excedeu o número de caracteres permitidos para o código do banco, preencha novamente.',
				'BP-DR-75'                   => 'O número do cartão informado é inválido. Confira se não houve um erro de digitação e tente de novo.',
				'BP-DR-83'                   => 'Apenas cartões do seu país de origem são permitidos para essa transação.',
				'BP-DR-84'                   => 'Seu pagamento já foi registrado, não precisa tentar de novo.',
				'BP-DR-90'                   => 'Ops, aconteceu algum problema. Entre em contato com o EBANX pelo e-mail suporte@ebanx.com para mais informações.',
				'BP-DR-93'                   => 'Ops, ocorreu um erro inesperado, tente novamente em alguns minutos.',
				'BP-DR-95'                   => 'Ops, confira se você preencheu mesmo com o seu nome e não com o número do cartão.',
				'BP-DR-97'                   => 'Você não pode parcelar suas compras com cartões pré-pagos. O que acha de tentar com um cartão de crédito?',
				'BP-DR-98'                   => 'O país do pagamento precisa ser igual ao seu país de residência, beleza?',
				'BP-DR-100'                  => 'Cartões de débito não realizam pagamentos parcelados, mas você pode tentar de novo com um cartão de crédito. :)',
				'BP-DR-101'                  => 'Ops! Esse cartão não está liberado para fazer compras na internet. Entre em contato com o seu banco para mais informações.',
				'BP-DR-102'                  => 'Ops, algo deu errado. Tente novamente com outro cartão.',
				'BP-R-12'                    => 'Desculpa, mas o número de parcelas selecionado não está disponível. :/',
				'BP-R-13'                    => 'O valor de cada parcela deve ser maior ou igual a R$X. Tente de novo com outro número de parcelas. :)',
				'BP-R-28'                    => 'Ops! O número de parcelas que você escolheu não está disponível para esse valor. Tente de novo com outra opção de parcela.',
				'BP-R-29'                    => 'O número de parcelas selecionado não está disponível. Tente de novo com outra opção de parcela.',
				'BP-R-30'                    => 'O número de parcelas que você selecionou não está disponível para essa compra. Tente de novo com outro número de parcela.',
				'BP-R-4'                     => 'Ops, faltou preencher o seu nome.',
				'BP-R-5'                     => 'Ei, faltou informar o seu e-mail.',
				'BP-ZIP-1'                   => 'Ei, você esqueceu de nos informar o seu CEP.',
				'BP-ZIP-2'                   => 'O CEP enviado não é válido. Por favor, tente de novo.',
				'BP-ZIP-3'                   => 'O CEP enviado é inválido ou inexistente. Por favor, tente de novo.',
				'MISSING-CARD-PARAMS'        => 'Verifique se os dados do cartão de crédito estão corretos.',
				'MISSING-DEVICE-FINGERPRINT' => 'Algo aconteceu e não conseguimos concluir a sua compra. Por favor tente novamente.',
				'MISSING-CVV'                => 'Por favor digite o CVV do seu cartão de crédito.',
				'MISSING-INSTALMENTS'        => 'Por favor escolha em quantas parcelas você quer pagar.',
				'MISSING-BANK-NAME'          => 'Escolha um banco que deseja efetuar a sua compra.',
				'MISSING-VOUCHER'            => 'Escolha o tipo de voucher que deseja para efetuar a sua compra.',
				'INVALID-SAFETYPAY-TYPE'     => 'Escolha uma opção para o método de pagamento SafetyPay.',
				'INVALID-FIELDS'             => 'Alguns campos não foram preenchidos corretamente. Por favor, verifique e tente novamente.',
				'INVALID-BILLING-COUNTRY'    => 'Por favor, escolha um país.',
				'INVALID-ADDRESS'            => 'Insira o seu endereço completo com o número da casa, apartamento ou estabelecimento.',
				'REFUSED-CC'                 => 'Não foi possível concluir a compra. Entre em contato com o banco/emissor do cartão ou tente novamente.',
				'SANDBOX-INVALID-CC-NUMBER'  => 'Detectamos que você está em modo Sandbox e por isso só permitimos apenas alguns números de cartões. <a href="https://www.ebanx.com/business/en/developers/integrations/testing/credit-card-test-numbers" target="_blank">Você pode utilizar um dos nossos cartões de teste acessando a EBANX Developer\'s Academy.</a>',
			),
			'es'    => array(
				'GENERAL'                    => 'No pudimos concluir tu compra. Por favor intenta nuevamente o entra en contacto con el sitio web.',
				'BP-DR-13'                   => '¡Ey!, olvidaste ingresar tu nombre.',
				'BP-DR-14'                   => '¡Ey!, su nombre tiene más caracteres que el límite permitido. Busque abreviado o coloque sólo un apellido.',
				'BP-DR-15'                   => '¡Espera! Falto ingresar tu correo electrónico.',
				'BP-DR-17'                   => 'Disculpa, pero ese correo electrónico no es válido.',
				'BP-DR-19'                   => '¡Ey!, no dejes de informar tu fecha de nacimiento.',
				'BP-DR-20'                   => 'La fecha de nacimiento debe seguir este formato: dd/mm/aaaa.',
				'BP-DR-21'                   => '¡Ey!, debes ser mayor de 16 años para realizar esa transacción.',
				'BP-DR-22'                   => '¡Lo sentimos!, pero el documento enviado no es válido.',
				'BP-DR-23'                   => 'Ingresa un documento con validez.',
				'BP-DR-24'                   => '¡Falta poco!, informa tu Código Postal.',
				'BP-DR-25'                   => '¡Ey!, has olvidado informar tu domicilio.',
				'BP-DR-26'                   => 'No dejes de informar el número de domicilio.',
				'BP-DR-27'                   => '¡Ups!, falto informar la Ciudad.',
				'BP-DR-28'                   => '¡Ups!, falto informar el Estado.',
				'BP-DR-29'                   => '-',
				'BP-DR-31'                   => '¡Ojo! Es obligatorio informar un número de teléfono.',
				'BP-DR-32'                   => 'El número de teléfono que informaste no es válido.',
				'BP-DR-34'                   => 'México: El número de mensualidades seleccionado no está disponible. Inténtelo de nuevo con otro número de mensualides.',
				'BP-DR-39'                   => '-',
				'BP-DR-40'                   => '¡Ups!, la transacción que deseas realizar ultrapasa el valor límite permitido por EBANX. Intenta realizar una transacción de menor valor.',
				'BP-DR-42'                   => 'Como tu transacción es con (Documento de cada país), es necesario señalas al responsable.',
				'BP-DR-43'                   => '¡Ey!, olvidaste ingresar tu nombre.',
				'BP-DR-44'                   => '¡Ups!, olvidaste informar el número de tu documento.',
				'BP-DR-45'                   => '¡Ey!, olvidaste informar la fecha de tu nacimiento.',
				'BP-DR-46'                   => '-',
				'BP-DR-49'                   => '¡Ey!, olvidaste introducir el número de tu tarjeta.',
				'BP-DR-51'                   => '¡Ups!, faltó llenar este campo. Llénalo con el nombre que aparece en tu tarjeta.',
				'BP-DR-54'                   => '¡Ups!, falto el CVV de la tarjeta.',
				'BP-DR-56'                   => '¡Ey!, has omitido la fecha de vencimiento de la tarjeta.',
				'BP-DR-68'                   => '¡Ey!, olvidaste ingresar el número de tu cuenta.',
				'BP-DR-69'                   => '¡Ups!, excediste el número de caracteres permitidos, inténtalo nuevamente.',
				'BP-DR-70'                   => '¡Ey!, olvidaste llenar el campo "agencia".',
				'BP-DR-71'                   => 'Excediste el número de caracteres permitidos para el código de el banco, inténtalo nuevamente.',
				'BP-DR-75'                   => 'El número de la tarjeta es invalido. Confirma si no hay un error de escritura y vuelva a intentarlo.',
				'BP-DR-83'                   => 'Esa tarjeta no es permitida para completar la transacción.',
				'BP-DR-84'                   => 'La transacción ha sido registrada, no es necesario volver a intentar.',
				'BP-DR-90'                   => '¡Ups!, ocurrió algún problema. Entra en contacto con EBANX, envía un correo a soporte@ebanx.com para más información.',
				'BP-DR-93'                   => 'Ocurrió un error inesperado, intentalo de nuevo en unos minutos más.',
				'BP-DR-95'                   => '¡Ups! Confirma que hallas llenado este campo con tu nombre y no con otros datos de la tarjeta.',
				'BP-DR-97'                   => 'Pagos en mensualidades no son permitidos para tarjetas de prepago.',
				'BP-DR-98'                   => '¡Ojo! El país desde el cual realizas tu pago debe corresponder al país donde resides.',
				'BP-DR-100'                  => 'Pagos en mensualidades no son permitidos para tarjetas de débito.',
				'BP-DR-101'                  => '¡Lo sentimos!, vuelva a intentarlo con otra tarjeta.',
				'BP-DR-102'                  => '¡Ups!, algo salió mal, vuelve a intentarlo con otra tarjeta.',
				'BP-R-12'                    => '¡Lo sentimos!, el número de mensualidades seleccionado no está disponible.',
				'BP-R-13'                    => 'El valor de cada mensualidad debe ser igual o mayor a $X.',
				'BP-R-28'                    => '¡Ups! El número de mensualidades seleccionado es mayor al permitido.',
				'BP-R-29'                    => '¡Lo sentimos!, el número de mensualidades seleccionado no está disponible.',
				'BP-R-30'                    => '¡Lo sentimos!, el número de mensualidades seleccionado no está disponible.',
				'BP-R-4'                     => '¡Ups!, falto informar tu nombre.',
				'BP-R-5'                     => '¡Ey!, falto informar tu correo electrónico.',
				'BP-ZIP-1'                   => '¡Ey!, olvidaste informar el Código Postal.',
				'BP-ZIP-2'                   => 'El Código Postal informado, no es válido. Por favor, ingresalo nuevamente.',
				'BP-ZIP-3'                   => 'El Código Postal es invalido o no existe. Por favor, prueba con otro.',
				'MISSING-CARD-PARAMS'        => 'Por favor, verifica que la información de la tarjeta esté correcta.',
				'MISSING-DEVICE-FINGERPRINT' => 'Hemos encontrado un error y no fue posible concluir la compra. Por favor intenta de nuevo.',
				'MISSING-CVV'                => 'Por favor, introduce el CVV de tu tarjeta de crédito.',
				'MISSING-INSTALMENTS'        => 'Por favor, escoge en cuántos meses sin intereses deseas pagar.',
				'MISSING-BANK-NAME'          => 'Por favor, escoge el banco para finalizar la compra.',
				'MISSING-VOUCHER'            => 'Por favor, escoge el tipo de voucher que desea para finalizar la compra.',
				'INVALID-SAFETYPAY-TYPE'     => 'Por favor, escoge una opción para el método de pago SafetyPay.',
				'INVALID-FIELDS'             => 'Algunos campos no fueron llenados correctamente. Por favor verifica e inténtalo de nuevo.',
				'INVALID-BILLING-COUNTRY'    => 'Por favor, escoge un país.',
				'INVALID-ADDRESS'            => 'Por favor, introduce tu dirección completa. Número de residencia o apartamento.',
				'REFUSED-CC'                 => 'No pudimos concluir tu compra. Ponte en contacto con el banco/emisor de la tarjeta o vuelve a intentarlo.',
				'SANDBOX-INVALID-CC-NUMBER'  => 'Detectamos que estás en modo Sandbox y por eso restringimos algunos números de tarjetas. <a href="https://www.ebanx.com/business/en/developers/integrations/testing/credit-card-test-numbers" target="_blank">Puedes utilizar una de nuestras tarjetas de prueba accediendo a EBANX Developer\'s Academy.</a>',
			),
		);
	}

	/**
	 * Get the error message
	 *
	 * @param Exception $exception
	 * @param string    $country
	 * @return string
	 */
	public static function get_error_message( $exception, $country ) {
		$code = $exception->getCode() ?: $exception->getMessage();

		$languages = array(
			'ar' => 'es',
			'mx' => 'es',
			'cl' => 'es',
			'pe' => 'es',
			'co' => 'es',
			'br' => 'pt-br',
		);
		$language  = $languages[ $country ];

		$errors = static::get_errors();

		if ( 'BP-DR-6' === $code && 'es' === $language ) {
			$error_info = array();
			preg_match(
				'/Amount must be greater than (\w{3}) (.+)/',
				$exception->getMessage(),
				$error_info
			);
			$amount   = $error_info[2];
			$currency = $error_info[1];
			return sprintf( $errors[ $language ][ $code ], wc_price( $amount, [ 'currency' => $currency ] ) );
		}

		return ! empty( $errors[ $language ][ $code ] ) ? $errors[ $language ][ $code ] : $errors[ $language ]['GENERAL'] . " ({$code})";
	}
}
