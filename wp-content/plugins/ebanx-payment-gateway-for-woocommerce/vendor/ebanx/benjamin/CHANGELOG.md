## 1.16.0
* Feature - Add BankTransfer as a payment method [#141](https://github.com/ebanx/benjamin/pull/141)

## 1.15.1
* Fix - Use customer config to get instalments [#140](https://github.com/ebanx/benjamin/pull/140)

## 1.15.0
* Feature - Add function that return instalments by country [#139](https://github.com/ebanx/benjamin/pull/139)

## 1.14.0
* Feature - Change APIs URL to increase performance [138](https://github.com/ebanx/benjamin/pull/138)

## 1.13.0
* Feature - Add manual review flag to Request model [#136](https://github.com/ebanx/benjamin/pull/136)

## 1.12.0
* Feature - Removed guzzle dependency [#135](https://github.com/ebanx/benjamin/pull/135)

## 1.11.0
* Feature - Send manual review flag to EBANX [#133](https://github.com/ebanx/benjamin/pull/133)

## 1.10.1
* Fix - EBANX Account should not accept BRL [#132](https://github.com/ebanx/benjamin/pull/132)

## 1.10.0
* Feature - Send a profile id for risk analysis model [#131](https://github.com/ebanx/benjamin/pull/131)

## 1.9.1
* Fix - Getting Pago Efectivo voucher now works as intented [#130](https://github.com/ebanx/benjamin/pull/130)

## 1.9.0
* Feature - Support for document type [#126](https://github.com/ebanx/benjamin/pull/126)
* Feature - Add method for canceling open payments [#128](https://github.com/ebanx/benjamin/pull/128)
* Feature - Add methods for validating if passed keys are valid [#129](https://github.com/ebanx/benjamin/pull/129)

## 1.8.2
* Fix - Send document also on LATAM countries requests [#123](https://github.com/ebanx/benjamin/pull/123)

## 1.8.1
* Fix - Put due date inside payment key on request payload [#119](https://github.com/ebanx/benjamin/pull/119)

## 1.8.0
* Feature - Added getTicketHtml method to main façade to work with printable gateway payments [#114](https://github.com/ebanx/benjamin/pull/114)

## 1.7.0
* Feature - Create method to translate country names into pay's required form [#111](https://github.com/ebanx/benjamin/pull/111)
* Fix - Missing Ecuador country code translation for API [#110](https://github.com/ebanx/benjamin/pull/110)

## 1.6.0
* Feature - Acquirer instalment limits for all creditcard currencies [#103](https://github.com/ebanx/benjamin/pull/103)
* Feature - Add creditcard on Argentina [#104](https://github.com/ebanx/benjamin/pull/104)
* Feature - Add safetypay to Ecuador [#105](https://github.com/ebanx/benjamin/pull/105)
* Feature - Spei isn't a Redirect method anymore [#106](https://github.com/ebanx/benjamin/pull/106)
* Feature - Redirect URL for hosted gateway request model [#108](https://github.com/ebanx/benjamin/pull/108)
* Fix - Check if country is in array before returning [#107](https://github.com/ebanx/benjamin/pull/107)

## 1.5.0
* Feature - Add address model to request [#101](https://github.com/ebanx/benjamin/pull/101)
* Feature - Hosted gateway full request support [#102](https://github.com/ebanx/benjamin/pull/102)

## 1.4.1
* Fix - Default http client not respecting connection mode set in config [#100](https://github.com/ebanx/benjamin/pull/100)

## 1.4.0
* Feature - Hosted payment gateway, request method is deprecated [#94](https://github.com/ebanx/benjamin/pull/94)
* Feature - ISO codes for country model [#97](https://github.com/ebanx/benjamin/pull/97)
* Fix - Internal gateway implementation of http service override [#95](https://github.com/ebanx/benjamin/pull/95)
* Fix - Code style [#96](https://github.com/ebanx/benjamin/pull/96)
* Fix - Credit card adapter optional field null reference error [#98](https://github.com/ebanx/benjamin/pull/98)

## 1.3.0
* Feature - Fetch rate is now a public method [#93](https://github.com/ebanx/benjamin/pull/93)
* Fix - Argentina can now process payments in local currency correctly [#92](https://github.com/ebanx/benjamin/pull/92)

## 1.2.1
* Added the ability to mock the API's http client for facade class as a means to avoid dependent projects to hit the API during their tests. [#90](https://github.com/ebanx/benjamin/pull/90)

## 1.2.0
* Feature - Merchant taxes flag support [#84](https://github.com/ebanx/benjamin/pull/84)
* Fix - Check if birthdate exists before formatting [#88](https://github.com/ebanx/benjamin/pull/88)
* Fix - Fix first instalment for credit card payments below minimum amount [#89](https://github.com/ebanx/benjamin/pull/89)

## 1.1.0
* Feature - Added SPEI as payment method on Mexico [#77](https://github.com/ebanx/benjamin/pull/77)
* Feature - Added Rapipago as payment method on Argentina [#78](https://github.com/ebanx/benjamin/pull/78)
* Feature - Added PagoFacil as payment method on Argentina [#79](https://github.com/ebanx/benjamin/pull/79)
* Feature - Added Otros Cupones as payment method on Argentina [#80](https://github.com/ebanx/benjamin/pull/80)

## 1.0.0
* First stable version
